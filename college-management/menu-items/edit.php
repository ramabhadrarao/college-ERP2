<?php
/**
 * Create Menu Item Page
 */
require_once '../config/database.php';
require_once '../config/functions.php';

// Initialize session
initSession();

// Include header
require_once '../includes/header.php';

// Check if user has permission to manage menu items
requirePermission('menu_item_manage');

// Get menu ID from URL parameter
$menuId = sanitizeInput($_GET['menu_id'] ?? '');

if (empty($menuId)) {
    setFlashMessage('error', 'Invalid menu ID');
    redirect(BASE_URL . '/menus/index.php');
}

// Get menu data
$menuSql = "SELECT m.id, m.name, m.description 
           FROM menus m 
           WHERE m.id = ?";
$menu = fetchOne($menuSql, "i", [$menuId]);

if (!$menu) {
    setFlashMessage('error', 'Menu not found');
    redirect(BASE_URL . '/menus/index.php');
}

// Get parent menu items for dropdown
$parentItemsSql = "SELECT id, title FROM menu_items WHERE menu_id = ? ORDER BY title";
$parentItems = fetchAll($parentItemsSql, "i", [$menuId]);

// Get available permissions for dropdown
$permissionsSql = "SELECT id, name, module FROM permissions ORDER BY module, name";
$permissionsResult = fetchAll($permissionsSql);

// Group permissions by module
$permissionsByModule = [];
foreach ($permissionsResult as $permission) {
    $module = $permission['module'] ?? 'General';
    if (!isset($permissionsByModule[$module])) {
        $permissionsByModule[$module] = [];
    }
    $permissionsByModule[$module][] = $permission;
}

// Initialize variables
$errors = [];
$itemData = [
    'title' => '',
    'route' => '',
    'icon' => '',
    'parent_id' => null,
    'item_order' => 0,
    'is_active' => true,
    'target' => '_self',
    'permissions' => []
];

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get and sanitize input
    $itemData['title'] = sanitizeInput($_POST['title'] ?? '');
    $itemData['route'] = sanitizeInput($_POST['route'] ?? '');
    $itemData['icon'] = sanitizeInput($_POST['icon'] ?? '');
    $itemData['parent_id'] = $_POST['parent_id'] !== '' ? sanitizeInput($_POST['parent_id']) : null;
    $itemData['item_order'] = isset($_POST['item_order']) ? intval($_POST['item_order']) : 0;
    $itemData['is_active'] = isset($_POST['is_active']) && $_POST['is_active'] == 1;
    $itemData['target'] = sanitizeInput($_POST['target'] ?? '_self');
    $itemData['permissions'] = $_POST['permissions'] ?? [];
    
    // Validate input
    if (empty($itemData['title'])) {
        $errors[] = 'Menu item title is required';
    }
    
    // If no errors, create the menu item
    if (empty($errors)) {
        // Insert the menu item
        $insertSql = "INSERT INTO menu_items (menu_id, parent_id, title, route, icon, item_order, is_active, target, created_at, updated_at) 
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $itemId = insertAndGetId($insertSql, "iisssisss", [
            $menuId,
            $itemData['parent_id'],
            $itemData['title'],
            $itemData['route'],
            $itemData['icon'],
            $itemData['item_order'],
            $itemData['is_active'] ? 1 : 0,
            $itemData['target'],
            getCurrentDateTime(),
            getCurrentDateTime()
        ]);
        
        if ($itemId) {
            // Assign permissions to the menu item
            foreach ($itemData['permissions'] as $permissionId) {
                $permSql = "INSERT INTO menu_item_permissions (menu_item_id, permission_id, created_at) VALUES (?, ?, ?)";
                executeUpdate($permSql, "iis", [$itemId, $permissionId, getCurrentDateTime()]);
            }
            
            // Redirect to menu items list with success message
            setFlashMessage('success', 'Menu item created successfully');
            redirect(BASE_URL . '/menu-items/index.php?menu_id=' . $menuId);
        } else {
            $errors[] = 'Failed to create menu item. Please try again.';
        }
    }
}

// Get common Tabler icon classes for selection
$iconOptions = [
    'General' => [
        'ti ti-home', 'ti ti-user', 'ti ti-users', 'ti ti-settings', 'ti ti-file', 'ti ti-folder',
        'ti ti-dashboard', 'ti ti-grid', 'ti ti-list', 'ti ti-calendar', 'ti ti-mail', 'ti ti-bell',
        'ti ti-search', 'ti ti-star', 'ti ti-heart', 'ti ti-dots-vertical', 'ti ti-dots-horizontal',
        'ti ti-menu-2', 'ti ti-logout', 'ti ti-login', 'ti ti-edit', 'ti ti-trash', 'ti ti-plus',
        'ti ti-check', 'ti ti-x', 'ti ti-circle', 'ti ti-circle-check', 'ti ti-circle-x'
    ],
    'Arrows' => [
        'ti ti-arrow-up', 'ti ti-arrow-down', 'ti ti-arrow-left', 'ti ti-arrow-right',
        'ti ti-chevron-up', 'ti ti-chevron-down', 'ti ti-chevron-left', 'ti ti-chevron-right',
        'ti ti-arrow-up-circle', 'ti ti-arrow-down-circle', 'ti ti-arrow-left-circle', 'ti ti-arrow-right-circle'
    ],
    'Business' => [
        'ti ti-building', 'ti ti-briefcase', 'ti ti-chart-bar', 'ti ti-chart-pie', 'ti ti-chart-line',
        'ti ti-report', 'ti ti-receipt', 'ti ti-notes', 'ti ti-clipboard', 'ti ti-id', 'ti ti-certificate',
        'ti ti-credit-card', 'ti ti-coin', 'ti ti-currency-dollar', 'ti ti-calculator'
    ],
    'Education' => [
        'ti ti-book', 'ti ti-books', 'ti ti-notebook', 'ti ti-pencil', 'ti ti-school', 'ti ti-certificate',
        'ti ti-backpack', 'ti ti-presentation', 'ti ti-award', 'ti ti-bulb', 'ti ti-school-bench'
    ],
    'Security' => [
        'ti ti-shield', 'ti ti-shield-check', 'ti ti-shield-x', 'ti ti-lock', 'ti ti-lock-open',
        'ti ti-key', 'ti ti-eye', 'ti ti-eye-off', 'ti ti-user-check', 'ti ti-user-x', 'ti ti-password'
    ]
];
?>

<!-- Create menu item form -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Create New Menu Item for: <?php echo $menu['name']; ?></h3>
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
        
        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'] . '?menu_id=' . $menuId); ?>" method="post">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label required">Title</label>
                    <input type="text" name="title" class="form-control" placeholder="Enter menu item title" value="<?php echo $itemData['title']; ?>" required>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label class="form-label">Route</label>
                    <input type="text" name="route" class="form-control" placeholder="Enter route (e.g., /users/index.php)" value="<?php echo $itemData['route']; ?>">
                    <small class="form-hint">Leave empty for parent menu items or to use as a divider</small>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label class="form-label">Parent Item</label>
                    <select name="parent_id" class="form-select">
                        <option value="">-- No Parent (Top Level) --</option>
                        <?php foreach ($parentItems as $parent): ?>
                            <option value="<?php echo $parent['id']; ?>" <?php echo $itemData['parent_id'] === $parent['id'] ? 'selected' : ''; ?>>
                                <?php echo $parent['title']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label class="form-label">Display Order</label>
                    <input type="number" name="item_order" class="form-control" min="0" value="<?php echo $itemData['item_order']; ?>">
                    <small class="form-hint">Items are ordered from lowest to highest value</small>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label class="form-label">Icon</label>
                    <div class="input-group">
                        <span class="input-group-text" id="icon-preview">
                            <i id="icon-preview-i" class="<?php echo $itemData['icon'] ?: 'ti ti-circle'; ?>"></i>
                        </span>
                        <input type="text" name="icon" id="icon-input" class="form-control" placeholder="Select or enter icon class" value="<?php echo $itemData['icon']; ?>">
                        <button class="btn btn-outline-secondary" type="button" data-bs-toggle="modal" data-bs-target="#iconModal">
                            <i class="ti ti-mood-smile"></i> Pick Icon
                        </button>
                    </div>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label class="form-label">Target</label>
                    <select name="target" class="form-select">
                        <option value="_self" <?php echo $itemData['target'] === '_self' ? 'selected' : ''; ?>>Same Window (_self)</option>
                        <option value="_blank" <?php echo $itemData['target'] === '_blank' ? 'selected' : ''; ?>>New Window (_blank)</option>
                        <option value="_parent" <?php echo $itemData['target'] === '_parent' ? 'selected' : ''; ?>>Parent Frame (_parent)</option>
                        <option value="_top" <?php echo $itemData['target'] === '_top' ? 'selected' : ''; ?>>Full Window (_top)</option>
                    </select>
                </div>
                
                <div class="col-md-12 mb-3">
                    <label class="form-check">
                        <input type="checkbox" name="is_active" value="1" class="form-check-input" <?php echo $itemData['is_active'] ? 'checked' : ''; ?>>
                        <span class="form-check-label">Active</span>
                    </label>
                </div>
                
                <div class="col-md-12 mb-3">
                    <label class="form-label">Required Permissions</label>
                    <p class="form-hint">Users must have at least one of these permissions to see this menu item</p>
                    
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
                                                               <?php echo in_array($permission['id'], $itemData['permissions']) ? 'checked' : ''; ?>>
                                                        <span class="form-check-label"><?php echo $permission['name']; ?></span>
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
                <a href="<?php echo BASE_URL; ?>/menu-items/index.php?menu_id=<?php echo $menuId; ?>" class="btn btn-outline-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary ms-2">Create Menu Item</button>
            </div>
        </form>
    </div>
</div>

<!-- Icon Selection Modal -->
<div class="modal modal-blur fade" id="iconModal" tabindex="-1" aria-labelledby="iconModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="iconModalLabel">Select Icon</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <input type="text" id="icon-search" class="form-control" placeholder="Search icons...">
                </div>
                
                <?php foreach ($iconOptions as $category => $icons): ?>
                    <h4><?php echo $category; ?></h4>
                    <div class="row icon-grid mb-3">
                        <?php foreach ($icons as $icon): ?>
                            <div class="col-md-3 col-sm-4 col-6 icon-item" data-icon="<?php echo $icon; ?>">
                                <div class="card icon-card">
                                    <div class="card-body p-2 text-center">
                                        <i class="<?php echo $icon; ?> mb-2" style="font-size: 24px;"></i>
                                        <div class="small text-muted"><?php echo $icon; ?></div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle icon preview
    const iconInput = document.getElementById('icon-input');
    const iconPreviewI = document.getElementById('icon-preview-i');
    
    iconInput.addEventListener('input', function() {
        updateIconPreview(this.value);
    });
    
    function updateIconPreview(iconClass) {
        iconPreviewI.className = iconClass || 'ti ti-circle';
    }
    
    // Handle icon selection from modal
    const iconItems = document.querySelectorAll('.icon-item');
    iconItems.forEach(function(item) {
        item.addEventListener('click', function() {
            const iconClass = this.getAttribute('data-icon');
            iconInput.value = iconClass;
            updateIconPreview(iconClass);
            bootstrap.Modal.getInstance(document.getElementById('iconModal')).hide();
        });
    });
    
    // Handle icon search
    const iconSearch = document.getElementById('icon-search');
    iconSearch.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        document.querySelectorAll('.icon-item').forEach(function(item) {
            const iconClass = item.getAttribute('data-icon').toLowerCase();
            if (iconClass.includes(searchTerm)) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    });
    
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

<style>
.icon-card {
    cursor: pointer;
    transition: all 0.2s ease;
    margin-bottom: 10px;
}
.icon-card:hover {
    background-color: rgba(32, 107, 196, 0.1);
    border-color: #206bc4;
}
</style>

<?php
// Include footer
require_once '../includes/footer.php';
?>