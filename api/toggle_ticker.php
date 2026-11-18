<?php
/**
 * SND Media — Toggle Breaking Ticker Status API
 */

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';

// Must be staff with super_admin or editor role
if (!has_role(['super_admin', 'editor'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized.']);
    exit;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$token = $_GET['token'] ?? '';

if (!csrf_token_validate($token) || $id <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid parameters or expired token.']);
    exit;
}

$pdo = get_db_connection();
if (!$pdo) {
    echo json_encode(['status' => 'error', 'message' => 'Database offline.']);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT is_active FROM breaking_ticker WHERE id = ?");
    $stmt->execute([$id]);
    $curr = $stmt->fetchColumn();

    if ($curr === false) {
        echo json_encode(['status' => 'error', 'message' => 'Ticker item not found.']);
        exit;
    }

    $new_status = ((int)$curr === 1) ? 0 : 1;
    $upd = $pdo->prepare("UPDATE breaking_ticker SET is_active = ? WHERE id = ?");
    $upd->execute([$new_status, $id]);

    echo json_encode([
        'status' => 'success',
        'is_active' => $new_status,
        'message' => $new_status ? 'Ticker stream activated' : 'Ticker stream paused'
    ]);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
exit;
