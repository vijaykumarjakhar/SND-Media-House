<?php
/**
 * SND Media — Core Utilities & Sanitizers
 * Phase 1 Production Architecture
 */

// Remove technology footprint headers
@header_remove('X-Powered-By');
@ini_set('expose_php', 'off');

// Start session if not already active
if (session_status() === PHP_SESSION_NONE) {
    // Configure secure session parameters
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
        ini_set('session.cookie_secure', 1);
    }
    @session_start();
}

/**
 * Escape plain-text output safely for HTML
 * @param mixed $str
 * @return string
 */
function sanitize_output($str) {
    if ($str === null) return '';
    return htmlspecialchars((string)$str, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * Sanitize WYSIWYG rich HTML content allowing only safe tags & attributes
 * Strips <script>, <iframe>, event handlers, and javascript: links
 * @param string $html
 * @return string
 */
function sanitize_rich_html($html) {
    if (empty($html)) return '';

    // Allowlist safe semantic tags
    $allowed_tags = '<p><br><strong><b><em><i><u><s><ul><ol><li><h2><h3><h4><h5><h6><blockquote><a><img><figure><figcaption><table><thead><tbody>tr<th><td><hr>';
    $clean = strip_tags($html, $allowed_tags);

    // Strip out on* event handlers (e.g. onclick, onload, onerror)
    $clean = preg_replace('/\s*on[a-zA-Z]+\s*=\s*(["\']).*?\1/i', '', $clean);
    $clean = preg_replace('/\s*on[a-zA-Z]+\s*=\s*[^ >]+/i', '', $clean);

    // Strip out javascript: and vbscript: URIs
    $clean = preg_replace('/href\s*=\s*(["\'])\s*(javascript|vbscript):.*?\1/i', 'href="#"', $clean);
    $clean = preg_replace('/src\s*=\s*(["\'])\s*(javascript|vbscript):.*?\1/i', 'src=""', $clean);

    return $clean;
}

/**
 * Generate URL-friendly slug from string
 * @param string $text
 * @return string
 */
function slug_generate($text) {
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    $text = preg_replace('~[^-\w]+~', '', $text);
    $text = trim($text, '-');
    $text = preg_replace('~-+~', '-', $text);
    $text = strtolower($text);
    if (empty($text)) {
        return 'n-a-' . substr(md5(uniqid()), 0, 8);
    }
    return $text;
}

/**
 * Calculate relative time ago string
 * @param string|int $datetime
 * @return string
 */
function time_ago($datetime) {
    if (empty($datetime)) return 'Recently';
    $time = is_numeric($datetime) ? (int)$datetime : strtotime($datetime);
    $diff = time() - $time;

    if ($diff < 60) return 'Just now';
    if ($diff < 3600) {
        $mins = round($diff / 60);
        return $mins . ($mins == 1 ? ' min ago' : ' mins ago');
    }
    if ($diff < 86400) {
        $hours = round($diff / 3600);
        return $hours . ($hours == 1 ? ' hour ago' : ' hours ago');
    }
    if ($diff < 604800) {
        $days = round($diff / 86400);
        return $days . ($days == 1 ? ' day ago' : ' days ago');
    }
    if ($diff < 2592000) {
        $weeks = round($diff / 604800);
        return $weeks . ($weeks == 1 ? ' week ago' : ' weeks ago');
    }
    return date('d M Y', $time);
}

/**
 * Extract 11-character YouTube Video ID from any URL or ID string
 * @param string $input
 * @return string
 */
function extract_youtube_id($input) {
    $input = trim($input);
    if (empty($input)) return '';

    // If it's already an 11-char ID
    if (preg_match('/^[a-zA-Z0-9_-]{11}$/', $input)) {
        return $input;
    }

    // Match full or shortened URLs
    $patterns = [
        '/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/|youtube\.com\/shorts\/)([a-zA-Z0-9_-]{11})/i'
    ];

    foreach ($patterns as $pattern) {
        if (preg_match($pattern, $input, $matches)) {
            return $matches[1];
        }
    }

    return '';
}

/**
 * Generate CSRF token for the session
 * @return string
 */
function csrf_token_generate() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Validate CSRF token against session
 * @param string $token
 * @return bool
 */
function csrf_token_validate($token) {
    if (empty($_SESSION['csrf_token']) || empty($token)) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Render a hidden CSRF token input field
 * @return string
 */
function csrf_field() {
    $token = csrf_token_generate();
    return '<input type="hidden" name="csrf_token" value="' . sanitize_output($token) . '">';
}

/**
 * Session Flash Messaging (PRG Pattern)
 * @param string $type ('success'|'error'|'warning'|'info')
 * @param string $message
 */
function flash_set($type, $message) {
    $_SESSION['flash_message'] = [
        'type'    => $type,
        'message' => $message
    ];
}

/**
 * Retrieve and clear flash message
 * @return array|null
 */
function flash_get() {
    if (!empty($_SESSION['flash_message'])) {
        $flash = $_SESSION['flash_message'];
        unset($_SESSION['flash_message']);
        return $flash;
    }
    return null;
}

/**
 * Safely handle file upload with extension and MIME verification
 * @param array $file_array $_FILES['name']
 * @param string $subdir Subdirectory under assets/uploads/
 * @param array $allowed_mimes Array of permitted MIME types
 * @param int $max_size_mb Maximum size in MB
 * @return array ['success' => bool, 'url' => string, 'error' => string]
 */
function handle_file_upload($file_array, $subdir, $allowed_mimes = ['image/jpeg', 'image/png', 'image/webp', 'image/svg+xml'], $max_size_mb = 5) {
    if (empty($file_array) || !isset($file_array['error']) || $file_array['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'error' => 'No file uploaded or upload error occurred.'];
    }

    if ($file_array['size'] > ($max_size_mb * 1024 * 1024)) {
        return ['success' => false, 'error' => "File size exceeds the limit of {$max_size_mb}MB."];
    }

    // Verify MIME using finfo
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file_array['tmp_name']);

    if (!in_array($mime, $allowed_mimes)) {
        return ['success' => false, 'error' => "Invalid file format ({$mime}). Allowed types: " . implode(', ', $allowed_mimes)];
    }

    // Map extensions safely
    $mime_map = [
        'image/jpeg'        => 'jpg',
        'image/png'         => 'png',
        'image/webp'        => 'webp',
        'image/svg+xml'     => 'svg',
        'application/pdf'   => 'pdf'
    ];
    $ext = isset($mime_map[$mime]) ? $mime_map[$mime] : pathinfo($file_array['name'], PATHINFO_EXTENSION);

    $base_dir = dirname(__DIR__) . '/assets/uploads/' . trim($subdir, '/\\');
    if (!is_dir($base_dir)) {
        mkdir($base_dir, 0755, true);
    }

    $filename = time() . '_' . bin2hex(random_bytes(8)) . '.' . $ext;
    $target_path = $base_dir . '/' . $filename;

    if (move_uploaded_file($file_array['tmp_name'], $target_path)) {
        $public_url = 'assets/uploads/' . trim($subdir, '/') . '/' . $filename;
        return ['success' => true, 'url' => $public_url];
    }

    return ['success' => false, 'error' => 'Failed to save uploaded file to destination directory.'];
}

