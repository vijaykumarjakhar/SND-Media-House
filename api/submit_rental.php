<?php
/**
 * SND Media — Commercial Studio Rental Lead Submission API
 */

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/settings.php';
require_once __DIR__ . '/../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
    exit;
}

// Honeypot check for bots
if (!empty($_POST['website_url_hp'])) {
    // Silently return success to bot without saving
    echo json_encode(['status' => 'success', 'message' => 'Enquiry registered.']);
    exit;
}

// CSRF validation
$token = $_POST['csrf_token'] ?? '';
if (!csrf_token_validate($token)) {
    echo json_encode(['status' => 'error', 'message' => 'Security token expired or invalid. Please refresh the page and try again.']);
    exit;
}

$client_name     = trim($_POST['client_name'] ?? '');
$company_name    = trim($_POST['company_name'] ?? '');
$email           = trim($_POST['email'] ?? '');
$phone           = trim($_POST['phone'] ?? '');
$facility_needed = trim($_POST['facility_needed'] ?? 'Studio A - Newsroom Stage');
$preferred_date  = !empty($_POST['preferred_date']) ? trim($_POST['preferred_date']) : null;
$slot_duration   = trim($_POST['slot_duration'] ?? 'Half Day');
$notes           = trim($_POST['notes'] ?? '');
$ip_address      = $_SERVER['REMOTE_ADDR'] ?? '';

// Basic validations
if (empty($client_name) || mb_strlen($client_name) < 2) {
    echo json_encode(['status' => 'error', 'message' => 'Please provide your full name.']);
    exit;
}

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['status' => 'error', 'message' => 'Please provide a valid business email address.']);
    exit;
}

$clean_phone = preg_replace('/[^0-9+]/', '', $phone);
if (strlen($clean_phone) < 8) {
    echo json_encode(['status' => 'error', 'message' => 'Please provide a valid contact phone or WhatsApp number.']);
    exit;
}

$settings = get_all_settings();
$studio_whatsapp = preg_replace('/[^0-9]/', '', $settings['phone_whatsapp'] ?? '917206622711');
if (strlen($studio_whatsapp) === 10) {
    $studio_whatsapp = '91' . $studio_whatsapp;
}

$enquiry_id = null;
$pdo = get_db_connection();

if ($pdo) {
    try {
        // IP rate limit check (max 5 per hour per IP)
        if (!empty($ip_address)) {
            $chk = $pdo->prepare("SELECT COUNT(*) FROM rental_enquiries WHERE ip_address = ? AND created_at > NOW() - INTERVAL 1 HOUR");
            $chk->execute([$ip_address]);
            if ((int)$chk->fetchColumn() >= 5) {
                echo json_encode(['status' => 'error', 'message' => 'Too many enquiries submitted from this network. Please contact our space leasing desk directly at +91-7206622711.']);
                exit;
            }
        }

        $stmt = $pdo->prepare("
            INSERT INTO rental_enquiries 
            (client_name, company_name, production_house, email, phone, facility_needed, facility_requested, preferred_date, target_date, slot_duration, notes, ip_address, status, created_at)
            VALUES 
            (:client_name, :company_name, :prod_house, :email, :phone, :facility_needed, :facility_req, :preferred_date, :target_date, :slot_duration, :notes, :ip_address, 'new', NOW())
        ");
        $stmt->execute([
            ':client_name'     => $client_name,
            ':company_name'    => $company_name ?: null,
            ':prod_house'      => $company_name ?: null,
            ':email'           => $email,
            ':phone'           => $phone,
            ':facility_needed' => $facility_needed,
            ':facility_req'    => $facility_needed,
            ':preferred_date'  => $preferred_date,
            ':target_date'     => $preferred_date,
            ':slot_duration'   => $slot_duration,
            ':notes'           => $notes ?: null,
            ':ip_address'      => $ip_address
        ]);
        $enquiry_id = $pdo->lastInsertId();
    } catch (Exception $e) {
        error_log("Rental Lead DB Insert Error: " . $e->getMessage());
    }
}

// Generate pre-filled WhatsApp message URL
$wa_text = "Hello SND Media Space Desk, I have submitted a rental enquiry on sndmedia.in.\n"
         . "Name: " . $client_name . ($company_name ? " (" . $company_name . ")" : "") . "\n"
         . "Facility: " . $facility_needed . "\n"
         . "Date: " . ($preferred_date ?: "Flexible") . "\n"
         . "Duration: " . $slot_duration . "\n"
         . "Phone: " . $phone;

$whatsapp_url = "https://wa.me/" . $studio_whatsapp . "?text=" . rawurlencode($wa_text);

echo json_encode([
    'status'       => 'success',
    'message'      => 'Thank you, ' . htmlspecialchars($client_name, ENT_QUOTES, 'UTF-8') . '! Your studio space enquiry has been received by our Commercial Leasing Desk.',
    'enquiry_id'   => $enquiry_id,
    'whatsapp_url' => $whatsapp_url
]);
exit;
