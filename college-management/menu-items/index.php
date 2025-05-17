<?php
/**
 * Edit Menu Page
 */
require_once '../config/database.php';
require_once '../config/functions.php';

// Initialize session
initSession();

// Include header
require_once '../includes/header.php';

// Check if user has permission to edit menus
requirePermission('menu_edit');

// Get menu ID from URL parameter
$menuId = sanitizeInput($_GET['id'] ?? '');

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

// Initialize error array
$errors = [];

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get and sanitize input
    $name = sanitizeInput($_POST['name'] ?? '');
    $description = sanitizeInput($_POST['description'] ?? '');
    
    // Validate input
    if (empty($name)) {
        $errors[] = 'Menu name is required';
    } else if ($name !== $menu['name']) {
        // Check if menu name is already in use by another menu
        $checkSql = "SELECT id FROM menus WHERE name = ? AND id != ?";
        $existingMenu = fetchOne($checkSql, "si", [$name, $menuId]);
        if ($existingMenu) {
            $errors[] = 'Menu name already exists';
        }
    }
    
    // If no errors, update the menu
    if (empty($errors)) {
        // Update menu
        $updateSql = "UPDATE menus SET name = ?, description = ?, updated_at = ? WHERE id = ?";
        $result = executeUpdate($updateSql, "sssi", [$name, $description, getCurrentDateTime(), $menuId]);
        
        if ($result !== false) {
            // Redirect to menu list with success message
            setFlashMessage('success', 'Menu updated successfully');
            redirect(BASE_URL . '/menus/index.php');
        } else {
            $errors[] = 'Failed to update menu. Please try again.';
        }
    }
    
    // Update menu object with submitted values for re-rendering the form
    $menu['name'] = $name;
    $menu['description'] = $description;
}
?>

<!-- Edit menu form -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Edit Menu: <?php echo $menu['name']; ?></h3>
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
        
        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'] . '?id=' . $menuId); ?>" method="post">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label required">Menu Name</label>
                    <input type="text" name="name" class="form-control" placeholder="Enter menu name" value="<?php echo $menu['name']; ?>" required>
                    <small class="form-hint">Use a unique identifier, e.g., main_sidebar, user_menu</small>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label class="form-label">Description</label>
                    <input type="text" name="description" class="form-control" placeholder="Enter menu description" value="<?php echo $menu['description']; ?>">
                </div>
            </div>
            
            <div class="form-footer">
                <a href="<?php echo BASE_URL; ?>/menus/index.php" class="btn btn-outline-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary ms-2">Update Menu</button>
                
                <?php if (hasPermission('menu_item_manage')): ?>
                    <a href="<?php echo BASE_URL; ?>/menu-items/index.php?menu_id=<?php echo $menuId; ?>" class="btn btn-outline-primary ms-2">
                        <i class="ti ti-list"></i> Manage Menu Items
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<?php
// Include footer
require_once '../includes/footer.php';
?>