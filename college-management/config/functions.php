<?php
/**
 * Constants and utility functions for the application
 */

// Application Settings
define('APP_NAME', 'College Management System');
define('APP_VERSION', '1.0.0');
define('BASE_URL', 'http://localhost:91/college-management');

// Session settings
define('SESSION_NAME', 'college_management_session');
define('SESSION_LIFETIME', 3600); // 1 hour in seconds

// Default time zone
date_default_timezone_set('Asia/Kolkata');

// User status constants
define('USER_STATUS_ACTIVE', 'active');
define('USER_STATUS_INACTIVE', 'inactive');
define('USER_STATUS_LOCKED', 'locked');

// Permission levels
define('PERMISSION_NONE', 0);
define('PERMISSION_VIEW', 1);
define('PERMISSION_EDIT', 2);
define('PERMISSION_CREATE', 3);
define('PERMISSION_DELETE', 4);

// Max login attempts before account is locked
define('MAX_LOGIN_ATTEMPTS', 5);

// Password requirements
define('PASSWORD_MIN_LENGTH', 8);
define('PASSWORD_REQUIRE_UPPERCASE', true);
define('PASSWORD_REQUIRE_LOWERCASE', true);
define('PASSWORD_REQUIRE_NUMBER', true);
define('PASSWORD_REQUIRE_SPECIAL', true);

/**
 * Generate a UUID v4
 * 
 * @return string Returns a UUID v4
 */
function generateUuid() {
    // Generate 16 bytes (128 bits) of random data
    $data = random_bytes(16);
    
    // Set version to 0100
    $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
    // Set bits 6-7 to 10
    $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
    
    // Output the 36 character UUID
    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}

/**
 * Clean and sanitize user input
 * 
 * @param string $data The input to be sanitized
 * @return string The sanitized input
 */
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

/**
 * Validate an email address
 * 
 * @param string $email The email to validate
 * @return boolean Whether the email is valid
 */
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Check if a password meets the requirements
 * 
 * @param string $password The password to check
 * @return array An array of validation results
 */
function validatePassword($password) {
    $result = [
        'valid' => true,
        'errors' => []
    ];
    
    // Check minimum length
    if (strlen($password) < PASSWORD_MIN_LENGTH) {
        $result['valid'] = false;
        $result['errors'][] = "Password must be at least " . PASSWORD_MIN_LENGTH . " characters";
    }
    
    // Check for uppercase letters
    if (PASSWORD_REQUIRE_UPPERCASE && !preg_match('/[A-Z]/', $password)) {
        $result['valid'] = false;
        $result['errors'][] = "Password must contain at least one uppercase letter";
    }
    
    // Check for lowercase letters
    if (PASSWORD_REQUIRE_LOWERCASE && !preg_match('/[a-z]/', $password)) {
        $result['valid'] = false;
        $result['errors'][] = "Password must contain at least one lowercase letter";
    }
    
    // Check for numbers
    if (PASSWORD_REQUIRE_NUMBER && !preg_match('/[0-9]/', $password)) {
        $result['valid'] = false;
        $result['errors'][] = "Password must contain at least one number";
    }
    
    // Check for special characters
    if (PASSWORD_REQUIRE_SPECIAL && !preg_match('/[^A-Za-z0-9]/', $password)) {
        $result['valid'] = false;
        $result['errors'][] = "Password must contain at least one special character";
    }
    
    return $result;
}

/**
 * Generate a secure password hash
 * 
 * @param string $password The password to hash
 * @return string The hashed password
 */
function hashPassword($password) {
    return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
}

/**
 * Verify a password against a hash
 * 
 * @param string $password The password to verify
 * @param string $hash The hash to verify against
 * @return boolean Whether the password is valid
 */
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

/**
 * Set a flash message to be displayed on the next page load
 * 
 * @param string $type The type of message (success, error, warning, info)
 * @param string $message The message to display
 */
function setFlashMessage($type, $message) {
    if (!isset($_SESSION['flash_messages'])) {
        $_SESSION['flash_messages'] = [];
    }
    
    $_SESSION['flash_messages'][] = [
        'type' => $type,
        'message' => $message
    ];
}

/**
 * Get and clear all flash messages
 * 
 * @return array An array of flash messages
 */
function getFlashMessages() {
    $messages = $_SESSION['flash_messages'] ?? [];
    unset($_SESSION['flash_messages']);
    return $messages;
}

/**
 * Redirect to another page
 * 
 * @param string $location The URL to redirect to
 */
function redirect($location) {
    header("Location: $location");
    exit;
}

/**
 * Check if user is logged in
 * 
 * @return boolean Whether the user is logged in
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Require the user to be logged in, redirect to login if not
 */
function requireLogin() {
    if (!isLoggedIn()) {
        setFlashMessage('error', 'You must be logged in to access that page');
        redirect(BASE_URL . '/auth/login.php');
    }
}

/**
 * Check if user has a specific permission
 * 
 * @param string $permissionName The name of the permission to check
 * @return boolean Whether the user has the permission
 */
function hasPermission($permissionName) {
    if (!isLoggedIn()) {
        return false;
    }
    
    // If the user is a system admin, they have all permissions
    if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']) {
        return true;
    }
    
    // Check if the user has the specific permission
    return isset($_SESSION['permissions']) && in_array($permissionName, $_SESSION['permissions']);
}

/**
 * Require the user to have a specific permission, redirect if not
 * 
 * @param string $permissionName The name of the permission to check
 */
function requirePermission($permissionName) {
    if (!hasPermission($permissionName)) {
        setFlashMessage('error', 'You do not have permission to access that page');
        redirect(BASE_URL . '/index.php');
    }
}

/**
 * Format a date for display
 * 
 * @param string $date The date to format
 * @param string $format The format to use
 * @return string The formatted date
 */
function formatDate($date, $format = 'd M Y') {
    if (empty($date)) {
        return '';
    }
    
    $dateObj = new DateTime($date);
    return $dateObj->format($format);
}

/**
 * Get the current date and time in MySQL format
 * 
 * @return string The current date and time
 */
function getCurrentDateTime() {
    return date('Y-m-d H:i:s');
}

/**
 * Initialize the session with secure settings
 */
function initSession() {
    // Set secure session parameters
    $sessionParams = session_get_cookie_params();
    session_set_cookie_params(
        SESSION_LIFETIME,
        $sessionParams['path'],
        $sessionParams['domain'],
        isset($_SERVER['HTTPS']), // Secure if HTTPS
        true // HttpOnly
    );
    
    // Use custom session name
    session_name(SESSION_NAME);
    
    // Start the session
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // Regenerate session ID periodically to prevent fixation attacks
    if (!isset($_SESSION['last_regeneration'])) {
        regenerateSession();
    } else if (time() - $_SESSION['last_regeneration'] > SESSION_LIFETIME / 2) {
        regenerateSession();
    }
}

/**
 * Regenerate the session ID and update the regeneration timestamp
 */
function regenerateSession() {
    session_regenerate_id(true);
    $_SESSION['last_regeneration'] = time();
}