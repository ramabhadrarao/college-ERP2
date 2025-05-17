<?php
/**
 * Standalone Database Connection and User Fetch Test
 *
 * Instructions:
 * 1. Place this file in your project's root directory (alongside the 'config' folder).
 * 2. Ensure 'config/database.php' exists and has the correct credentials.
 * 3. Access this script via your browser (e.g., http://localhost/yourproject/test_db_connection.php).
 */

// Enable full error reporting for this test script
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Database Connection & User Fetch Test</h1>";

// Define a simple path to your config directory
// Adjust if this script is placed elsewhere
define('CONFIG_PATH', __DIR__ . '/config/');

if (!file_exists(CONFIG_PATH . 'database.php')) {
    echo "<p style='color:red;'><strong>Error:</strong> `config/database.php` not found at the expected location: " . CONFIG_PATH . "database.php</p>";
    echo "<p>Please ensure the path is correct and the file exists.</p>";
    exit;
}

require_once CONFIG_PATH . 'database.php';
echo "<p>Attempting to load `config/database.php`... Loaded successfully.</p>";

echo "<h2>1. Testing Database Connection (getDbConnection()):</h2>";
$conn = null;
try {
    $conn = getDbConnection(); // Uses the function from your database.php
    if ($conn) {
        echo "<p style='color:green;'><strong>SUCCESS:</strong> Successfully connected to the database '" . DB_NAME . "' on host '" . DB_HOST . "' with user '" . DB_USER . "'.</p>";
        echo "<p>Server version: " . $conn->server_info . "</p>";
        echo "<p>Client version: " . $conn->client_info . "</p>";
        echo "<p>Host info: " . $conn->host_info . "</p>";
        echo "<p>Connection charset: " . $conn->character_set_name() . "</p>";
    } else {
        // getDbConnection() in your file calls die() or throws an exception on failure,
        // so this specific 'else' might not be reached if it behaves as written.
        echo "<p style='color:red;'><strong>FAILURE:</strong> getDbConnection() returned null or false without throwing an exception (unexpected).</p>";
    }
} catch (Exception $e) {
    echo "<p style='color:red;'><strong>FAILURE:</strong> An exception occurred during getDbConnection(): " . htmlspecialchars($e->getMessage()) . "</p>";
    // If connection fails here, $conn will be null, and the next steps will be skipped.
}

if ($conn) {
    echo "<h2>2. Testing Fetching Users (SELECT * FROM users):</h2>";

    $sql = "SELECT id, username, email, password_hash, is_active, created_at FROM users";
    echo "<p>Executing SQL: <code>" . htmlspecialchars($sql) . "</code></p>";

    // Using direct mysqli query for simplicity in this standalone test
    // This bypasses your executeQuery/fetchOne to directly test the connection and basic query execution.
    $result = $conn->query($sql);

    if ($result === false) {
        echo "<p style='color:red;'><strong>QUERY FAILED:</strong> MySQLi Error: " . htmlspecialchars($conn->error) . "</p>";
    } else {
        echo "<p style='color:green;'><strong>QUERY EXECUTED SUCCESSFULLY.</strong></p>";
        echo "<p>Number of users found: " . $result->num_rows . "</p>";

        if ($result->num_rows > 0) {
            echo "<h3>User Data:</h3>";
            echo "<table border='1' cellpadding='5' cellspacing='0'>";
            echo "<thead><tr><th>ID</th><th>Username</th><th>Email</th><th>Password Hash (partial)</th><th>Is Active</th><th>Created At</th></tr></thead>";
            echo "<tbody>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                echo "<td>" . htmlspecialchars($row['username']) . "</td>";
                echo "<td>" . htmlspecialchars($row['email'] ?? 'N/A') . "</td>";
                echo "<td>" . htmlspecialchars(substr($row['password_hash'] ?? 'N/A', 0, 20)) . "...</td>"; // Show only partial hash
                echo "<td>" . ($row['is_active'] ? 'Yes (1)' : 'No (0)') . "</td>";
                echo "<td>" . htmlspecialchars($row['created_at'] ?? 'N/A') . "</td>";
                echo "</tr>";
            }
            echo "</tbody>";
            echo "</table>";
            $result->free(); // Free the result set
        } else {
            echo "<p>No users found in the 'users' table.</p>";
        }
    }

    echo "<h2>3. Testing mysqlnd Availability (for get_result()):</h2>";
    if (function_exists('mysqli_stmt_get_result')) {
        echo "<p style='color:green;'><strong>SUCCESS:</strong> `mysqli_stmt_get_result` function exists. This indicates mysqlnd driver is likely available and integrated with mysqli.</p>";
    } else {
        echo "<p style='color:red;'><strong>WARNING:</strong> `mysqli_stmt_get_result` function does NOT exist. This is required for your `WorkspaceOne` and `WorkspaceAll` functions to work correctly. You need to install/enable the `php_mysqlnd` extension (or `php-mysql` on some systems which includes mysqlnd).</p>";
    }


    // Close the connection
    $conn->close();
    echo "<p>Database connection closed.</p>";
} else {
    echo "<p style='color:red;'>Cannot proceed to fetch users as the database connection failed.</p>";
}

echo "<h2>Test Complete.</h2>";

?>