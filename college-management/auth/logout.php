<?php
/**
 * Logout page
 */
require_once '../config/functions.php';

// Initialize session
initSession();

// Clear all session data
$_SESSION = [];

// If a session cookie is used, destroy it
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// Destroy the session
session_destroy();

// Redirect to login page
setFlashMessage('success', 'You have been successfully logged out');
redirect(BASE_URL . '/auth/login.php');