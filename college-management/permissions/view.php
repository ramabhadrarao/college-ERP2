<?php
/**
 * View Permission Details Page
 */
require_once '../config/database.php';
require_once '../config/functions.php';

// Initialize session
initSession();

// Include header
require_once '../includes/header.php';

// Check if user has permission to view permissions
requirePermission('permission_view');

// Get permission ID from URL parameter
$permissionId = sanitizeInput($_GET['id'] ?? '');

if (empty($permissionId)) {
    setFlashMessage('error', 'Invalid permission ID');
    redirect(BASE_URL . '/permissions/index.php');
}

// Get permission data
$permissionSql = "SELECT p.id, p.name, p.description, p.module, p.created_at, p.updated_at
                 FROM permissions p 
                 WHERE p.id = ?";
$permission = fetchOne($permissionSql, "i", [$permissionId]);

if (!$permission) {
    setFlashMessage('error', 'Permission not found');
    redirect(BASE_URL . '/permissions/index.php');
}

// Get roles assigned this permission
$rolesSql = "SELECT r.id, r.name, r.description, r.is_system_role
            FROM roles r
            JOIN role_permissions rp ON r.id = rp.role_id
            WHERE rp.permission_id = ?
            ORDER BY r.name";
$roles = fetchAll($rolesSql, "i", [$permissionId]);
?>

<!-- Permission details card -->
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Permission Information</h3>
                <?php if (hasPermission('permission_edit')): ?>
                    <div class="card-actions">
                        <a href="<?php echo BASE_URL; ?>/permissions/edit.php?id=<?php echo $permission['id']; ?>" class="btn btn-primary">
                            <i class="ti ti-edit"></i> Edit Permission
                        </a>
                    </div>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center mb-4">
                    <span class="avatar avatar-xl me-3 bg-primary-lt">
                        <i class="ti ti-lock"></i>
                    </span>
                    <div>
                        <h3 class="mb-0"><?php echo $permission['name']; ?></h3>
                        <div class="text-muted"><?php echo $permission['description'] ?: 'No description'; ?></div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <h4 class="mb-1">Module</h4>
                    <div>
                        <span class="badge bg-blue-lt"><?php echo $permission['module'] ?: 'General'; ?></span>
                    </div>
                </div>
                
                <div class="mb-3">
                    <h4 class="mb-1">Roles Assigned</h4>
                    <div><?php echo count($roles); ?></div>
                </div>
                
                <div class="mb-3">
                    <h4 class="mb-1">Created</h4>
                    <div><?php echo formatDate($permission['created_at'], 'd M Y H:i'); ?></div>
                </div>
                
                <div class="mb-3">
                    <h4 class="mb-1">Last Updated</h4>
                    <div><?php echo formatDate($permission['updated_at'], 'd M Y H:i'); ?></div>
                </div>
            </div>
            <div class="card-footer">
                <a href="<?php echo BASE_URL; ?>/permissions/index.php" class="btn btn-outline-secondary">
                    <i class="ti ti-arrow-left"></i> Back to Permissions
                </a>
                <?php if (hasPermission('permission_delete') && count($roles) === 0): ?>
                    <form action="<?php echo BASE_URL; ?>/permissions/index.php" method="post" class="d-inline ms-2">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="permission_id" value="<?php echo $permission['id']; ?>">
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this permission? This action cannot be undone.')">
                            <i class="ti ti-trash"></i> Delete Permission
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <!-- Roles card -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Roles with this Permission (<?php echo count($roles); ?>)</h3>
            </div>
            <div class="card-body">
                <?php if (empty($roles)): ?>
                    <div class="alert alert-info">
                        <i class="ti ti-info-circle"></i> No roles have been assigned this permission.
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-vcenter card-table">
                            <thead>
                                <tr>
                                    <th>Role Name</th>
                                    <th>Description</th>
                                    <th>Type</th>
                                    <?php if (hasPermission('role_view')): ?>
                                        <th class="w-1">Actions</th>
                                    <?php endif; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($roles as $role): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <span class="avatar me-2 bg-primary-lt">
                                                    <i class="ti ti-shield"></i>
                                                </span>
                                                <?php echo $role['name']; ?>
                                            </div>
                                        </td>
                                        <td><?php echo $role['description'] ?: 'No description'; ?></td>
                                        <td>
                                            <?php if ($role['is_system_role']): ?>
                                                <span class="badge bg-primary">System</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">Custom</span>
                                            <?php endif; ?>
                                        </td>
                                        <?php if (hasPermission('role_view')): ?>
                                            <td>
                                                <a href="<?php echo BASE_URL; ?>/roles/view.php?id=<?php echo $role['id']; ?>" class="btn btn-sm btn-outline-primary">
                                                    <i class="ti ti-eye"></i> View
                                                </a>
                                            </td>
                                        <?php endif; ?>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php
// Include footer
require_once '../includes/footer.php';
?>