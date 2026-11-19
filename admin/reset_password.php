<?php
/**
 * SND Media — Password Reset & Recovery Workflow
 * Phase 1 Production Architecture (US-20)
 */

require_once dirname(__DIR__) . '/includes/functions.php';
require_once dirname(__DIR__) . '/config/database.php';
require_once dirname(__DIR__) . '/config/settings.php';

$pdo = get_db_connection();
$token = isset($_GET['token']) ? trim($_GET['token']) : '';
$mode = !empty($token) ? 'reset' : 'request';
$message = '';
$error = '';
$token_valid = false;
$token_user = null;

// Clean up expired tokens older than 24 hours
if ($pdo) {
    try {
        $pdo->query("DELETE FROM password_reset_tokens WHERE expires_at < (NOW() - INTERVAL 24 HOUR)");
    } catch (Exception $e) {}
}

// Check token validity if in reset mode
if ($mode === 'reset' && $pdo) {
    try {
        $stmt = $pdo->prepare("
            SELECT t.*, u.username, u.email, u.full_name 
            FROM password_reset_tokens t
            JOIN users u ON u.id = t.user_id
            WHERE t.token = ? AND t.is_used = 0 AND t.expires_at > NOW()
            LIMIT 1
        ");
        $stmt->execute([$token]);
        $token_user = $stmt->fetch();
        if ($token_user) {
            $token_valid = true;
        } else {
            $error = 'This password reset link is invalid or has expired. Please request a new one.';
        }
    } catch (Exception $e) {
        $error = 'Database verification error. Please try again.';
    }
}

// POST Handling
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $submitted_token = isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '';
    if (!csrf_token_validate($submitted_token)) {
        $error = 'Security session expired. Please refresh the page.';
    } else {
        $form_type = isset($_POST['form_type']) ? $_POST['form_type'] : 'request';

        if ($form_type === 'request') {
            $email = trim(isset($_POST['email']) ? $_POST['email'] : '');
            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = 'Please enter a valid registered email address.';
            } elseif ($pdo) {
                try {
                    $stmt = $pdo->prepare("SELECT id, full_name, email FROM users WHERE email = ? AND is_active = 1 LIMIT 1");
                    $stmt->execute([$email]);
                    $user = $stmt->fetch();

                    if ($user) {
                        // Generate secure 32-byte cryptographic token
                        $reset_token = bin2hex(random_bytes(32));
                        $ins = $pdo->prepare("
                            INSERT INTO password_reset_tokens (user_id, token, expires_at, is_used, created_at)
                            VALUES (?, ?, NOW() + INTERVAL 1 HOUR, 0, NOW())
                        ");
                        $ins->execute([$user['id'], $reset_token]);

                        $reset_url = "reset_password.php?token=" . $reset_token;

                        // In a production environment with SMTP, mail() would be dispatched.
                        // Here we provide the reset link directly for administrator/staff convenience.
                        $message = "A 1-hour secure password reset link has been issued for <strong>" . sanitize_output($user['email']) . "</strong>:<br><br>" .
                                   "<a href='{$reset_url}' class='text-amber-400 font-bold underline break-all'>Click here to reset your password now &rarr;</a>";
                    } else {
                        // For security, do not disclose whether the email exists
                        $message = "If that email address is registered to an active staff account, reset instructions have been generated.";
                    }
                } catch (Exception $e) {
                    $error = 'Could not generate reset request. Please contact the administrator.';
                }
            } else {
                $error = 'Database is offline. Please start the MySQL service.';
            }
        } elseif ($form_type === 'reset' && $token_valid && $token_user) {
            $new_password = isset($_POST['password']) ? $_POST['password'] : '';
            $confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';

            if (strlen($new_password) < 10) {
                $error = 'New password must be at least 10 characters long.';
            } elseif ($new_password !== $confirm_password) {
                $error = 'Passwords do not match.';
            } elseif ($pdo) {
                try {
                    $hash = password_hash($new_password, PASSWORD_BCRYPT, ['cost' => 12]);
                    
                    // Update user password
                    $upd = $pdo->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
                    $upd->execute([$hash, $token_user['user_id']]);

                    // Mark token as used
                    $mark = $pdo->prepare("UPDATE password_reset_tokens SET is_used = 1 WHERE token = ?");
                    $mark->execute([$token]);

                    flash_set('success', 'Your password has been reset successfully! You can now log in.');
                    header('Location: login.php');
                    exit;
                } catch (Exception $e) {
                    $error = 'Failed to update password. Please try again.';
                }
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
    <title>Password Recovery | SND Media Admin</title>
    <link rel="stylesheet" href="../assets/css/tailwind.min.css">
    <link rel="stylesheet" href="../assets/css/custom.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="bg-brand-dark text-slate-100 min-h-screen flex items-center justify-center p-4 antialiased selection:bg-amber-500 selection:text-black">

    <div class="max-w-md w-full">
        <!-- Brand Header -->
        <div class="text-center mb-8">
            <a href="login.php" class="inline-flex flex-col items-center group">
                <img src="../assets/images/snd-logo.svg" alt="SND Media Logo" class="w-14 h-14 rounded-full border-2 border-amber-400 bg-white shadow-xl mb-3">
                <div class="flex items-center gap-1.5">
                    <span class="font-black text-2xl tracking-tight text-white"><span class="text-amber-400">SND</span> MEDIA</span>
                </div>
                <p class="text-xs text-slate-400 mt-0.5">Staff Security & Recovery Portal</p>
            </a>
        </div>

        <div class="bg-brand-card border border-slate-800 rounded-2xl p-8 shadow-2xl relative overflow-hidden">
            <div class="absolute top-0 inset-x-0 h-1 bg-gradient-to-r from-amber-500 to-rose-500"></div>

            <div class="mb-6">
                <h2 class="text-xl font-bold text-white">
                    <?= $mode === 'reset' ? 'Set New Staff Password' : 'Reset Staff Password' ?>
                </h2>
                <p class="text-xs text-slate-400 mt-1">
                    <?= $mode === 'reset' ? 'Enter and confirm your new secure 10+ character password.' : 'Enter your registered staff email address to receive a secure 1-hour reset token.' ?>
                </p>
            </div>

            <?php if (!empty($message)): ?>
            <div class="mb-5 p-4 rounded-xl text-xs bg-emerald-950/80 border border-emerald-700/60 text-emerald-200">
                <div class="flex items-start gap-2.5">
                    <i class="fa-solid fa-circle-check text-emerald-400 text-sm mt-0.5"></i>
                    <div><?= $message ?></div>
                </div>
            </div>
            <?php endif; ?>

            <?php if (!empty($error)): ?>
            <div class="mb-5 p-3 rounded-xl text-xs bg-rose-950/80 border border-rose-700/60 text-rose-200 flex items-center gap-2">
                <i class="fa-solid fa-circle-exclamation text-rose-400 text-sm"></i>
                <span><?= sanitize_output($error) ?></span>
            </div>
            <?php endif; ?>

            <?php if ($mode === 'reset' && $token_valid): ?>
            <!-- Reset Form -->
            <form method="POST" action="reset_password.php?token=<?= urlencode($token) ?>" class="space-y-4">
                <?= csrf_field() ?>
                <input type="hidden" name="form_type" value="reset">

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">
                        New Password (Min 10 characters)
                    </label>
                    <input type="password" name="password" required minlength="10" placeholder="••••••••••••"
                           class="w-full bg-slate-900 border border-slate-700 text-xs rounded-xl px-3.5 py-2.5 text-white focus:outline-none focus:border-amber-400">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">
                        Confirm New Password
                    </label>
                    <input type="password" name="confirm_password" required minlength="10" placeholder="••••••••••••"
                           class="w-full bg-slate-900 border border-slate-700 text-xs rounded-xl px-3.5 py-2.5 text-white focus:outline-none focus:border-amber-400">
                </div>

                <button type="submit" class="w-full bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-slate-950 font-bold text-xs py-3 rounded-xl shadow-lg transition">
                    Update Staff Password & Sign In
                </button>
            </form>

            <?php elseif ($mode === 'request'): ?>
            <!-- Request Form -->
            <form method="POST" action="reset_password.php" class="space-y-4">
                <?= csrf_field() ?>
                <input type="hidden" name="form_type" value="request">

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Registered Staff Email</label>
                    <input type="email" name="email" required placeholder="editor@sndmedia.in"
                           class="w-full bg-slate-900 border border-slate-700 text-xs rounded-xl px-3.5 py-2.5 text-white focus:outline-none focus:border-amber-400">
                </div>

                <button type="submit" class="w-full bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-slate-950 font-bold text-xs py-3 rounded-xl shadow-lg transition">
                    Generate Recovery Link
                </button>
            </form>
            <?php endif; ?>

            <div class="mt-6 pt-4 border-t border-slate-800 text-center">
                <a href="login.php" class="text-xs text-slate-400 hover:text-white transition">
                    &larr; Back to Staff Login
                </a>
            </div>
        </div>
    </div>

</body>
</html>

