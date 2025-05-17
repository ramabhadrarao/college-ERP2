<?php
/**
 * Edit User Page
 */
require_once '../config/database.php';
require_once '../config/functions.php';

// Initialize session
initSession();

// Include header
require_once '../includes/header.php';

// Check if user has permission to edit users
requirePermission('user_edit');

// Get user ID from URL parameter
$userId = sanitizeInput($_GET['id'] ?? '');

if (empty($userId)) {
    setFlashMessage('error', 'Invalid user ID');
    redirect(BASE_URL . '/users/index.php');
}

// Get user data
$userSql = "SELECT u.id, u.username, u.email, u.is_active, u.is_verified 
            FROM users u 
            WHERE u.id = ?";
$user = fetchOne($userSql, "s", [$userId]);

if (!$user) {
    setFlashMessage('error', 'User not found');
    redirect(BASE_URL . '/users/index.php');
}

// Get user's current roles
$userRolesSql = "SELECT role_id FROM user_roles WHERE user_id = ?";
$userRolesResult = fetchAll($userRolesSql, "s", [$userId]);
$userRoles = [];
foreach ($userRolesResult as $role) {
    $userRoles[] = $role['role_id'];
}

// Get available roles
$rolesSql = "SELECT id, name, description FROM roles ORDER BY name";
$roles = fetchAll($rolesSql);

// Initialize error array
$errors = [];

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get and sanitize input
    $email = sanitizeInput($_POST['email'] ?? '');
    $isActive = isset($_POST['is_active']) && $_POST['is_active'] == 1;
    $isVerified = isset($_POST['is_verified']) && $_POST['is_verified'] == 1;
    $selectedRoles = $_POST['roles'] ?? [];
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    
    // Validate input
    if (empty($email)) {
        $errors[] = 'Email is required';
    } else if (!isValidEmail($email)) {
        $errors[] = 'Please enter a valid email address';
    } else if ($email !== $user['email']) {
        // Check if email is already in use by another user
        $checkSql = "SELECT id FROM users WHERE email = ? AND id != ?";
        $existingEmail = fetchOne($checkSql, "ss", [$email, $userId]);
        if ($existingEmail) {
            $errors[] = 'Email already exists';
        }
    }
    
    // Password validation (only if a new password is being set)
    if (!empty($password)) {
        $passwordValidation = validatePassword($password);
        if (!$passwordValidation['valid']) {
            $errors = array_merge($errors, $passwordValidation['errors']);
        }
        
        if ($password !== $confirmPassword) {
            $errors[] = 'Passwords do not match';
        }
    }
    
    if (empty($selectedRoles)) {
        $errors[] = 'Please select at least one role';
    }
    
    // Check if user is removing admin role from the last admin
    $isRemovingLastAdmin = false;
    
    // Check if user is an admin
    $isAdminSql = "SELECT COUNT(*) as admin_count 
                  FROM user_roles ur 
                  JOIN roles r ON ur.role_id = r.id 
                  WHERE ur.user_id = ? AND r.is_system_role = 1 AND r.name = 'Admin'";
    $isAdminResult = fetchOne($isAdminSql, "s", [$userId]);
    $isAdmin = $isAdminResult && $isAdminResult['admin_count'] > 0;
    
    if ($isAdmin) {
        // Get admin role ID
        $adminRoleSql = "SELECT id FROM roles WHERE is_system_role = 1 AND name = 'Admin'";
        $adminRoleResult = fetchOne($adminRoleSql);
        
        if ($adminRoleResult && !in_array($adminRoleResult['id'], $selectedRoles)) {
            // User is trying to remove admin role
            $adminCountSql = "SELECT COUNT(*) as admin_count 
                             FROM users u 
                             JOIN user_roles ur ON u.id = ur.user_id 
                             JOIN roles r ON ur.role_id = r.id 
                             WHERE r.is_system_role = 1 AND r.name = 'Admin'";
            $adminCountResult = fetchOne($adminCountSql);
            
            if ($adminCountResult && $adminCountResult['admin_count'] <= 1) {
                $isRemovingLastAdmin = true;
                $errors[] = 'Cannot remove admin role from the last admin user';
            }
        }
    }
    
    // If no errors, update the user
    if (empty($errors)) {
        // Prepare update statement
        $updateSql = "UPDATE users SET email = ?, is_active = ?, is_verified = ?, 
                     updated_at = ?, updated_by = ?";
        $params = [$email, $isActive ? 1 : 0, $isVerified ? 1 : 0, getCurrentDateTime(), $_SESSION['user_id']];
        $types = "siiss";
        
        // Add password to update if a new one is set
        if (!empty($password)) {
            $passwordHash = hashPassword($password);
            $updateSql .= ", password_hash = ?, failed_login_attempts = 0, lockout_until = NULL";
            $params[] = $passwordHash;
            $types .= "s";
        }
        
        // Complete the SQL
        $updateSql .= " WHERE id = ?";
        $params[] = $userId;
        $types .= "s";
        
        // Execute update
        $result = executeUpdate($updateSql, $types, $params);
        
        if ($result !== false) {
            // Delete existing roles
            $deleteRolesSql = "DELETE FROM user_roles WHERE user_id = ?";
            executeUpdate($deleteRolesSql, "s", [$userId]);
            
            // Assign new roles
            foreach ($selectedRoles as $roleId) {
                $roleSql = "INSERT INTO user_roles (user_id, role_id) VALUES (?, ?)";
                executeUpdate($roleSql, "si", [$userId, $roleId]);
            }
            
            // Redirect to user list with success message
            setFlashMessage('success', 'User updated successfully');
            redirect(BASE_URL . '/users/index.php');
        } else {
            $errors[] = 'Failed to update user. Please try again.';
        }
    }
    
    // Update user object with submitted values for re-rendering the form
    $user['email'] = $email;
    $user['is_active'] = $isActive;
    $user['is_verified'] = $isVerified;
    $userRoles = $selectedRoles;
}
?>

<!-- Edit user form -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Edit User: <?php echo $user['username']; ?></h3>
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
        
        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'] . '?id=' . $userId); ?>" method="post">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" class="form-control" value="<?php echo $user['username']; ?>" readonly disabled>
                    <small class="form-hint">Username cannot be changed</small>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label class="form-label required">Email</label>
                    <input type="email" name="email" class="form-control" placeholder="Enter email" value="<?php echo $user['email']; ?>" required>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label class="form-label">New Password</label>
                    <input type="password" name="password" class="form-control" placeholder="Enter new password (leave blank to keep current)">
                    <small class="form-hint">
                        Leave blank to keep current password. New password must be at least <?php echo PASSWORD_MIN_LENGTH; ?> characters long
                        and include uppercase letters, lowercase letters, numbers, and special characters.
                    </small>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label class="form-label">Confirm New Password</label>
                    <input type="password" name="confirm_password" class="form-control" placeholder="Confirm new password">
                </div>
                
                <div class="col-md-6 mb-3">
                    <label class="form-check">
                        <input type="checkbox" name="is_active" value="1" class="form-check-input" <?php echo $user['is_active'] ? 'checked' : ''; ?>>
                        <span class="form-check-label">Active</span>
                    </label>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label class="form-check">
                        <input type="checkbox" name="is_verified" value="1" class="form-check-input" <?php echo $user['is_verified'] ? 'checked' : ''; ?>>
                        <span class="form-check-label">Email Verified</span>
                    </label>
                </div>
                
                <div class="col-md-12 mb-3">
                    <label class="form-label required">Roles</label>
                    <div class="form-selectgroup">
                        <?php foreach ($roles as $role): ?>
                            <label class="form-selectgroup-item">
                                <input type="checkbox" name="roles[]" value="<?php echo $role['id']; ?>" class="form-selectgroup-input" 
                                       <?php echo in_array($role['id'], $userRoles) ? 'checked' : ''; ?>>
                                <span class="form-selectgroup-label">
                                    <?php echo $role['name']; ?>
                                    <?php if (!empty($role['description'])): ?>
                                        <span class="form-help" data-bs-toggle="popover" data-bs-placement="top" data-bs-html="true"
                                              data-bs-content="<?php echo htmlspecialchars($role['description']); ?>">?</span>
                                    <?php endif; ?>
                                </span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            
            <div class="form-footer">
                <a href="<?php echo BASE_URL; ?>/users/index.php" class="btn btn-outline-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary ms-2">Update User</button>
            </div>
        </form>
    </div>
</div>

<?php
// Include footer
require_once '../includes/footer.php';
?>