<?php
/**
 * Change Password Page
 */
require_once '../config/database.php';
require_once '../config/functions.php';

// Initialize session
initSession();

// Include header
require_once '../includes/header.php';

// Process form submission
$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get input
    $currentPassword = $_POST['current_password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    
    // Validate input
    if (empty($currentPassword)) {
        $errors[] = 'Current password is required';
    }
    
    if (empty($newPassword)) {
        $errors[] = 'New password is required';
    } else {
        $passwordValidation = validatePassword($newPassword);
        if (!$passwordValidation['valid']) {
            $errors = array_merge($errors, $passwordValidation['errors']);
        }
    }
    
    if ($newPassword !== $confirmPassword) {
        $errors[] = 'New passwords do not match';
    }
    
    // If no validation errors, check current password and update
    if (empty($errors)) {
        // Get current user's password hash
        $userSql = "SELECT password_hash FROM users WHERE id = ?";
        $user = fetchOne($userSql, "s", [$_SESSION['user_id']]);
        
        if ($user && verifyPassword($currentPassword, $user['password_hash'])) {
            // Current password is correct, update with new password
            $passwordHash = hashPassword($newPassword);
            
            $updateSql = "UPDATE users SET password_hash = ?, updated_at = ? WHERE id = ?";
            $result = executeUpdate($updateSql, "sss", [$passwordHash, getCurrentDateTime(), $_SESSION['user_id']]);
            
            if ($result !== false) {
                $success = true;
            } else {
                $errors[] = 'Failed to update password. Please try again.';
            }
        } else {
            $errors[] = 'Current password is incorrect';
        }
    }
}
?>

<!-- Change password page content -->
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Change Password</h3>
            </div>
            <div class="card-body">
                <?php if ($success): ?>
                    <div class="alert alert-success">
                        <h4 class="alert-title">Password Changed</h4>
                        <div class="text-muted">Your password has been updated successfully.</div>
                    </div>
                    
                    <div class="form-footer">
                        <a href="<?php echo BASE_URL; ?>/users/profile.php" class="btn btn-primary">Return to Profile</a>
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
                    
                    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
                        <div class="mb-3">
                            <label class="form-label required">Current Password</label>
                            <input type="password" name="current_password" class="form-control" placeholder="Enter current password" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label required">New Password</label>
                            <input type="password" name="new_password" class="form-control" placeholder="Enter new password" required>
                            <small class="form-hint">
                                Password must be at least <?php echo PASSWORD_MIN_LENGTH; ?> characters long and include 
                                uppercase letters, lowercase letters, numbers, and special characters.
                            </small>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label required">Confirm New Password</label>
                            <input type="password" name="confirm_password" class="form-control" placeholder="Confirm new password" required>
                        </div>
                        
                        <div class="form-footer">
                            <a href="<?php echo BASE_URL; ?>/users/profile.php" class="btn btn-outline-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary ms-2">Change Password</button>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php
// Include footer
require_once '../includes/footer.php';
?>