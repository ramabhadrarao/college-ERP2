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
define('DB_PASS', 'Nihita@1981');
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
        error_log($e->getMessage());
        die("Database connection failed. Please contact the administrator.");
    }
    
    return $conn;
}

/**
 * Execute a prepared statement with parameters
 * 
 * @param string $sql SQL query with placeholders
 * @param string $types Parameter types (i: integer, d: double, s: string, b: blob)
 * @param array $params Array of parameters to bind
 * @return mysqli_stmt|false Returns the statement object or false on failure
 */
function executeQuery($sql, $types = "", $params = []) {
    $conn = getDbConnection();
    $stmt = $conn->prepare($sql);
    
    if ($stmt === false) {
        error_log("Prepare failed: " . $conn->error);
        $conn->close();
        return false;
    }
    
    // Bind parameters if there are any
    if (!empty($params) && !empty($types)) {
        // Create array of references for bind_param
        $bindParams = array();
        $bindParams[] = $types;
        
        for ($i = 0; $i < count($params); $i++) {
            $bindParams[] = &$params[$i];
        }
        
        call_user_func_array(array($stmt, 'bind_param'), $bindParams);
    }
    
    // Execute the statement
    if (!$stmt->execute()) {
        error_log("Execute failed: " . $stmt->error);
        $stmt->close();
        $conn->close();
        return false;
    }
    
    return $stmt;
}

/**
 * Fetch all rows from a query result
 * 
 * @param string $sql SQL query with placeholders
 * @param string $types Parameter types (i: integer, d: double, s: string, b: blob)
 * @param array $params Array of parameters to bind
 * @return array|false Returns array of result rows or false on failure
 */
function fetchAll($sql, $types = "", $params = []) {
    $stmt = executeQuery($sql, $types, $params);
    
    if ($stmt === false) {
        return false;
    }
    
    $result = $stmt->get_result();
    $rows = [];
    
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }
    
    $stmt->close();
    return $rows;
}

/**
 * Fetch a single row from a query result
 * 
 * @param string $sql SQL query with placeholders
 * @param string $types Parameter types (i: integer, d: double, s: string, b: blob)
 * @param array $params Array of parameters to bind
 * @return array|false Returns a single result row or false on failure
 */
/**
 * Fetch a single row from a query result
 * 
 * @param string $sql SQL query with placeholders
 * @param string $types Parameter types (i: integer, d: double, s: string, b: blob)
 * @param array $params Array of parameters to bind
 * @return array|false Returns a single result row or false on failure
 */
function fetchOne($sql, $types = "", $params = []) {
    $stmt = executeQuery($sql, $types, $params);
    
    if ($stmt === false) {
        return false;
    }
    
    $result = $stmt->get_result();
    
    // Add this check to prevent the fatal error
    if ($result === false) {
        $stmt->close();
        return false;
    }
    
    $row = $result->fetch_assoc();
    
    $stmt->close();
    return $row;
}

/**
 * Insert data and return the last inserted ID
 * 
 * @param string $sql SQL query with placeholders
 * @param string $types Parameter types (i: integer, d: double, s: string, b: blob)
 * @param array $params Array of parameters to bind
 * @return int|false Returns the last inserted ID or false on failure
 */
function insertAndGetId($sql, $types = "", $params = []) {
    $conn = getDbConnection();
    $stmt = $conn->prepare($sql);
    
    if ($stmt === false) {
        error_log("Prepare failed: " . $conn->error);
        $conn->close();
        return false;
    }
    
    // Bind parameters if there are any
    if (!empty($params) && !empty($types)) {
        // Create array of references for bind_param
        $bindParams = array();
        $bindParams[] = $types;
        
        for ($i = 0; $i < count($params); $i++) {
            $bindParams[] = &$params[$i];
        }
        
        call_user_func_array(array($stmt, 'bind_param'), $bindParams);
    }
    
    // Execute the statement
    if (!$stmt->execute()) {
        error_log("Execute failed: " . $stmt->error);
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
 * 
 * @param string $sql SQL query with placeholders
 * @param string $types Parameter types (i: integer, d: double, s: string, b: blob)
 * @param array $params Array of parameters to bind
 * @return int|false Returns the number of affected rows or false on failure
 */
function executeUpdate($sql, $types = "", $params = []) {
    $conn = getDbConnection();
    $stmt = $conn->prepare($sql);
    
    if ($stmt === false) {
        error_log("Prepare failed: " . $conn->error);
        $conn->close();
        return false;
    }
    
    // Bind parameters if there are any
    if (!empty($params) && !empty($types)) {
        // Create array of references for bind_param
        $bindParams = array();
        $bindParams[] = $types;
        
        for ($i = 0; $i < count($params); $i++) {
            $bindParams[] = &$params[$i];
        }
        
        call_user_func_array(array($stmt, 'bind_param'), $bindParams);
    }
    
    // Execute the statement
    if (!$stmt->execute()) {
        error_log("Execute failed: " . $stmt->error);
        $stmt->close();
        $conn->close();
        return false;
    }
    
    $affectedRows = $stmt->affected_rows;
    $stmt->close();
    $conn->close();
    
    return $affectedRows;
}