<?php
/**
 * AnshVoid Admin Panel Configuration & Utilities
 */

define('ANSHVOID_ROOT', __DIR__);
define('SITE_ROOT', dirname(__DIR__));
define('DATA_DIR', SITE_ROOT . '/data');
define('PUBLIC_ASSETS_DIR', SITE_ROOT . '/public/assets');
define('CONTENT_FILE', DATA_DIR . '/site_content.json');
define('CREDENTIALS_FILE', DATA_DIR . '/admin_credentials.json');

// Ensure data and assets directories exist
if (!is_dir(DATA_DIR)) {
    @mkdir(DATA_DIR, 0755, true);
}
if (!is_dir(PUBLIC_ASSETS_DIR)) {
    @mkdir(PUBLIC_ASSETS_DIR, 0755, true);
}

// Ensure dedicated sessions directory inside data/ for reliable cross-platform sessions
$sessionDir = DATA_DIR . '/sessions';
if (!is_dir($sessionDir)) {
    @mkdir($sessionDir, 0700, true);
}
if (is_dir($sessionDir) && is_writable($sessionDir)) {
    session_save_path($sessionDir);
}

if (session_status() === PHP_SESSION_NONE) {
    // Set secure session cookie parameters
    @session_set_cookie_params([
        'lifetime' => 86400 * 7, // 7 days
        'path' => '/',
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
    @session_start();
}

/**
 * Generate CSRF Token
 */
function anshvoid_csrf_token() {
    if (empty($_SESSION['anshvoid_csrf'])) {
        $_SESSION['anshvoid_csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['anshvoid_csrf'];
}

/**
 * Verify CSRF Token
 */
function anshvoid_verify_csrf($token) {
    if (empty($_SESSION['anshvoid_csrf']) || empty($token)) {
        return false;
    }
    return hash_equals($_SESSION['anshvoid_csrf'], $token);
}

/**
 * Get Admin Credentials
 */
function anshvoid_get_credentials() {
    if (file_exists(CREDENTIALS_FILE)) {
        $raw = file_get_contents(CREDENTIALS_FILE);
        $data = json_decode($raw, true);
        if (is_array($data) && !empty($data['password_hash'])) {
            return $data;
        }
    }

    // Default setup: username: anshvoid, password: anshvoid123
    $defaultCreds = [
        'username' => 'anshvoid',
        'password_hash' => password_hash('anshvoid123', PASSWORD_BCRYPT),
        'created_at' => date('Y-m-d H:i:s'),
        'last_login' => null
    ];
    file_put_contents(CREDENTIALS_FILE, json_encode($defaultCreds, JSON_PRETTY_PRINT));
    return $defaultCreds;
}

/**
 * Save Admin Credentials
 */
function anshvoid_save_credentials($data) {
    $tmp = CREDENTIALS_FILE . '.tmp';
    if (file_put_contents($tmp, json_encode($data, JSON_PRETTY_PRINT)) !== false) {
        return rename($tmp, CREDENTIALS_FILE);
    }
    return false;
}

/**
 * Get Site Data
 */
function anshvoid_get_site_data() {
    if (file_exists(CONTENT_FILE)) {
        $raw = file_get_contents(CONTENT_FILE);
        $data = json_decode($raw, true);
        if (is_array($data)) {
            return $data;
        }
    }
    return [];
}

/**
 * Save Site Data (Atomic Write)
 */
function anshvoid_save_site_data($data) {
    $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    if ($json === false) {
        return false;
    }
    $tmp = CONTENT_FILE . '.tmp';
    if (file_put_contents($tmp, $json) !== false) {
        return rename($tmp, CONTENT_FILE);
    }
    return false;
}

/**
 * Get all available image assets in /public/assets
 */
function anshvoid_list_assets() {
    $assets = [];
    if (!is_dir(PUBLIC_ASSETS_DIR)) {
        return $assets;
    }

    $files = scandir(PUBLIC_ASSETS_DIR);
    $allowedExts = ['png', 'jpg', 'jpeg', 'webp', 'svg', 'gif'];

    foreach ($files as $file) {
        if ($file === '.' || $file === '..') continue;
        $fullPath = PUBLIC_ASSETS_DIR . '/' . $file;
        if (!is_file($fullPath)) continue;

        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        if (in_array($ext, $allowedExts)) {
            $sizeBytes = filesize($fullPath);
            $sizeFormatted = $sizeBytes > 1048576 
                ? round($sizeBytes / 1048576, 2) . ' MB' 
                : round($sizeBytes / 1024, 1) . ' KB';

            $assets[] = [
                'name' => $file,
                'path' => 'public/assets/' . $file,
                'size' => $sizeFormatted,
                'size_bytes' => $sizeBytes,
                'modified' => date('Y-m-d H:i', filemtime($fullPath)),
                'ext' => $ext
            ];
        }
    }

    // Sort alphabetically by name
    usort($assets, fn($a, $b) => strcasecmp($a['name'], $b['name']));
    return $assets;
}
