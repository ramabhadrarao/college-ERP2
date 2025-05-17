<?php
/**
 * Login page (DEBUG VERSION)
 * IMPORTANT: THIS VERSION CONTAINS EXTENSIVE DEBUGGING OUTPUT.
 * DO NOT USE IN PRODUCTION. REMOVE DEBUG CODE AFTER USE.
 */

// Enable full error reporting for debugging purposes
error_reporting(E_ALL);
ini_set('display_errors', 1);

$debug_messages = []; // Array to hold debug messages
$debug_messages[] = "DEBUG MODE ENABLED FOR LOGIN.PHP";
$debug_messages[] = "Timestamp: " . date('Y-m-d H:i:s');

require_once '../config/database.php';
$debug_messages[] = "Loaded: ../config/database.php";
require_once '../config/functions.php';
$debug_messages[] = "Loaded: ../config/functions.php";

// Initialize session
initSession();
$debug_messages[] = "Session initialized. Session ID: " . session_id();

// Redirect if already logged in
if (isLoggedIn()) {
    $debug_messages[] = "User is already logged in (User ID: " . ($_SESSION['user_id'] ?? 'N/A') . "). Redirecting to index.php.";
    // Output debug messages before redirecting if desired, or log them
    // For immediate redirect, this debug log point might be missed in browser.
    // Consider logging to a file for this specific case if needed.
    redirect(BASE_URL . '/index.php');
} else {
    $debug_messages[] = "User is not logged in. Proceeding with login page.";
}

$errors = [];
$username = '';

// Process login form if submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $debug_messages[] = "Login form submitted (POST request).";

    // Get and sanitize input
    $input_username_raw = $_POST['username'] ?? '';
    $input_password_raw = $_POST['password'] ?? '';
    $debug_messages[] = "Raw Username from POST: '" . $input_username_raw . "'";
    $debug_messages[] = "Raw Password from POST is " . (empty($input_password_raw) ? "EMPTY" : "NOT EMPTY (length: " . strlen($input_password_raw) . ")");


    $username = sanitizeInput($input_username_raw);
    $password = $input_password_raw; // Password should not be sanitized in a way that alters it before verification
    $remember = isset($_POST['remember']);

    $debug_messages[] = "Sanitized Username: '" . $username . "'";
    $debug_messages[] = "Password for verification is " . (empty($password) ? "EMPTY" : "NOT EMPTY");
    $debug_messages[] = "Remember me: " . ($remember ? 'Yes' : 'No');

    // Validate input
    if (empty($username)) {
        $errors[] = 'Username is required';
        $debug_messages[] = "Validation Error: Username is required.";
    }

    if (empty($password)) {
        $errors[] = 'Password is required';
        $debug_messages[] = "Validation Error: Password is required.";
    }

    $debug_messages[] = "Initial validation errors count: " . count($errors);

    // If no validation errors, attempt login
    if (empty($errors)) {
        $debug_messages[] = "Attempting login for username: '" . $username . "'";

        // Get user by username
        $sql = "SELECT id, username, password_hash, email, is_active, is_verified,
                failed_login_attempts, lockout_until
                FROM users
                WHERE username = ?";
        $debug_messages[] = "Executing SQL to fetch user: " . $sql . " with params: ['" . $username . "']";

        $user = fetchOne($sql, "s", [$username]);

        $debug_messages[] = "Result of fetchOne(): " . ($user ? "User data FOUND" : "User data NOT FOUND (null or false)");

        if ($user) {
            $debug_messages[] = "User data fetched successfully: " . htmlspecialchars(print_r($user, true));
            $debug_messages[] = "Stored Password Hash: '" . ($user['password_hash'] ?? 'NOT SET') . "'";
            $debug_messages[] = "Is Active: '" . ($user['is_active'] ?? 'NOT SET') . "'";
            $debug_messages[] = "Lockout Until: '" . ($user['lockout_until'] ?? 'NOT SET') . "'";

            // Check if account is locked
            if ($user['lockout_until'] !== null && strtotime($user['lockout_until']) > time()) {
                $errors[] = 'Your account is temporarily locked. Please try again later or contact an administrator.';
                $debug_messages[] = "Account Status: LOCKED until " . $user['lockout_until'];
            }
            // Check if account is active
            else if (!isset($user['is_active']) || $user['is_active'] == 0) { // Also check if is_active is even set
                $errors[] = 'Your account is inactive. Please contact an administrator.';
                $debug_messages[] = "Account Status: INACTIVE (is_active: " . ($user['is_active'] ?? 'NOT SET') . ")";
            }
            // Verify password
            else {
                $debug_messages[] = "Account is active and not locked. Proceeding to password verification.";
                $debug_messages[] = "Calling verifyPassword(input_password, stored_hash: '" . $user['password_hash'] . "')";
                $is_password_correct = verifyPassword($password, $user['password_hash']);
                $debug_messages[] = "Result of verifyPassword(): " . ($is_password_correct ? "TRUE (Password MATCHES)" : "FALSE (Password DOES NOT MATCH)");

                if ($is_password_correct) {
                    $debug_messages[] = "Password verification SUCCESSFUL. Logging in user.";
                    // Password is correct, login successful

                    // Reset failed login attempts
                    $updateSql = "UPDATE users SET failed_login_attempts = 0, lockout_until = NULL,
                                 last_login_at = ? WHERE id = ?";
                    $debug_messages[] = "Executing SQL to update user login stats: " . $updateSql;
                    executeUpdate($updateSql, "ss", [getCurrentDateTime(), $user['id']]);
                    $debug_messages[] = "User login stats updated.";

                    // Get user roles
                    $rolesSql = "SELECT r.id, r.name, r.is_system_role
                                 FROM roles r
                                 JOIN user_roles ur ON r.id = ur.role_id
                                 WHERE ur.user_id = ?";
                    $debug_messages[] = "Fetching user roles. SQL: " . $rolesSql . " with User ID: " . $user['id'];
                    $roles = fetchAll($rolesSql, "s", [$user['id']]);
                    $debug_messages[] = "Roles fetched: " . htmlspecialchars(print_r($roles, true));

                    // Get user permissions
                    $permSql = "SELECT DISTINCT p.name
                                FROM permissions p
                                JOIN role_permissions rp ON p.id = rp.permission_id
                                JOIN user_roles ur ON rp.role_id = ur.role_id
                                WHERE ur.user_id = ?";
                    $debug_messages[] = "Fetching user permissions. SQL: " . $permSql . " with User ID: " . $user['id'];
                    $permResult = fetchAll($permSql, "s", [$user['id']]);
                    $debug_messages[] = "Permissions fetched: " . htmlspecialchars(print_r($permResult, true));

                    $permissions = [];
                    foreach ($permResult as $perm) {
                        $permissions[] = $perm['name'];
                    }

                    // Check if user is a system admin
                    $isAdmin = false;
                    foreach ($roles as $role) {
                        if ($role['is_system_role'] == 1 && $role['name'] === 'Admin') {
                            $isAdmin = true;
                            break;
                        }
                    }
                    $debug_messages[] = "Is Admin: " . ($isAdmin ? "Yes" : "No");

                    // Store user data in session
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['email'] = $user['email'];
                    $_SESSION['roles'] = $roles;
                    $_SESSION['permissions'] = $permissions;
                    $_SESSION['is_admin'] = $isAdmin;
                    $debug_messages[] = "User data stored in session: " . htmlspecialchars(print_r($_SESSION, true));

                    // Redirect to dashboard
                    $debug_messages[] = "Redirecting to dashboard (index.php).";
                    // Before redirecting, it might be useful to see the debug messages.
                    // For critical debug, you might comment out the redirect and print debug messages.
                    // echo "<pre>" . implode("\n", array_map('htmlspecialchars', $debug_messages)) . "</pre>"; die(); // Uncomment to see debug before redirect
                    redirect(BASE_URL . '/index.php');
                } else {
                    // Password is incorrect, increment failed login attempts
                    $debug_messages[] = "Password verification FAILED.";
                    $failedAttempts = isset($user['failed_login_attempts']) ? $user['failed_login_attempts'] + 1 : 1;
                    $debug_messages[] = "Failed login attempts for user: " . $failedAttempts;

                    // Lock account if too many failed attempts
                    $lockoutUntil = $user['lockout_until'] ?? null; // Preserve existing lockout if any
                    if ($failedAttempts >= MAX_LOGIN_ATTEMPTS) {
                        // Lock account for 15 minutes
                        $lockoutUntil = date('Y-m-d H:i:s', strtotime('+15 minutes'));
                        $debug_messages[] = "MAX_LOGIN_ATTEMPTS reached. Locking account until: " . $lockoutUntil;
                    }

                    // Update failed login attempts
                    $updateSql = "UPDATE users SET failed_login_attempts = ?, lockout_until = ? WHERE id = ?";
                    $debug_messages[] = "Executing SQL to update failed attempts: " . $updateSql;
                    executeUpdate($updateSql, "iss", [$failedAttempts, $lockoutUntil, $user['id']]);
                    $debug_messages[] = "Failed attempts and lockout updated in DB.";

                    // Error message depends on how many attempts left
                    $attemptsLeft = MAX_LOGIN_ATTEMPTS - $failedAttempts;
                    if ($attemptsLeft <= 0) {
                        $errors[] = 'Too many failed login attempts. Your account has been temporarily locked.';
                        $debug_messages[] = "Error message: Account locked.";
                    } else {
                        $errors[] = "Invalid username or password. You have {$attemptsLeft} attempts remaining.";
                        $debug_messages[] = "Error message: Invalid credentials, {$attemptsLeft} attempts left.";
                    }
                }
            }
        } else {
            // User not found
            $errors[] = 'Invalid username or password';
            $debug_messages[] = "User not found by fetchOne. Generic error message displayed.";
        }
    } else {
        $debug_messages[] = "Skipped login attempt due to initial validation errors.";
    }
} else {
    $debug_messages[] = "Login page loaded (GET request or no POST data).";
}

// --- DEBUG OUTPUT ---
// Option 1: Output directly before HTML starts (can break page structure if not careful)
/*
if (!empty($debug_messages)) {
    echo "<div style='background-color: #f0f0f0; border: 1px solid #ccc; padding: 10px; margin: 10px; font-family: monospace; white-space: pre-wrap;'>";
    echo "<strong>DEBUG LOG FOR LOGIN.PHP:</strong><br>";
    foreach ($debug_messages as $msg) {
        echo htmlspecialchars($msg) . "<br>\n";
    }
    echo "</div>";
}
*/
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?php echo APP_NAME; ?> (DEBUG)</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta17/dist/css/tabler.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@2.30.0/tabler-icons.min.css">
    <style>
        .debug-log {
            background-color: #1e1e1e; /* Dark background */
            color: #d4d4d4; /* Light grey text */
            border: 1px solid #555;
            padding: 15px;
            margin: 15px;
            font-family: Consolas, "Courier New", monospace;
            white-space: pre-wrap; /* Respect newlines and spaces */
            overflow-x: auto; /* Allow horizontal scrolling if needed */
            font-size: 0.9em;
            max-height: 400px; /* Limit height and make scrollable */
            overflow-y: auto;
        }
        .debug-log strong {
            color: #4ec9b0; /* Teal for headings */
        }
    </style>
</head>
<body class="d-flex flex-column">

    <?php
    // Option 2: Output debug messages at the top of the body (more robust)
    if (!empty($debug_messages)) {
        echo "<div class='debug-log'>";
        echo "<strong>DEBUG LOG FOR LOGIN.PHP:</strong><br><br>";
        foreach ($debug_messages as $msg) {
            echo htmlspecialchars($msg) . "<br>\n";
        }
        echo "</div>";
    }
    ?>

    <div class="page page-center">
        <div class="container container-tight py-4">
            <div class="text-center mb-4">
                <h1><?php echo APP_NAME; ?></h1>
            </div>
            <div class="card card-md">
                <div class="card-body">
                    <h2 class="text-center mb-4">Login to your account</h2>

                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger mb-3">
                            <ul class="mb-0">
                                <?php foreach ($errors as $error): ?>
                                    <li><?php echo htmlspecialchars($error); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post" autocomplete="off">
                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" name="username" class="form-control" placeholder="Enter username" value="<?php echo htmlspecialchars($username); ?>" required autofocus>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" placeholder="Enter password" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-check">
                                <input type="checkbox" name="remember" class="form-check-input">
                                <span class="form-check-label">Remember me</span>
                            </label>
                        </div>
                        <div class="form-footer">
                            <button type="submit" class="btn btn-primary w-100">Sign in</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="text-center text-muted mt-3">
                <a href="forgot_password.php">Forgot password?</a>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta17/dist/js/tabler.min.js"></script>
</body>
</html>