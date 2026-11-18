<?php
/**
 * SND Media — Breaking News Ticker Manager
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/settings.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/layout.php';

require_role(['super_admin', 'editor']);

$pdo = get_db_connection();
$action = $_GET['action'] ?? 'list';
$error = '';

// Handle Create / Edit POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && in_array($action, ['save_create', 'save_edit'])) {
    if (!csrf_token_validate($_POST['csrf_token'] ?? '')) {
        flash_set('error', 'Token expired. Please retry.');
        header('Location: ticker.php');
        exit;
    }

    $ticker_text   = trim($_POST['ticker_text'] ?? '');
    $link_url      = trim($_POST['link_url'] ?? '#');
    $urgency_level = trim($_POST['urgency_level'] ?? 'breaking');
    $sort_order    = (int)($_POST['sort_order'] ?? 0);
    $is_active     = isset($_POST['is_active']) ? 1 : 0;

    if (empty($ticker_text)) {
        flash_set('error', 'Ticker headline text cannot be blank.');
    } elseif ($pdo) {
        try {
            if ($action === 'save_create') {
                $stmt = $pdo->prepare("INSERT INTO breaking_ticker (ticker_text, link_url, urgency_level, is_active, sort_order) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$ticker_text, $link_url, $urgency_level, $is_active, $sort_order]);
                flash_set('success', 'Ticker item published.');
            } else {
                $id = (int)$_POST['id'];
                $stmt = $pdo->prepare("UPDATE breaking_ticker SET ticker_text = ?, link_url = ?, urgency_level = ?, is_active = ?, sort_order = ? WHERE id = ?");
                $stmt->execute([$ticker_text, $link_url, $urgency_level, $is_active, $sort_order, $id]);
                flash_set('success', 'Ticker item updated.');
            }
        } catch (Exception $e) {
            flash_set('error', 'Database error: ' . $e->getMessage());
        }
    }
    header('Location: ticker.php');
    exit;
}

// Handle Delete
if ($action === 'delete') {
    $id = (int)($_GET['id'] ?? 0);
    if ($id > 0 && csrf_token_validate($_GET['token'] ?? '') && $pdo) {
        $stmt = $pdo->prepare("DELETE FROM breaking_ticker WHERE id = ?");
        $stmt->execute([$id]);
        flash_set('success', 'Ticker item removed.');
    }
    header('Location: ticker.php');
    exit;
}

// Fetch all tickers
$tickers = [];
if ($pdo) {
    try {
        $stmt = $pdo->query("SELECT * FROM breaking_ticker ORDER BY is_active DESC, sort_order ASC, id DESC");
        $tickers = $stmt->fetchAll();
    } catch (Exception $e) {
        $tickers = [];
    }
}

// Render UI
render_admin_layout('Breaking Ticker Marquee Manager', function() use ($tickers) {
?>
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold text-white">Breaking News Marquee Items</h2>
            <p class="text-xs text-slate-400 mt-0.5">Control the 24/7 red breaking ticker banner streaming at the top of the portal.</p>
        </div>
        <button onclick="document.getElementById('create-ticker-card').classList.toggle('hidden')" class="bg-brand-red hover:bg-rose-700 text-white font-bold text-xs px-4 py-2.5 rounded-xl shadow-lg transition flex items-center gap-1.5 shrink-0">
            <i class="fa-solid fa-plus"></i> Add Marquee Alert
        </button>
    </div>

    <!-- Create Ticker Item Card (Toggleable) -->
    <div id="create-ticker-card" class="bg-brand-card rounded-2xl border-2 border-brand-red/50 p-6 shadow-2xl hidden">
        <h3 class="text-xs font-black uppercase text-brand-red tracking-wider mb-4 flex items-center gap-2">
            <i class="fa-solid fa-bullhorn"></i> New Marquee Alert
        </h3>
        <form method="POST" action="ticker.php?action=save_create" class="space-y-4 text-xs">
            <?= csrf_field() ?>
            <div>
                <label class="block text-slate-300 font-bold mb-1">Headline Text (UPPERCASE recommended)</label>
                <input type="text" name="ticker_text" required placeholder="e.g. CENTRE UNVEILS RS 1.2 LAKH CR DIGITAL INFRASTRUCTURE CORRIDOR" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3 py-2 text-white font-semibold focus:outline-none focus:border-amber-400">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div>
                    <label class="block text-slate-300 font-bold mb-1">Link URL</label>
                    <input type="text" name="link_url" value="#" placeholder="article.php?slug=... or #section" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3 py-2 text-white focus:outline-none focus:border-amber-400">
                </div>
                <div>
                    <label class="block text-slate-300 font-bold mb-1">Urgency Level</label>
                    <select name="urgency_level" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3 py-2 text-white focus:outline-none focus:border-amber-400">
                        <option value="breaking">Breaking (Red Flash)</option>
                        <option value="flash">Flash Urgent (Amber)</option>
                        <option value="routine">Routine Notice (Blue)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-slate-300 font-bold mb-1">Sort Priority</label>
                    <input type="number" name="sort_order" value="1" min="1" max="99" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3 py-2 text-white focus:outline-none focus:border-amber-400">
                </div>
            </div>

            <div class="flex items-center justify-between pt-2">
                <label class="flex items-center gap-2 text-slate-300 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" checked class="rounded text-amber-500">
                    <span class="font-bold text-white">Broadcast Live Immediately</span>
                </label>
                <div class="flex gap-2">
                    <button type="button" onclick="document.getElementById('create-ticker-card').classList.add('hidden')" class="bg-slate-800 text-slate-300 px-4 py-2 rounded-lg">Cancel</button>
                    <button type="submit" class="bg-brand-red hover:bg-rose-700 text-white font-bold px-4 py-2 rounded-lg shadow">Publish to Marquee</button>
                </div>
            </div>
        </form>
    </div>

    <!-- Ticker Table -->
    <div class="bg-brand-card rounded-2xl border border-slate-800 overflow-hidden shadow-xl">
        <table class="w-full text-left text-xs">
            <thead class="bg-slate-900 text-slate-400 uppercase text-[10px] font-bold tracking-wider border-b border-slate-800">
                <tr>
                    <th class="py-3 px-4">Priority</th>
                    <th class="py-3 px-4">Urgency</th>
                    <th class="py-3 px-4">Marquee Headline</th>
                    <th class="py-3 px-4">Link</th>
                    <th class="py-3 px-4">Status</th>
                    <th class="py-3 px-4 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800 text-slate-300">
                <?php if (empty($tickers)): ?>
                    <tr>
                        <td colspan="6" class="py-8 text-center text-slate-500">No ticker items present.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($tickers as $tk): ?>
                        <tr class="hover:bg-slate-900/60 transition">
                            <td class="py-3 px-4 font-mono text-slate-400 font-bold">#<?= (int)$tk['sort_order'] ?></td>
                            <td class="py-3 px-4">
                                <?php if ($tk['urgency_level'] === 'breaking'): ?>
                                    <span class="bg-rose-600/20 text-rose-400 border border-rose-500/30 px-2 py-0.5 rounded font-black text-[9px] uppercase">Breaking</span>
                                <?php elseif ($tk['urgency_level'] === 'flash'): ?>
                                    <span class="bg-amber-500/20 text-amber-300 border border-amber-500/30 px-2 py-0.5 rounded font-black text-[9px] uppercase">Flash</span>
                                <?php else: ?>
                                    <span class="bg-sky-500/20 text-sky-400 border border-sky-500/30 px-2 py-0.5 rounded font-bold text-[9px] uppercase">Routine</span>
                                <?php endif; ?>
                            </td>
                            <td class="py-3 px-4 font-semibold text-white max-w-md truncate">
                                <?= sanitize_output($tk['ticker_text']) ?>
                            </td>
                            <td class="py-3 px-4 font-mono text-[11px] text-slate-400 truncate max-w-xs">
                                <?= sanitize_output($tk['link_url']) ?>
                            </td>
                            <td class="py-3 px-4">
                                <button onclick="toggleTickerActive(<?= $tk['id'] ?>, this)" class="px-2.5 py-1 rounded text-[10px] font-bold uppercase transition <?= $tk['is_active'] ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30' : 'bg-slate-800 text-slate-500 border border-slate-700' ?>">
                                    <?= $tk['is_active'] ? 'Streaming' : 'Paused' ?>
                                </button>
                            </td>
                            <td class="py-3 px-4 text-right">
                                <a href="ticker.php?action=delete&id=<?= $tk['id'] ?>&token=<?= csrf_token_generate() ?>" onclick="return confirm('Remove marquee alert?');" class="text-slate-400 hover:text-rose-400 px-2 py-1">
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

<script>
async function toggleTickerActive(id, btn) {
    btn.disabled = true;
    try {
        const res = await fetch(`../api/toggle_ticker.php?id=${id}&token=<?= csrf_token_generate() ?>`);
        const data = await res.json();
        if (data.status === 'success') {
            if (data.is_active === 1) {
                btn.className = 'px-2.5 py-1 rounded text-[10px] font-bold uppercase transition bg-emerald-500/20 text-emerald-400 border border-emerald-500/30';
                btn.textContent = 'Streaming';
            } else {
                btn.className = 'px-2.5 py-1 rounded text-[10px] font-bold uppercase transition bg-slate-800 text-slate-500 border border-slate-700';
                btn.textContent = 'Paused';
            }
        } else {
            alert(data.message || 'Error updating status');
        }
    } catch (e) {
        alert('Network communication error.');
    } finally {
        btn.disabled = false;
    }
}
</script>
<?php
});
