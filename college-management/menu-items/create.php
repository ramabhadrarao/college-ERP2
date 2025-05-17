<?php
/**
 * Menu Items List Page
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

// Get menu items with hierarchical structure
$itemsSql = "SELECT mi.id, mi.title, mi.route, mi.icon, mi.parent_id, mi.item_order, mi.is_active,
            (SELECT COUNT(*) FROM menu_items mi2 WHERE mi2.parent_id = mi.id) as child_count
            FROM menu_items mi
            WHERE mi.menu_id = ?
            ORDER BY mi.parent_id, mi.item_order";
$items = fetchAll($itemsSql, "i", [$menuId]);

// Build hierarchical structure
$menuItems = [];
$menuMap = [];

// First, create a map of menu items keyed by ID
foreach ($items as $item) {
    $menuMap[$item['id']] = $item;
    $menuMap[$item['id']]['children'] = [];
}

// Then, build the hierarchical structure
foreach ($items as $item) {
    $id = $item['id'];
    $parentId = $item['parent_id'];
    
    if ($parentId === null) {
        // Root level menu item
        $menuItems[] = &$menuMap[$id];
    } else {
        // Child menu item
        if (isset($menuMap[$parentId])) {
            $menuMap[$parentId]['children'][] = &$menuMap[$id];
        }
    }
}

// Process actions if submitted (delete, activate, deactivate)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = sanitizeInput($_POST['action'] ?? '');
    $itemId = sanitizeInput($_POST['item_id'] ?? '');
    
    if (!empty($itemId)) {
        switch ($action) {
            case 'activate':
                $updateSql = "UPDATE menu_items SET is_active = 1, updated_at = ? WHERE id = ?";
                if (executeUpdate($updateSql, "si", [getCurrentDateTime(), $itemId])) {
                    setFlashMessage('success', 'Menu item activated successfully');
                } else {
                    setFlashMessage('error', 'Failed to activate menu item');
                }
                break;
                
            case 'deactivate':
                $updateSql = "UPDATE menu_items SET is_active = 0, updated_at = ? WHERE id = ?";
                if (executeUpdate($updateSql, "si", [getCurrentDateTime(), $itemId])) {
                    setFlashMessage('success', 'Menu item deactivated successfully');
                } else {
                    setFlashMessage('error', 'Failed to deactivate menu item');
                }
                break;
                
            case 'delete':
                // Check if item has children
                $childCheckSql = "SELECT COUNT(*) as child_count FROM menu_items WHERE parent_id = ?";
                $childCheck = fetchOne($childCheckSql, "i", [$itemId]);
                
                if ($childCheck && $childCheck['child_count'] > 0) {
                    setFlashMessage('error', 'Menu item cannot be deleted because it has ' . $childCheck['child_count'] . ' child item(s)');
                } else {
                    // Delete the menu item
                    $deleteItemSql = "DELETE FROM menu_items WHERE id = ?";
                    if (executeUpdate($deleteItemSql, "i", [$itemId])) {
                        setFlashMessage('success', 'Menu item deleted successfully');
                    } else {
                        setFlashMessage('error', 'Failed to delete menu item');
                    }
                }
                break;
                
            case 'reorder':
                $parentId = sanitizeInput($_POST['parent_id'] ?? 'null');
                $newOrder = intval($_POST['new_order'] ?? 0);
                
                // Update the item order
                $updateSql = "UPDATE menu_items SET item_order = ?, parent_id = ?, updated_at = ? WHERE id = ?";
                if (executeUpdate($updateSql, "iisi", [$newOrder, $parentId === 'null' ? null : $parentId, getCurrentDateTime(), $itemId])) {
                    setFlashMessage('success', 'Menu item order updated successfully');
                } else {
                    setFlashMessage('error', 'Failed to update menu item order');
                }
                break;
        }
        
        // Redirect to refresh the page
        redirect($_SERVER['PHP_SELF'] . '?menu_id=' . $menuId);
    }
}

// Function to render menu items recursively
function renderMenuItems($items, $level = 0) {
    global $menuId;
    $html = '';
    
    foreach ($items as $index => $item) {
        $hasChildren = !empty($item['children']);
        $paddingLeft = $level * 20;
        
        $html .= '<tr>';
        
        // Title with padding for hierarchy
        $html .= '<td>';
        $html .= '<div class="d-flex align-items-center" style="padding-left: ' . $paddingLeft . 'px;">';
        if ($hasChildren) {
            $html .= '<span class="me-2"><i class="ti ti-folder"></i></span>';
        } else {
            $html .= '<span class="me-2"><i class="ti ti-file"></i></span>';
        }
        $html .= htmlspecialchars($item['title']);
        if (!$item['is_active']) {
            $html .= ' <span class="badge bg-secondary ms-1">Inactive</span>';
        }
        $html .= '</div>';
        $html .= '</td>';
        
        // Route
        $html .= '<td>' . htmlspecialchars($item['route'] ?: 'No route') . '</td>';
        
        // Icon
        $html .= '<td>';
        if ($item['icon']) {
            $html .= '<i class="' . htmlspecialchars($item['icon']) . '"></i> ' . htmlspecialchars($item['icon']);
        } else {
            $html .= 'No icon';
        }
        $html .= '</td>';
        
        // Order
        $html .= '<td>' . $item['item_order'] . '</td>';
        
        // Actions
        $html .= '<td>';
        $html .= '<div class="btn-group">';
        $html .= '<button type="button" class="btn btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">Actions</button>';
        $html .= '<div class="dropdown-menu dropdown-menu-end">';
        
        // Edit action
        $html .= '<a href="' . BASE_URL . '/menu-items/edit.php?id=' . $item['id'] . '&menu_id=' . $menuId . '" class="dropdown-item">';
        $html .= '<i class="ti ti-edit"></i> Edit';
        $html .= '</a>';
        
        // Move/reorder actions
        $html .= '<button type="button" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#reorderModal" ' .
                'data-item-id="' . $item['id'] . '" data-item-title="' . htmlspecialchars($item['title']) . '" ' .
                'data-item-parent="' . ($item['parent_id'] ?? 'null') . '" data-item-order="' . $item['item_order'] . '">';
        $html .= '<i class="ti ti-arrows-move"></i> Move/Reorder';
        $html .= '</button>';
        
        $html .= '<div class="dropdown-divider"></div>';
        
        // Activate/Deactivate action
        if ($item['is_active']) {
            $html .= '<form action="' . htmlspecialchars($_SERVER['PHP_SELF']) . '?menu_id=' . $menuId . '" method="post">';
            $html .= '<input type="hidden" name="action" value="deactivate">';
            $html .= '<input type="hidden" name="item_id" value="' . $item['id'] . '">';
            $html .= '<button type="submit" class="dropdown-item">';
            $html .= '<i class="ti ti-eye-off"></i> Deactivate';
            $html .= '</button>';
            $html .= '</form>';
        } else {
            $html .= '<form action="' . htmlspecialchars($_SERVER['PHP_SELF']) . '?menu_id=' . $menuId . '" method="post">';
            $html .= '<input type="hidden" name="action" value="activate">';
            $html .= '<input type="hidden" name="item_id" value="' . $item['id'] . '">';
            $html .= '<button type="submit" class="dropdown-item">';
            $html .= '<i class="ti ti-eye"></i> Activate';
            $html .= '</button>';
            $html .= '</form>';
        }
        
        // Delete action
        if (!$hasChildren) {
            $html .= '<form action="' . htmlspecialchars($_SERVER['PHP_SELF']) . '?menu_id=' . $menuId . '" method="post">';
            $html .= '<input type="hidden" name="action" value="delete">';
            $html .= '<input type="hidden" name="item_id" value="' . $item['id'] . '">';
            $html .= '<button type="submit" class="dropdown-item text-danger" onclick="return confirm(\'Are you sure you want to delete this menu item? This action cannot be undone.\')">';
            $html .= '<i class="ti ti-trash"></i> Delete';
            $html .= '</button>';
            $html .= '</form>';
        }
        
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</td>';
        $html .= '</tr>';
        
        // Render children recursively
        if ($hasChildren) {
            $html .= renderMenuItems($item['children'], $level + 1);
        }
    }
    
    return $html;
}

// Get all menu items for parent dropdown in reorder modal
$allItemsSql = "SELECT id, title FROM menu_items WHERE menu_id = ? ORDER BY title";
$allItems = fetchAll($allItemsSql, "i", [$menuId]);
?>

<!-- Menu items list page content -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Menu Items: <?php echo $menu['name']; ?></h3>
        <div class="card-actions">
            <a href="<?php echo BASE_URL; ?>/menu-items/create.php?menu_id=<?php echo $menuId; ?>" class="btn btn-primary">
                <i class="ti ti-plus"></i> Add New Item
            </a>
        </div>
    </div>
    <div class="card-body">
        <?php if (empty($menuItems)): ?>
            <div class="alert alert-info">
                <i class="ti ti-info-circle"></i> No menu items found for this menu.
            </div>
            <div class="text-center">
                <a href="<?php echo BASE_URL; ?>/menu-items/create.php?menu_id=<?php echo $menuId; ?>" class="btn btn-primary">
                    <i class="ti ti-plus"></i> Add First Menu Item
                </a>
            </div>
        <?php else: ?>
            <!-- Menu items table -->
            <div class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Route</th>
                            <th>Icon</th>
                            <th>Order</th>
                            <th class="w-1">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php echo renderMenuItems($menuItems); ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
    <div class="card-footer">
        <a href="<?php echo BASE_URL; ?>/menus/index.php" class="btn btn-outline-secondary">
            <i class="ti ti-arrow-left"></i> Back to Menus
        </a>
    </div>
</div>

<!-- Reorder Modal -->
<div class="modal modal-blur fade" id="reorderModal" tabindex="-1" aria-labelledby="reorderModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="reorderForm" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'] . '?menu_id=' . $menuId); ?>" method="post">
                <input type="hidden" name="action" value="reorder">
                <input type="hidden" name="item_id" id="reorder-item-id">
                
                <div class="modal-header">
                    <h5 class="modal-title" id="reorderModalLabel">Move/Reorder Menu Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Reordering: <strong id="reorder-item-title"></strong></p>
                    
                    <div class="mb-3">
                        <label class="form-label">Parent Menu Item</label>
                        <select name="parent_id" id="reorder-parent" class="form-select">
                            <option value="null">-- No Parent (Top Level) --</option>
                            <?php foreach ($allItems as $item): ?>
                                <option value="<?php echo $item['id']; ?>"><?php echo $item['title']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Order</label>
                        <input type="number" name="new_order" id="reorder-order" class="form-control" min="0" value="0">
                        <small class="form-hint">Items are ordered from lowest to highest value.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle reorder modal
    const reorderModal = document.getElementById('reorderModal');
    if (reorderModal) {
        reorderModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const itemId = button.getAttribute('data-item-id');
            const itemTitle = button.getAttribute('data-item-title');
            const parentId = button.getAttribute('data-item-parent');
            const itemOrder = button.getAttribute('data-item-order');
            
            document.getElementById('reorder-item-id').value = itemId;
            document.getElementById('reorder-item-title').textContent = itemTitle;
            
            // Set parent dropdown value
            const parentSelect = document.getElementById('reorder-parent');
            
            // Remove the current item from available parents to prevent circular reference
            for (let i = 0; i < parentSelect.options.length; i++) {
                if (parentSelect.options[i].value === itemId) {
                    parentSelect.options[i].disabled = true;
                } else {
                    parentSelect.options[i].disabled = false;
                }
            }
            
            // Set the current parent
            for (let i = 0; i < parentSelect.options.length; i++) {
                if (parentSelect.options[i].value === parentId) {
                    parentSelect.selectedIndex = i;
                    break;
                }
            }
            
            // Set the current order
            document.getElementById('reorder-order').value = itemOrder;
        });
    }
});
</script>

<?php
// Include footer
require_once '../includes/footer.php';
?>