<?php
/**
 * Standalone Test for fetchOne() function
 *
 * Instructions:
 * 1. Ensure 'config/database.php' contains the DEBUG versions of executeQuery() and fetchOne()
 * that log detailed steps to the PHP error log.
 * 2. Place this file in your project's root directory (alongside the 'config' folder).
 * 3. Access this script via your browser (e.g., http://localhost/yourproject/test_fetch_one.php).
 * 4. Check BOTH the browser output AND your PHP error log file for detailed diagnostics.
 */

// Enable full error reporting for this test script
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Test `WorkspaceOne()` Function</h1>";

// --- Configuration & Setup ---
define('CONFIG_PATH', __DIR__ . '/config/');

if (!file_exists(CONFIG_PATH . 'database.php')) {
    echo "<p style='color:red;'><strong>Error:</strong> `config/database.php` not found.</p>";
    exit;
}
if (!file_exists(CONFIG_PATH . 'functions.php')) {
    echo "<p style='color:red;'><strong>Error:</strong> `config/functions.php` not found.</p>";
    // exit; // May not be strictly necessary if fetchOne only depends on database.php's executeQuery
}

echo "<p>Attempting to load `config/database.php` and `config/functions.php`...</p>";
require_once CONFIG_PATH . 'database.php'; // This should have the DEBUG versions of executeQuery & fetchOne
require_once CONFIG_PATH . 'functions.php'; // For completeness, e.g. if sanitizeInput were used on username
echo "<p>Files loaded.</p>";

// --- Test Parameters ---
$test_username = 'admin'; // The username you are testing
$sql = "SELECT id, username, password_hash, email, is_active, is_verified,
        failed_login_attempts, lockout_until
        FROM users
        WHERE username = ?";
$types = "s";
$params = [$test_username];

echo "<h2>Attempting to fetch user: '" . htmlspecialchars($test_username) . "' using `WorkspaceOne()`</h2>";
echo "<p>SQL: <code>" . htmlspecialchars($sql) . "</code></p>";
echo "<p>Params: <code>" . htmlspecialchars(print_r($params, true)) . "</code></p>";
echo "<p style='color:blue; font-weight:bold;'>IMPORTANT: Check your PHP error log for detailed DEBUG messages from inside `executeQuery()` and `WorkspaceOne()` after this script runs.</p>";

// --- Execute fetchOne ---
$user_data = null;
try {
    $user_data = fetchOne($sql, $types, $params);
} catch (Exception $e) {
    echo "<p style='color:red;'><strong>EXCEPTION during `WorkspaceOne()`:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}

// --- Display Results ---
echo "<h3>Result of `WorkspaceOne()`:</h3>";

if ($user_data === false) {
    echo "<p style='color:red;'><strong>`WorkspaceOne()` returned `false`.</strong> This indicates an error occurred during query preparation, execution, or result fetching (e.g., `get_result()` failed). Check the PHP error log for 'DEBUG' messages from `database.php`.</p>";
} elseif ($user_data === null) {
    echo "<p style='color:orange;'><strong>`WorkspaceOne()` returned `null`.</strong> This means the query executed successfully but found NO matching rows for username '" . htmlspecialchars($test_username) . "'. Check the PHP error log for 'DEBUG' messages, specifically the 'Number of rows in result set'.</p>";
} else {
    echo "<p style_color:green;'><strong>`WorkspaceOne()` returned user data!</strong></p>";
    echo "<pre>" . htmlspecialchars(print_r($user_data, true)) . "</pre>";
}

echo "<hr>";
echo "<p style='color:blue; font-weight:bold;'>Reminder: The most detailed information about *why* `WorkspaceOne` behaved as it did will be in your PHP error log file, showing the step-by-step execution within `executeQuery` and `WorkspaceOne` from your modified `database.php`.</p>";

echo "<h2>Test Complete.</h2>";

?>