<?php
/**
 * Create Role Page
 */
require_once '../config/database.php';
require_once '../config/functions.php';

// Initialize session
initSession();

// Include header
require_once '../includes/header.php';

// Check if user has permission to create roles
requirePermission('role_create');

// Initialize variables
$errors = [];
$roleData = [
    'name' => '',
    'description' => '',
    'permissions' => []
];

// Get available permissions grouped by module
$permissionsSql = "SELECT id, name, description, module FROM permissions ORDER BY module, name";
$permissionsResult = fetchAll($permissionsSql);

$permissionsByModule = [];
foreach ($permissionsResult as $permission) {
    $module = $permission['module'] ?? 'General';
    if (!isset($permissionsByModule[$module])) {
        $permissionsByModule[$module] = [];
    }
    $permissionsByModule[$module][] = $permission;
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get and sanitize input
    $roleData['name'] = sanitizeInput($_POST['name'] ?? '');
    $roleData['description'] = sanitizeInput($_POST['description'] ?? '');
    $roleData['permissions'] = $_POST['permissions'] ?? [];
    
    // Validate input
    if (empty($roleData['name'])) {
        $errors[] = 'Role name is required';
    } else {
        // Check if role name already exists
        $checkSql = "SELECT id FROM roles WHERE name = ?";
        $existingRole = fetchOne($checkSql, "s", [$roleData['name']]);
        if ($existingRole) {
            $errors[] = 'Role name already exists';
        }
    }
    
    // If no errors, create the role
    if (empty($errors)) {
        // Insert the role
        $insertSql = "INSERT INTO roles (name, description, is_system_role, created_at, updated_at) 
                     VALUES (?, ?, 0, ?, ?)";
        $roleId = insertAndGetId($insertSql, "ssss", [
            $roleData['name'],
            $roleData['description'],
            getCurrentDateTime(),
            getCurrentDateTime()
        ]);
        
        if ($roleId) {
            // Assign permissions to the role
            foreach ($roleData['permissions'] as $permissionId) {
                $permSql = "INSERT INTO role_permissions (role_id, permission_id, created_at) VALUES (?, ?, ?)";
                executeUpdate($permSql, "iis", [$roleId, $permissionId, getCurrentDateTime()]);
            }
            
            // Redirect to role list with success message
            setFlashMessage('success', 'Role created successfully');
            redirect(BASE_URL . '/roles/index.php');
        } else {
            $errors[] = 'Failed to create role. Please try again.';
        }
    }
}
?>

<!-- Create role form -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Create New Role</h3>
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
                    <label class="form-label required">Role Name</label>
                    <input type="text" name="name" class="form-control" placeholder="Enter role name" value="<?php echo $roleData['name']; ?>" required>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label class="form-label">Description</label>
                    <input type="text" name="description" class="form-control" placeholder="Enter role description" value="<?php echo $roleData['description']; ?>">
                </div>
                
                <div class="col-md-12 mb-3">
                    <label class="form-label">Permissions</label>
                    
                    <div class="accordion" id="permissions-accordion">
                        <?php $counter = 0; ?>
                        <?php foreach ($permissionsByModule as $module => $modulePermissions): ?>
                            <?php $counter++; ?>
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="heading-<?php echo $counter; ?>">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" 
                                            data-bs-target="#collapse-<?php echo $counter; ?>" aria-expanded="false" 
                                            aria-controls="collapse-<?php echo $counter; ?>">
                                        <?php echo $module; ?> (<?php echo count($modulePermissions); ?>)
                                    </button>
                                </h2>
                                <div id="collapse-<?php echo $counter; ?>" class="accordion-collapse collapse" 
                                     aria-labelledby="heading-<?php echo $counter; ?>" data-bs-parent="#permissions-accordion">
                                    <div class="accordion-body">
                                        <div class="mb-2">
                                            <button type="button" class="btn btn-sm btn-outline-primary select-all" data-module="<?php echo $module; ?>">
                                                Select All
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary deselect-all" data-module="<?php echo $module; ?>">
                                                Deselect All
                                            </button>
                                        </div>
                                        
                                        <div class="row g-2 module-permissions" data-module="<?php echo $module; ?>">
                                            <?php foreach ($modulePermissions as $permission): ?>
                                                <div class="col-md-6 col-lg-4">
                                                    <label class="form-check">
                                                        <input type="checkbox" name="permissions[]" value="<?php echo $permission['id']; ?>" class="form-check-input" 
                                                               <?php echo in_array($permission['id'], $roleData['permissions']) ? 'checked' : ''; ?>>
                                                        <span class="form-check-label">
                                                            <?php echo $permission['name']; ?>
                                                            <?php if (!empty($permission['description'])): ?>
                                                                <span class="form-help" data-bs-toggle="popover" data-bs-placement="top" data-bs-html="true"
                                                                      data-bs-content="<?php echo htmlspecialchars($permission['description']); ?>">?</span>
                                                            <?php endif; ?>
                                                        </span>
                                                    </label>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            
            <div class="form-footer">
                <a href="<?php echo BASE_URL; ?>/roles/index.php" class="btn btn-outline-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary ms-2">Create Role</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle select/deselect all buttons
    document.querySelectorAll('.select-all').forEach(function(button) {
        button.addEventListener('click', function() {
            const module = this.getAttribute('data-module');
            document.querySelectorAll(`.module-permissions[data-module="${module}"] input[type="checkbox"]`).forEach(function(checkbox) {
                checkbox.checked = true;
            });
        });
    });
    
    document.querySelectorAll('.deselect-all').forEach(function(button) {
        button.addEventListener('click', function() {
            const module = this.getAttribute('data-module');
            document.querySelectorAll(`.module-permissions[data-module="${module}"] input[type="checkbox"]`).forEach(function(checkbox) {
                checkbox.checked = false;
            });
        });
    });
});
</script>

<?php
// Include footer
require_once '../includes/footer.php';
?>