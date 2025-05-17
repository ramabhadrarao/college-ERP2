<?php
/**
 * Header include file
 * Contains the HTML header, navigation, and sidebar
 */

// Require login for all pages that include the header
requireLogin();

// Get user's menus
$menuItems = [];
if (isLoggedIn()) {
    // Get the main sidebar menu items for the logged-in user
    $sql = "SELECT mi.id, mi.title, mi.route, mi.icon, mi.parent_id, mi.item_order 
            FROM menu_items mi
            JOIN menus m ON mi.menu_id = m.id
            LEFT JOIN menu_item_permissions mip ON mi.id = mip.menu_item_id
            LEFT JOIN permissions p ON mip.permission_id = p.id
            LEFT JOIN role_permissions rp ON p.id = rp.permission_id
            LEFT JOIN user_roles ur ON rp.role_id = ur.role_id
            WHERE m.name = 'main_sidebar'
            AND mi.is_active = 1
            AND ur.user_id = ?
            GROUP BY mi.id
            ORDER BY mi.parent_id, mi.item_order";
    
    $menuItems = fetchAll($sql, "s", [$_SESSION['user_id']]);
}

// Organize menu items into a hierarchical structure
$menuTree = [];
$menuMap = [];

// First, create a map of menu items keyed by ID
foreach ($menuItems as $item) {
    $menuMap[$item['id']] = $item;
    $menuMap[$item['id']]['children'] = [];
}

// Then, build the hierarchical structure
foreach ($menuItems as $item) {
    $id = $item['id'];
    $parentId = $item['parent_id'];
    
    if ($parentId === null) {
        // Root level menu item
        $menuTree[] = &$menuMap[$id];
    } else {
        // Child menu item
        if (isset($menuMap[$parentId])) {
            $menuMap[$parentId]['children'][] = &$menuMap[$id];
        }
    }
}

// Function to determine if a menu item should be active
function isMenuActive($route) {
    if (empty($route)) {
        return false;
    }
    
    // Get the current script name relative to the base URL
    $currentPath = str_replace(dirname($_SERVER['PHP_SELF'], 2), '', $_SERVER['PHP_SELF']);
    
    // If route ends with .php, do an exact match
    if (substr($route, -4) === '.php') {
        return $currentPath === $route;
    }
    
    // Otherwise, check if current path starts with the route
    return strpos($currentPath, $route) === 0;
}

// Get the current page title
$pageTitle = 'Dashboard';
foreach ($menuItems as $item) {
    if (isMenuActive($item['route'])) {
        $pageTitle = $item['title'];
        break;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - <?php echo APP_NAME; ?></title>
    <!-- Tabler CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta17/dist/css/tabler.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@2.30.0/tabler-icons.min.css">
</head>
<body>
    <div class="page">
        <!-- Sidebar -->
        <aside class="navbar navbar-vertical navbar-expand-lg navbar-dark">
            <div class="container-fluid">
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#sidebar-menu" aria-controls="sidebar-menu" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <h1 class="navbar-brand navbar-brand-autodark">
                    <a href="<?php echo BASE_URL; ?>/index.php"><?php echo APP_NAME; ?></a>
                </h1>
                <div class="collapse navbar-collapse" id="sidebar-menu">
                    <ul class="navbar-nav pt-lg-3">
                        <li class="nav-item">
                            <a class="nav-link<?php echo isMenuActive('/index.php') ? ' active' : ''; ?>" href="<?php echo BASE_URL; ?>/index.php">
                                <span class="nav-link-icon d-md-none d-lg-inline-block">
                                    <i class="ti ti-home"></i>
                                </span>
                                <span class="nav-link-title">Dashboard</span>
                            </a>
                        </li>
                        
                        <?php foreach ($menuTree as $item): ?>
                            <?php if (empty($item['children'])): ?>
                                <li class="nav-item">
                                    <a class="nav-link<?php echo isMenuActive($item['route']) ? ' active' : ''; ?>" href="<?php echo BASE_URL . $item['route']; ?>">
                                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                                            <i class="ti <?php echo $item['icon'] ?? 'ti-circle'; ?>"></i>
                                        </span>
                                        <span class="nav-link-title"><?php echo $item['title']; ?></span>
                                    </a>
                                </li>
                            <?php else: ?>
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#navbar-<?php echo $item['id']; ?>" data-bs-toggle="dropdown" data-bs-auto-close="false" role="button" aria-expanded="false">
                                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                                            <i class="ti <?php echo $item['icon'] ?? 'ti-circle'; ?>"></i>
                                        </span>
                                        <span class="nav-link-title"><?php echo $item['title']; ?></span>
                                    </a>
                                    <div class="dropdown-menu">
                                        <div class="dropdown-menu-columns">
                                            <div class="dropdown-menu-column">
                                                <?php foreach ($item['children'] as $child): ?>
                                                    <a class="dropdown-item<?php echo isMenuActive($child['route']) ? ' active' : ''; ?>" href="<?php echo BASE_URL . $child['route']; ?>">
                                                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                                                            <i class="ti <?php echo $child['icon'] ?? 'ti-circle-dot'; ?>"></i>
                                                        </span>
                                                        <?php echo $child['title']; ?>
                                                    </a>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </aside>
        <div class="page-wrapper">
            <!-- Page header -->
            <header class="navbar navbar-expand-md navbar-light d-print-none">
                <div class="container-xl">
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-menu" aria-controls="navbar-menu" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="navbar-nav flex-row order-md-last">
                        <div class="nav-item dropdown">
                            <a href="#" class="nav-link d-flex lh-1 text-reset p-0" data-bs-toggle="dropdown" aria-label="Open user menu">
                                <div class="d-none d-xl-block ps-2">
                                    <div><?php echo $_SESSION['username']; ?></div>
                                    <div class="mt-1 small text-muted">
                                        <?php 
                                            $roles = array_map(function($role) { 
                                                return $role['name']; 
                                            }, $_SESSION['roles']);
                                            echo implode(', ', $roles);
                                        ?>
                                    </div>
                                </div>
                                <span class="avatar avatar-sm">
                                    <i class="ti ti-user"></i>
                                </span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                                <a href="<?php echo BASE_URL; ?>/users/profile.php" class="dropdown-item">Profile</a>
                                <div class="dropdown-divider"></div>
                                <a href="<?php echo BASE_URL; ?>/auth/logout.php" class="dropdown-item">Logout</a>
                            </div>
                        </div>
                    </div>
                    <div class="collapse navbar-collapse" id="navbar-menu">
                        <div>
                            <ol class="breadcrumb breadcrumb-arrows">
                                <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>/index.php">Home</a></li>
                                <li class="breadcrumb-item active" aria-current="page"><?php echo $pageTitle; ?></li>
                            </ol>
                        </div>
                    </div>
                </div>
            </header>
            
            <!-- Page body -->
            <div class="page-body">
                <div class="container-xl">
                    <!-- Flash messages -->
                    <?php $flashMessages = getFlashMessages(); ?>
                    <?php if (!empty($flashMessages)): ?>
                        <?php foreach ($flashMessages as $flashMessage): ?>
                            <div class="alert alert-<?php echo $flashMessage['type']; ?> alert-dismissible" role="alert">
                                <?php echo $flashMessage['message']; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    
                    <!-- Page title -->
                    <div class="page-header d-print-none">
                        <div class="row align-items-center">
                            <div class="col">
                                <h2 class="page-title"><?php echo $pageTitle; ?></h2>
                            </div>
                        </div>
                    </div>