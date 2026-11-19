<?php
/**
 * SND Media — Staff Authentication (Login)
 * Phase 1 Production Architecture (US-13)
 */

require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/config/settings.php';

// Redirect if already logged in
if (is_logged_in()) {
    header('Location: index.php');
    exit;
}

$error = '';
$flash = flash_get();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '';
    $identifier = trim(isset($_POST['identifier']) ? $_POST['identifier'] : '');
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    if (!csrf_token_validate($token)) {
        $error = 'Security session expired. Please refresh and try again.';
    } elseif (empty($identifier) || empty($password)) {
        $error = 'Please enter your username/email and password.';
    } else {
        $pdo = get_db_connection();
        if ($pdo) {
            try {
                $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :id1 OR email = :id2 LIMIT 1");
                $stmt->execute([':id1' => $identifier, ':id2' => $identifier]);
                $user = $stmt->fetch();

                if ($user && password_verify($password, $user['password_hash'])) {
                    if ((int)$user['is_active'] !== 1) {
                        $error = 'Your account has been deactivated. Please contact your system administrator.';
                    } else {
                        // Successful login
                        session_regenerate_id(true);
                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['user_role'] = $user['role'];
                        $_SESSION['user_full_name'] = $user['full_name'];
                        $_SESSION['user_email'] = $user['email'];

                        // Update last_login timestamp
                        $upd = $pdo->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
                        $upd->execute([$user['id']]);

                        flash_set('success', 'Welcome back, ' . $user['full_name'] . '!');
                        header('Location: index.php');
                        exit;
                    }
                } else {
                    // Generic security message (no field disclosure)
                    $error = 'Invalid username/email or password.';
                }
            } catch (Exception $e) {
                error_log("Login DB Query failed: " . $e->getMessage());
                $error = 'An internal system error occurred. Please try again later.';
            }
        } else {
            // Local fallback demo mode when MySQL service is offline
            if ($identifier === 'admin' && $password === 'AdminPassword123!') {
                session_regenerate_id(true);
                $_SESSION['user_id'] = 1;
                $_SESSION['user_role'] = 'super_admin';
                $_SESSION['user_full_name'] = 'SND Chief Executive';
                $_SESSION['user_email'] = 'admin@sndmedia.in';
                flash_set('warning', 'Logged in via fallback mode (database offline).');
                header('Location: index.php');
                exit;
            } else {
                $error = 'Database offline and credentials did not match emergency fallback.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Login | SND Media Admin Console</title>
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="../assets/images/snd-logo.svg">
    <link rel="alternate icon" href="../favicon.ico">
    <link rel="apple-touch-icon" href="../assets/images/snd-logo.svg">
    <!-- Pre-compiled Tailwind CSS -->
    <link rel="stylesheet" href="../assets/css/tailwind.min.css">
    <link rel="stylesheet" href="../assets/css/custom.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="bg-brand-dark text-slate-100 min-h-screen flex items-center justify-center p-4 antialiased selection:bg-amber-500 selection:text-black">

    <div class="max-w-md w-full">
        <!-- Logo & Branding Header -->
        <div class="text-center mb-8">
            <a href="../" class="inline-flex flex-col items-center group">
                <img src="../assets/images/snd-logo.svg" alt="SND Media Official Logo" class="w-16 h-16 rounded-full border-2 border-amber-400 bg-white shadow-xl shadow-amber-500/20 group-hover:scale-105 transition mb-3">
                <div class="flex items-center gap-1.5">
                    <span class="font-black text-2xl tracking-tight text-white"><span class="text-amber-400">SND</span> MEDIA</span>
                    <span class="bg-amber-400/20 text-amber-300 text-[10px] font-bold px-1.5 py-0.5 rounded border border-amber-400/30">ADMIN</span>
                </div>
                <p class="text-xs text-slate-400 mt-1">Editorial Desk &bull; Lead CRM &bull; Broadcast Hub</p>
            </a>
        </div>

        <!-- Login Card -->
        <div class="bg-brand-card border border-slate-800 rounded-2xl p-8 shadow-2xl relative overflow-hidden">
            <div class="absolute top-0 inset-x-0 h-1 bg-gradient-to-r from-amber-500 via-rose-500 to-amber-500"></div>

            <div class="mb-6">
                <h2 class="text-xl font-bold text-white">Staff Sign In</h2>
                <p class="text-xs text-slate-400 mt-0.5">Enter your credentials to access the SND Media control panel.</p>
            </div>

            <!-- Flash Error / Success Notifications -->
            <?php if ($flash): ?>
            <div class="mb-5 p-3 rounded-lg text-xs <?= $flash['type'] === 'success' ? 'bg-emerald-950/80 border border-emerald-700/60 text-emerald-200' : 'bg-rose-950/80 border border-rose-700/60 text-rose-200' ?> flex items-center gap-2">
                <i class="fa-solid <?= $flash['type'] === 'success' ? 'fa-circle-check text-emerald-400' : 'fa-triangle-exclamation text-rose-400' ?>"></i>
                <span><?= sanitize_output($flash['message']) ?></span>
            </div>
            <?php endif; ?>

            <?php if (!empty($error)): ?>
            <div class="mb-5 p-3 rounded-lg text-xs bg-rose-950/80 border border-rose-700/60 text-rose-200 flex items-center gap-2">
                <i class="fa-solid fa-circle-exclamation text-rose-400 text-sm"></i>
                <span><?= sanitize_output($error) ?></span>
            </div>
            <?php endif; ?>

            <form method="POST" action="login.php" class="space-y-4">
                <?= csrf_field() ?>

                <div>
                    <label for="identifier" class="block text-xs font-semibold text-slate-300 mb-1.5">
                        Username or Email Address
                    </label>
                    <div class="relative">
                        <i class="fa-solid fa-user absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-500 text-xs"></i>
                        <input type="text" id="identifier" name="identifier" required autofocus 
                               value="<?= isset($_POST['identifier']) ? sanitize_output($_POST['identifier']) : '' ?>"
                               placeholder="e.g. admin or editor@sndmedia.in" 
                               class="w-full bg-slate-900 border border-slate-700 text-slate-100 text-xs rounded-xl pl-9 pr-3.5 py-2.5 focus:outline-none focus:border-amber-400 focus:ring-1 focus:ring-amber-400 transition placeholder:text-slate-600">
                    </div>
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="password" class="text-xs font-semibold text-slate-300">
                            Password
                        </label>
                        <a href="reset_password.php" class="text-[11px] text-amber-400 hover:text-amber-300 transition">
                            Forgot Password?
                        </a>
                    </div>
                    <div class="relative">
                        <i class="fa-solid fa-lock absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-500 text-xs"></i>
                        <input type="password" id="password" name="password" required 
                               placeholder="••••••••••••" 
                               class="w-full bg-slate-900 border border-slate-700 text-slate-100 text-xs rounded-xl pl-9 pr-3.5 py-2.5 focus:outline-none focus:border-amber-400 focus:ring-1 focus:ring-amber-400 transition placeholder:text-slate-600">
                    </div>
                </div>

                <button type="submit" class="w-full bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-slate-950 font-bold text-xs py-3 rounded-xl shadow-lg shadow-amber-500/20 transition transform active:scale-95 flex items-center justify-center gap-2 mt-2">
                    <i class="fa-solid fa-right-to-bracket"></i> Sign In to Desk
                </button>
            </form>

            <div class="mt-6 pt-4 border-t border-slate-800/80 text-center text-[11px] text-slate-500">
                Default Credentials: <code class="text-slate-400 bg-slate-900 px-1.5 py-0.5 rounded">admin</code> / <code class="text-slate-400 bg-slate-900 px-1.5 py-0.5 rounded">AdminPassword123!</code>
            </div>
        </div>

        <!-- Footer link back to portal -->
        <div class="mt-6 text-center text-xs text-slate-500">
            <a href="../" class="hover:text-slate-300 transition inline-flex items-center gap-1.5">
                <i class="fa-solid fa-arrow-left text-[10px]"></i> Return to Public Broadcast Portal
            </a>
        </div>
    </div>

</body>
</html>

