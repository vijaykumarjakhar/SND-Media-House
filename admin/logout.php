<?php
/**
 * SND Media — Staff Logout Handler
 * Phase 1 Production Architecture
 */

require_once dirname(__DIR__) . '/includes/functions.php';

// Unset all session variables
$_SESSION = [];

// Delete session cookie
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

// Destroy session
session_destroy();

// Start fresh session for flash notification
session_start();
flash_set('info', 'You have been successfully logged out.');

header('Location: login.php');
exit;

