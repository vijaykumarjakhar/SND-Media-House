<?php
/**
 * SND Media — Database Connection (PDO)
 * Phase 1 Production Architecture
 */

// Load environment overrides if set, else fallback to standard XAMPP local credentials (127.0.0.1 avoids Windows IPv6 delay)
$db_host = getenv('DB_HOST') ?: '127.0.0.1';
$db_port = getenv('DB_PORT') ?: '3306';
$db_name = getenv('DB_NAME') ?: 'sndmedia_db';
$db_user = getenv('DB_USER') ?: 'root';
$db_pass = getenv('DB_PASS') !== false ? getenv('DB_PASS') : '';

$dsn = "mysql:host={$db_host};port={$db_port};dbname={$db_name};charset=utf8mb4";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
];

try {
    $pdo = new PDO($dsn, $db_user, $db_pass, $options);
} catch (PDOException $e) {
    // When running locally without active MySQL daemon or in development mode, provide safe fallback
    if (defined('DB_LOG_ERRORS') && DB_LOG_ERRORS) {
        error_log("Database Connection Error: " . $e->getMessage());
    }
    $pdo = null;
}

/**
 * Helper to get the PDO instance safely
 * @return PDO|null
 */
function get_db_connection() {
    global $pdo;
    return $pdo;
}

