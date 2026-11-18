<?php
/**
 * SND Media — Studio Space CRM & Commercial Leads Manager
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/settings.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/layout.php';

require_role(['super_admin', 'space_manager']);

$pdo = get_db_connection();

// CSV Export Action
if (isset($_GET['action']) && $_GET['action'] === 'export_csv') {
    if (!csrf_token_validate($_GET['token'] ?? '')) {
        die('Invalid CSRF token.');
    }
    
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="snd_media_studio_leads_' . date('Ymd_His') . '.csv"');
    $output = fopen('php://output', 'w');
    fputcsv($output, ['ID', 'Client Name', 'Company', 'Email', 'Phone', 'Facility Needed', 'Date', 'Duration', 'Status', 'Received At', 'Notes']);

    if ($pdo) {
        $stmt = $pdo->query("SELECT * FROM rental_enquiries ORDER BY id DESC");
        while ($row = $stmt->fetch()) {
            fputcsv($output, [
                $row['id'], $row['client_name'], $row['company_name'], $row['email'], $row['phone'],
                $row['facility_needed'], $row['preferred_date'], $row['slot_duration'], $row['status'],
                $row['created_at'], $row['notes']
            ]);
        }
    }
    fclose($output);
    exit;
}

// Fetch Leads
$status_filter = trim($_GET['status'] ?? '');
$leads = [];
if ($pdo) {
    try {
        $sql = "SELECT * FROM rental_enquiries";
        $params = [];
        if (!empty($status_filter)) {
            $sql .= " WHERE status = :st";
            $params[':st'] = $status_filter;
        }
        $sql .= " ORDER BY id DESC LIMIT 100";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $leads = $stmt->fetchAll();
    } catch (Exception $e) {
        $leads = [];
    }
}

render_admin_layout('Studio Rental CRM & Commercial Leads', function() use ($leads, $status_filter) {
?>
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold text-white">Commercial Rental Enquiries</h2>
            <p class="text-xs text-slate-400 mt-0.5">Direct commercial leads generated from website booking modals and studio showcase pages.</p>
        </div>
        <div class="flex items-center gap-2.5">
            <a href="leads.php?action=export_csv&token=<?= csrf_token_generate() ?>" class="bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold px-3.5 py-2.5 rounded-xl border border-slate-700 transition flex items-center gap-1.5 shadow">
                <i class="fa-solid fa-file-csv text-emerald-400"></i> Export CSV
            </a>
        </div>
    </div>

    <!-- Filter Tabs -->
    <div class="flex flex-wrap gap-2 text-xs font-bold border-b border-slate-800 pb-3">
        <a href="leads.php" class="px-3 py-1.5 rounded-lg <?= empty($status_filter) ? 'bg-amber-500 text-slate-950 font-bold' : 'bg-slate-900 text-slate-400 hover:text-white' ?>">All Leads</a>
        <a href="leads.php?status=new" class="px-3 py-1.5 rounded-lg <?= $status_filter === 'new' ? 'bg-amber-500 text-slate-950 font-bold' : 'bg-slate-900 text-slate-400 hover:text-white' ?>">New Enquiries</a>
        <a href="leads.php?status=contacted" class="px-3 py-1.5 rounded-lg <?= $status_filter === 'contacted' ? 'bg-amber-500 text-slate-950 font-bold' : 'bg-slate-900 text-slate-400 hover:text-white' ?>">Contacted</a>
        <a href="leads.php?status=quoted" class="px-3 py-1.5 rounded-lg <?= $status_filter === 'quoted' ? 'bg-amber-500 text-slate-950 font-bold' : 'bg-slate-900 text-slate-400 hover:text-white' ?>">Quoted</a>
        <a href="leads.php?status=negotiating" class="px-3 py-1.5 rounded-lg <?= $status_filter === 'negotiating' ? 'bg-amber-500 text-slate-950 font-bold' : 'bg-slate-900 text-slate-400 hover:text-white' ?>">Negotiating</a>
        <a href="leads.php?status=booked" class="px-3 py-1.5 rounded-lg <?= $status_filter === 'booked' ? 'bg-amber-500 text-slate-950 font-bold' : 'bg-slate-900 text-slate-400 hover:text-white' ?>">Booked / Confirmed</a>
        <a href="leads.php?status=archived" class="px-3 py-1.5 rounded-lg <?= $status_filter === 'archived' ? 'bg-amber-500 text-slate-950 font-bold' : 'bg-slate-900 text-slate-400 hover:text-white' ?>">Archived</a>
    </div>

    <!-- Leads Table -->
    <div class="bg-brand-card rounded-2xl border border-slate-800 overflow-hidden shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-900 text-slate-400 uppercase text-[10px] font-bold tracking-wider border-b border-slate-800">
                    <tr>
                        <th class="py-3 px-4">Client &amp; Org</th>
                        <th class="py-3 px-4">Contact</th>
                        <th class="py-3 px-4">Facility Requested</th>
                        <th class="py-3 px-4">Shoot Date &amp; Duration</th>
                        <th class="py-3 px-4">Lead Status</th>
                        <th class="py-3 px-4">Received</th>
                        <th class="py-3 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800 text-slate-300">
                    <?php if (empty($leads)): ?>
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-500">
                                No commercial leads in this view.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($leads as $lead): ?>
                            <?php 
                            $clean_ph = preg_replace('/[^0-9]/', '', $lead['phone']);
                            if (strlen($clean_ph) === 10) $clean_ph = '91' . $clean_ph;
                            ?>
                            <tr class="hover:bg-slate-900/60 transition">
                                <td class="py-3 px-4">
                                    <strong class="text-white block font-bold text-xs"><?= sanitize_output($lead['client_name']) ?></strong>
                                    <?php if (!empty($lead['company_name'])): ?>
                                        <span class="text-slate-400 text-[11px]"><?= sanitize_output($lead['company_name']) ?></span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-3 px-4">
                                    <span class="block text-slate-200"><?= sanitize_output($lead['phone']) ?></span>
                                    <span class="block text-slate-400 text-[11px]"><?= sanitize_output($lead['email']) ?></span>
                                </td>
                                <td class="py-3 px-4">
                                    <span class="font-bold text-amber-300"><?= sanitize_output($lead['facility_needed']) ?></span>
                                    <?php if (!empty($lead['notes'])): ?>
                                        <p class="text-[10.5px] text-slate-400 italic mt-0.5 line-clamp-1" title="<?= sanitize_output($lead['notes']) ?>"><?= sanitize_output($lead['notes']) ?></p>
                                    <?php endif; ?>
                                </td>
                                <td class="py-3 px-4 text-[11px]">
                                    <span class="block text-slate-200"><?= $lead['preferred_date'] ? date('d M Y', strtotime($lead['preferred_date'])) : 'Flexible' ?></span>
                                    <span class="block text-slate-400"><?= sanitize_output($lead['slot_duration'] ?? 'Half Day') ?></span>
                                </td>
                                <td class="py-3 px-4">
                                    <select onchange="updateLeadStatus(<?= $lead['id'] ?>, this.value)" class="bg-slate-900 border border-slate-700 rounded px-2 py-1 text-[11px] font-bold text-white focus:outline-none focus:border-amber-400">
                                        <option value="new" <?= $lead['status'] === 'new' ? 'selected' : '' ?>>New</option>
                                        <option value="contacted" <?= $lead['status'] === 'contacted' ? 'selected' : '' ?>>Contacted</option>
                                        <option value="quoted" <?= $lead['status'] === 'quoted' ? 'selected' : '' ?>>Quoted</option>
                                        <option value="negotiating" <?= $lead['status'] === 'negotiating' ? 'selected' : '' ?>>Negotiating</option>
                                        <option value="booked" <?= $lead['status'] === 'booked' ? 'selected' : '' ?>>Booked</option>
                                        <option value="rejected" <?= $lead['status'] === 'rejected' ? 'selected' : '' ?>>Rejected</option>
                                        <option value="archived" <?= $lead['status'] === 'archived' ? 'selected' : '' ?>>Archived</option>
                                    </select>
                                </td>
                                <td class="py-3 px-4 text-slate-400 text-[11px] whitespace-nowrap">
                                    <?= time_ago($lead['created_at']) ?>
                                </td>
                                <td class="py-3 px-4 text-right whitespace-nowrap">
                                    <a href="https://wa.me/<?= $clean_ph ?>?text=<?= rawurlencode('Hello ' . $lead['client_name'] . ', this is the SND Media Space Leasing Desk regarding your enquiry for ' . $lead['facility_needed'] . '.') ?>" target="_blank" rel="noopener noreferrer" class="bg-emerald-600/20 hover:bg-emerald-600 text-emerald-400 hover:text-white px-2.5 py-1.5 rounded-lg transition inline-flex items-center gap-1 font-semibold text-[11px]" title="Chat on WhatsApp">
                                        <i class="fa-brands fa-whatsapp text-xs"></i> WhatsApp
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
async function updateLeadStatus(id, newStatus) {
    try {
        const formData = new FormData();
        formData.append('id', id);
        formData.append('status', newStatus);
        formData.append('csrf_token', '<?= csrf_token_generate() ?>');

        const res = await fetch('../api/update_lead_status.php', {
            method: 'POST',
            body: formData
        });
        const data = await res.json();
        if (data.status !== 'success') {
            alert(data.message || 'Error updating status');
        }
    } catch (e) {
        alert('Failed to update lead status over network.');
    }
}
</script>
<?php
});
