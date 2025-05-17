<?php
/**
 * Database Configuration File
 *
 * This file contains the database connection parameters and establishes
 * a connection to the MySQL database.
 */

// Database credentials
define('DB_HOST', 'localhost');
define('DB_USER', 'wsl_user');
define('DB_PASS', 'Nihita@1981'); // Be cautious with hardcoding passwords in shared code
define('DB_NAME', 'college_management');

// Create connection
function getDbConnection() {
    $conn = null;

    try {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

        // Check connection
        if ($conn->connect_error) {
            throw new Exception("Connection failed: " . $conn->connect_error);
        }

        // Set charset to UTF-8
        $conn->set_charset("utf8mb4");

    } catch (Exception $e) {
        error_log("getDbConnection Exception: " . $e->getMessage()); // Log the actual connection error
        // Consider if die() is appropriate for all contexts, or if returning null/false
        // and letting the caller handle it is better for some scripts.
        // For a general library function, throwing the exception might be better.
        die("Database connection failed. Please contact the administrator. Check PHP error log for details.");
    }

    return $conn;
}

/**
 * Execute a prepared statement with parameters
 */
function executeQuery($sql, $types = "", $params = []) {
    $debug_query_log = [];
    $debug_query_log[] = "DEBUG executeQuery: Called at " . date('Y-m-d H:i:s');
    $debug_query_log[] = "DEBUG executeQuery: SQL: " . $sql;
    $debug_query_log[] = "DEBUG executeQuery: Types: '" . $types . "'";
    $debug_query_log[] = "DEBUG executeQuery: Params: " . htmlspecialchars(print_r($params, true));

    $conn = getDbConnection();

    if (!$conn) {
        $debug_query_log[] = "DEBUG executeQuery: getDbConnection() FAILED.";
        error_log(implode("\n", $debug_query_log));
        return false;
    }
    $debug_query_log[] = "DEBUG executeQuery: getDbConnection() SUCCESSFUL.";

    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        $debug_query_log[] = "DEBUG executeQuery: \$conn->prepare() FAILED. MySQL Error: [" . $conn->errno . "] " . $conn->error;
        error_log(implode("\n", $debug_query_log));
        $conn->close();
        return false;
    }
    $debug_query_log[] = "DEBUG executeQuery: \$conn->prepare() SUCCESSFUL.";

    if (!empty($params) && !empty($types)) {
        $bindParams = [];
        $bindParams[] = $types;
        for ($i = 0; $i < count($params); $i++) {
            $bindParams[] = &$params[$i]; // Pass by reference
        }
        $debug_query_log[] = "DEBUG executeQuery: Attempting \$stmt->bind_param() with types: " . $types;
        if (!call_user_func_array(array($stmt, 'bind_param'), $bindParams)) {
            $debug_query_log[] = "DEBUG executeQuery: \$stmt->bind_param() FAILED. MySQLi Stmt Error: [" . $stmt->errno . "] " . $stmt->error;
            error_log(implode("\n", $debug_query_log));
            $stmt->close();
            $conn->close();
            return false;
        }
        $debug_query_log[] = "DEBUG executeQuery: \$stmt->bind_param() SUCCESSFUL.";
    }

    $debug_query_log[] = "DEBUG executeQuery: Attempting \$stmt->execute().";
    if (!$stmt->execute()) {
        $debug_query_log[] = "DEBUG executeQuery: \$stmt->execute() FAILED. MySQLi Stmt Error: [" . $stmt->errno . "] " . $stmt->error;
        error_log(implode("\n", $debug_query_log));
        $stmt->close();
        $conn->close();
        return false;
    }
    $debug_query_log[] = "DEBUG executeQuery: \$stmt->execute() SUCCESSFUL.";
    $debug_query_log[] = "DEBUG executeQuery: \$stmt->affected_rows after execute: " . $stmt->affected_rows;
    // $stmt->num_rows is typically used after $stmt->store_result() for SELECT queries with prepared statements
    // or $result->num_rows after $stmt->get_result().
    // $debug_query_log[] = "DEBUG executeQuery: \$stmt->num_rows (may not be accurate here for SELECT before get_result/store_result): " . $stmt->num_rows;
    error_log(implode("\n", $debug_query_log));

    return $stmt; // Connection is NOT closed here if successful, statement is returned
}

/**
 * Fetch a single row from a query result
 */
function fetchOne($sql, $types = "", $params = []) {
    $debug_fetch_log = [];
    $debug_fetch_log[] = "DEBUG fetchOne: Called at " . date('Y-m-d H:i:s');
    $debug_fetch_log[] = "DEBUG fetchOne: SQL: " . $sql;
    $debug_fetch_log[] = "DEBUG fetchOne: Types: '" . $types . "'";
    $debug_fetch_log[] = "DEBUG fetchOne: Params: " . htmlspecialchars(print_r($params, true));

    $stmt = executeQuery($sql, $types, $params); // executeQuery has its own detailed logging

    if ($stmt === false) {
        $debug_fetch_log[] = "DEBUG fetchOne: executeQuery() returned false. Aborting fetchOne.";
        error_log(implode("\n", $debug_fetch_log));
        return false;
    }
    $debug_fetch_log[] = "DEBUG fetchOne: executeQuery() returned a statement object successfully.";

    $debug_fetch_log[] = "DEBUG fetchOne: Attempting \$stmt->get_result().";
    $result = $stmt->get_result();

    if ($result === false) {
        $debug_fetch_log[] = "DEBUG fetchOne: \$stmt->get_result() FAILED. MySQLi Stmt Error: [" . $stmt->errno . "] " . $stmt->error . ". This can happen if mysqlnd is not properly configured or if the statement is invalid for get_result().";
        error_log(implode("\n", $debug_fetch_log));
        $stmt->close();
        // The connection associated with $stmt is not explicitly closed here,
        // but it was opened in executeQuery and not closed there if $stmt was returned.
        // It will be closed when the script ends if not closed sooner.
        return false;
    }
    $debug_fetch_log[] = "DEBUG fetchOne: \$stmt->get_result() SUCCESSFUL.";
    $debug_fetch_log[] = "DEBUG fetchOne: Number of rows in result set (from \$result->num_rows): " . $result->num_rows;

    $row = $result->fetch_assoc();

    if ($row === null) {
        $debug_fetch_log[] = "DEBUG fetchOne: \$result->fetch_assoc() returned NULL (no matching rows found).";
    } else {
        $debug_fetch_log[] = "DEBUG fetchOne: \$result->fetch_assoc() returned a row: " . htmlspecialchars(print_r($row, true));
    }
    $debug_fetch_log[] = "DEBUG fetchOne: Closing statement and freeing result.";
    error_log(implode("\n", $debug_fetch_log));

    $result->free();
    $stmt->close();
    // Connection associated with the original $stmt is not closed here.
    return $row;
}

/**
 * Fetch all rows from a query result
 */
function fetchAll($sql, $types = "", $params = []) {
    $debug_fetchall_log = [];
    $debug_fetchall_log[] = "DEBUG fetchAll: Called at " . date('Y-m-d H:i:s');
    $debug_fetchall_log[] = "DEBUG fetchAll: SQL: " . $sql;
    $debug_fetchall_log[] = "DEBUG fetchAll: Types: '" . $types . "'";
    $debug_fetchall_log[] = "DEBUG fetchAll: Params: " . htmlspecialchars(print_r($params, true));

    $stmt = executeQuery($sql, $types, $params); // executeQuery has its own detailed logging

    if ($stmt === false) {
        $debug_fetchall_log[] = "DEBUG fetchAll: executeQuery() returned false. Aborting fetchAll.";
        error_log(implode("\n", $debug_fetchall_log));
        return false;
    }
    $debug_fetchall_log[] = "DEBUG fetchAll: executeQuery() returned a statement object successfully.";

    $debug_fetchall_log[] = "DEBUG fetchAll: Attempting \$stmt->get_result().";
    $result = $stmt->get_result();

    if ($result === false) {
        $debug_fetchall_log[] = "DEBUG fetchAll: \$stmt->get_result() FAILED. MySQLi Stmt Error: [" . $stmt->errno . "] " . $stmt->error;
        error_log(implode("\n", $debug_fetchall_log));
        $stmt->close();
        return false;
    }
    $debug_fetchall_log[] = "DEBUG fetchAll: \$stmt->get_result() SUCCESSFUL.";
    $debug_fetchall_log[] = "DEBUG fetchAll: Number of rows in result set (from \$result->num_rows): " . $result->num_rows;

    $rows = [];
    while ($row_data = $result->fetch_assoc()) {
        $rows[] = $row_data;
    }
    $debug_fetchall_log[] = "DEBUG fetchAll: Fetched " . count($rows) . " rows.";

    $debug_fetchall_log[] = "DEBUG fetchAll: Closing statement and freeing result.";
    error_log(implode("\n", $debug_fetchall_log));

    $result->free();
    $stmt->close();
    return $rows;
}


/**
 * Insert data and return the last inserted ID
 */
function insertAndGetId($sql, $types = "", $params = []) {
    // For consistency, you might want to add similar detailed logging here if debugging inserts
    $conn = getDbConnection(); // Ensure connection is fetched for this specific operation
    if (!$conn) return false; // Added connection check

    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        error_log("Prepare failed in insertAndGetId: [" . $conn->errno . "] " . $conn->error . " | SQL: " . $sql);
        $conn->close();
        return false;
    }

    if (!empty($params) && !empty($types)) {
        $bindParams = [];
        $bindParams[] = $types;
        for ($i = 0; $i < count($params); $i++) {
            $bindParams[] = &$params[$i];
        }
        if (!call_user_func_array(array($stmt, 'bind_param'), $bindParams)) {
            error_log("Bind failed in insertAndGetId: [" . $stmt->errno . "] " . $stmt->error . " | SQL: " . $sql);
            $stmt->close();
            $conn->close();
            return false;
        }
    }

    if (!$stmt->execute()) {
        error_log("Execute failed in insertAndGetId: [" . $stmt->errno . "] " . $stmt->error . " | SQL: " . $sql);
        $stmt->close();
        $conn->close();
        return false;
    }

    $lastId = $conn->insert_id;
    $stmt->close();
    $conn->close();

    return $lastId;
}

/**
 * Update data and return the number of affected rows
 */
function executeUpdate($sql, $types = "", $params = []) {
    // For consistency, you might want to add similar detailed logging here if debugging updates/deletes
    $conn = getDbConnection(); // Ensure connection is fetched for this specific operation
    if (!$conn) return false; // Added connection check

    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        error_log("Prepare failed in executeUpdate: [" . $conn->errno . "] " . $conn->error . " | SQL: " . $sql);
        $conn->close();
        return false;
    }

    if (!empty($params) && !empty($types)) {
        $bindParams = [];
        $bindParams[] = $types;
        for ($i = 0; $i < count($params); $i++) {
            $bindParams[] = &$params[$i];
        }
        if (!call_user_func_array(array($stmt, 'bind_param'), $bindParams)) {
            error_log("Bind failed in executeUpdate: [" . $stmt->errno . "] " . $stmt->error . " | SQL: " . $sql);
            $stmt->close();
            $conn->close();
            return false;
        }
    }

    if (!$stmt->execute()) {
        error_log("Execute failed in executeUpdate: [" . $stmt->errno . "] " . $stmt->error . " | SQL: " . $sql);
        $stmt->close();
        $conn->close();
        return false;
    }

    $affectedRows = $stmt->affected_rows;
    $stmt->close();
    $conn->close();

    return $affectedRows;
}

?>