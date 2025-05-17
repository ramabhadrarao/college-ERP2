<?php
/**
 * Database Installation and Seeding Script
 * 
 * This script creates the database schema and initial data for the College Management System.
 * It can also seed the database with test data.
 */

// For debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set maximum execution time to avoid timeouts during installation
ini_set('max_execution_time', 300); // 5 minutes

// Load configuration and functions
require_once './config/database.php';
require_once './config/functions.php';

// Start session for flash messages
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Flag to track if this is a form submission or initial page load
$isSubmitted = $_SERVER['REQUEST_METHOD'] === 'POST';
$isInstalled = false;
$errors = [];
$messages = [];
$debugInfo = []; // For troubleshooting
$seedMode = isset($_GET['seed']) && $_GET['seed'] === 'true';

// Function to execute SQL file
function executeSqlFile($filename) {
    global $errors, $messages, $debugInfo;
    
    // Check if file exists
    if (!file_exists($filename)) {
        $errors[] = "SQL file not found: $filename";
        $debugInfo[] = "File path checked: " . realpath(dirname($filename)) . '/' . basename($filename);
        return false;
    }
    
    // Read SQL file
    $sql = file_get_contents($filename);
    if ($sql === false) {
        $errors[] = "Failed to read SQL file: $filename";
        return false;
    }
    
    try {
        // Connect to database
        $conn = getDbConnection();
        
        // Execute SQL statements
        $result = $conn->multi_query($sql);
        
        if (!$result) {
            $errors[] = "SQL Error: " . $conn->error . " in file: " . basename($filename);
            $conn->close();
            return false;
        }
        
        // Process all results to clear the queue
        do {
            if ($result = $conn->store_result()) {
                $result->free();
            }
            
            if ($conn->error) {
                $errors[] = "SQL Error: " . $conn->error . " in file: " . basename($filename);
                $conn->close();
                return false;
            }
            
            if (!$conn->more_results()) {
                break;
            }
            
            $conn->next_result();
        } while (true);
        
        $conn->close();
        $messages[] = "Successfully executed SQL file: " . basename($filename);
        return true;
    } catch (Exception $e) {
        $errors[] = "Error executing SQL file: " . $e->getMessage();
        $debugInfo[] = "Exception details: " . $e->getTraceAsString();
        return false;
    }
}

// Function to check if database is already installed
function isDatabaseInstalled() {
    global $debugInfo;
    
    try {
        $conn = getDbConnection();
        
        // Check if users table exists
        $result = $conn->query("SHOW TABLES LIKE 'users'");
        
        $installed = $result && $result->num_rows > 0;
        
        $debugInfo[] = "Database check: users table " . ($installed ? "exists" : "doesn't exist");
        
        $conn->close();
        return $installed;
    } catch (Exception $e) {
        $debugInfo[] = "Error checking if database is installed: " . $e->getMessage();
        return false;
    }
}

// Check if database is already installed
$isAlreadyInstalled = isDatabaseInstalled();
$debugInfo[] = "Is database already installed: " . ($isAlreadyInstalled ? "Yes" : "No");

// Process the seeding if requested and already installed
if ($seedMode && $isAlreadyInstalled) {
    if ($isSubmitted) {
        $seedType = sanitizeInput($_POST['seed_type'] ?? '');
        $seedCount = intval($_POST['seed_count'] ?? 10);
        
        // Limit the number of records for safety
        if ($seedCount > 100) {
            $seedCount = 100;
        }
        
        $debugInfo[] = "Seed type: $seedType, Seed count: $seedCount";
        
        // Run the appropriate seeding function
        // Note: These functions would be defined here, similar to the previous version
    }
}
// Process installation if not in seed mode and not already installed
else if (!$seedMode && !$isAlreadyInstalled && $isSubmitted) {
    $debugInfo[] = "Processing installation submission";
    
    // Check if admin account details are provided
    $adminUsername = sanitizeInput($_POST['admin_username'] ?? '');
    $adminEmail = sanitizeInput($_POST['admin_email'] ?? '');
    $adminPassword = $_POST['admin_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    
    // Validate admin account details
    if (empty($adminUsername)) {
        $errors[] = "Admin username is required";
    }
    
    if (empty($adminEmail)) {
        $errors[] = "Admin email is required";
    } else if (!isValidEmail($adminEmail)) {
        $errors[] = "Please enter a valid email address";
    }
    
    if (empty($adminPassword)) {
        $errors[] = "Admin password is required";
    } else {
        $passwordValidation = validatePassword($adminPassword);
        if (!$passwordValidation['valid']) {
            $errors = array_merge($errors, $passwordValidation['errors']);
        }
    }
    
    if ($adminPassword !== $confirmPassword) {
        $errors[] = "Passwords do not match";
    }
    
    $debugInfo[] = "Validation errors: " . count($errors);
    
    // If validation passes, proceed with installation
    if (empty($errors)) {
        $debugInfo[] = "Starting installation process";
        
        try {
            // Create the database schema
            $schemaFiles = [
                // Core schema files - Ensure these files exist
                "./sql/schema/0_initial_management.sql",
                "./sql/schema/1_core_schema.sql",
                "./sql/schema/2_academic_structure.sql",
            ];
            
            $filesExecuted = 0;
            
            // Execute each schema file
            foreach ($schemaFiles as $file) {
                $debugInfo[] = "Attempting to execute: $file";
                
                if (executeSqlFile($file)) {
                    $filesExecuted++;
                } else {
                    break;
                }
            }
            
            $debugInfo[] = "Files executed: $filesExecuted out of " . count($schemaFiles);
            
            // If no errors occurred, create the default data
            if (empty($errors)) {
                $debugInfo[] = "Creating admin user";
                
                // Generate admin user ID
                $adminId = generateUuid();
                
                // Hash the admin password
                $passwordHash = hashPassword($adminPassword);
                
                // Insert admin user
                $insertAdminSql = "INSERT INTO users (id, username, password_hash, email, is_active, is_verified, created_at, updated_at) 
                                  VALUES (?, ?, ?, ?, 1, 1, ?, ?)";
                $currentDateTime = getCurrentDateTime();
                $result = executeUpdate($insertAdminSql, "ssssss", [
                    $adminId,
                    $adminUsername,
                    $passwordHash,
                    $adminEmail,
                    $currentDateTime,
                    $currentDateTime
                ]);
                
                if ($result === false) {
                    $errors[] = "Failed to create admin user";
                    $debugInfo[] = "Admin user creation failed";
                } else {
                    $debugInfo[] = "Admin user created successfully";
                    
                    // Create default roles and permissions
                    $debugInfo[] = "Attempting to create roles and permissions";
                    
                    if (executeSqlFile("./sql/data/default_roles_permissions.sql")) {
                        $debugInfo[] = "Roles and permissions created successfully";
                        
                        // Assign admin role to admin user
                        $assignRoleSql = "INSERT INTO user_roles (user_id, role_id, created_at) 
                                         VALUES (?, (SELECT id FROM roles WHERE name = 'Admin'), ?)";
                        $result = executeUpdate($assignRoleSql, "ss", [$adminId, $currentDateTime]);
                        
                        if ($result === false) {
                            $errors[] = "Failed to assign admin role to admin user";
                            $debugInfo[] = "Role assignment failed";
                        } else {
                            $debugInfo[] = "Admin role assigned successfully";
                            
                            // Create default menus
                            $debugInfo[] = "Attempting to create default menus";
                            
                            if (executeSqlFile("./sql/data/default_menus.sql")) {
                                $debugInfo[] = "Default menus created successfully";
                                $messages[] = "Successfully created default menus";
                                $isInstalled = true;
                            } else {
                                $debugInfo[] = "Failed to create default menus";
                            }
                        }
                    } else {
                        $debugInfo[] = "Failed to create roles and permissions";
                    }
                }
            }
            
            // If installation was successful, prepare for redirect
            if ($isInstalled) {
                $debugInfo[] = "Installation successful, preparing redirect";
                setFlashMessage('success', 'Installation completed successfully. You can now log in with your admin account.');
                
                // We'll redirect at the end of the script, not here
                // This allows us to show any debug info if needed
            }
        } catch (Exception $e) {
            $errors[] = "Installation error: " . $e->getMessage();
            $debugInfo[] = "Installation exception: " . $e->getMessage();
            $debugInfo[] = "Stack trace: " . $e->getTraceAsString();
        }
    }
}

// Define the seeding functions (stub versions)
function seedUsers($count = 10) {
    global $errors, $messages;
    $messages[] = "Seeded $count users";
    return true;
}

function seedDepartments($count = 5) {
    global $errors, $messages;
    $messages[] = "Seeded $count departments";
    return true;
}

function seedPrograms($count = 10) {
    global $errors, $messages;
    $messages[] = "Seeded $count programs";
    return true;
}

function seedStudents($count = 20) {
    global $errors, $messages;
    $messages[] = "Seeded $count students";
    return true;
}

function seedFaculty($count = 10) {
    global $errors, $messages;
    $messages[] = "Seeded $count faculty members";
    return true;
}

function seedAllData($count = 10) {
    seedDepartments(5);
    seedPrograms(10);
    seedUsers($count);
    seedStudents($count * 2);
    seedFaculty($count);
}

// Determine if we should show debug information
$showDebug = isset($_GET['debug']) && $_GET['debug'] === 'true';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo ($seedMode ? 'Database Seeding' : 'Install'); ?> - <?php echo APP_NAME; ?></title>
    <!-- Tabler CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta17/dist/css/tabler.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@2.30.0/tabler-icons.min.css">
</head>
<body class="d-flex flex-column">
    <div class="page page-center">
        <div class="container container-tight py-4">
            <div class="text-center mb-4">
                <h1><?php echo APP_NAME; ?> - <?php echo ($seedMode ? 'Database Seeding' : 'Installation'); ?></h1>
            </div>
            
            <!-- Debug Information -->
            <?php if ($showDebug): ?>
                <div class="card mb-3">
                    <div class="card-header">
                        <h3 class="card-title">Debug Information</h3>
                    </div>
                    <div class="card-body">
                        <pre><?php print_r($debugInfo); ?></pre>
                    </div>
                </div>
            <?php endif; ?>
            
            <div class="card card-md">
                <div class="card-body">
                    <?php if ($isAlreadyInstalled && !$seedMode): ?>
                        <div class="alert alert-info">
                            <h4 class="alert-title">System is already installed</h4>
                            <p>The database schema already exists. If you want to seed the database with test data, use the link below:</p>
                        </div>
                        <div class="mt-3">
                            <a href="?seed=true" class="btn btn-primary w-100">Seed Database with Test Data</a>
                            <a href="index.php" class="btn btn-outline-secondary w-100 mt-2">Go to Dashboard</a>
                        </div>
                    <?php elseif ($seedMode && $isAlreadyInstalled): ?>
                        <h2 class="text-center mb-4">Database Seeding</h2>
                        
                        <?php if (!empty($errors)): ?>
                            <div class="alert alert-danger mb-3">
                                <h4 class="alert-title">Seeding Error</h4>
                                <ul class="mb-0">
                                    <?php foreach ($errors as $error): ?>
                                        <li><?php echo $error; ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($messages)): ?>
                            <div class="alert alert-success mb-3">
                                <h4 class="alert-title">Seeding Progress</h4>
                                <ul class="mb-0">
                                    <?php foreach ($messages as $message): ?>
                                        <li><?php echo $message; ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                        
                        <form action="?seed=true" method="post">
                            <div class="mb-3">
                                <label class="form-label">Seed Type</label>
                                <select name="seed_type" class="form-select">
                                    <option value="users">Users</option>
                                    <option value="departments">Departments</option>
                                    <option value="programs">Programs</option>
                                    <option value="students">Students</option>
                                    <option value="faculty">Faculty</option>
                                    <option value="all" selected>All Data Types</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Number of Records</label>
                                <input type="number" name="seed_count" class="form-control" value="10" min="1" max="100">
                                <small class="form-hint">Maximum 100 records per type for performance reasons.</small>
                            </div>
                            
                            <div class="alert alert-warning">
                                <h4 class="alert-title">Warning</h4>
                                <p>Seeding will add test data to your database. This is intended for development and testing purposes only.</p>
                            </div>
                            
                            <div class="form-footer">
                                <a href="install.php" class="btn btn-outline-secondary">Back</a>
                                <button type="submit" class="btn btn-primary ms-auto">Seed Database</button>
                            </div>
                        </form>
                    <?php elseif ($isInstalled): ?>
                        <div class="alert alert-success">
                            <h4 class="alert-title">Installation completed successfully</h4>
                            <p>The system has been installed successfully. You can now log in with your admin account.</p>
                        </div>
                        <div class="mt-3">
                            <a href="auth/login.php" class="btn btn-primary w-100">Go to Login</a>
                        </div>
                    <?php else: ?>
                        <h2 class="text-center mb-4">System Installation</h2>
                        
                        <?php if (!empty($errors)): ?>
                            <div class="alert alert-danger mb-3">
                                <h4 class="alert-title">Installation Error</h4>
                                <ul class="mb-0">
                                    <?php foreach ($errors as $error): ?>
                                        <li><?php echo $error; ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($messages)): ?>
                            <div class="alert alert-success mb-3">
                                <h4 class="alert-title">Installation Progress</h4>
                                <ul class="mb-0">
                                    <?php foreach ($messages as $message): ?>
                                        <li><?php echo $message; ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                        
                        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
                            <div class="mb-3">
                                <h3>Admin Account</h3>
                                <p class="text-muted">Create an administrator account to manage the system.</p>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label required">Username</label>
                                <input type="text" name="admin_username" class="form-control" placeholder="Enter admin username" value="<?php echo isset($adminUsername) ? $adminUsername : 'admin'; ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label required">Email</label>
                                <input type="email" name="admin_email" class="form-control" placeholder="Enter admin email" value="<?php echo isset($adminEmail) ? $adminEmail : ''; ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label required">Password</label>
                                <input type="password" name="admin_password" class="form-control" placeholder="Enter admin password" required>
                                <small class="form-hint">
                                    Password must be at least <?php echo PASSWORD_MIN_LENGTH; ?> characters long and include 
                                    uppercase letters, lowercase letters, numbers, and special characters.
                                </small>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label required">Confirm Password</label>
                                <input type="password" name="confirm_password" class="form-control" placeholder="Confirm admin password" required>
                            </div>
                            
                            <div class="form-footer">
                                <button type="submit" class="btn btn-primary w-100">Install System</button>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Additional links -->
            <div class="text-center mt-3">
                <?php if (!$isAlreadyInstalled): ?>
                    <a href="?debug=true" class="text-muted">Debug Mode</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Tabler JS -->
    <script src="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta17/dist/js/tabler.min.js"></script>
    
    <?php
    // Only redirect after showing the page
    if ($isInstalled && $isSubmitted) {
        echo "<script>setTimeout(function() { window.location.href = 'auth/login.php'; }, 3000);</script>";
    }
    ?>
</body>
</html>