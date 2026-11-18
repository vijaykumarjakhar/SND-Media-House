<?php
/**
 * SND Media — Studio Facilities & Commercial Rate Card Manager
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/settings.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/layout.php';

require_role(['super_admin', 'space_manager']);

$pdo = get_db_connection();
$action = $_GET['action'] ?? 'list';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'update_facility') {
    if (!csrf_token_validate($_POST['csrf_token'] ?? '')) {
        flash_set('error', 'Token expired.');
        header('Location: facilities.php');
        exit;
    }

    $id             = (int)$_POST['id'];
    $facility_name  = trim($_POST['facility_name'] ?? '');
    $hourly_rate    = (float)$_POST['hourly_rate'];
    $half_day_rate  = (float)$_POST['half_day_rate'];
    $full_day_rate  = (float)$_POST['full_day_rate'];
    $short_desc     = trim($_POST['short_description'] ?? '');
    $is_available   = isset($_POST['is_available']) ? 1 : 0;
    $raw_specs      = trim($_POST['specifications'] ?? '');
    $specs_arr      = array_filter(array_map('trim', explode("\n", $raw_specs)));
    $specs_json     = json_encode(array_values($specs_arr));

    if ($pdo && $id > 0) {
        try {
            $stmt = $pdo->prepare("
                UPDATE studio_facilities SET 
                facility_name = ?, hourly_rate = ?, half_day_rate = ?, full_day_rate = ?, short_description = ?, specifications = ?, is_available = ?, updated_at = NOW()
                WHERE id = ?
            ");
            $stmt->execute([$facility_name, $hourly_rate, $half_day_rate, $full_day_rate, $short_desc, $specs_json, $is_available, $id]);
            flash_set('success', 'Facility rate card and specs updated.');
        } catch (Exception $e) {
            flash_set('error', 'Database error: ' . $e->getMessage());
        }
    }
    header('Location: facilities.php');
    exit;
}

// Fetch facilities
$facilities = [];
if ($pdo) {
    try {
        $stmt = $pdo->query("SELECT * FROM studio_facilities ORDER BY sort_order ASC, id ASC");
        $facilities = $stmt->fetchAll();
    } catch (Exception $e) {
        $facilities = [];
    }
}

render_admin_layout('Studio Facilities & Rate Card Manager', function() use ($facilities) {
?>
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold text-white">Commercial Studio Facilities &amp; Rate Cards</h2>
            <p class="text-xs text-slate-400 mt-0.5">Manage rental pricing, acoustic specifications, and booking availability at Palwal Campus.</p>
        </div>
        <a href="../studio-rental.php" target="_blank" class="bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold px-3.5 py-2 rounded-xl border border-slate-700 transition flex items-center gap-1.5 shrink-0">
            <i class="fa-solid fa-arrow-up-right-from-square"></i> Preview Public Rate Card
        </a>
    </div>

    <!-- Facility Cards -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <?php foreach ($facilities as $fac): ?>
            <div class="bg-brand-card rounded-2xl border border-slate-800 p-6 shadow-xl space-y-4">
                <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                    <div class="flex items-center gap-2.5">
                        <span class="w-8 h-8 rounded-lg bg-amber-400/20 text-amber-400 flex items-center justify-center font-bold text-xs">
                            <i class="fa-solid fa-building-columns"></i>
                        </span>
                        <h3 class="font-bold text-white text-base"><?= sanitize_output($fac['facility_name']) ?></h3>
                    </div>
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase <?= $fac['is_available'] ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30' : 'bg-rose-500/20 text-rose-400' ?>">
                        <?= $fac['is_available'] ? 'Available for Rent' : 'Leased Out' ?>
                    </span>
                </div>

                <form method="POST" action="facilities.php?action=update_facility" class="space-y-3 text-xs">
                    <?= csrf_field() ?>
                    <input type="hidden" name="id" value="<?= $fac['id'] ?>">

                    <div>
                        <label class="block text-slate-400 font-bold mb-1">Facility Name</label>
                        <input type="text" name="facility_name" value="<?= sanitize_output($fac['facility_name']) ?>" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3 py-2 text-white font-semibold focus:outline-none focus:border-amber-400">
                    </div>

                    <div>
                        <label class="block text-slate-400 font-bold mb-1">Short Description</label>
                        <textarea name="short_description" rows="2" class="w-full bg-slate-900 border border-slate-700 rounded-lg p-2.5 text-slate-200 focus:outline-none focus:border-amber-400"><?= sanitize_output($fac['short_description']) ?></textarea>
                    </div>

                    <div class="grid grid-cols-3 gap-2">
                        <div>
                            <label class="block text-slate-400 font-bold mb-1">Hourly (₹)</label>
                            <input type="number" step="100" name="hourly_rate" value="<?= (float)$fac['hourly_rate'] ?>" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1.5 text-white font-mono focus:outline-none focus:border-amber-400">
                        </div>
                        <div>
                            <label class="block text-slate-400 font-bold mb-1">Half Day (₹)</label>
                            <input type="number" step="500" name="half_day_rate" value="<?= (float)$fac['half_day_rate'] ?>" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1.5 text-white font-mono focus:outline-none focus:border-amber-400">
                        </div>
                        <div>
                            <label class="block text-slate-400 font-bold mb-1">Full Shift (₹)</label>
                            <input type="number" step="1000" name="full_day_rate" value="<?= (float)$fac['full_day_rate'] ?>" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1.5 text-white font-mono focus:outline-none focus:border-amber-400">
                        </div>
                    </div>

                    <?php
                    $specs_list = '';
                    if (!empty($fac['specifications'])) {
                        $decoded = json_decode($fac['specifications'], true);
                        if (is_array($decoded)) {
                            $specs_list = implode("\n", $decoded);
                        }
                    }
                    ?>
                    <div>
                        <label class="block text-slate-400 font-bold mb-1">Feature Specifications (One per line)</label>
                        <textarea name="specifications" rows="3" placeholder="e.g. 4K Blackmagic Camera Setup&#10;Gigabit Fiber Uplink" class="w-full bg-slate-900 border border-slate-700 rounded-lg p-2.5 text-slate-200 font-mono text-[11px] focus:outline-none focus:border-amber-400"><?= sanitize_output($specs_list) ?></textarea>
                    </div>

                    <div class="flex items-center justify-between pt-3 border-t border-slate-800">
                        <label class="flex items-center gap-2 text-slate-300 cursor-pointer">
                            <input type="checkbox" name="is_available" value="1" <?= $fac['is_available'] ? 'checked' : '' ?> class="rounded text-amber-500">
                            <span class="font-semibold text-white">Active in Booking Modal</span>
                        </label>
                        <button type="submit" class="bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold px-4 py-2 rounded-lg transition shadow">
                            Save Rates
                        </button>
                    </div>
                </form>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<?php
});
