<?php
/**
 * SND Media — Global Site Settings Panel
 * Phase 1 Production Architecture (US-23)
 */

require_once __DIR__ . '/layout.php';

// Super Admin Only
require_role('super_admin');

$pdo = get_db_connection();
$settings = get_all_settings(true);

// Process Settings Save
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '';
    if (!csrf_token_validate($token)) {
        flash_set('error', 'CSRF verification failed.');
        header('Location: settings.php');
        exit;
    }

    // Handle PDF Upload if provided
    if (isset($_FILES['media_kit_pdf']) && $_FILES['media_kit_pdf']['error'] === UPLOAD_ERR_OK) {
        $upload_res = handle_file_upload($_FILES['media_kit_pdf'], 'docs', ['application/pdf'], 15);
        if ($upload_res['success']) {
            $_POST['media_kit_pdf_url'] = $upload_res['url'];
        } else {
            flash_set('error', 'PDF Upload failed: ' . $upload_res['error']);
        }
    } elseif (isset($_POST['remove_pdf']) && $_POST['remove_pdf'] == '1') {
        $_POST['media_kit_pdf_url'] = '';
    }

    // Validate WhatsApp format if filled
    if (!empty($_POST['phone_whatsapp']) && !preg_match('/^\+?[1-9]\d{7,14}$/', trim($_POST['phone_whatsapp']))) {
        flash_set('error', 'WhatsApp phone number should follow international format (e.g. +917206622711).');
    } else {
        // Defined registry keys that can be updated from this form
        $allowed_keys = [
            'site_name', 'site_domain', 'site_tagline', 'site_meta_description', 'editions_text',
            'yt_handle', 'yt_subscriber_label', 'social_youtube', 'social_twitter', 'social_instagram', 'social_linkedin', 'social_facebook',
            'hq_address', 'phone_studio', 'phone_whatsapp', 'email_newsdesk', 'email_space', 'email_ads',
            'studio_rate_display', 'media_kit_pdf_url',
            'sensex_value', 'sensex_change', 'sensex_direction', 'nifty_value', 'nifty_change', 'nifty_direction', 'market_auto_feed',
            'ad_leaderboard_image', 'ad_leaderboard_url', 'ad_leaderboard_label', 'ad_leaderboard_active',
            'ad_sidebar_image', 'ad_sidebar_url', 'ad_sidebar_label', 'ad_sidebar_active',
            'policy_privacy', 'policy_tos', 'policy_rental_rules', 'policy_editorial_ethics'
        ];

        // Handle checkbox toggles that might be absent from POST
        $_POST['market_auto_feed'] = isset($_POST['market_auto_feed']) ? '1' : '0';
        $_POST['ad_leaderboard_active'] = isset($_POST['ad_leaderboard_active']) ? '1' : '0';
        $_POST['ad_sidebar_active'] = isset($_POST['ad_sidebar_active']) ? '1' : '0';

        foreach ($allowed_keys as $key) {
            if (isset($_POST[$key])) {
                $raw_val = $_POST[$key];
                // Rich HTML for legal policies, plain text sanitize for others
                if (strpos($key, 'policy_') === 0) {
                    $val = sanitize_rich_html($raw_val);
                } else {
                    $val = trim($raw_val);
                }
                update_setting($key, $val);
            }
        }

        flash_set('success', 'Global site settings updated successfully.');
        header('Location: settings.php');
        exit;
    }
}

// Refresh settings after potential post
$settings = get_all_settings(true);

render_admin_header('Global Site Settings', 'settings');
?>

<div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <h2 class="text-xl font-bold text-white">System Settings & Registry</h2>
        <p class="text-xs text-slate-400 mt-0.5">Control site branding, contact details, financial ticker feeds, and commercial policy pages without touching code.</p>
    </div>
    <div class="flex items-center gap-2">
        <a href="settings.php" class="bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs px-3 py-2 rounded-xl border border-slate-700 transition">
            <i class="fa-solid fa-rotate-right mr-1"></i> Reload
        </a>
    </div>
</div>

<form method="POST" action="settings.php" enctype="multipart/form-data" class="space-y-8">
    <?= csrf_field() ?>

    <!-- SECTION 1: Utility Bar, Site Meta & Editions -->
    <div class="bg-brand-card border border-slate-800 rounded-2xl p-6 shadow-xl">
        <div class="flex items-center gap-2.5 mb-5 pb-3 border-b border-slate-800">
            <div class="w-7 h-7 rounded-lg bg-blue-500/10 text-blue-400 flex items-center justify-center text-xs">
                <i class="fa-solid fa-window-maximize"></i>
            </div>
            <div>
                <h3 class="font-bold text-sm text-white">1. Utility Bar, Site Meta & Editions</h3>
                <p class="text-[11px] text-slate-400">Controls top broadcast bar and meta description tags</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
            <div>
                <label class="block font-semibold text-slate-300 mb-1">Site Brand Name</label>
                <input type="text" name="site_name" value="<?= sanitize_output($settings['site_name']) ?>" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-white focus:outline-none focus:border-amber-400">
            </div>
            <div>
                <label class="block font-semibold text-slate-300 mb-1">Canonical Domain</label>
                <input type="text" name="site_domain" value="<?= sanitize_output($settings['site_domain']) ?>" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-white focus:outline-none focus:border-amber-400">
            </div>
            <div>
                <label class="block font-semibold text-slate-300 mb-1">City Editions Text</label>
                <input type="text" name="editions_text" value="<?= sanitize_output($settings['editions_text']) ?>" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-white focus:outline-none focus:border-amber-400">
            </div>
            <div>
                <label class="block font-semibold text-slate-300 mb-1">YouTube Subscriber Label</label>
                <input type="text" name="yt_subscriber_label" value="<?= sanitize_output($settings['yt_subscriber_label']) ?>" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-white focus:outline-none focus:border-amber-400">
            </div>
            <div class="md:col-span-2">
                <label class="block font-semibold text-slate-300 mb-1">Default SEO Meta Description</label>
                <textarea name="site_meta_description" rows="2" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-white focus:outline-none focus:border-amber-400"><?= sanitize_output($settings['site_meta_description']) ?></textarea>
            </div>
        </div>
    </div>

    <!-- SECTION 2: Financial Market Data -->
    <div class="bg-brand-card border border-slate-800 rounded-2xl p-6 shadow-xl">
        <div class="flex items-center justify-between mb-5 pb-3 border-b border-slate-800">
            <div class="flex items-center gap-2.5">
                <div class="w-7 h-7 rounded-lg bg-emerald-500/10 text-emerald-400 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-arrow-trend-up"></i>
                </div>
                <div>
                    <h3 class="font-bold text-sm text-white">2. Financial Market Data (SENSEX & NIFTY)</h3>
                    <p class="text-[11px] text-slate-400">Configure real-time or editorial fallback values with instant live preview</p>
                </div>
            </div>
            <label class="inline-flex items-center gap-2 cursor-pointer text-xs">
                <input type="checkbox" name="market_auto_feed" value="1" <?= $settings['market_auto_feed'] == '1' ? 'checked' : '' ?> class="rounded bg-slate-900 border-slate-700 text-amber-500">
                <span class="text-slate-300 font-semibold">Enable Free Auto-Feed API</span>
            </label>
        </div>

        <!-- Live Ticker Bar Preview -->
        <div class="mb-5 bg-slate-950 p-3 rounded-xl border border-slate-800 flex items-center gap-4 text-xs font-mono">
            <span class="text-slate-500 text-[10px] uppercase font-bold tracking-widest font-sans">Live Preview:</span>
            <div id="preview-sensex" class="flex items-center gap-1 <?= $settings['sensex_direction'] === 'down' ? 'text-rose-400' : 'text-emerald-400' ?>">
                <i class="fa-solid <?= $settings['sensex_direction'] === 'down' ? 'fa-arrow-trend-down' : 'fa-arrow-trend-up' ?>"></i>
                <span>SENSEX: <span id="pv-sensex-val"><?= sanitize_output($settings['sensex_value']) ?></span> (<span id="pv-sensex-chg"><?= sanitize_output($settings['sensex_change']) ?></span>)</span>
            </div>
            <span class="text-slate-700">|</span>
            <div id="preview-nifty" class="flex items-center gap-1 <?= $settings['nifty_direction'] === 'down' ? 'text-rose-400' : 'text-emerald-400' ?>">
                <i class="fa-solid <?= $settings['nifty_direction'] === 'down' ? 'fa-arrow-trend-down' : 'fa-arrow-trend-up' ?>"></i>
                <span>NIFTY: <span id="pv-nifty-val"><?= sanitize_output($settings['nifty_value']) ?></span> (<span id="pv-nifty-chg"><?= sanitize_output($settings['nifty_change']) ?></span>)</span>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
            <div>
                <label class="block font-semibold text-slate-300 mb-1">SENSEX Value</label>
                <input type="text" id="inp-sensex-val" name="sensex_value" value="<?= sanitize_output($settings['sensex_value']) ?>" oninput="updateMarketPreview()" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-white focus:outline-none focus:border-amber-400">
            </div>
            <div>
                <label class="block font-semibold text-slate-300 mb-1">SENSEX % Change</label>
                <input type="text" id="inp-sensex-chg" name="sensex_change" value="<?= sanitize_output($settings['sensex_change']) ?>" oninput="updateMarketPreview()" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-white focus:outline-none focus:border-amber-400">
            </div>
            <div>
                <label class="block font-semibold text-slate-300 mb-1">SENSEX Direction</label>
                <div class="flex gap-4 pt-2">
                    <label class="inline-flex items-center gap-1.5 cursor-pointer text-emerald-400">
                        <input type="radio" name="sensex_direction" value="up" <?= $settings['sensex_direction'] === 'up' ? 'checked' : '' ?> onchange="updateMarketPreview()"> Up (&uarr;)
                    </label>
                    <label class="inline-flex items-center gap-1.5 cursor-pointer text-rose-400">
                        <input type="radio" name="sensex_direction" value="down" <?= $settings['sensex_direction'] === 'down' ? 'checked' : '' ?> onchange="updateMarketPreview()"> Down (&darr;)
                    </label>
                </div>
            </div>

            <div>
                <label class="block font-semibold text-slate-300 mb-1">NIFTY Value</label>
                <input type="text" id="inp-nifty-val" name="nifty_value" value="<?= sanitize_output($settings['nifty_value']) ?>" oninput="updateMarketPreview()" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-white focus:outline-none focus:border-amber-400">
            </div>
            <div>
                <label class="block font-semibold text-slate-300 mb-1">NIFTY % Change</label>
                <input type="text" id="inp-nifty-chg" name="nifty_change" value="<?= sanitize_output($settings['nifty_change']) ?>" oninput="updateMarketPreview()" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-white focus:outline-none focus:border-amber-400">
            </div>
            <div>
                <label class="block font-semibold text-slate-300 mb-1">NIFTY Direction</label>
                <div class="flex gap-4 pt-2">
                    <label class="inline-flex items-center gap-1.5 cursor-pointer text-emerald-400">
                        <input type="radio" name="nifty_direction" value="up" <?= $settings['nifty_direction'] === 'up' ? 'checked' : '' ?> onchange="updateMarketPreview()"> Up (&uarr;)
                    </label>
                    <label class="inline-flex items-center gap-1.5 cursor-pointer text-rose-400">
                        <input type="radio" name="nifty_direction" value="down" <?= $settings['nifty_direction'] === 'down' ? 'checked' : '' ?> onchange="updateMarketPreview()"> Down (&darr;)
                    </label>
                </div>
            </div>
        </div>
    </div>

    <!-- SECTION 3: Contact & Offices -->
    <div class="bg-brand-card border border-slate-800 rounded-2xl p-6 shadow-xl">
        <div class="flex items-center gap-2.5 mb-5 pb-3 border-b border-slate-800">
            <div class="w-7 h-7 rounded-lg bg-amber-500/10 text-amber-400 flex items-center justify-center text-xs">
                <i class="fa-solid fa-building"></i>
            </div>
            <div>
                <h3 class="font-bold text-sm text-white">3. Contact Details & Official Desks</h3>
                <p class="text-[11px] text-slate-400">Palwal campus headquarters address, phones, and official desk emails</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">
            <div class="md:col-span-3">
                <label class="block font-semibold text-slate-300 mb-1">Headquarters Campus Address</label>
                <input type="text" name="hq_address" value="<?= sanitize_output($settings['hq_address']) ?>" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-white focus:outline-none focus:border-amber-400">
            </div>
            <div>
                <label class="block font-semibold text-slate-300 mb-1">Studio / Desk Phone</label>
                <input type="text" name="phone_studio" value="<?= sanitize_output($settings['phone_studio']) ?>" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-white focus:outline-none focus:border-amber-400">
            </div>
            <div>
                <label class="block font-semibold text-slate-300 mb-1">WhatsApp Closing Number</label>
                <input type="text" name="phone_whatsapp" value="<?= sanitize_output($settings['phone_whatsapp']) ?>" placeholder="+917206622711" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-white focus:outline-none focus:border-amber-400">
            </div>
            <div>
                <label class="block font-semibold text-slate-300 mb-1">Starting Studio Rate Display</label>
                <input type="text" name="studio_rate_display" value="<?= sanitize_output($settings['studio_rate_display']) ?>" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-white focus:outline-none focus:border-amber-400">
            </div>
            <div>
                <label class="block font-semibold text-slate-300 mb-1">News Desk Email</label>
                <input type="email" name="email_newsdesk" value="<?= sanitize_output($settings['email_newsdesk']) ?>" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-white focus:outline-none focus:border-amber-400">
            </div>
            <div>
                <label class="block font-semibold text-slate-300 mb-1">Studio Leasing Desk Email</label>
                <input type="email" name="email_space" value="<?= sanitize_output($settings['email_space']) ?>" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-white focus:outline-none focus:border-amber-400">
            </div>
            <div>
                <label class="block font-semibold text-slate-300 mb-1">Advertising & Sponsorships Email</label>
                <input type="email" name="email_ads" value="<?= sanitize_output($settings['email_ads']) ?>" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-white focus:outline-none focus:border-amber-400">
            </div>
        </div>
    </div>

    <!-- SECTION 4: Social Media Channels -->
    <div class="bg-brand-card border border-slate-800 rounded-2xl p-6 shadow-xl">
        <div class="flex items-center gap-2.5 mb-5 pb-3 border-b border-slate-800">
            <div class="w-7 h-7 rounded-lg bg-rose-500/10 text-rose-400 flex items-center justify-center text-xs">
                <i class="fa-solid fa-share-nodes"></i>
            </div>
            <div>
                <h3 class="font-bold text-sm text-white">4. Social Media Channels</h3>
                <p class="text-[11px] text-slate-400">Official broadcast channel and network profile links</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
            <div>
                <label class="block font-semibold text-slate-300 mb-1">YouTube Channel URL</label>
                <input type="url" name="social_youtube" value="<?= sanitize_output($settings['social_youtube']) ?>" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-white focus:outline-none focus:border-amber-400">
            </div>
            <div>
                <label class="block font-semibold text-slate-300 mb-1">YouTube Handle Label</label>
                <input type="text" name="yt_handle" value="<?= sanitize_output($settings['yt_handle']) ?>" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-white focus:outline-none focus:border-amber-400">
            </div>
            <div>
                <label class="block font-semibold text-slate-300 mb-1">Twitter / X Profile</label>
                <input type="url" name="social_twitter" value="<?= sanitize_output($settings['social_twitter']) ?>" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-white focus:outline-none focus:border-amber-400">
            </div>
            <div>
                <label class="block font-semibold text-slate-300 mb-1">Instagram Profile</label>
                <input type="url" name="social_instagram" value="<?= sanitize_output($settings['social_instagram']) ?>" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-white focus:outline-none focus:border-amber-400">
            </div>
            <div>
                <label class="block font-semibold text-slate-300 mb-1">LinkedIn Page</label>
                <input type="url" name="social_linkedin" value="<?= sanitize_output($settings['social_linkedin']) ?>" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-white focus:outline-none focus:border-amber-400">
            </div>
            <div>
                <label class="block font-semibold text-slate-300 mb-1">Facebook Page</label>
                <input type="url" name="social_facebook" value="<?= sanitize_output($settings['social_facebook']) ?>" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-white focus:outline-none focus:border-amber-400">
            </div>
        </div>
    </div>

    <!-- SECTION 5: Media Kit PDF Upload & Rate Card -->
    <div class="bg-brand-card border border-slate-800 rounded-2xl p-6 shadow-xl">
        <div class="flex items-center gap-2.5 mb-5 pb-3 border-b border-slate-800">
            <div class="w-7 h-7 rounded-lg bg-purple-500/10 text-purple-400 flex items-center justify-center text-xs">
                <i class="fa-solid fa-file-pdf"></i>
            </div>
            <div>
                <h3 class="font-bold text-sm text-white">5. Commercial Media Kit & Rate Card PDF</h3>
                <p class="text-[11px] text-slate-400">Upload or link the official studio booking rate card PDF</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
            <div>
                <label class="block font-semibold text-slate-300 mb-1">Upload New PDF File</label>
                <input type="file" name="media_kit_pdf" accept="application/pdf" class="w-full text-slate-400 text-xs file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-slate-800 file:text-slate-200 hover:file:bg-slate-700">
            </div>
            <div>
                <label class="block font-semibold text-slate-300 mb-1">Current Media Kit URL</label>
                <div class="flex items-center gap-2">
                    <input type="text" name="media_kit_pdf_url" value="<?= sanitize_output($settings['media_kit_pdf_url']) ?>" placeholder="assets/uploads/docs/rate_card.pdf" class="flex-1 bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-white focus:outline-none focus:border-amber-400">
                    <?php if (!empty($settings['media_kit_pdf_url'])): ?>
                    <button type="submit" name="remove_pdf" value="1" class="bg-rose-950 text-rose-300 hover:bg-rose-900 border border-rose-800/50 px-3 py-2 rounded-xl text-xs font-semibold">
                        Remove
                    </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- SECTION 6: Legal & Compliance Policy Pages -->
    <div class="bg-brand-card border border-slate-800 rounded-2xl p-6 shadow-xl">
        <div class="flex items-center gap-2.5 mb-5 pb-3 border-b border-slate-800">
            <div class="w-7 h-7 rounded-lg bg-teal-500/10 text-teal-400 flex items-center justify-center text-xs">
                <i class="fa-solid fa-scale-balanced"></i>
            </div>
            <div>
                <h3 class="font-bold text-sm text-white">6. Legal & Policy Content Pages (/legal/*)</h3>
                <p class="text-[11px] text-slate-400">Managed rich HTML text for public compliance routes</p>
            </div>
        </div>

        <div class="space-y-5 text-xs">
            <div>
                <div class="flex items-center justify-between mb-1">
                    <label class="font-semibold text-slate-300">Privacy Policy (/legal/privacy-policy)</label>
                    <a href="../legal.php?page=privacy-policy" target="_blank" class="text-amber-400 hover:underline text-[11px]">Preview Page ↗</a>
                </div>
                <textarea name="policy_privacy" rows="4" class="w-full bg-slate-900 border border-slate-700 rounded-xl p-3 text-white font-mono text-[11px] focus:outline-none focus:border-amber-400"><?= sanitize_output($settings['policy_privacy']) ?></textarea>
            </div>

            <div>
                <div class="flex items-center justify-between mb-1">
                    <label class="font-semibold text-slate-300">Terms of Service (/legal/terms-of-service)</label>
                    <a href="../legal.php?page=terms-of-service" target="_blank" class="text-amber-400 hover:underline text-[11px]">Preview Page ↗</a>
                </div>
                <textarea name="policy_tos" rows="4" class="w-full bg-slate-900 border border-slate-700 rounded-xl p-3 text-white font-mono text-[11px] focus:outline-none focus:border-amber-400"><?= sanitize_output($settings['policy_tos']) ?></textarea>
            </div>

            <div>
                <div class="flex items-center justify-between mb-1">
                    <label class="font-semibold text-slate-300">Studio Rental Rules & Regulations (/legal/rental-rules)</label>
                    <a href="../legal.php?page=rental-rules" target="_blank" class="text-amber-400 hover:underline text-[11px]">Preview Page ↗</a>
                </div>
                <textarea name="policy_rental_rules" rows="4" class="w-full bg-slate-900 border border-slate-700 rounded-xl p-3 text-white font-mono text-[11px] focus:outline-none focus:border-amber-400"><?= sanitize_output($settings['policy_rental_rules']) ?></textarea>
            </div>

            <div>
                <div class="flex items-center justify-between mb-1">
                    <label class="font-semibold text-slate-300">Editorial Ethics Code (/legal/editorial-ethics)</label>
                    <a href="../legal.php?page=editorial-ethics" target="_blank" class="text-amber-400 hover:underline text-[11px]">Preview Page ↗</a>
                </div>
                <textarea name="policy_editorial_ethics" rows="4" class="w-full bg-slate-900 border border-slate-700 rounded-xl p-3 text-white font-mono text-[11px] focus:outline-none focus:border-amber-400"><?= sanitize_output($settings['policy_editorial_ethics']) ?></textarea>
            </div>
        </div>
    </div>

    <!-- Sticky Save Action Bar -->
    <div class="sticky bottom-4 z-20 bg-slate-900/90 backdrop-blur border border-slate-800 rounded-2xl p-4 shadow-2xl flex items-center justify-between gap-4">
        <div class="text-xs text-slate-400">
            <i class="fa-solid fa-cloud-arrow-up text-amber-400 mr-1.5"></i> Settings changes take effect immediately across all public routes.
        </div>
        <button type="submit" class="bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-slate-950 font-bold text-xs px-6 py-2.5 rounded-xl shadow-lg shadow-amber-500/20 transition transform active:scale-95">
            Save All Settings
        </button>
    </div>
</form>

<script>
function updateMarketPreview() {
    const sVal = document.getElementById('inp-sensex-val').value;
    const sChg = document.getElementById('inp-sensex-chg').value;
    const sDir = document.querySelector('input[name="sensex_direction"]:checked')?.value || 'up';

    const nVal = document.getElementById('inp-nifty-val').value;
    const nChg = document.getElementById('inp-nifty-chg').value;
    const nDir = document.querySelector('input[name="nifty_direction"]:checked')?.value || 'up';

    document.getElementById('pv-sensex-val').innerText = sVal;
    document.getElementById('pv-sensex-chg').innerText = sChg;
    document.getElementById('preview-sensex').className = 'flex items-center gap-1 ' + (sDir === 'down' ? 'text-rose-400' : 'text-emerald-400');
    document.getElementById('preview-sensex').querySelector('i').className = 'fa-solid ' + (sDir === 'down' ? 'fa-arrow-trend-down' : 'fa-arrow-trend-up');

    document.getElementById('pv-nifty-val').innerText = nVal;
    document.getElementById('pv-nifty-chg').innerText = nChg;
    document.getElementById('preview-nifty').className = 'flex items-center gap-1 ' + (nDir === 'down' ? 'text-rose-400' : 'text-emerald-400');
    document.getElementById('preview-nifty').querySelector('i').className = 'fa-solid ' + (nDir === 'down' ? 'fa-arrow-trend-down' : 'fa-arrow-trend-up');
}
</script>

<?php
render_admin_footer();

