<?php
/**
 * AnshVoid Admin API Endpoints
 */
require_once __DIR__ . '/auth.php';

header('Content-Type: application/json; charset=utf-8');

if (!anshvoid_is_logged_in()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access. Please log in.']);
    exit;
}

$action = $_GET['action'] ?? $_POST['action'] ?? '';

// Check CSRF token for state-changing requests
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if (!anshvoid_verify_csrf($token)) {
        echo json_encode(['success' => false, 'message' => 'Invalid or expired CSRF security token.']);
        exit;
    }
}

switch ($action) {
    case 'get_content':
        $data = anshvoid_get_site_data();
        echo json_encode(['success' => true, 'data' => $data]);
        break;

    case 'save_section':
        $section = $_POST['section'] ?? '';
        $payload = $_POST['data'] ?? '';
        if (empty($section) || empty($payload)) {
            echo json_encode(['success' => false, 'message' => 'Section or data missing.']);
            exit;
        }

        $decodedSectionData = json_decode($payload, true);
        if ($decodedSectionData === null && json_last_error() !== JSON_ERROR_NONE) {
            echo json_encode(['success' => false, 'message' => 'Invalid JSON payload: ' . json_last_error_msg()]);
            exit;
        }

        $siteData = anshvoid_get_site_data();
        $siteData[$section] = $decodedSectionData;

        if (anshvoid_save_site_data($siteData)) {
            echo json_encode(['success' => true, 'message' => "Section '{$section}' saved successfully!"]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to save site data to disk.']);
        }
        break;

    case 'save_raw_json':
        $raw = $_POST['raw_json'] ?? '';
        $decoded = json_decode($raw, true);
        if ($decoded === null && json_last_error() !== JSON_ERROR_NONE) {
            echo json_encode(['success' => false, 'message' => 'JSON Syntax Error: ' . json_last_error_msg()]);
            exit;
        }

        if (anshvoid_save_site_data($decoded)) {
            echo json_encode(['success' => true, 'message' => 'Complete site content updated successfully!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to write to content file.']);
        }
        break;

    case 'save_all':
        $payload = $_POST['data'] ?? '';
        $decoded = json_decode($payload, true);
        if ($decoded === null && json_last_error() !== JSON_ERROR_NONE) {
            echo json_encode(['success' => false, 'message' => 'Invalid data payload.']);
            exit;
        }

        if (anshvoid_save_site_data($decoded)) {
            echo json_encode(['success' => true, 'message' => 'All changes saved successfully!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to write site content file.']);
        }
        break;

    case 'list_assets':
        $assets = anshvoid_list_assets();
        echo json_encode(['success' => true, 'assets' => $assets]);
        break;

    case 'upload_asset':
        if (empty($_FILES['asset_file'])) {
            echo json_encode(['success' => false, 'message' => 'No file uploaded.']);
            exit;
        }

        $file = $_FILES['asset_file'];
        if ($file['error'] !== UPLOAD_ERR_OK) {
            echo json_encode(['success' => false, 'message' => 'Upload error code: ' . $file['error']]);
            exit;
        }

        $origName = basename($file['name']);
        $ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
        $allowed = ['png', 'jpg', 'jpeg', 'webp', 'svg', 'gif'];

        if (!in_array($ext, $allowed)) {
            echo json_encode(['success' => false, 'message' => 'File type not allowed. Allowed: ' . implode(', ', $allowed)]);
            exit;
        }

        // Sanitize filename
        $customName = trim($_POST['custom_name'] ?? '');
        if (!empty($customName)) {
            $baseName = preg_replace('/[^a-zA-Z0-9_\-\s]/', '', pathinfo($customName, PATHINFO_FILENAME));
            $targetName = $baseName . '.' . $ext;
        } else {
            $baseName = preg_replace('/[^a-zA-Z0-9_\-\s]/', '', pathinfo($origName, PATHINFO_FILENAME));
            $targetName = $baseName . '.' . $ext;
        }

        $targetPath = PUBLIC_ASSETS_DIR . '/' . $targetName;
        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            echo json_encode([
                'success' => true,
                'message' => 'Asset uploaded successfully!',
                'asset' => [
                    'name' => $targetName,
                    'path' => 'public/assets/' . $targetName,
                    'size' => round(filesize($targetPath) / 1024, 1) . ' KB'
                ]
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to move uploaded file to public/assets.']);
        }
        break;

    case 'delete_asset':
        $assetName = basename($_POST['asset_name'] ?? '');
        if (empty($assetName)) {
            echo json_encode(['success' => false, 'message' => 'Asset name not provided.']);
            exit;
        }

        $filePath = PUBLIC_ASSETS_DIR . '/' . $assetName;
        if (!file_exists($filePath)) {
            echo json_encode(['success' => false, 'message' => 'Asset file not found.']);
            exit;
        }

        if (unlink($filePath)) {
            echo json_encode(['success' => true, 'message' => "Asset '{$assetName}' removed successfully!"]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete asset file.']);
        }
        break;

    case 'update_credentials':
        $currentPass = $_POST['current_password'] ?? '';
        $newUsername = trim($_POST['new_username'] ?? '');
        $newPass = $_POST['new_password'] ?? '';
        $confirmPass = $_POST['confirm_password'] ?? '';

        $creds = anshvoid_get_credentials();

        if (!password_verify($currentPass, $creds['password_hash'])) {
            echo json_encode(['success' => false, 'message' => 'Current password is incorrect.']);
            exit;
        }

        if (empty($newUsername)) {
            echo json_encode(['success' => false, 'message' => 'Username cannot be empty.']);
            exit;
        }

        $creds['username'] = $newUsername;
        $_SESSION['anshvoid_user'] = $newUsername;

        if (!empty($newPass)) {
            if ($newPass !== $confirmPass) {
                echo json_encode(['success' => false, 'message' => 'New passwords do not match.']);
                exit;
            }
            if (strlen($newPass) < 6) {
                echo json_encode(['success' => false, 'message' => 'New password must be at least 6 characters.']);
                exit;
            }
            $creds['password_hash'] = password_hash($newPass, PASSWORD_BCRYPT);
        }

        if (anshvoid_save_credentials($creds)) {
            echo json_encode(['success' => true, 'message' => 'Credentials updated successfully!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to save updated credentials.']);
        }
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Unknown action: ' . htmlspecialchars($action)]);
        break;
}
