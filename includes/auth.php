<?php
/**
 * SND Media — Role-Based Access Control (RBAC) Guard
 * Phase 1 Production Architecture
 */

require_once __DIR__ . '/functions.php';
require_once dirname(__DIR__) . '/config/database.php';

/**
 * Check if a staff user is currently logged in
 * @return bool
 */
function is_logged_in() {
    return !empty($_SESSION['user_id']) && !empty($_SESSION['user_role']);
}

/**
 * Retrieve current logged-in user ID
 * @return int|null
 */
function current_user_id() {
    return is_logged_in() ? (int)$_SESSION['user_id'] : null;
}

/**
 * Retrieve current logged-in user role
 * @return string|null
 */
function current_user_role() {
    return is_logged_in() ? (string)$_SESSION['user_role'] : null;
}

/**
 * Retrieve current logged-in user's full name
 * @return string
 */
function current_user_name() {
    return !empty($_SESSION['user_full_name']) ? $_SESSION['user_full_name'] : 'Staff Member';
}

/**
 * Check if the current user has one of the allowed roles
 * @param string|array $allowed_roles
 * @return bool
 */
function has_role($allowed_roles) {
    if (!is_logged_in()) return false;
    $role = current_user_role();
    if (is_array($allowed_roles)) {
        return in_array($role, $allowed_roles, true);
    }
    return $role === $allowed_roles;
}

/**
 * Human-readable display label for RBAC roles
 * @param string $role
 * @return string
 */
function role_display_name($role) {
    $map = [
        'super_admin'   => 'Super Admin',
        'editor'        => 'Senior Editor',
        'reporter'      => 'Reporter Desk',
        'space_manager' => 'Space Manager'
    ];
    return isset($map[$role]) ? $map[$role] : ucfirst(str_replace('_', ' ', $role));
}

/**
 * Enforce authentication on protected admin pages
 * Redirects to login with a flash error if unauthenticated or deactivated
 */
function require_login() {
    if (!is_logged_in()) {
        flash_set('error', 'Please log in to access the SND Admin Console.');
        header('Location: login.php');
        exit;
    }

    // Verify user is still active in database
    $pdo = get_db_connection();
    if ($pdo) {
        try {
            $stmt = $pdo->prepare("SELECT is_active, role FROM users WHERE id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            $user = $stmt->fetch();

            if (!$user || (int)$user['is_active'] !== 1) {
                // Deactivated or deleted
                session_unset();
                session_destroy();
                session_start();
                flash_set('error', 'Your staff account has been deactivated. Please contact the administrator.');
                header('Location: login.php');
                exit;
            }

            // Keep session role synchronized
            $_SESSION['user_role'] = $user['role'];
        } catch (Exception $e) {
            error_log("Active verification check failed: " . $e->getMessage());
        }
    }
}

/**
 * Enforce RBAC role requirement on protected admin actions
 * @param string|array $allowed_roles
 */
function require_role($allowed_roles) {
    require_login();

    if (!has_role($allowed_roles)) {
        http_response_code(403);
        $role_names = is_array($allowed_roles) ? implode(', ', array_map('role_display_name', $allowed_roles)) : role_display_name($allowed_roles);
        
        // Show clean branded 403 Access Denied template
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>403 Access Denied | SND Admin</title>
            <script src="https://cdn.tailwindcss.com"></script>
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
        </head>
        <body class="bg-slate-950 text-slate-100 flex items-center justify-center min-h-screen p-4">
            <div class="max-w-md w-full bg-slate-900 border border-slate-800 rounded-2xl p-8 text-center shadow-2xl">
                <div class="w-16 h-16 rounded-full bg-rose-500/10 text-rose-500 flex items-center justify-center mx-auto text-3xl mb-4 border border-rose-500/20">
                    <i class="fa-solid fa-lock"></i>
                </div>
                <h1 class="text-2xl font-bold text-white mb-2">Access Restricted</h1>
                <p class="text-sm text-slate-400 mb-6">
                    Your current role (<strong><?= sanitize_output(role_display_name(current_user_role())) ?></strong>) does not have permission to view this section. Requires: <strong><?= sanitize_output($role_names) ?></strong>.
                </p>
                <div class="flex gap-3 justify-center">
                    <a href="index.php" class="bg-slate-800 hover:bg-slate-700 text-white text-xs font-semibold px-4 py-2.5 rounded-lg transition">
                        <i class="fa-solid fa-arrow-left mr-1"></i> Dashboard
                    </a>
                    <a href="logout.php" class="bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold px-4 py-2.5 rounded-lg transition">
                        Log Out
                    </a>
                </div>
            </div>
        </body>
        </html>
        <?php
        exit;
    }
}

