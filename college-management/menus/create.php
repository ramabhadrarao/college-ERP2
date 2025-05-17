<?php
/**
 * Create Menu Page
 */
require_once '../config/database.php';
require_once '../config/functions.php';

// Initialize session
initSession();

// Include header
require_once '../includes/header.php';

// Check if user has permission to create menus
requirePermission('menu_create');

// Initialize variables
$errors = [];
$menuData = [
    'name' => '',
    'description' => ''
];

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get and sanitize input
    $menuData['name'] = sanitizeInput($_POST['name'] ?? '');
    $menuData['description'] = sanitizeInput($_POST['description'] ?? '');
    
    // Validate input
    if (empty($menuData['name'])) {
        $errors[] = 'Menu name is required';
    } else {
        // Check if menu name already exists
        $checkSql = "SELECT id FROM menus WHERE name = ?";
        $existingMenu = fetchOne($checkSql, "s", [$menuData['name']]);
        if ($existingMenu) {
            $errors[] = 'Menu name already exists';
        }
    }
    
    // If no errors, create the menu
    if (empty($errors)) {
        // Insert the menu
        $insertSql = "INSERT INTO menus (name, description, created_at, updated_at) 
                     VALUES (?, ?, ?, ?)";
        $menuId = insertAndGetId($insertSql, "ssss", [
            $menuData['name'],
            $menuData['description'],
            getCurrentDateTime(),
            getCurrentDateTime()
        ]);
        
        if ($menuId) {
            // Redirect to menu list with success message
            setFlashMessage('success', 'Menu created successfully');
            
            // Check if we should redirect to menu items
            if (isset($_POST['add_items']) && $_POST['add_items'] == 1) {
                redirect(BASE_URL . '/menu-items/index.php?menu_id=' . $menuId);
            } else {
                redirect(BASE_URL . '/menus/index.php');
            }
        } else {
            $errors[] = 'Failed to create menu. Please try again.';
        }
    }
}
?>

<!-- Create menu form -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Create New Menu</h3>
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
        
        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label required">Menu Name</label>
                    <input type="text" name="name" class="form-control" placeholder="Enter menu name" value="<?php echo $menuData['name']; ?>" required>
                    <small class="form-hint">Use a unique identifier, e.g., main_sidebar, user_menu</small>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label class="form-label">Description</label>
                    <input type="text" name="description" class="form-control" placeholder="Enter menu description" value="<?php echo $menuData['description']; ?>">
                </div>
                
                <div class="col-md-12 mb-3">
                    <label class="form-check">
                        <input type="checkbox" name="add_items" value="1" class="form-check-input">
                        <span class="form-check-label">Add menu items after creation</span>
                    </label>
                </div>
            </div>
            
            <div class="form-footer">
                <a href="<?php echo BASE_URL; ?>/menus/index.php" class="btn btn-outline-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary ms-2">Create Menu</button>
            </div>
        </form>
    </div>
</div>

<?php
// Include footer
require_once '../includes/footer.php';
?>