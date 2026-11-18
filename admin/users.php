<?php
/**
 * SND Media — Staff & RBAC User Management Panel
 * Phase 1 Production Architecture (US-19)
 */

require_once __DIR__ . '/layout.php';

// Restricted strictly to Super Admin
require_role('super_admin');

$pdo = get_db_connection();
$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$edit_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$error = '';

// Handle Form Submission (Create or Edit)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '';
    if (!csrf_token_validate($token)) {
        flash_set('error', 'CSRF verification failed. Please try again.');
        header('Location: users.php');
        exit;
    }

    $form_action = isset($_POST['form_action']) ? $_POST['form_action'] : 'create';
    $username = trim(isset($_POST['username']) ? $_POST['username'] : '');
    $email = trim(isset($_POST['email']) ? $_POST['email'] : '');
    $full_name = trim(isset($_POST['full_name']) ? $_POST['full_name'] : '');
    $role = isset($_POST['role']) ? $_POST['role'] : 'reporter';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    $valid_roles = ['super_admin', 'editor', 'reporter', 'space_manager'];
    if (!in_array($role, $valid_roles, true)) {
        $role = 'reporter';
    }

    if ($form_action === 'create') {
        if (empty($username) || empty($email) || empty($full_name) || empty($password)) {
            $error = 'All fields are required to create a new staff account.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Please provide a valid email address.';
        } elseif (strlen($password) < 10) {
            $error = 'Password must be at least 10 characters long.';
        } elseif ($password !== $confirm_password) {
            $error = 'Password and confirmation do not match.';
        } elseif ($pdo) {
            // Check uniqueness
            $check = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
            $check->execute([$username, $email]);
            if ($check->fetch()) {
                $error = 'A staff user with that username or email already exists.';
            } else {
                $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
                $stmt = $pdo->prepare("
                    INSERT INTO users (username, email, password_hash, full_name, role, is_active, created_at)
                    VALUES (?, ?, ?, ?, ?, ?, NOW())
                ");
                $stmt->execute([$username, $email, $hash, $full_name, $role, $is_active]);
                flash_set('success', "Staff member '{$full_name}' created successfully.");
                header('Location: users.php');
                exit;
            }
        }
    } elseif ($form_action === 'edit' && $edit_id > 0) {
        if (empty($username) || empty($email) || empty($full_name)) {
            $error = 'Name, username, and email cannot be blank.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Please provide a valid email address.';
        } elseif (!empty($password) && strlen($password) < 10) {
            $error = 'New password must be at least 10 characters long.';
        } elseif (!empty($password) && $password !== $confirm_password) {
            $error = 'Password and confirmation do not match.';
        } elseif ($pdo) {
            // Prevent super admin from deactivating self
            if ($edit_id === current_user_id() && $is_active === 0) {
                $is_active = 1;
                flash_set('warning', 'You cannot deactivate your own Super Admin account.');
            }

            // Check duplicate username/email
            $check = $pdo->prepare("SELECT id FROM users WHERE (username = ? OR email = ?) AND id != ?");
            $check->execute([$username, $email, $edit_id]);
            if ($check->fetch()) {
                $error = 'Another staff member already uses this username or email.';
            } else {
                if (!empty($password)) {
                    $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
                    $stmt = $pdo->prepare("
                        UPDATE users 
                        SET username = ?, email = ?, password_hash = ?, full_name = ?, role = ?, is_active = ?
                        WHERE id = ?
                    ");
                    $stmt->execute([$username, $email, $hash, $full_name, $role, $is_active, $edit_id]);
                } else {
                    $stmt = $pdo->prepare("
                        UPDATE users 
                        SET username = ?, email = ?, full_name = ?, role = ?, is_active = ?
                        WHERE id = ?
                    ");
                    $stmt->execute([$username, $email, $full_name, $role, $is_active, $edit_id]);
                }
                flash_set('success', "Staff account '{$full_name}' updated successfully.");
                header('Location: users.php');
                exit;
            }
        }
    }
}

// Fetch users list or single user for edit
$users = [];
$edit_user = null;

if ($pdo) {
    if ($action === 'edit' && $edit_id > 0) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$edit_id]);
        $edit_user = $stmt->fetch();
        if (!$edit_user) {
            flash_set('error', 'User not found.');
            header('Location: users.php');
            exit;
        }
    } else {
        $users = $pdo->query("SELECT * FROM users ORDER BY id ASC")->fetchAll();
    }
}

render_admin_header('Staff & RBAC User Management', 'users');
?>

<div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <h2 class="text-xl font-bold text-white">Staff Accounts & Permissions</h2>
        <p class="text-xs text-slate-400 mt-0.5">Control role-based authorization across Super Admin, Editor, Reporter, and Space Manager.</p>
    </div>
    <?php if ($action === 'list'): ?>
    <a href="users.php?action=create" class="bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs px-4 py-2.5 rounded-xl shadow-lg shadow-amber-500/20 transition flex items-center gap-1.5 self-start">
        <i class="fa-solid fa-user-plus"></i> Add New Staff
    </a>
    <?php else: ?>
    <a href="users.php" class="bg-slate-800 hover:bg-slate-700 text-slate-200 font-semibold text-xs px-4 py-2.5 rounded-xl border border-slate-700 transition flex items-center gap-1.5 self-start">
        <i class="fa-solid fa-arrow-left"></i> Back to User List
    </a>
    <?php endif; ?>
</div>

<?php if (!empty($error)): ?>
<div class="mb-6 p-4 rounded-xl text-xs bg-rose-950/80 border border-rose-700/60 text-rose-200 flex items-center gap-2">
    <i class="fa-solid fa-circle-exclamation text-rose-400 text-sm"></i>
    <span><?= sanitize_output($error) ?></span>
</div>
<?php endif; ?>

<?php if ($action === 'create' || $action === 'edit'): ?>
<!-- Create / Edit Form Card -->
<div class="max-w-2xl bg-brand-card border border-slate-800 rounded-2xl p-6 shadow-xl">
    <h3 class="text-base font-bold text-white mb-4">
        <?= $action === 'edit' ? 'Edit Staff Member: ' . sanitize_output($edit_user['full_name']) : 'Create New Staff Account' ?>
    </h3>

    <form method="POST" action="users.php<?= $action === 'edit' ? '?action=edit&id=' . $edit_id : '' ?>" class="space-y-4">
        <?= csrf_field() ?>
        <input type="hidden" name="form_action" value="<?= $action ?>">

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Full Name</label>
                <input type="text" name="full_name" required 
                       value="<?= $edit_user ? sanitize_output($edit_user['full_name']) : (isset($_POST['full_name']) ? sanitize_output($_POST['full_name']) : '') ?>" 
                       placeholder="e.g. Ramesh Kumar"
                       class="w-full bg-slate-900 border border-slate-700 text-xs rounded-xl px-3 py-2 text-white focus:outline-none focus:border-amber-400">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Username</label>
                <input type="text" name="username" required 
                       value="<?= $edit_user ? sanitize_output($edit_user['username']) : (isset($_POST['username']) ? sanitize_output($_POST['username']) : '') ?>" 
                       placeholder="e.g. ramesh_desk"
                       class="w-full bg-slate-900 border border-slate-700 text-xs rounded-xl px-3 py-2 text-white focus:outline-none focus:border-amber-400">
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Email Address</label>
                <input type="email" name="email" required 
                       value="<?= $edit_user ? sanitize_output($edit_user['email']) : (isset($_POST['email']) ? sanitize_output($_POST['email']) : '') ?>" 
                       placeholder="ramesh@sndmedia.in"
                       class="w-full bg-slate-900 border border-slate-700 text-xs rounded-xl px-3 py-2 text-white focus:outline-none focus:border-amber-400">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Role Permission</label>
                <select name="role" class="w-full bg-slate-900 border border-slate-700 text-xs rounded-xl px-3 py-2 text-white focus:outline-none focus:border-amber-400">
                    <?php
                    $current_r = $edit_user ? $edit_user['role'] : (isset($_POST['role']) ? $_POST['role'] : 'reporter');
                    $roles_list = [
                        'super_admin'   => 'Super Admin (Full Platform Control)',
                        'editor'        => 'Senior Editor (Newsroom & YouTube)',
                        'reporter'      => 'Reporter (Draft Stories Only)',
                        'space_manager' => 'Space Manager (Studio Leasing & Leads CRM)'
                    ];
                    foreach ($roles_list as $r_key => $r_label) {
                        $sel = ($current_r === $r_key) ? 'selected' : '';
                        echo "<option value=\"{$r_key}\" {$sel}>{$r_label}</option>";
                    }
                    ?>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2 border-t border-slate-800">
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">
                    <?= $action === 'edit' ? 'Change Password (Leave blank to keep current)' : 'Password (Min 10 characters)' ?>
                </label>
                <input type="password" name="password" <?= $action === 'create' ? 'required' : '' ?> minlength="10" 
                       placeholder="••••••••••••"
                       class="w-full bg-slate-900 border border-slate-700 text-xs rounded-xl px-3 py-2 text-white focus:outline-none focus:border-amber-400">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Confirm Password</label>
                <input type="password" name="confirm_password" <?= $action === 'create' ? 'required' : '' ?> minlength="10" 
                       placeholder="••••••••••••"
                       class="w-full bg-slate-900 border border-slate-700 text-xs rounded-xl px-3 py-2 text-white focus:outline-none focus:border-amber-400">
            </div>
        </div>

        <div class="pt-2">
            <label class="inline-flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="is_active" value="1" 
                       <?= (!$edit_user || (int)$edit_user['is_active'] === 1) ? 'checked' : '' ?> 
                       class="rounded bg-slate-900 border-slate-700 text-amber-500 focus:ring-amber-400">
                <span class="text-xs text-slate-300 font-semibold">Account Active (Permitted to log in)</span>
            </label>
        </div>

        <div class="pt-4 flex gap-3">
            <button type="submit" class="bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs px-5 py-2.5 rounded-xl shadow-lg transition">
                <?= $action === 'edit' ? 'Save Changes' : 'Create User' ?>
            </button>
            <a href="users.php" class="bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold px-4 py-2.5 rounded-xl transition">
                Cancel
            </a>
        </div>
    </form>
</div>

<?php else: ?>
<!-- Users List Table -->
<div class="bg-brand-card border border-slate-800 rounded-2xl overflow-hidden shadow-xl">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-xs text-slate-300">
            <thead class="bg-slate-900/80 uppercase text-[10px] tracking-wider text-slate-400 border-b border-slate-800">
                <tr>
                    <th class="py-3.5 px-4">Staff Member</th>
                    <th class="py-3.5 px-4">Username</th>
                    <th class="py-3.5 px-4">Role</th>
                    <th class="py-3.5 px-4">Status</th>
                    <th class="py-3.5 px-4">Last Login</th>
                    <th class="py-3.5 px-4 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800/80">
                <?php if (!empty($users)): ?>
                <?php foreach ($users as $u): ?>
                <tr class="hover:bg-slate-900/40 transition">
                    <td class="py-3.5 px-4">
                        <div class="font-bold text-white"><?= sanitize_output($u['full_name']) ?></div>
                        <div class="text-[11px] text-slate-400"><?= sanitize_output($u['email']) ?></div>
                    </td>
                    <td class="py-3.5 px-4 font-mono text-slate-300">
                        <?= sanitize_output($u['username']) ?>
                    </td>
                    <td class="py-3.5 px-4">
                        <?php
                        $role_colors = [
                            'super_admin'   => 'bg-purple-950 text-purple-300 border-purple-800/50',
                            'editor'        => 'bg-blue-950 text-blue-300 border-blue-800/50',
                            'reporter'      => 'bg-slate-800 text-slate-300 border-slate-700',
                            'space_manager' => 'bg-emerald-950 text-emerald-300 border-emerald-800/50'
                        ];
                        $badge_class = isset($role_colors[$u['role']]) ? $role_colors[$u['role']] : 'bg-slate-800 text-slate-300';
                        ?>
                        <span class="inline-block text-[10px] font-bold uppercase px-2 py-0.5 rounded-full border <?= $badge_class ?>">
                            <?= role_display_name($u['role']) ?>
                        </span>
                    </td>
                    <td class="py-3.5 px-4">
                        <?php if ((int)$u['is_active'] === 1): ?>
                        <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-emerald-400">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span> Active
                        </span>
                        <?php else: ?>
                        <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-rose-400">
                            <span class="w-1.5 h-1.5 rounded-full bg-rose-400"></span> Deactivated
                        </span>
                        <?php endif; ?>
                    </td>
                    <td class="py-3.5 px-4 text-slate-400">
                        <?= !empty($u['last_login']) ? time_ago($u['last_login']) : '<span class="text-slate-600">Never</span>' ?>
                    </td>
                    <td class="py-3.5 px-4 text-right">
                        <a href="users.php?action=edit&id=<?= (int)$u['id'] ?>" class="bg-slate-800 hover:bg-slate-700 text-amber-300 text-xs px-3 py-1 rounded-lg border border-slate-700 transition inline-flex items-center gap-1">
                            <i class="fa-solid fa-pen-to-square text-[10px]"></i> Edit
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php else: ?>
                <tr>
                    <td colspan="6" class="p-8 text-center text-slate-500">
                        No users found in database.
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<?php
render_admin_footer();

