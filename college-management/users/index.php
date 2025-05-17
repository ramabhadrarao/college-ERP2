<?php
/**
 * Dashboard / Home Page
 */
require_once 'config/database.php';
require_once 'config/functions.php';

// Initialize session
initSession();

// Include header
require_once 'includes/header.php';

// Get user statistics if user has permission
$userStats = [
    'total' => 0,
    'active' => 0,
    'inactive' => 0,
    'locked' => 0
];

if (hasPermission('user_view')) {
    $userStatsSql = "SELECT 
                        COUNT(*) as total,
                        SUM(CASE WHEN is_active = 1 AND (lockout_until IS NULL OR lockout_until < NOW()) THEN 1 ELSE 0 END) as active,
                        SUM(CASE WHEN is_active = 0 THEN 1 ELSE 0 END) as inactive,
                        SUM(CASE WHEN lockout_until IS NOT NULL AND lockout_until >= NOW() THEN 1 ELSE 0 END) as locked
                     FROM users";
    $userStatsResult = fetchOne($userStatsSql);
    
    if ($userStatsResult) {
        $userStats = $userStatsResult;
    }
}

// Get role statistics if user has permission
$roleStats = [
    'total' => 0,
    'system' => 0,
    'custom' => 0
];

if (hasPermission('role_view')) {
    $roleStatsSql = "SELECT 
                        COUNT(*) as total,
                        SUM(CASE WHEN is_system_role = 1 THEN 1 ELSE 0 END) as system,
                        SUM(CASE WHEN is_system_role = 0 THEN 1 ELSE 0 END) as custom
                     FROM roles";
    $roleStatsResult = fetchOne($roleStatsSql);
    
    if ($roleStatsResult) {
        $roleStats = $roleStatsResult;
    }
}

// Get recent login activity
$recentLogins = [];
if (hasPermission('user_view')) {
    $loginsSql = "SELECT u.username, u.last_login_at 
                  FROM users u 
                  WHERE u.last_login_at IS NOT NULL 
                  ORDER BY u.last_login_at DESC 
                  LIMIT 5";
    $recentLogins = fetchAll($loginsSql);
}

// Get user's role names
$userRoleNames = [];
foreach ($_SESSION['roles'] as $role) {
    $userRoleNames[] = $role['name'];
}
?>

<!-- Dashboard content -->
<div class="row row-deck row-cards">
    <!-- Welcome Card -->
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h3 class="card-title">Welcome, <?php echo $_SESSION['username']; ?>!</h3>
                <p>You are logged in as: <?php echo implode(', ', $userRoleNames); ?></p>
                <p>Last login: <?php echo isset($_SESSION['last_login']) ? formatDate($_SESSION['last_login'], 'd M Y H:i') : 'First login'; ?></p>
            </div>
        </div>
    </div>
    
    <?php if (hasPermission('user_view')): ?>
    <!-- User Statistics -->
    <div class="col-sm-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="subheader">Total Users</div>
                    <div class="ms-auto lh-1">
                        <a href="<?php echo BASE_URL; ?>/users/index.php" class="text-muted">View all</a>
                    </div>
                </div>
                <div class="h1 mb-3"><?php echo number_format($userStats['total']); ?></div>
                <div class="d-flex mb-2">
                    <div>Active Users</div>
                    <div class="ms-auto">
                        <span class="text-green d-inline-flex align-items-center lh-1">
                            <?php echo number_format($userStats['active']); ?>
                        </span>
                    </div>
                </div>
                <div class="progress progress-sm">
                    <div class="progress-bar bg-primary" style="width: <?php echo $userStats['total'] > 0 ? ($userStats['active'] / $userStats['total'] * 100) : 0; ?>%" role="progressbar" aria-valuenow="<?php echo $userStats['active']; ?>" aria-valuemin="0" aria-valuemax="<?php echo $userStats['total']; ?>">
                        <span class="visually-hidden"><?php echo $userStats['total'] > 0 ? round($userStats['active'] / $userStats['total'] * 100) : 0; ?>% Active</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
    <?php if (hasPermission('role_view')): ?>
    <!-- Role Statistics -->
    <div class="col-sm-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="subheader">Total Roles</div>
                    <div class="ms-auto lh-1">
                        <a href="<?php echo BASE_URL; ?>/roles/index.php" class="text-muted">View all</a>
                    </div>
                </div>
                <div class="h1 mb-3"><?php echo number_format($roleStats['total']); ?></div>
                <div class="d-flex mb-2">
                    <div>Custom Roles</div>
                    <div class="ms-auto">
                        <span class="text-green d-inline-flex align-items-center lh-1">
                            <?php echo number_format($roleStats['custom']); ?>
                        </span>
                    </div>
                </div>
                <div class="progress progress-sm">
                    <div class="progress-bar bg-primary" style="width: <?php echo $roleStats['total'] > 0 ? ($roleStats['custom'] / $roleStats['total'] * 100) : 0; ?>%" role="progressbar" aria-valuenow="<?php echo $roleStats['custom']; ?>" aria-valuemin="0" aria-valuemax="<?php echo $roleStats['total']; ?>">
                        <span class="visually-hidden"><?php echo $roleStats['total'] > 0 ? round($roleStats['custom'] / $roleStats['total'] * 100) : 0; ?>% Custom</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Quick Actions -->
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Quick Actions</h3>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <?php if (hasPermission('user_create')): ?>
                    <div class="col-6 col-sm-4">
                        <a href="<?php echo BASE_URL; ?>/users/create.php" class="btn btn-outline-primary w-100 btn-icon">
                            <i class="ti ti-user-plus"></i>
                            <div>Add User</div>
                        </a>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (hasPermission('role_create')): ?>
                    <div class="col-6 col-sm-4">
                        <a href="<?php echo BASE_URL; ?>/roles/create.php" class="btn btn-outline-primary w-100 btn-icon">
                            <i class="ti ti-shield"></i>
                            <div>Add Role</div>
                        </a>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (hasPermission('menu_create')): ?>
                    <div class="col-6 col-sm-4">
                        <a href="<?php echo BASE_URL; ?>/menus/create.php" class="btn btn-outline-primary w-100 btn-icon">
                            <i class="ti ti-menu-2"></i>
                            <div>Add Menu</div>
                        </a>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (hasPermission('permission_create')): ?>
                    <div class="col-6 col-sm-4">
                        <a href="<?php echo BASE_URL; ?>/permissions/create.php" class="btn btn-outline-primary w-100 btn-icon">
                            <i class="ti ti-lock"></i>
                            <div>Add Permission</div>
                        </a>
                    </div>
                    <?php endif; ?>
                    
                    <div class="col-6 col-sm-4">
                        <a href="<?php echo BASE_URL; ?>/users/profile.php" class="btn btn-outline-primary w-100 btn-icon">
                            <i class="ti ti-user"></i>
                            <div>Profile</div>
                        </a>
                    </div>
                    
                    <div class="col-6 col-sm-4">
                        <a href="<?php echo BASE_URL; ?>/users/change_password.php" class="btn btn-outline-primary w-100 btn-icon">
                            <i class="ti ti-key"></i>
                            <div>Change Password</div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php if (hasPermission('user_view') && !empty($recentLogins)): ?>
    <!-- Recent Activity -->
    <div class="col-md-6 col-lg-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Recent Login Activity</h3>
            </div>
            <div class="list-group list-group-flush">
                <?php foreach ($recentLogins as $login): ?>
                <div class="list-group-item">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="avatar">
                                <i class="ti ti-user"></i>
                            </span>
                        </div>
                        <div class="col">
                            <div class="text-body"><?php echo $login['username']; ?></div>
                            <div class="text-muted"><?php echo formatDate($login['last_login_at'], 'd M Y H:i'); ?></div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- System Info -->
    <div class="col-md-6 col-lg-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">System Information</h3>
            </div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-5">PHP Version:</dt>
                    <dd class="col-7"><?php echo phpversion(); ?></dd>
                    
                    <dt class="col-5">MySQL Version:</dt>
                    <dd class="col-7"><?php 
                        $conn = getDbConnection();
                        echo $conn->server_info;
                        $conn->close();
                    ?></dd>
                    
                    <dt class="col-5">Server:</dt>
                    <dd class="col-7"><?php echo $_SERVER['SERVER_SOFTWARE']; ?></dd>
                    
                    <dt class="col-5">Time Zone:</dt>
                    <dd class="col-7"><?php echo date_default_timezone_get(); ?></dd>
                    
                    <dt class="col-5">Current Time:</dt>
                    <dd class="col-7"><?php echo date('d M Y H:i:s'); ?></dd>
                </dl>
            </div>
        </div>
    </div>
    
    <!-- Support -->
    <div class="col-md-6 col-lg-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Support</h3>
            </div>
            <div class="card-body">
                <p>If you need help or have any questions about the system, please contact the administrator.</p>
                <p><strong>Email:</strong> <a href="mailto:admin@example.com">admin@example.com</a></p>
                <p><strong>Phone:</strong> +1234567890</p>
            </div>
        </div>
    </div>
</div>

<?php
// Include footer
require_once 'includes/footer.php';
?>