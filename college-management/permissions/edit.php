<?php
/**
 * Edit Permission Page
 */
require_once '../config/database.php';
require_once '../config/functions.php';

// Initialize session
initSession();

// Include header
require_once '../includes/header.php';

// Check if user has permission to edit permissions
requirePermission('permission_edit');

// Get permission ID from URL parameter
$permissionId = sanitizeInput($_GET['id'] ?? '');

if (empty($permissionId)) {
    setFlashMessage('error', 'Invalid permission ID');
    redirect(BASE_URL . '/permissions/index.php');
}

// Get permission data
$permissionSql = "SELECT p.id, p.name, p.description, p.module 
                 FROM permissions p 
                 WHERE p.id = ?";
$permission = fetchOne($permissionSql, "i", [$permissionId]);

if (!$permission) {
    setFlashMessage('error', 'Permission not found');
    redirect(BASE_URL . '/permissions/index.php');
}

// Get available modules for dropdown
$modulesSql = "SELECT DISTINCT module FROM permissions ORDER BY module";
$modules = fetchAll($modulesSql);

// Initialize error array
$errors = [];

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get and sanitize input
    $name = sanitizeInput($_POST['name'] ?? '');
    $description = sanitizeInput($_POST['description'] ?? '');
    $module = sanitizeInput($_POST['module'] ?? '');
    $newModule = sanitizeInput($_POST['new_module'] ?? '');
    
    // Use new module if provided
    if (!empty($newModule)) {
        $module = $newModule;
    }
    
    // Validate input
    if (empty($name)) {
        $errors[] = 'Permission name is required';
    } else if ($name !== $permission['name']) {
        // Check if permission name is already in use by another permission
        $checkSql = "SELECT id FROM permissions WHERE name = ? AND id != ?";
        $existingPermission = fetchOne($checkSql, "si", [$name, $permissionId]);
        if ($existingPermission) {
            $errors[] = 'Permission name already exists';
        }
    }
    
    // If no errors, update the permission
    if (empty($errors)) {
        // Update permission
        $updateSql = "UPDATE permissions SET name = ?, description = ?, module = ?, updated_at = ? WHERE id = ?";
        $result = executeUpdate($updateSql, "ssssi", [$name, $description, $module, getCurrentDateTime(), $permissionId]);
        
        if ($result !== false) {
            // Redirect to permission list with success message
            setFlashMessage('success', 'Permission updated successfully');
            redirect(BASE_URL . '/permissions/index.php');
        } else {
            $errors[] = 'Failed to update permission. Please try again.';
        }
    }
    
    // Update permission object with submitted values for re-rendering the form
    $permission['name'] = $name;
    $permission['description'] = $description;
    $permission['module'] = $module;
}
?>

<!-- Edit permission form -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Edit Permission: <?php echo $permission['name']; ?></h3>
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
        
        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'] . '?id=' . $permissionId); ?>" method="post">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label required">Permission Name</label>
                    <input type="text" name="name" class="form-control" placeholder="Enter permission name" value="<?php echo $permission['name']; ?>" required>
                    <small class="form-hint">Use lowercase letters and underscores, e.g., user_create, role_view</small>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label class="form-label">Module</label>
                    <select name="module" id="module-select" class="form-select">
                        <option value="">Select Module</option>
                        <?php foreach ($modules as $mod): ?>
                            <?php $moduleName = $mod['module'] ?: 'General'; ?>
                            <option value="<?php echo $mod['module']; ?>" <?php echo $permission['module'] === $mod['module'] ? 'selected' : ''; ?>>
                                <?php echo $moduleName; ?>
                            </option>
                        <?php endforeach; ?>
                        <option value="new">+ Add New Module</option>
                    </select>
                </div>
                
                <div class="col-md-6 mb-3" id="new-module-container" style="display: none;">
                    <label class="form-label">New Module Name</label>
                    <input type="text" name="new_module" id="new-module" class="form-control" placeholder="Enter new module name">
                </div>
                
                <div class="col-md-12 mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="3" placeholder="Enter permission description"><?php echo $permission['description']; ?></textarea>
                </div>
            </div>
            
            <div class="form-footer">
                <a href="<?php echo BASE_URL; ?>/permissions/index.php" class="btn btn-outline-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary ms-2">Update Permission</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const moduleSelect = document.getElementById('module-select');
    const newModuleContainer = document.getElementById('new-module-container');
    const newModuleInput = document.getElementById('new-module');
    
    // Check initial state
    if (moduleSelect.value === 'new') {
        newModuleContainer.style.display = 'block';
        newModuleInput.setAttribute('required', 'required');
    }
    
    moduleSelect.addEventListener('change', function() {
        if (this.value === 'new') {
            newModuleContainer.style.display = 'block';
            newModuleInput.setAttribute('required', 'required');
        } else {
            newModuleContainer.style.display = 'none';
            newModuleInput.removeAttribute('required');
        }
    });
});
</script>

<?php
// Include footer
require_once '../includes/footer.php';
?>