<?php
/**
 * SND Media — YouTube Broadcasts & Video Hub Manager
 * Syncs channel content from @sndschoolmedia for the homepage and video hubs
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/settings.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/layout.php';

require_role(['super_admin', 'editor', 'reporter']);

$pdo = get_db_connection();
$action = $_GET['action'] ?? 'list';

// Handle Save
if ($_SERVER['REQUEST_METHOD'] === 'POST' && in_array($action, ['save_create', 'save_edit'])) {
    if (!csrf_token_validate($_POST['csrf_token'] ?? '')) {
        flash_set('error', 'Token expired. Please retry.');
        header('Location: youtube.php');
        exit;
    }

    $raw_video = trim($_POST['video_id'] ?? '');
    $video_id  = extract_youtube_id($raw_video) ?: $raw_video;
    $title     = trim($_POST['title'] ?? '');
    $category  = trim($_POST['category_badge'] ?? 'BROADCAST');
    $duration  = trim($_POST['duration'] ?? '10:00');
    $view_count= trim($_POST['view_count'] ?? '1.2K views');
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    $sort_order = (int)($_POST['sort_order'] ?? 1);

    if (empty($video_id) || empty($title)) {
        flash_set('error', 'YouTube Video ID/URL and Title are required.');
    } elseif ($pdo) {
        try {
            if ($is_featured) {
                $pdo->exec("UPDATE youtube_bulletins SET is_featured = 0");
            }

            if ($action === 'save_create') {
                $stmt = $pdo->prepare("
                    INSERT INTO youtube_bulletins 
                    (video_id, title, category_badge, duration, view_count, is_featured, is_active, sort_order)
                    VALUES (?, ?, ?, ?, ?, ?, 1, ?)
                ");
                $stmt->execute([$video_id, $title, $category, $duration, $view_count, $is_featured, $sort_order]);
                flash_set('success', 'YouTube bulletin linked successfully.');
            } else {
                $id = (int)$_POST['id'];
                $stmt = $pdo->prepare("
                    UPDATE youtube_bulletins SET 
                    video_id = ?, title = ?, category_badge = ?, duration = ?, view_count = ?, is_featured = ?, sort_order = ?
                    WHERE id = ?
                ");
                $stmt->execute([$video_id, $title, $category, $duration, $view_count, $is_featured, $sort_order, $id]);
                flash_set('success', 'YouTube bulletin updated.');
            }
        } catch (Exception $e) {
            flash_set('error', 'Database error: ' . $e->getMessage());
        }
    }
    header('Location: youtube.php');
    exit;
}

// Handle Set Featured
if ($action === 'set_featured') {
    $id = (int)($_GET['id'] ?? 0);
    if ($id > 0 && csrf_token_validate($_GET['token'] ?? '') && $pdo) {
        $pdo->exec("UPDATE youtube_bulletins SET is_featured = 0");
        $stmt = $pdo->prepare("UPDATE youtube_bulletins SET is_featured = 1 WHERE id = ?");
        $stmt->execute([$id]);
        flash_set('success', 'Featured player broadcast updated.');
    }
    header('Location: youtube.php');
    exit;
}

// Handle Delete
if ($action === 'delete') {
    $id = (int)($_GET['id'] ?? 0);
    if ($id > 0 && csrf_token_validate($_GET['token'] ?? '') && $pdo) {
        $stmt = $pdo->prepare("DELETE FROM youtube_bulletins WHERE id = ?");
        $stmt->execute([$id]);
        flash_set('success', 'YouTube bulletin unlinked.');
    }
    header('Location: youtube.php');
    exit;
}

// Fetch bulletins
$bulletins = [];
if ($pdo) {
    try {
        $stmt = $pdo->query("SELECT * FROM youtube_bulletins ORDER BY is_featured DESC, sort_order ASC, id DESC");
        $bulletins = $stmt->fetchAll();
    } catch (Exception $e) {
        $bulletins = [];
    }
}

render_admin_layout('YouTube Video Broadcasts Desk', function() use ($bulletins) {
?>
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold text-white">YouTube Broadcast Bulletins</h2>
            <p class="text-xs text-slate-400 mt-0.5">Manage curated broadcasts and playlist sync from the official <code class="text-rose-400">@sndschoolmedia</code> channel.</p>
        </div>
        <button onclick="document.getElementById('add-video-card').classList.toggle('hidden')" class="bg-brand-red hover:bg-rose-700 text-white font-bold text-xs px-4 py-2.5 rounded-xl shadow-lg transition flex items-center gap-1.5 shrink-0">
            <i class="fa-brands fa-youtube"></i> Add Channel Broadcast
        </button>
    </div>

    <!-- Add Video Card -->
    <div id="add-video-card" class="bg-brand-card rounded-2xl border-2 border-rose-600/40 p-6 shadow-2xl hidden">
        <h3 class="text-xs font-black uppercase text-rose-500 tracking-wider mb-4 flex items-center gap-2">
            <i class="fa-solid fa-video"></i> Link New Video Broadcast
        </h3>
        <form method="POST" action="youtube.php?action=save_create" class="space-y-4 text-xs">
            <?= csrf_field() ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-slate-300 font-bold mb-1">YouTube Video URL or ID <span class="text-rose-500">*</span></label>
                    <input type="text" name="video_id" required placeholder="e.g. https://www.youtube.com/watch?v=25tvHivopPE or 25tvHivopPE" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3 py-2 text-white font-mono focus:outline-none focus:border-amber-400">
                </div>
                <div>
                    <label class="block text-slate-300 font-bold mb-1">Broadcast Title <span class="text-rose-500">*</span></label>
                    <input type="text" name="title" required placeholder="e.g. GST Reform: Court Approval Before Taxpayer Arrests" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3 py-2 text-white font-semibold focus:outline-none focus:border-amber-400">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                <div>
                    <label class="block text-slate-300 font-bold mb-1">Badge Tag</label>
                    <input type="text" name="category_badge" value="SPECIAL REPORT" placeholder="SPECIAL REPORT / SPORTS" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3 py-2 text-white focus:outline-none focus:border-amber-400">
                </div>
                <div>
                    <label class="block text-slate-300 font-bold mb-1">Duration</label>
                    <input type="text" name="duration" value="12:40" placeholder="12:40" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3 py-2 text-white font-mono focus:outline-none focus:border-amber-400">
                </div>
                <div>
                    <label class="block text-slate-300 font-bold mb-1">View Count</label>
                    <input type="text" name="view_count" value="1.8K views" placeholder="1.8K views" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3 py-2 text-white focus:outline-none focus:border-amber-400">
                </div>
                <div>
                    <label class="block text-slate-300 font-bold mb-1">Order Priority</label>
                    <input type="number" name="sort_order" value="1" min="1" max="99" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3 py-2 text-white focus:outline-none focus:border-amber-400">
                </div>
            </div>

            <div class="flex items-center justify-between pt-2">
                <label class="flex items-center gap-2 text-slate-300 cursor-pointer">
                    <input type="checkbox" name="is_featured" value="1" class="rounded text-amber-500">
                    <span class="font-bold text-amber-300">Set as Main Video Screen on Homepage</span>
                </label>
                <div class="flex gap-2">
                    <button type="button" onclick="document.getElementById('add-video-card').classList.add('hidden')" class="bg-slate-800 text-slate-300 px-4 py-2 rounded-lg">Cancel</button>
                    <button type="submit" class="bg-brand-red hover:bg-rose-700 text-white font-bold px-4 py-2 rounded-lg shadow">Link to Portal</button>
                </div>
            </div>
        </form>
    </div>

    <!-- Video Grid Table -->
    <div class="bg-brand-card rounded-2xl border border-slate-800 overflow-hidden shadow-xl">
        <table class="w-full text-left text-xs">
            <thead class="bg-slate-900 text-slate-400 uppercase text-[10px] font-bold tracking-wider border-b border-slate-800">
                <tr>
                    <th class="py-3 px-4">Video</th>
                    <th class="py-3 px-4">Broadcast Title</th>
                    <th class="py-3 px-4">Badge</th>
                    <th class="py-3 px-4">Duration</th>
                    <th class="py-3 px-4">Featured</th>
                    <th class="py-3 px-4 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800 text-slate-300">
                <?php if (empty($bulletins)): ?>
                    <tr><td colspan="6" class="py-8 text-center text-slate-500">No broadcasts found.</td></tr>
                <?php else: ?>
                    <?php foreach ($bulletins as $vb): ?>
                        <tr class="hover:bg-slate-900/60 transition">
                            <td class="py-3 px-4 w-32">
                                <div class="relative aspect-video rounded-lg overflow-hidden bg-black border border-slate-800">
                                    <img src="https://img.youtube.com/vi/<?= sanitize_output($vb['video_id']) ?>/mqdefault.jpg" class="w-full h-full object-cover">
                                    <span class="absolute bottom-0.5 right-0.5 bg-black/80 font-mono text-[9px] text-white px-1 rounded"><?= sanitize_output($vb['duration']) ?></span>
                                </div>
                            </td>
                            <td class="py-3 px-4 max-w-sm">
                                <a href="https://www.youtube.com/watch?v=<?= sanitize_output($vb['video_id']) ?>" target="_blank" class="font-bold text-white hover:text-amber-400 line-clamp-2">
                                    <?= sanitize_output($vb['title']) ?>
                                </a>
                                <span class="font-mono text-[10px] text-slate-500">ID: <?= sanitize_output($vb['video_id']) ?></span>
                            </td>
                            <td class="py-3 px-4 font-bold text-amber-400 text-[10px] uppercase">
                                <?= sanitize_output($vb['category_badge']) ?>
                            </td>
                            <td class="py-3 px-4 font-mono text-slate-400"><?= sanitize_output($vb['duration']) ?></td>
                            <td class="py-3 px-4">
                                <?php if ($vb['is_featured']): ?>
                                    <span class="bg-amber-400 text-slate-950 font-black text-[9px] px-2 py-0.5 rounded shadow">MAIN SCREEN</span>
                                <?php else: ?>
                                    <a href="youtube.php?action=set_featured&id=<?= $vb['id'] ?>&token=<?= csrf_token_generate() ?>" class="text-[10px] text-slate-500 hover:text-white border border-slate-800 hover:border-slate-700 px-2 py-0.5 rounded transition">
                                        Make Main
                                    </a>
                                <?php endif; ?>
                            </td>
                            <td class="py-3 px-4 text-right">
                                <a href="youtube.php?action=delete&id=<?= $vb['id'] ?>&token=<?= csrf_token_generate() ?>" onclick="return confirm('Unlink this video bulletin?');" class="text-slate-400 hover:text-rose-400 px-2 py-1">
                                    <i class="fa-solid fa-trash-can"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php
});
