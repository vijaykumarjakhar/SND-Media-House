<?php
/**
 * SND Media — Update Studio Rental Lead Status API
 */

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';

if (!has_role(['super_admin', 'space_manager'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid method.']);
    exit;
}

$id = (int)($_POST['id'] ?? 0);
$status = trim($_POST['status'] ?? '');
$token = $_POST['csrf_token'] ?? '';

if (!csrf_token_validate($token) || $id <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid token or ID.']);
    exit;
}

$valid_statuses = ['new', 'contacted', 'quoted', 'negotiating', 'booked', 'archived', 'rejected'];
if (!in_array($status, $valid_statuses)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid status.']);
    exit;
}

$pdo = get_db_connection();
if (!$pdo) {
    echo json_encode(['status' => 'error', 'message' => 'Database offline.']);
    exit;
}

try {
    $stmt = $pdo->prepare("UPDATE rental_enquiries SET status = ? WHERE id = ?");
    $stmt->execute([$status, $id]);

    echo json_encode(['status' => 'success', 'message' => 'Status updated to ' . $status]);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
exit;
