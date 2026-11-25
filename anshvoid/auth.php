<?php
/**
 * AnshVoid Authentication Middleware
 */
require_once __DIR__ . '/config.php';

function anshvoid_is_logged_in() {
    return !empty($_SESSION['anshvoid_user']) && !empty($_SESSION['anshvoid_logged_in']);
}

function anshvoid_require_auth() {
    if (!anshvoid_is_logged_in()) {
        header('Location: login.php');
        exit;
    }
}

function anshvoid_attempt_login($username, $password) {
    $creds = anshvoid_get_credentials();
    $username = trim($username);

    if (strtolower($username) === strtolower($creds['username']) && password_verify($password, $creds['password_hash'])) {
        // Regenerate session ID for security
        if (session_status() === PHP_SESSION_ACTIVE) {
            @session_regenerate_id(true);
        }
        $_SESSION['anshvoid_user'] = $creds['username'];
        $_SESSION['anshvoid_logged_in'] = true;
        $_SESSION['anshvoid_login_time'] = time();

        // Update last login
        $creds['last_login'] = date('Y-m-d H:i:s');
        anshvoid_save_credentials($creds);

        return true;
    }

    return false;
}

function anshvoid_logout() {
    $_SESSION = [];
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
    session_destroy();
}
