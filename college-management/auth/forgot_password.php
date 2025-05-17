<?php
/**
 * Forgot Password Page
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
$email = '';

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get and sanitize input
    $email = sanitizeInput($_POST['email'] ?? '');
    
    // Validate input
    if (empty($email)) {
        $errors[] = 'Email is required';
    } else if (!isValidEmail($email)) {
        $errors[] = 'Please enter a valid email address';
    }
    
    // If no errors, process the password reset request
    if (empty($errors)) {
        // Check if email exists in the database
        $sql = "SELECT id, username FROM users WHERE email = ? AND is_active = 1";
        $user = fetchOne($sql, "s", [$email]);
        
        if ($user) {
            // Generate a unique token
            $token = bin2hex(random_bytes(32));
            $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
            
            // Delete any existing tokens for this user
            $deleteSql = "DELETE FROM password_resets WHERE user_id = ?";
            executeUpdate($deleteSql, "s", [$user['id']]);
            
            // Insert the new token
            $insertSql = "INSERT INTO password_resets (user_id, token, expires_at) VALUES (?, ?, ?)";
            $tokenId = insertAndGetId($insertSql, "sss", [$user['id'], $token, $expires]);
            
            if ($tokenId) {
                // In a real application, this would send an email
                // For now, just display the reset link
                $resetLink = BASE_URL . '/auth/reset_password.php?token=' . $token;
                
                // Set success message
                $success = true;
                
                // Clear email to prevent resubmission
                $email = '';
            } else {
                $errors[] = 'An error occurred. Please try again later.';
            }
        } else {
            // To prevent user enumeration, we'll show a success message even if the email doesn't exist
            $success = true;
            $email = '';
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
                    <h2 class="text-center mb-4">Forgot Password</h2>
                    
                    <?php if ($success): ?>
                        <div class="alert alert-success mb-3">
                            <h4 class="alert-title">Password Reset Email Sent</h4>
                            <p>If an account with this email exists, you will receive instructions on how to reset your password.</p>
                            
                            <!-- In a real application, this would not be shown, but is useful for testing purposes -->
                            <?php if (isset($resetLink)): ?>
                                <p class="mb-0"><strong>Reset Link:</strong> <a href="<?php echo $resetLink; ?>"><?php echo $resetLink; ?></a></p>
                            <?php endif; ?>
                        </div>
                        
                        <div class="text-center">
                            <a href="login.php" class="btn btn-primary">Return to Login</a>
                        </div>
                    <?php else: ?>
                        <?php if (!empty($errors)): ?>
                            <div class="alert alert-danger mb-3">
                                <ul class="mb-0">
                                    <?php foreach ($errors as $error): ?>
                                        <li><?php echo $error; ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                        
                        <p class="text-muted mb-4">Enter your email address and we'll send you a link to reset your password.</p>
                        
                        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
                            <div class="mb-3">
                                <label class="form-label">Email address</label>
                                <input type="email" name="email" class="form-control" placeholder="Enter email" value="<?php echo $email; ?>" required>
                            </div>
                            <div class="form-footer">
                                <button type="submit" class="btn btn-primary w-100">Send Reset Link</button>
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