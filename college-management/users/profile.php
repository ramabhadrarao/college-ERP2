<?php
/**
 * User Profile Page
 */
require_once '../config/database.php';
require_once '../config/functions.php';

// Initialize session
initSession();

// Include header
require_once '../includes/header.php';

// Get current user's data
$userSql = "SELECT u.id, u.username, u.email, u.is_active, u.is_verified, u.last_login_at, 
           u.created_at, u.profile_picture_path
           FROM users u 
           WHERE u.id = ?";
$user = fetchOne($userSql, "s", [$_SESSION['user_id']]);

if (!$user) {
    setFlashMessage('error', 'User not found');
    redirect(BASE_URL . '/index.php');
}

// Get user's roles
$rolesSql = "SELECT r.name, r.description 
            FROM roles r
            JOIN user_roles ur ON r.id = ur.role_id
            WHERE ur.user_id = ?
            ORDER BY r.name";
$roles = fetchAll($rolesSql, "s", [$_SESSION['user_id']]);

// Process form submission for profile update
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get and sanitize input
    $email = sanitizeInput($_POST['email'] ?? '');
    
    // Validate input
    if (empty($email)) {
        $errors[] = 'Email is required';
    } else if (!isValidEmail($email)) {
        $errors[] = 'Please enter a valid email address';
    } else if ($email !== $user['email']) {
        // Check if email is already in use by another user
        $checkSql = "SELECT id FROM users WHERE email = ? AND id != ?";
        $existingEmail = fetchOne($checkSql, "ss", [$email, $_SESSION['user_id']]);
        if ($existingEmail) {
            $errors[] = 'Email already exists';
        }
    }
    
    // If no errors, update the profile
    if (empty($errors)) {
        $updateSql = "UPDATE users SET email = ?, updated_at = ? WHERE id = ?";
        $result = executeUpdate($updateSql, "sss", [$email, getCurrentDateTime(), $_SESSION['user_id']]);
        
        if ($result !== false) {
            // Update session data
            $_SESSION['email'] = $email;
            $user['email'] = $email;
            
            setFlashMessage('success', 'Profile updated successfully');
            redirect(BASE_URL . '/users/profile.php');
        } else {
            $errors[] = 'Failed to update profile. Please try again.';
        }
    }
}
?>

<!-- User profile page content -->
<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Profile Information</h3>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center mb-4">
                    <span class="avatar avatar-xl me-3">
                        <?php if ($user['profile_picture_path']): ?>
                            <img src="<?php echo BASE_URL; ?>/uploads/<?php echo $user['profile_picture_path']; ?>" alt="Profile Picture">
                        <?php else: ?>
                            <i class="ti ti-user"></i>
                        <?php endif; ?>
                    </span>
                    <div>
                        <h3 class="mb-0"><?php echo $user['username']; ?></h3>
                        <div class="text-muted"><?php echo $user['email']; ?></div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <h4 class="mb-1">Status</h4>
                    <div>
                        <?php if ($user['is_active']): ?>
                            <span class="badge bg-success me-1">Active</span>
                        <?php else: ?>
                            <span class="badge bg-secondary me-1">Inactive</span>
                        <?php endif; ?>
                        
                        <?php if (!$user['is_verified']): ?>
                            <span class="badge bg-warning me-1">Unverified</span>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="mb-3">
                    <h4 class="mb-1">Roles</h4>
                    <div>
                        <?php foreach ($roles as $role): ?>
                            <span class="badge bg-primary me-1"><?php echo $role['name']; ?></span>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <div class="mb-3">
                    <h4 class="mb-1">Last Login</h4>
                    <div><?php echo $user['last_login_at'] ? formatDate($user['last_login_at'], 'd M Y H:i') : 'Never'; ?></div>
                </div>
                
                <div class="mb-3">
                    <h4 class="mb-1">Account Created</h4>
                    <div><?php echo formatDate($user['created_at'], 'd M Y'); ?></div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Edit Profile</h3>
            </div>
            <div class="card-body">
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
                        <label class="form-label">Username</label>
                        <input type="text" class="form-control" value="<?php echo $user['username']; ?>" disabled readonly>
                        <small class="form-hint">Username cannot be changed</small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label required">Email</label>
                        <input type="email" name="email" class="form-control" placeholder="Enter email" value="<?php echo $user['email']; ?>" required>
                    </div>
                    
                    <div class="form-footer">
                        <button type="submit" class="btn btn-primary">Update Profile</button>
                        <a href="<?php echo BASE_URL; ?>/users/change_password.php" class="btn btn-outline-secondary ms-2">
                            Change Password
                        </a>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Profile Picture Upload (in a future version) -->
        <div class="card mt-3">
            <div class="card-header">
                <h3 class="card-title">Profile Picture</h3>
            </div>
            <div class="card-body">
                <p>Profile picture upload functionality will be available in a future version.</p>
                <div class="alert alert-info">
                    <i class="ti ti-info-circle"></i> You can change your password from the <a href="<?php echo BASE_URL; ?>/users/change_password.php">Change Password</a> page.
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Include footer
require_once '../includes/footer.php';
?>