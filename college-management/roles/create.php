<?php
/**
 * Roles List Page
 */
require_once '../config/database.php';
require_once '../config/functions.php';

// Initialize session
initSession();

// Include header
require_once '../includes/header.php';

// Check if user has permission to view roles
requirePermission('role_view');

// Get search parameters
$search = sanitizeInput($_GET['search'] ?? '');
$type = sanitizeInput($_GET['type'] ?? '');
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$perPage = 10;
$offset = ($page - 1) * $perPage;

// Build the query
$countSql = "SELECT COUNT(*) as total FROM roles";
$rolesSql = "SELECT r.id, r.name, r.description, r.is_system_role,
             (SELECT COUNT(*) FROM user_roles ur WHERE ur.role_id = r.id) as user_count,
             (SELECT COUNT(*) FROM role_permissions rp WHERE rp.role_id = r.id) as permission_count,
             r.created_at, r.updated_at
             FROM roles r";

$params = [];
$types = "";
$whereClauses = [];

// Add search conditions
if (!empty($search)) {
    $whereClauses[] = "(r.name LIKE ? OR r.description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $types .= "ss";
}

// Add type filter
if (!empty($type)) {
    if ($type === 'system') {
        $whereClauses[] = "r.is_system_role = 1";
    } elseif ($type === 'custom') {
        $whereClauses[] = "r.is_system_role = 0";
    }
}

// Combine where clauses
if (!empty($whereClauses)) {
    $countSql .= " WHERE " . implode(" AND ", $whereClauses);
    $rolesSql .= " WHERE " . implode(" AND ", $whereClauses);
}

// Add order and limit
$rolesSql .= " ORDER BY r.name LIMIT ?, ?";
$params[] = $offset;
$params[] = $perPage;
$types .= "ii";

// Get total count
$countResult = fetchOne($countSql, $types, $params);
$totalRoles = $countResult ? $countResult['total'] : 0;
$totalPages = ceil($totalRoles / $perPage);

// Get roles for current page
$roles = fetchAll($rolesSql, $types, $params);

// Process actions if submitted (delete)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && hasPermission('role_delete')) {
    $action = sanitizeInput($_POST['action'] ?? '');
    $roleId = sanitizeInput($_POST['role_id'] ?? '');
    
    if (!empty($roleId) && $action === 'delete') {
        // Check if this is a system role
        $checkSql = "SELECT is_system_role FROM roles WHERE id = ?";
        $roleCheck = fetchOne($checkSql, "i", [$roleId]);
        
        if ($roleCheck && $roleCheck['is_system_role']) {
            setFlashMessage('error', 'System roles cannot be deleted');
        } else {
            // Check if role is assigned to any users
            $userCheckSql = "SELECT COUNT(*) as user_count FROM user_roles WHERE role_id = ?";
            $userCheck = fetchOne($userCheckSql, "i", [$roleId]);
            
            if ($userCheck && $userCheck['user_count'] > 0) {
                setFlashMessage('error', 'Role cannot be deleted because it is assigned to ' . $userCheck['user_count'] . ' user(s)');
            } else {
                // Delete role permissions first
                $deletePermsSql = "DELETE FROM role_permissions WHERE role_id = ?";
                executeUpdate($deletePermsSql, "i", [$roleId]);
                
                // Then delete the role
                $deleteRoleSql = "DELETE FROM roles WHERE id = ?";
                if (executeUpdate($deleteRoleSql, "i", [$roleId])) {
                    setFlashMessage('success', 'Role deleted successfully');
                } else {
                    setFlashMessage('error', 'Failed to delete role');
                }
            }
        }
        
        // Redirect to refresh the page
        redirect($_SERVER['PHP_SELF'] . '?' . http_build_query($_GET));
    }
}
?>

<!-- Role list page content -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Role Management</h3>
        <div class="card-actions">
            <?php if (hasPermission('role_create')): ?>
                <a href="<?php echo BASE_URL; ?>/roles/create.php" class="btn btn-primary">
                    <i class="ti ti-plus"></i> Add New Role
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
                    <label class="form-label">Type</label>
                    <select name="type" class="form-select">
                        <option value="">All</option>
                        <option value="system" <?php echo $type === 'system' ? 'selected' : ''; ?>>System</option>
                        <option value="custom" <?php echo $type === 'custom' ? 'selected' : ''; ?>>Custom</option>
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
        
        <?php if (empty($roles)): ?>
            <div class="alert alert-info">
                <i class="ti ti-info-circle"></i> No roles found matching your criteria.
            </div>
        <?php else: ?>
            <!-- Roles table -->
            <div class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Type</th>
                            <th>Users</th>
                            <th>Permissions</th>
                            <th>Created</th>
                            <th class="w-1">Actions</th>
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
                                <td><?php echo $role['user_count']; ?></td>
                                <td><?php echo $role['permission_count']; ?></td>
                                <td><?php echo formatDate($role['created_at'], 'd M Y'); ?></td>
                                <td>
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                            Actions
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            <?php if (hasPermission('role_view')): ?>
                                                <a href="<?php echo BASE_URL; ?>/roles/view.php?id=<?php echo $role['id']; ?>" class="dropdown-item">
                                                    <i class="ti ti-eye"></i> View
                                                </a>
                                            <?php endif; ?>
                                            
                                            <?php if (hasPermission('role_edit') && !$role['is_system_role']): ?>
                                                <a href="<?php echo BASE_URL; ?>/roles/edit.php?id=<?php echo $role['id']; ?>" class="dropdown-item">
                                                    <i class="ti ti-edit"></i> Edit
                                                </a>
                                            <?php endif; ?>
                                            
                                            <?php if (hasPermission('role_delete') && !$role['is_system_role'] && $role['user_count'] == 0): ?>
                                                <div class="dropdown-divider"></div>
                                                <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>?<?php echo http_build_query($_GET); ?>" method="post">
                                                    <input type="hidden" name="action" value="delete">
                                                    <input type="hidden" name="role_id" value="<?php echo $role['id']; ?>">
                                                    <button type="submit" class="dropdown-item text-danger" onclick="return confirm('Are you sure you want to delete this role? This action cannot be undone.')">
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