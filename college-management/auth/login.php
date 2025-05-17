<?php
/**
 * Login page
 */
require_once '../config/database.php';
require_once '../config/functions.php';

// Initialize session
initSession();

// Redirect if already logged in
if (isLoggedIn()) {
    redirect(BASE_URL . '/index.php');
}

$errors = [];
$username = '';

// Process login form if submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get and sanitize input
    $username = sanitizeInput($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']);
    
    // Validate input
    if (empty($username)) {
        $errors[] = 'Username is required';
    }
    
    if (empty($password)) {
        $errors[] = 'Password is required';
    }
    
    // If no validation errors, attempt login
    if (empty($errors)) {
        // Get user by username
        $sql = "SELECT id, username, password_hash, email, is_active, is_verified, 
                failed_login_attempts, lockout_until 
                FROM users 
                WHERE username = ?";
        $user = fetchOne($sql, "s", [$username]);
        
        // Check if user exists
        if ($user) {
            // Check if account is locked
            if ($user['lockout_until'] !== null && strtotime($user['lockout_until']) > time()) {
                $errors[] = 'Your account is temporarily locked. Please try again later or contact an administrator.';
            } 
            // Check if account is active
            else if ($user['is_active'] == 0) {
                $errors[] = 'Your account is inactive. Please contact an administrator.';
            } 
            // Verify password
            else if (verifyPassword($password, $user['password_hash'])) {
                // Password is correct, login successful
                
                // Reset failed login attempts
                $updateSql = "UPDATE users SET failed_login_attempts = 0, lockout_until = NULL, 
                             last_login_at = ? WHERE id = ?";
                executeUpdate($updateSql, "ss", [getCurrentDateTime(), $user['id']]);
                
                // Get user roles
                $rolesSql = "SELECT r.id, r.name, r.is_system_role 
                             FROM roles r
                             JOIN user_roles ur ON r.id = ur.role_id
                             WHERE ur.user_id = ?";
                $roles = fetchAll($rolesSql, "s", [$user['id']]);
                
                // Get user permissions
                $permSql = "SELECT DISTINCT p.name 
                            FROM permissions p
                            JOIN role_permissions rp ON p.id = rp.permission_id
                            JOIN user_roles ur ON rp.role_id = ur.role_id
                            WHERE ur.user_id = ?";
                $permResult = fetchAll($permSql, "s", [$user['id']]);
                
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
                
                // Store user data in session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['roles'] = $roles;
                $_SESSION['permissions'] = $permissions;
                $_SESSION['is_admin'] = $isAdmin;
                
                // Redirect to dashboard
                redirect(BASE_URL . '/index.php');
            } else {
                // Password is incorrect, increment failed login attempts
                $failedAttempts = $user['failed_login_attempts'] + 1;
                
                // Lock account if too many failed attempts
                $lockoutUntil = null;
                if ($failedAttempts >= MAX_LOGIN_ATTEMPTS) {
                    // Lock account for 15 minutes
                    $lockoutUntil = date('Y-m-d H:i:s', strtotime('+15 minutes'));
                }
                
                // Update failed login attempts
                $updateSql = "UPDATE users SET failed_login_attempts = ?, lockout_until = ? WHERE id = ?";
                executeUpdate($updateSql, "iss", [$failedAttempts, $lockoutUntil, $user['id']]);
                
                // Error message depends on how many attempts left
                $attemptsLeft = MAX_LOGIN_ATTEMPTS - $failedAttempts;
                if ($attemptsLeft <= 0) {
                    $errors[] = 'Too many failed login attempts. Your account has been temporarily locked.';
                } else {
                    $errors[] = "Invalid username or password. You have {$attemptsLeft} attempts remaining.";
                }
            }
        } else {
            // User not found
            $errors[] = 'Invalid username or password';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?php echo APP_NAME; ?></title>
    <!-- Tabler CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta17/dist/css/tabler.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@2.30.0/tabler-icons.min.css">
</head>
<body class="d-flex flex-column">
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
                                    <li><?php echo $error; ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    
                    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post" autocomplete="off">
                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" name="username" class="form-control" placeholder="Enter username" value="<?php echo $username; ?>" required autofocus>
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
    <!-- Tabler JS -->
    <script src="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta17/dist/js/tabler.min.js"></script>
</body>
</html>