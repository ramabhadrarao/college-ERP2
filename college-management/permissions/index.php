<?php
/**
 * Permissions List Page
 */
require_once '../config/database.php';
require_once '../config/functions.php';

// Initialize session
initSession();

// Include header
require_once '../includes/header.php';

// Check if user has permission to view permissions
requirePermission('permission_view');

// Get search parameters
$search = sanitizeInput($_GET['search'] ?? '');
$module = sanitizeInput($_GET['module'] ?? '');
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$perPage = 15;
$offset = ($page - 1) * $perPage;

// Build the query
$countSql = "SELECT COUNT(*) as total FROM permissions";
$permissionsSql = "SELECT p.id, p.name, p.description, p.module, 
                  (SELECT COUNT(*) FROM role_permissions rp WHERE rp.permission_id = p.id) as role_count
                  FROM permissions p";

$params = [];
$types = "";
$whereClauses = [];

// Add search conditions
if (!empty($search)) {
    $whereClauses[] = "(p.name LIKE ? OR p.description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $types .= "ss";
}

// Add module filter
if (!empty($module)) {
    $whereClauses[] = "p.module = ?";
    $params[] = $module;
    $types .= "s";
}

// Combine where clauses
if (!empty($whereClauses)) {
    $countSql .= " WHERE " . implode(" AND ", $whereClauses);
    $permissionsSql .= " WHERE " . implode(" AND ", $whereClauses);
}

// Add order and limit
$permissionsSql .= " ORDER BY p.module, p.name LIMIT ?, ?";
$params[] = $offset;
$params[] = $perPage;
$types .= "ii";

// Get total count
$countResult = fetchOne($countSql, $types, $params);
$totalPermissions = $countResult ? $countResult['total'] : 0;
$totalPages = ceil($totalPermissions / $perPage);

// Get permissions for current page
$permissions = fetchAll($permissionsSql, $types, $params);

// Get available modules for filtering
$modulesSql = "SELECT DISTINCT module FROM permissions ORDER BY module";
$modules = fetchAll($modulesSql);

// Process actions if submitted (delete)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && hasPermission('permission_delete')) {
    $action = sanitizeInput($_POST['action'] ?? '');
    $permissionId = sanitizeInput($_POST['permission_id'] ?? '');
    
    if (!empty($permissionId) && $action === 'delete') {
        // Check if permission is assigned to any roles
        $roleCheckSql = "SELECT COUNT(*) as role_count FROM role_permissions WHERE permission_id = ?";
        $roleCheck = fetchOne($roleCheckSql, "i", [$permissionId]);
        
        if ($roleCheck && $roleCheck['role_count'] > 0) {
            setFlashMessage('error', 'Permission cannot be deleted because it is assigned to ' . $roleCheck['role_count'] . ' role(s)');
        } else {
            // Delete the permission
            $deletePermSql = "DELETE FROM permissions WHERE id = ?";
            if (executeUpdate($deletePermSql, "i", [$permissionId])) {
                setFlashMessage('success', 'Permission deleted successfully');
            } else {
                setFlashMessage('error', 'Failed to delete permission');
            }
        }
        
        // Redirect to refresh the page
        redirect($_SERVER['PHP_SELF'] . '?' . http_build_query($_GET));
    }
}
?>

<!-- Permissions list page content -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Permission Management</h3>
        <div class="card-actions">
            <?php if (hasPermission('permission_create')): ?>
                <a href="<?php echo BASE_URL; ?>/permissions/create.php" class="btn btn-primary">
                    <i class="ti ti-plus"></i> Add New Permission
                </a>
            <?php endif; ?>
        </div>
    </div>
    <div class="card-body">
        <!-- Search and filter -->
        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="get" class="mb-4">
            <div class="row g-2">
                <div class="col-md-4">
                    <label class="form-label">Search</label>
                    <input type="text" name="search" class="form-control" placeholder="Search by name or description" value="<?php echo $search; ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Module</label>
                    <select name="module" class="form-select">
                        <option value="">All Modules</option>
                        <?php foreach ($modules as $mod): ?>
                            <option value="<?php echo $mod['module']; ?>" <?php echo $module === $mod['module'] ? 'selected' : ''; ?>>
                                <?php echo $mod['module'] ?: 'General'; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="ti ti-search"></i> Search
                    </button>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <a href="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" class="btn btn-outline-secondary w-100">
                        <i class="ti ti-refresh"></i> Reset
                    </a>
                </div>
            </div>
        </form>
        
        <?php if (empty($permissions)): ?>
            <div class="alert alert-info">
                <i class="ti ti-info-circle"></i> No permissions found matching your criteria.
            </div>
        <?php else: ?>
            <!-- Permissions table -->
            <div class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Module</th>
                            <th>Description</th>
                            <th>Roles</th>
                            <th class="w-1">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($permissions as $permission): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <span class="avatar me-2 bg-primary-lt">
                                            <i class="ti ti-lock"></i>
                                        </span>
                                        <?php echo $permission['name']; ?>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-blue-lt"><?php echo $permission['module'] ?: 'General'; ?></span>
                                </td>
                                <td><?php echo $permission['description'] ?: 'No description'; ?></td>
                                <td><?php echo $permission['role_count']; ?></td>
                                <td>
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                            Actions
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            <?php if (hasPermission('permission_view')): ?>
                                                <a href="<?php echo BASE_URL; ?>/permissions/view.php?id=<?php echo $permission['id']; ?>" class="dropdown-item">
                                                    <i class="ti ti-eye"></i> View
                                                </a>
                                            <?php endif; ?>
                                            
                                            <?php if (hasPermission('permission_edit')): ?>
                                                <a href="<?php echo BASE_URL; ?>/permissions/edit.php?id=<?php echo $permission['id']; ?>" class="dropdown-item">
                                                    <i class="ti ti-edit"></i> Edit
                                                </a>
                                            <?php endif; ?>
                                            
                                            <?php if (hasPermission('permission_delete') && $permission['role_count'] == 0): ?>
                                                <div class="dropdown-divider"></div>
                                                <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>?<?php echo http_build_query($_GET); ?>" method="post">
                                                    <input type="hidden" name="action" value="delete">
                                                    <input type="hidden" name="permission_id" value="<?php echo $permission['id']; ?>">
                                                    <button type="submit" class="dropdown-item text-danger" onclick="return confirm('Are you sure you want to delete this permission? This action cannot be undone.')">
                                                        <i class="ti ti-trash"></i> Delete
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
                <div class="d-flex justify-content-center mt-4">
                    <ul class="pagination">
                        <?php if ($page > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>?<?php echo http_build_query(array_merge($_GET, ['page' => $page - 1])); ?>">
                                    <i class="ti ti-chevron-left"></i>
                                    <span>Prev</span>
                                </a>
                            </li>
                        <?php else: ?>
                            <li class="page-item disabled">
                                <a class="page-link" href="#">
                                    <i class="ti ti-chevron-left"></i>
                                    <span>Prev</span>
                                </a>
                            </li>
                        <?php endif; ?>
                        
                        <?php
                        $startPage = max(1, min($page - 2, $totalPages - 4));
                        $endPage = min($totalPages, $startPage + 4);
                        
                        for ($i = $startPage; $i <= $endPage; $i++):
                        ?>
                            <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                                <a class="page-link" href="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>">
                                    <?php echo $i; ?>
                                </a>
                            </li>
                        <?php endfor; ?>
                        
                        <?php if ($page < $totalPages): ?>
                            <li class="page-item">
                                <a class="page-link" href="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>?<?php echo http_build_query(array_merge($_GET, ['page' => $page + 1])); ?>">
                                    <span>Next</span>
                                    <i class="ti ti-chevron-right"></i>
                                </a>
                            </li>
                        <?php else: ?>
                            <li class="page-item disabled">
                                <a class="page-link" href="#">
                                    <span>Next</span>
                                    <i class="ti ti-chevron-right"></i>
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<?php
// Include footer
require_once '../includes/footer.php';
?>