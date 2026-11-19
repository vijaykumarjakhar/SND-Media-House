<?php
/**
 * SND Media — Admin Shell Layout Template
 * Phase 1 Production Architecture
 */

require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/config/settings.php';

function render_admin_header($page_title = 'Dashboard', $active_nav = 'dashboard') {
    $user_name = current_user_name();
    $user_role = current_user_role();
    $role_label = role_display_name($user_role);
    $flash = flash_get();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= sanitize_output($page_title) ?> | SND Media Admin Console</title>
    <!-- Pre-compiled Tailwind CSS -->
    <link rel="stylesheet" href="../assets/css/tailwind.min.css">
    <link rel="stylesheet" href="../assets/css/custom.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 5px; height: 5px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #070b14; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #1e293b; border-radius: 4px; }
    </style>
</head>
<body class="bg-brand-dark text-slate-100 min-h-screen flex flex-col md:flex-row antialiased">

    <!-- Mobile Admin Top Bar -->
    <div class="md:hidden bg-slate-900 border-b border-slate-800 px-4 py-3 flex items-center justify-between z-30">
        <div class="flex items-center gap-2">
            <img src="../assets/images/snd-logo.svg" alt="SND Logo" class="w-8 h-8 rounded-full border border-amber-400 bg-white">
            <span class="font-extrabold text-white text-base tracking-tight"><span class="text-amber-400">SND</span> ADMIN</span>
        </div>
        <button onclick="document.getElementById('admin-sidebar').classList.toggle('hidden')" class="text-slate-300 hover:text-white p-1">
            <i class="fa-solid fa-bars text-lg"></i>
        </button>
    </div>

    <!-- Sidebar Navigation -->
    <aside id="admin-sidebar" class="hidden md:flex flex-col w-full md:w-64 bg-slate-900/95 border-r border-slate-800 shrink-0 min-h-screen z-20">
        <!-- Brand Masthead -->
        <div class="p-5 border-b border-slate-800/80 flex items-center gap-3">
            <img src="../assets/images/snd-logo.svg" alt="SND Logo" class="w-10 h-10 rounded-full border-2 border-amber-400 bg-white shadow-md">
            <div>
                <div class="font-black text-lg tracking-tight text-white leading-none">
                    <span class="text-amber-400">SND</span> MEDIA
                </div>
                <div class="text-[10px] uppercase font-bold text-amber-300/80 tracking-widest mt-1">
                    Editorial & CRM Desk
                </div>
            </div>
        </div>

        <!-- User Quick Profile Card -->
        <div class="px-5 py-4 border-b border-slate-800/60 bg-slate-950/40">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-gradient-to-tr from-amber-500 to-rose-600 flex items-center justify-center font-bold text-white text-sm">
                    <?= strtoupper(substr($user_name, 0, 1)) ?>
                </div>
                <div class="min-w-0 flex-1">
                    <div class="text-xs font-bold text-white truncate"><?= sanitize_output($user_name) ?></div>
                    <span class="inline-block mt-0.5 text-[10px] font-semibold px-2 py-0.5 rounded-full bg-slate-800 text-amber-300 border border-amber-400/20">
                        <?= sanitize_output($role_label) ?>
                    </span>
                </div>
            </div>
        </div>

        <!-- Role-Aware Navigation Links -->
        <nav class="flex-1 p-4 space-y-1.5 text-xs font-semibold overflow-y-auto custom-scrollbar">
            <!-- All Roles: Dashboard -->
            <a href="index.php" class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition <?= $active_nav === 'dashboard' ? 'bg-amber-500 text-slate-950 font-bold shadow-md' : 'text-slate-300 hover:bg-slate-800 hover:text-white' ?>">
                <i class="fa-solid fa-chart-line w-4 text-center"></i>
                <span>Desk Overview</span>
            </a>

            <!-- Editorial Section (Super Admin, Editor, Reporter) -->
            <?php if (has_role(['super_admin', 'editor', 'reporter'])): ?>
            <div class="pt-3 pb-1 text-[10px] font-bold text-slate-500 uppercase tracking-wider px-3">
                Editorial Operations
            </div>
            <a href="articles.php" class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition <?= $active_nav === 'articles' ? 'bg-amber-500 text-slate-950 font-bold shadow-md' : 'text-slate-300 hover:bg-slate-800 hover:text-white' ?>">
                <i class="fa-solid fa-newspaper w-4 text-center"></i>
                <span>News Articles</span>
            </a>
            <?php endif; ?>

            <?php if (has_role(['super_admin', 'editor'])): ?>
            <a href="ticker.php" class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition <?= $active_nav === 'ticker' ? 'bg-amber-500 text-slate-950 font-bold shadow-md' : 'text-slate-300 hover:bg-slate-800 hover:text-white' ?>">
                <i class="fa-solid fa-bolt w-4 text-center text-rose-400"></i>
                <span>24x7 Breaking Ticker</span>
            </a>
            <a href="youtube.php" class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition <?= $active_nav === 'youtube' ? 'bg-amber-500 text-slate-950 font-bold shadow-md' : 'text-slate-300 hover:bg-slate-800 hover:text-white' ?>">
                <i class="fa-brands fa-youtube w-4 text-center text-red-500"></i>
                <span>YouTube TV Desk</span>
            </a>
            <?php endif; ?>

            <!-- Commercial Section (Super Admin, Space Manager) -->
            <?php if (has_role(['super_admin', 'space_manager'])): ?>
            <div class="pt-3 pb-1 text-[10px] font-bold text-slate-500 uppercase tracking-wider px-3">
                Commercial & Studios
            </div>
            <a href="leads.php" class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition <?= $active_nav === 'leads' ? 'bg-amber-500 text-slate-950 font-bold shadow-md' : 'text-slate-300 hover:bg-slate-800 hover:text-white' ?>">
                <i class="fa-solid fa-envelope-open-text w-4 text-center text-emerald-400"></i>
                <span>Studio Leads CRM</span>
            </a>
            <a href="facilities.php" class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition <?= $active_nav === 'facilities' ? 'bg-amber-500 text-slate-950 font-bold shadow-md' : 'text-slate-300 hover:bg-slate-800 hover:text-white' ?>">
                <i class="fa-solid fa-building-circle-check w-4 text-center text-amber-400"></i>
                <span>Studio Spaces & Rates</span>
            </a>
            <?php endif; ?>

            <!-- Administration Section (Super Admin Only) -->
            <?php if (has_role('super_admin')): ?>
            <div class="pt-3 pb-1 text-[10px] font-bold text-slate-500 uppercase tracking-wider px-3">
                System Governance
            </div>
            <a href="users.php" class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition <?= $active_nav === 'users' ? 'bg-amber-500 text-slate-950 font-bold shadow-md' : 'text-slate-300 hover:bg-slate-800 hover:text-white' ?>">
                <i class="fa-solid fa-users-gear w-4 text-center"></i>
                <span>Staff & RBAC Users</span>
            </a>
            <a href="settings.php" class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition <?= $active_nav === 'settings' ? 'bg-amber-500 text-slate-950 font-bold shadow-md' : 'text-slate-300 hover:bg-slate-800 hover:text-white' ?>">
                <i class="fa-solid fa-sliders w-4 text-center"></i>
                <span>Global Site Settings</span>
            </a>
            <?php endif; ?>
        </nav>

        <!-- Sidebar Footer Action -->
        <div class="p-4 border-t border-slate-800/80 bg-slate-950/60 flex items-center justify-between text-xs">
            <a href="../" target="_blank" class="text-slate-400 hover:text-amber-400 flex items-center gap-1.5 transition">
                <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i> View Public Site
            </a>
            <a href="logout.php" class="text-rose-400 hover:text-rose-300 font-semibold flex items-center gap-1 transition">
                <i class="fa-solid fa-right-from-bracket"></i> Logout
            </a>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col min-w-0 overflow-y-auto custom-scrollbar">
        <!-- Top Operational Header -->
        <header class="bg-slate-900/60 backdrop-blur border-b border-slate-800/80 px-6 py-4 flex items-center justify-between gap-4">
            <div>
                <h1 class="text-lg md:text-xl font-extrabold text-white tracking-tight"><?= sanitize_output($page_title) ?></h1>
                <p class="text-xs text-slate-400">SND Media Digital Infrastructure &bull; Phase 1 Production Console</p>
            </div>
            <div class="flex items-center gap-3">
                <span class="hidden sm:inline-flex items-center gap-1.5 text-xs text-emerald-400 bg-emerald-950/60 border border-emerald-800/50 px-3 py-1 rounded-full">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>System Active &bull; sndmedia.in</span>
                </span>
                <a href="../" target="_blank" class="bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold px-3 py-1.5 rounded-lg border border-slate-700 transition">
                    <i class="fa-solid fa-globe mr-1"></i> Live Portal
                </a>
            </div>
        </header>

        <!-- Flash Messaging Banner -->
        <?php if ($flash): ?>
        <div class="px-6 pt-4">
            <?php
            $bg_color = 'bg-slate-800 border-slate-700 text-slate-200';
            $icon = 'fa-info-circle';
            if ($flash['type'] === 'success') {
                $bg_color = 'bg-emerald-950/80 border-emerald-700/60 text-emerald-200';
                $icon = 'fa-circle-check text-emerald-400';
            } elseif ($flash['type'] === 'error') {
                $bg_color = 'bg-rose-950/80 border-rose-700/60 text-rose-200';
                $icon = 'fa-triangle-exclamation text-rose-400';
            } elseif ($flash['type'] === 'warning') {
                $bg_color = 'bg-amber-950/80 border-amber-700/60 text-amber-200';
                $icon = 'fa-circle-exclamation text-amber-400';
            }
            ?>
            <div class="<?= $bg_color ?> border px-4 py-3 rounded-xl flex items-center justify-between text-xs shadow-md">
                <div class="flex items-center gap-2">
                    <i class="fa-solid <?= $icon ?> text-sm"></i>
                    <span><?= sanitize_output($flash['message']) ?></span>
                </div>
                <button onclick="this.parentElement.remove()" class="text-slate-400 hover:text-white ml-4">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        </div>
        <?php endif; ?>

        <!-- Page Inner Content -->
        <main class="p-6 flex-1">
<?php
}

function render_admin_footer() {
?>
        </main>
        
        <footer class="border-t border-slate-800/80 px-6 py-4 text-center text-xs text-slate-500 bg-slate-950/40">
            &copy; <?= date('Y') ?> SND Media (sndmedia.in) &bull; Designed &amp; Developed by <a href="https://securetechnology.in" target="_blank" rel="noopener noreferrer" class="font-bold hover:underline"><span class="text-emerald-400" style="color: #34d399;">Secure</span><span class="text-sky-400" style="color: #38bdf8;">Technology</span></a>
        </footer>
    </div>
</body>
</html>
<?php
}

/**
 * Helper to render admin page with a callback block
 * @param string $page_title
 * @param callable $content_callback
 * @param string $active_nav
 */
function render_admin_layout($page_title, $content_callback, $active_nav = 'dashboard') {
    render_admin_header($page_title, $active_nav);
    if (is_callable($content_callback)) {
        call_user_func($content_callback);
    }
    render_admin_footer();
}


