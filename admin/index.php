<?php
/**
 * SND Media — Role-Aware Admin Dashboard
 * Phase 1 Production Architecture (US-13, US-19, US-17)
 */

require_once __DIR__ . '/layout.php';

require_login();

$pdo = get_db_connection();
$user_role = current_user_role();
$user_id = current_user_id();

// Initial metric counts
$counts = [
    'articles_total'     => 5,
    'articles_published' => 5,
    'articles_draft'     => 0,
    'ticker_active'      => 5,
    'yt_videos'          => 5,
    'leads_total'        => 0,
    'leads_new'          => 0,
    'users_total'        => 4
];

$recent_articles = [];
$recent_leads = [];

if ($pdo) {
    try {
        // Query counts based on role
        if (has_role(['super_admin', 'editor'])) {
            $counts['articles_total'] = (int)$pdo->query("SELECT COUNT(*) FROM articles")->fetchColumn();
            $counts['articles_published'] = (int)$pdo->query("SELECT COUNT(*) FROM articles WHERE status = 'published'")->fetchColumn();
            $counts['articles_draft'] = (int)$pdo->query("SELECT COUNT(*) FROM articles WHERE status = 'draft'")->fetchColumn();
            $counts['ticker_active'] = (int)$pdo->query("SELECT COUNT(*) FROM breaking_ticker WHERE is_active = 1")->fetchColumn();
            $counts['yt_videos'] = (int)$pdo->query("SELECT COUNT(*) FROM youtube_bulletins")->fetchColumn();
        } elseif ($user_role === 'reporter') {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM articles WHERE author_id = ?");
            $stmt->execute([$user_id]);
            $counts['articles_total'] = (int)$stmt->fetchColumn();

            $stmt = $pdo->prepare("SELECT COUNT(*) FROM articles WHERE author_id = ? AND status = 'published'");
            $stmt->execute([$user_id]);
            $counts['articles_published'] = (int)$stmt->fetchColumn();

            $stmt = $pdo->prepare("SELECT COUNT(*) FROM articles WHERE author_id = ? AND status = 'draft'");
            $stmt->execute([$user_id]);
            $counts['articles_draft'] = (int)$stmt->fetchColumn();
        }

        if (has_role(['super_admin', 'space_manager'])) {
            $counts['leads_total'] = (int)$pdo->query("SELECT COUNT(*) FROM rental_enquiries")->fetchColumn();
            $counts['leads_new'] = (int)$pdo->query("SELECT COUNT(*) FROM rental_enquiries WHERE status = 'new'")->fetchColumn();
        }

        if (has_role('super_admin')) {
            $counts['users_total'] = (int)$pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
        }

        // Recent articles
        if (has_role(['super_admin', 'editor'])) {
            $recent_articles = $pdo->query("SELECT id, title, category, status, published_at, author_name FROM articles ORDER BY id DESC LIMIT 5")->fetchAll();
        } elseif ($user_role === 'reporter') {
            $stmt = $pdo->prepare("SELECT id, title, category, status, published_at, author_name FROM articles WHERE author_id = ? ORDER BY id DESC LIMIT 5");
            $stmt->execute([$user_id]);
            $recent_articles = $stmt->fetchAll();
        }

        // Recent leads
        if (has_role(['super_admin', 'space_manager'])) {
            $recent_leads = $pdo->query("SELECT * FROM rental_enquiries ORDER BY id DESC LIMIT 5")->fetchAll();
        }
    } catch (Exception $e) {
        error_log("Dashboard Metrics Query Error: " . $e->getMessage());
    }
}

render_admin_header('Desk Overview', 'dashboard');
?>

<!-- Welcome Banner -->
<div class="bg-gradient-to-r from-amber-500/10 via-slate-900 to-slate-900 border border-amber-500/20 rounded-2xl p-6 mb-8 relative overflow-hidden">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-amber-400/20 text-amber-300 text-[11px] font-bold uppercase tracking-wider mb-2 border border-amber-400/30">
                <i class="fa-solid fa-satellite-dish"></i> Operations Active
            </div>
            <h2 class="text-xl md:text-2xl font-extrabold text-white">
                SND Media &mdash; Operations Console
            </h2>
            <p class="text-xs text-slate-400 mt-1 max-w-xl">
                Managing national digital news, YouTube video broadcasts (<code class="text-amber-300">@sndschoolmedia</code>), and commercial studio floor rentals at SND Public School Campus, Palwal.
            </p>
        </div>
        <div class="flex items-center gap-2.5 shrink-0">
            <?php if (has_role(['super_admin', 'editor', 'reporter'])): ?>
            <a href="articles.php?action=create" class="bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs px-3.5 py-2.5 rounded-xl shadow-lg shadow-amber-500/20 transition flex items-center gap-1.5">
                <i class="fa-solid fa-pen-nib"></i> Compose Story
            </a>
            <?php endif; ?>
            <?php if (has_role(['super_admin', 'space_manager'])): ?>
            <a href="leads.php" class="bg-slate-800 hover:bg-slate-700 text-white font-semibold text-xs px-3.5 py-2.5 rounded-xl border border-slate-700 transition flex items-center gap-1.5">
                <i class="fa-solid fa-envelope-open-text text-emerald-400"></i> View Inquiries
            </a>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Key Performance Metrics Grid -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
    <?php if (has_role(['super_admin', 'editor', 'reporter'])): ?>
    <!-- Articles Metric -->
    <div class="bg-brand-card border border-slate-800 rounded-2xl p-5 shadow-lg">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">News Stories</span>
            <div class="w-8 h-8 rounded-lg bg-blue-500/10 text-blue-400 flex items-center justify-center text-sm">
                <i class="fa-solid fa-newspaper"></i>
            </div>
        </div>
        <div class="text-2xl font-extrabold text-white"><?= $counts['articles_total'] ?></div>
        <div class="text-[11px] text-slate-400 mt-1 flex items-center gap-2">
            <span class="text-emerald-400 font-semibold"><?= $counts['articles_published'] ?> Published</span>
            <span>&bull;</span>
            <span class="text-amber-400 font-semibold"><?= $counts['articles_draft'] ?> Drafts</span>
        </div>
    </div>
    <?php endif; ?>

    <?php if (has_role(['super_admin', 'editor'])): ?>
    <!-- Ticker Metric -->
    <div class="bg-brand-card border border-slate-800 rounded-2xl p-5 shadow-lg">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">24x7 Breaking Ticker</span>
            <div class="w-8 h-8 rounded-lg bg-rose-500/10 text-rose-400 flex items-center justify-center text-sm">
                <i class="fa-solid fa-bolt"></i>
            </div>
        </div>
        <div class="text-2xl font-extrabold text-white"><?= $counts['ticker_active'] ?></div>
        <div class="text-[11px] text-slate-400 mt-1">
            Active Marquee Bulletins Running
        </div>
    </div>

    <!-- YouTube Hub Metric -->
    <div class="bg-brand-card border border-slate-800 rounded-2xl p-5 shadow-lg">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">YouTube TV Broadcasts</span>
            <div class="w-8 h-8 rounded-lg bg-red-500/10 text-red-500 flex items-center justify-center text-sm">
                <i class="fa-brands fa-youtube"></i>
            </div>
        </div>
        <div class="text-2xl font-extrabold text-white"><?= $counts['yt_videos'] ?></div>
        <div class="text-[11px] text-slate-400 mt-1">
            Synced @sndschoolmedia Bulletins
        </div>
    </div>
    <?php endif; ?>

    <?php if (has_role(['super_admin', 'space_manager'])): ?>
    <!-- Commercial Leads Metric -->
    <div class="bg-brand-card border border-slate-800 rounded-2xl p-5 shadow-lg">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Studio Enquiries</span>
            <div class="w-8 h-8 rounded-lg bg-emerald-500/10 text-emerald-400 flex items-center justify-center text-sm">
                <i class="fa-solid fa-envelope-open-text"></i>
            </div>
        </div>
        <div class="text-2xl font-extrabold text-white"><?= $counts['leads_total'] ?></div>
        <div class="text-[11px] text-slate-400 mt-1 flex items-center gap-2">
            <span class="text-amber-400 font-semibold"><?= $counts['leads_new'] ?> New Inquiries</span>
            <span>&bull;</span>
            <span class="text-slate-400">Leasing CRM</span>
        </div>
    </div>
    <?php endif; ?>

    <?php if (has_role('super_admin')): ?>
    <!-- Users Metric -->
    <div class="bg-brand-card border border-slate-800 rounded-2xl p-5 shadow-lg">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Staff Accounts</span>
            <div class="w-8 h-8 rounded-lg bg-purple-500/10 text-purple-400 flex items-center justify-center text-sm">
                <i class="fa-solid fa-users"></i>
            </div>
        </div>
        <div class="text-2xl font-extrabold text-white"><?= $counts['users_total'] ?></div>
        <div class="text-[11px] text-slate-400 mt-1">
            Active RBAC Editorial & Sales Users
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Two-Column Operational Activity Tables -->
<div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
    
    <!-- Left Column: Recent News Articles -->
    <?php if (has_role(['super_admin', 'editor', 'reporter'])): ?>
    <div class="lg:col-span-7 bg-brand-card border border-slate-800 rounded-2xl p-6 shadow-xl">
        <div class="flex items-center justify-between mb-5">
            <div>
                <h3 class="font-bold text-base text-white">Recent News Articles</h3>
                <p class="text-xs text-slate-400 mt-0.5">Latest published dispatches and draft stories</p>
            </div>
            <a href="articles.php" class="text-xs text-amber-400 hover:text-amber-300 font-semibold">
                Manage All &rarr;
            </a>
        </div>

        <?php if (!empty($recent_articles)): ?>
        <div class="divide-y divide-slate-800/80">
            <?php foreach ($recent_articles as $art): ?>
            <div class="py-3 flex items-center justify-between gap-4">
                <div class="min-w-0 flex-1">
                    <div class="flex items-center gap-2 mb-1">
                        <span class="text-[10px] font-bold uppercase px-2 py-0.5 rounded bg-slate-800 text-slate-300 border border-slate-700">
                            <?= sanitize_output($art['category']) ?>
                        </span>
                        <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full <?= $art['status'] === 'published' ? 'bg-emerald-950 text-emerald-300 border border-emerald-800/50' : 'bg-amber-950 text-amber-300 border border-amber-800/50' ?>">
                            <?= ucfirst($art['status']) ?>
                        </span>
                    </div>
                    <a href="articles.php?action=edit&id=<?= (int)$art['id'] ?>" class="text-xs font-semibold text-slate-200 hover:text-white line-clamp-1 transition">
                        <?= sanitize_output($art['title']) ?>
                    </a>
                </div>
                <div class="text-right shrink-0">
                    <span class="text-[11px] text-slate-500 block"><?= time_ago($art['published_at']) ?></span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="p-8 text-center text-xs text-slate-500">
            <i class="fa-solid fa-newspaper text-3xl mb-2 text-slate-600 block"></i>
            No articles recorded yet. Start by drafting a new story.
        </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <!-- Right Column: Commercial Studio Inquiries / Quick Actions -->
    <div class="lg:col-span-5 space-y-6">
        <?php if (has_role(['super_admin', 'space_manager'])): ?>
        <div class="bg-brand-card border border-slate-800 rounded-2xl p-6 shadow-xl">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="font-bold text-base text-white">Inbound Studio Leads</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Commercial booking requests from creators & OTT</p>
                </div>
                <a href="leads.php" class="text-xs text-amber-400 hover:text-amber-300 font-semibold">
                    CRM Inbox &rarr;
                </a>
            </div>

            <?php if (!empty($recent_leads)): ?>
            <div class="divide-y divide-slate-800/80">
                <?php foreach ($recent_leads as $lead): ?>
                <div class="py-3 flex items-center justify-between gap-3 text-xs">
                    <div class="min-w-0 flex-1">
                        <div class="font-semibold text-white truncate"><?= sanitize_output($lead['client_name']) ?></div>
                        <div class="text-[11px] text-slate-400 truncate"><?= sanitize_output($lead['facility_requested']) ?></div>
                    </div>
                    <div class="shrink-0 text-right">
                        <span class="inline-block text-[10px] font-bold uppercase px-2 py-0.5 rounded bg-amber-400/10 text-amber-300 border border-amber-400/20">
                            <?= sanitize_output($lead['status']) ?>
                        </span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="p-6 text-center text-xs text-slate-500">
                <i class="fa-solid fa-inbox text-3xl mb-2 text-slate-600 block"></i>
                No new rental enquiries received yet. Forms on the live site will feed here instantly.
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <!-- Quick System Status Card -->
        <div class="bg-brand-card border border-slate-800 rounded-2xl p-6 shadow-xl">
            <h3 class="font-bold text-sm text-white mb-3">Live Integration Summary</h3>
            <div class="space-y-2.5 text-xs">
                <div class="flex items-center justify-between py-1.5 border-b border-slate-800">
                    <span class="text-slate-400">Website Domain:</span>
                    <span class="font-semibold text-white">sndmedia.in</span>
                </div>
                <div class="flex items-center justify-between py-1.5 border-b border-slate-800">
                    <span class="text-slate-400">Campus HQ:</span>
                    <span class="font-semibold text-white">SND Public School, Palwal</span>
                </div>
                <div class="flex items-center justify-between py-1.5 border-b border-slate-800">
                    <span class="text-slate-400">Official Channel:</span>
                    <a href="https://www.youtube.com/@sndschoolmedia" target="_blank" class="text-amber-400 hover:underline">@sndschoolmedia</a>
                </div>
                <div class="flex items-center justify-between py-1.5">
                    <span class="text-slate-400">Desk Telephone:</span>
                    <span class="font-mono text-emerald-400">+91-7206622711</span>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
render_admin_footer();

