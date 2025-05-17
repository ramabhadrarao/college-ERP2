<?php
/**
 * User List Page
 */
require_once '../config/database.php';
require_once '../config/functions.php';

// Initialize session
initSession();

// Include header
require_once '../includes/header.php';

// Check if user has permission to view users
requirePermission('user_view');

// Get search parameters
$search = sanitizeInput($_GET['search'] ?? '');
$status = sanitizeInput($_GET['status'] ?? '');
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$perPage = 10;
$offset = ($page - 1) * $perPage;

// Build the query
$countSql = "SELECT COUNT(*) as total FROM users";
$usersSql = "SELECT u.id, u.username, u.email, u.is_active, u.is_verified, 
            u.last_login_at, u.failed_login_attempts, u.lockout_until, u.created_at 
            FROM users u";

$params = [];
$types = "";
$whereClauses = [];

// Add search conditions
if (!empty($search)) {
    $whereClauses[] = "(u.username LIKE ? OR u.email LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $types .= "ss";
}

// Add status filter
if (!empty($status)) {
    if ($status === 'active') {
        $whereClauses[] = "u.is_active = 1 AND (u.lockout_until IS NULL OR u.lockout_until < NOW())";
    } elseif ($status === 'inactive') {
        $whereClauses[] = "u.is_active = 0";
    } elseif ($status === 'locked') {
        $whereClauses[] = "u.lockout_until IS NOT NULL AND u.lockout_until >= NOW()";
    } elseif ($status === 'unverified') {
        $whereClauses[] = "u.is_verified = 0";
    }
}

// Combine where clauses
if (!empty($whereClauses)) {
    $countSql .= " WHERE " . implode(" AND ", $whereClauses);
    $usersSql .= " WHERE " . implode(" AND ", $whereClauses);
}

// Add order and limit
$usersSql .= " ORDER BY u.created_at DESC LIMIT ?, ?";
$params[] = $offset;
$params[] = $perPage;
$types .= "ii";

// Get total count
$countResult = fetchOne($countSql, $types, $params);
$totalUsers = $countResult ? $countResult['total'] : 0;
$totalPages = ceil($totalUsers / $perPage);

// Get users for current page
$users = fetchAll($usersSql, $types, $params);

// Process actions if submitted (activate/deactivate/delete)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && hasPermission('user_edit')) {
    $action = sanitizeInput($_POST['action'] ?? '');
    $userId = sanitizeInput($_POST['user_id'] ?? '');
    
    if (!empty($userId)) {
        switch ($action) {
            case 'activate':
                if (hasPermission('user_edit')) {
                    $updateSql = "UPDATE users SET is_active = 1, updated_at = ?, updated_by = ? WHERE id = ?";
                    if (executeUpdate($updateSql, "sss", [getCurrentDateTime(), $_SESSION['user_id'], $userId])) {
                        setFlashMessage('success', 'User activated successfully');
                    } else {
                        setFlashMessage('error', 'Failed to activate user');
                    }
                }
                break;
                
            case 'deactivate':
                if (hasPermission('user_edit')) {
                    $updateSql = "UPDATE users SET is_active = 0, updated_at = ?, updated_by = ? WHERE id = ?";
                    if (executeUpdate($updateSql, "sss", [getCurrentDateTime(), $_SESSION['user_id'], $userId])) {
                        setFlashMessage('success', 'User deactivated successfully');
                    } else {
                        setFlashMessage('error', 'Failed to deactivate user');
                    }
                }
                break;
                
            case 'unlock':
                if (hasPermission('user_edit')) {
                    $updateSql = "UPDATE users SET failed_login_attempts = 0, lockout_until = NULL, 
                                 updated_at = ?, updated_by = ? WHERE id = ?";
                    if (executeUpdate($updateSql, "sss", [getCurrentDateTime(), $_SESSION['user_id'], $userId])) {
                        setFlashMessage('success', 'User unlocked successfully');
                    } else {
                        setFlashMessage('error', 'Failed to unlock user');
                    }
                }
                break;
                
            case 'delete':
                if (hasPermission('user_delete')) {
                    // Check if this is the last admin user
                    $adminCheckSql = "SELECT COUNT(*) as admin_count 
                                     FROM users u 
                                     JOIN user_roles ur ON u.id = ur.user_id 
                                     JOIN roles r ON ur.role_id = r.id 
                                     WHERE r.is_system_role = 1 AND r.name = 'Admin'";
                    $adminCheck = fetchOne($adminCheckSql);
                    
                    // Check if the user to be deleted is an admin
                    $isAdminSql = "SELECT COUNT(*) as is_admin 
                                  FROM user_roles ur 
                                  JOIN roles r ON ur.role_id = r.id 
                                  WHERE ur.user_id = ? AND r.is_system_role = 1 AND r.name = 'Admin'";
                    $isAdmin = fetchOne($isAdminSql, "s", [$userId]);
                    
                    if ($isAdmin && $isAdmin['is_admin'] > 0 && $adminCheck && $adminCheck['admin_count'] <= 1) {
                        setFlashMessage('error', 'Cannot delete the last admin user');
                    } else {
                        $deleteSql = "DELETE FROM users WHERE id = ?";
                        if (executeUpdate($deleteSql, "s", [$userId])) {
                            setFlashMessage('success', 'User deleted successfully');
                        } else {
                            setFlashMessage('error', 'Failed to delete user');
                        }
                    }
                }
                break;
        }
        
        // Redirect to refresh the page
        redirect($_SERVER['PHP_SELF'] . '?' . http_build_query($_GET));
    }
}
?>

<!-- User list page content -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">User Management</h3>
        <div class="card-actions">
            <?php if (hasPermission('user_create')): ?>
                <a href="<?php echo BASE_URL; ?>/users/create.php" class="btn btn-primary">
                    <i class="ti ti-plus"></i> Add New User
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
                    <input type="text" name="search" class="form-control" placeholder="Search by username or email" value="<?php echo $search; ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All</option>
                        <option value="active" <?php echo $status === 'active' ? 'selected' : ''; ?>>Active</option>
                        <option value="inactive" <?php echo $status === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                        <option value="locked" <?php echo $status === 'locked' ? 'selected' : ''; ?>>Locked</option>
                        <option value="unverified" <?php echo $status === 'unverified' ? 'selected' : ''; ?>>Unverified</option>
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
        
        <?php if (empty($users)): ?>
            <div class="alert alert-info">
                <i class="ti ti-info-circle"></i> No users found matching your criteria.
            </div>
        <?php else: ?>
            <!-- Users table -->
            <div class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead>
                        <tr>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Last Login</th>
                            <th>Created</th>
                            <th class="w-1">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <span class="avatar me-2">
                                            <i class="ti ti-user"></i>
                                        </span>
                                        <?php echo $user['username']; ?>
                                        <?php if (!$user['is_verified']): ?>
                                            <span class="badge bg-warning ms-1">Unverified</span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td><?php echo $user['email']; ?></td>
                                <td>
                                    <?php if ($user['lockout_until'] !== null && strtotime($user['lockout_until']) > time()): ?>
                                        <span class="badge bg-danger">Locked</span>
                                    <?php elseif ($user['is_active']): ?>
                                        <span class="badge bg-success">Active</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Inactive</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php echo $user['last_login_at'] ? formatDate($user['last_login_at'], 'd M Y H:i') : 'Never'; ?>
                                </td>
                                <td><?php echo formatDate($user['created_at'], 'd M Y'); ?></td>
                                <td>
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                            Actions
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            <?php if (hasPermission('user_view')): ?>
                                                <a href="<?php echo BASE_URL; ?>/users/view.php?id=<?php echo $user['id']; ?>" class="dropdown-item">
                                                    <i class="ti ti-eye"></i> View
                                                </a>
                                            <?php endif; ?>
                                            
                                            <?php if (hasPermission('user_edit')): ?>
                                                <a href="<?php echo BASE_URL; ?>/users/edit.php?id=<?php echo $user['id']; ?>" class="dropdown-item">
                                                    <i class="ti ti-edit"></i> Edit
                                                </a>
                                                
                                                <div class="dropdown-divider"></div>
                                                
                                                <?php if ($user['is_active']): ?>
                                                    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>?<?php echo http_build_query($_GET); ?>" method="post">
                                                        <input type="hidden" name="action" value="deactivate">
                                                        <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                                        <button type="submit" class="dropdown-item" onclick="return confirm('Are you sure you want to deactivate this user?')">
                                                            <i class="ti ti-ban"></i> Deactivate
                                                        </button>
                                                    </form>
                                                <?php else: ?>
                                                    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>?<?php echo http_build_query($_GET); ?>" method="post">
                                                        <input type="hidden" name="action" value="activate">
                                                        <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                                        <button type="submit" class="dropdown-item">
                                                            <i class="ti ti-check"></i> Activate
                                                        </button>
                                                    </form>
                                                <?php endif; ?>
                                                
                                                <?php if ($user['lockout_until'] !== null && strtotime($user['lockout_until']) > time()): ?>
                                                    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>?<?php echo http_build_query($_GET); ?>" method="post">
                                                        <input type="hidden" name="action" value="unlock">
                                                        <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                                        <button type="submit" class="dropdown-item">
                                                            <i class="ti ti-lock-open"></i> Unlock Account
                                                        </button>
                                                    </form>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                            
                                            <?php if (hasPermission('user_delete')): ?>
                                                <div class="dropdown-divider"></div>
                                                <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>?<?php echo http_build_query($_GET); ?>" method="post">
                                                    <input type="hidden" name="action" value="delete">
                                                    <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                                    <button type="submit" class="dropdown-item text-danger" onclick="return confirm('Are you sure you want to delete this user? This action cannot be undone.')">
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