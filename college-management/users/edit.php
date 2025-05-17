<?php
/**
 * Create User Page
 */
require_once '../config/database.php';
require_once '../config/functions.php';

// Initialize session
initSession();

// Include header
require_once '../includes/header.php';

// Check if user has permission to create users
requirePermission('user_create');

// Initialize variables
$errors = [];
$userData = [
    'username' => '',
    'email' => '',
    'is_active' => true,
    'roles' => []
];

// Get available roles
$rolesSql = "SELECT id, name, description FROM roles ORDER BY name";
$roles = fetchAll($rolesSql);

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get and sanitize input
    $userData['username'] = sanitizeInput($_POST['username'] ?? '');
    $userData['email'] = sanitizeInput($_POST['email'] ?? '');
    $userData['is_active'] = isset($_POST['is_active']) && $_POST['is_active'] == 1;
    $userData['roles'] = $_POST['roles'] ?? [];
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    
    // Validate input
    if (empty($userData['username'])) {
        $errors[] = 'Username is required';
    } else {
        // Check if username already exists
        $checkSql = "SELECT id FROM users WHERE username = ?";
        $existingUser = fetchOne($checkSql, "s", [$userData['username']]);
        if ($existingUser) {
            $errors[] = 'Username already exists';
        }
    }
    
    if (empty($userData['email'])) {
        $errors[] = 'Email is required';
    } else if (!isValidEmail($userData['email'])) {
        $errors[] = 'Please enter a valid email address';
    } else {
        // Check if email already exists
        $checkSql = "SELECT id FROM users WHERE email = ?";
        $existingEmail = fetchOne($checkSql, "s", [$userData['email']]);
        if ($existingEmail) {
            $errors[] = 'Email already exists';
        }
    }
    
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
    
    if (empty($userData['roles'])) {
        $errors[] = 'Please select at least one role';
    }
    
    // If no errors, create the user
    if (empty($errors)) {
        // Hash the password
        $passwordHash = hashPassword($password);
        
        // Generate a UUID
        $userId = generateUuid();
        
        // Insert the user
        $insertSql = "INSERT INTO users (id, username, password_hash, email, is_active, is_verified, 
                     created_at, updated_at, created_by) 
                     VALUES (?, ?, ?, ?, ?, 0, ?, ?, ?)";
        $result = executeUpdate($insertSql, "ssssisss", [
            $userId,
            $userData['username'],
            $passwordHash,
            $userData['email'],
            $userData['is_active'] ? 1 : 0,
            getCurrentDateTime(),
            getCurrentDateTime(),
            $_SESSION['user_id']
        ]);
        
        if ($result) {
            // Assign roles to the user
            foreach ($userData['roles'] as $roleId) {
                $roleSql = "INSERT INTO user_roles (user_id, role_id) VALUES (?, ?)";
                executeUpdate($roleSql, "si", [$userId, $roleId]);
            }
            
            // Redirect to user list with success message
            setFlashMessage('success', 'User created successfully');
            redirect(BASE_URL . '/users/index.php');
        } else {
            $errors[] = 'Failed to create user. Please try again.';
        }
    }
}
?>

<!-- Create user form -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Create New User</h3>
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
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label required">Username</label>
                    <input type="text" name="username" class="form-control" placeholder="Enter username" value="<?php echo $userData['username']; ?>" required>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label class="form-label required">Email</label>
                    <input type="email" name="email" class="form-control" placeholder="Enter email" value="<?php echo $userData['email']; ?>" required>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label class="form-label required">Password</label>
                    <input type="password" name="password" class="form-control" placeholder="Enter password" required>
                    <small class="form-hint">
                        Password must be at least <?php echo PASSWORD_MIN_LENGTH; ?> characters long and include 
                        uppercase letters, lowercase letters, numbers, and special characters.
                    </small>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label class="form-label required">Confirm Password</label>
                    <input type="password" name="confirm_password" class="form-control" placeholder="Confirm password" required>
                </div>
                
                <div class="col-md-12 mb-3">
                    <label class="form-check">
                        <input type="checkbox" name="is_active" value="1" class="form-check-input" <?php echo $userData['is_active'] ? 'checked' : ''; ?>>
                        <span class="form-check-label">Active</span>
                    </label>
                </div>
                
                <div class="col-md-12 mb-3">
                    <label class="form-label required">Roles</label>
                    <div class="form-selectgroup">
                        <?php foreach ($roles as $role): ?>
                            <label class="form-selectgroup-item">
                                <input type="checkbox" name="roles[]" value="<?php echo $role['id']; ?>" class="form-selectgroup-input" 
                                       <?php echo in_array($role['id'], $userData['roles']) ? 'checked' : ''; ?>>
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
                <button type="submit" class="btn btn-primary ms-2">Create User</button>
            </div>
        </form>
    </div>
</div>

<?php
// Include footer
require_once '../includes/footer.php';
?>