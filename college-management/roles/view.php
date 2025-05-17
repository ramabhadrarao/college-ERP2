<?php
/**
 * Edit Role Page
 */
require_once '../config/database.php';
require_once '../config/functions.php';

// Initialize session
initSession();

// Include header
require_once '../includes/header.php';

// Check if user has permission to edit roles
requirePermission('role_edit');

// Get role ID from URL parameter
$roleId = sanitizeInput($_GET['id'] ?? '');

if (empty($roleId)) {
    setFlashMessage('error', 'Invalid role ID');
    redirect(BASE_URL . '/roles/index.php');
}

// Get role data
$roleSql = "SELECT r.id, r.name, r.description, r.is_system_role 
           FROM roles r 
           WHERE r.id = ?";
$role = fetchOne($roleSql, "i", [$roleId]);

if (!$role) {
    setFlashMessage('error', 'Role not found');
    redirect(BASE_URL . '/roles/index.php');
}

// Check if this is a system role
if ($role['is_system_role']) {
    setFlashMessage('error', 'System roles cannot be edited');
    redirect(BASE_URL . '/roles/view.php?id=' . $roleId);
}

// Get role's current permissions
$rolePermsSql = "SELECT permission_id FROM role_permissions WHERE role_id = ?";
$rolePermsResult = fetchAll($rolePermsSql, "i", [$roleId]);
$rolePermissions = [];
foreach ($rolePermsResult as $perm) {
    $rolePermissions[] = $perm['permission_id'];
}

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

// Initialize error array
$errors = [];

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get and sanitize input
    $name = sanitizeInput($_POST['name'] ?? '');
    $description = sanitizeInput($_POST['description'] ?? '');
    $selectedPermissions = $_POST['permissions'] ?? [];
    
    // Validate input
    if (empty($name)) {
        $errors[] = 'Role name is required';
    } else if ($name !== $role['name']) {
        // Check if role name is already in use by another role
        $checkSql = "SELECT id FROM roles WHERE name = ? AND id != ?";
        $existingRole = fetchOne($checkSql, "si", [$name, $roleId]);
        if ($existingRole) {
            $errors[] = 'Role name already exists';
        }
    }
    
    // If no errors, update the role
    if (empty($errors)) {
        // Update role
        $updateSql = "UPDATE roles SET name = ?, description = ?, updated_at = ? WHERE id = ?";
        $result = executeUpdate($updateSql, "sssi", [$name, $description, getCurrentDateTime(), $roleId]);
        
        if ($result !== false) {
            // Delete existing permissions
            $deletePermsSql = "DELETE FROM role_permissions WHERE role_id = ?";
            executeUpdate($deletePermsSql, "i", [$roleId]);
            
            // Assign new permissions
            foreach ($selectedPermissions as $permissionId) {
                $permSql = "INSERT INTO role_permissions (role_id, permission_id, created_at) VALUES (?, ?, ?)";
                executeUpdate($permSql, "iis", [$roleId, $permissionId, getCurrentDateTime()]);
            }
            
            // Redirect to role list with success message
            setFlashMessage('success', 'Role updated successfully');
            redirect(BASE_URL . '/roles/index.php');
        } else {
            $errors[] = 'Failed to update role. Please try again.';
        }
    }
    
    // Update role object with submitted values for re-rendering the form
    $role['name'] = $name;
    $role['description'] = $description;
    $rolePermissions = $selectedPermissions;
}
?>

<!-- Edit role form -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Edit Role: <?php echo $role['name']; ?></h3>
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
        
        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'] . '?id=' . $roleId); ?>" method="post">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label required">Role Name</label>
                    <input type="text" name="name" class="form-control" placeholder="Enter role name" value="<?php echo $role['name']; ?>" required>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label class="form-label">Description</label>
                    <input type="text" name="description" class="form-control" placeholder="Enter role description" value="<?php echo $role['description']; ?>">
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
                                                               <?php echo in_array($permission['id'], $rolePermissions) ? 'checked' : ''; ?>>
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
                <button type="submit" class="btn btn-primary ms-2">Update Role</button>
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