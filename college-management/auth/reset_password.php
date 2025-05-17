<?php
/**
 * Reset Password Page
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
$success = false;
$token = sanitizeInput($_GET['token'] ?? '');

// Validate token
if (empty($token)) {
    $errors[] = 'Invalid or expired token';
} else {
    // Check if token exists and is not expired
    $sql = "SELECT pr.id, pr.user_id, pr.expires_at, u.username, u.email 
            FROM password_resets pr
            JOIN users u ON pr.user_id = u.id
            WHERE pr.token = ? AND pr.expires_at > NOW()";
    $resetInfo = fetchOne($sql, "s", [$token]);
    
    if (!$resetInfo) {
        $errors[] = 'Invalid or expired token';
    }
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($errors)) {
    // Get and sanitize input
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    
    // Validate input
    if (empty($password)) {
        $errors[] = 'Password is required';
    } else {
        $passwordValidation = validatePassword($password);
        if (!$passwordValidation['valid']) {
            $errors = array_merge($errors, $passwordValidation['errors']);
        }
    }
    
    if ($password !== $confirmPassword) {
        $errors[] = 'Passwords do not match';
    }
    
    // If no errors, update the password
    if (empty($errors)) {
        // Hash the password
        $passwordHash = hashPassword($password);
        
        // Update the user's password
        $updateSql = "UPDATE users SET password_hash = ?, failed_login_attempts = 0, 
                     lockout_until = NULL, updated_at = ? WHERE id = ?";
        $updated = executeUpdate($updateSql, "sss", [$passwordHash, getCurrentDateTime(), $resetInfo['user_id']]);
        
        if ($updated) {
            // Delete the reset token
            $deleteSql = "DELETE FROM password_resets WHERE id = ?";
            executeUpdate($deleteSql, "i", [$resetInfo['id']]);
            
            // Set success flag
            $success = true;
        } else {
            $errors[] = 'An error occurred while updating your password. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - <?php echo APP_NAME; ?></title>
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
                    <h2 class="text-center mb-4">Reset Password</h2>
                    
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger mb-3">
                            <ul class="mb-0">
                                <?php foreach ($errors as $error): ?>
                                    <li><?php echo $error; ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        
                        <div class="text-center">
                            <a href="forgot_password.php" class="btn btn-primary">Request New Reset Link</a>
                        </div>
                    <?php elseif ($success): ?>
                        <div class="alert alert-success mb-3">
                            <h4 class="alert-title">Password Reset Successful</h4>
                            <p>Your password has been reset successfully. You can now log in with your new password.</p>
                        </div>
                        
                        <div class="text-center">
                            <a href="login.php" class="btn btn-primary">Log In</a>
                        </div>
                    <?php else: ?>
                        <p class="text-muted mb-4">Enter your new password below:</p>
                        
                        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'] . '?token=' . $token); ?>" method="post">
                            <div class="mb-3">
                                <label class="form-label">New Password</label>
                                <input type="password" name="password" class="form-control" placeholder="Enter new password" required>
                                <small class="form-hint">
                                    Password must be at least <?php echo PASSWORD_MIN_LENGTH; ?> characters long and include 
                                    uppercase letters, lowercase letters, numbers, and special characters.
                                </small>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Confirm Password</label>
                                <input type="password" name="confirm_password" class="form-control" placeholder="Confirm new password" required>
                            </div>
                            <div class="form-footer">
                                <button type="submit" class="btn btn-primary w-100">Reset Password</button>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
            <div class="text-center text-muted mt-3">
                <a href="login.php">Back to login</a>
            </div>
        </div>
    </div>
    <!-- Tabler JS -->
    <script src="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta17/dist/js/tabler.min.js"></script>
</body>
</html>