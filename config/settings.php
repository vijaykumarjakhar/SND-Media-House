<?php
/**
 * SND Media — Dynamic Settings Registry & Loader
 * Canonical Settings Registry implementation for Phase 1
 */

require_once __DIR__ . '/database.php';

// In-memory runtime cache for system settings
$GLOBALS['__snd_system_settings_cache'] = null;

// Canonical default settings baseline matching approved client specifications
$DEFAULT_SETTINGS = [
    'site_name'             => 'SND Media',
    'site_domain'           => 'sndmedia.in',
    'site_tagline'          => 'Where Students Lead Media — Speak. Narrate. Discover.',
    'site_meta_description' => 'SND Media (sndmedia.in) — 24/7 Digital Newsroom, Official @sndschoolmedia Broadcasts & Commercial Production Studio Rentals at SND Public School Campus, Palwal.',
    'editions_text'         => 'Palwal • New Delhi • NCR • National',
    'yt_handle'             => '@sndschoolmedia',
    'yt_subscriber_label'   => '3.36K Subs • 666+ Videos',
    'social_youtube'        => 'https://www.youtube.com/@sndschoolmedia',
    'social_twitter'        => 'https://twitter.com/sndschoolmedia',
    'social_instagram'      => 'https://instagram.com/sndschoolmedia',
    'social_linkedin'       => 'https://linkedin.com/company/sndmedia',
    'social_facebook'       => 'https://facebook.com/sndschoolmedia',
    'hq_address'            => 'SND Public School, Palwal-Sohna Road, Village Meghpur, Palwal, Haryana — 121102',
    'phone_studio'          => '+91-7206622711',
    'phone_whatsapp'        => '+917206622711',
    'email_newsdesk'        => 'newsdesk@sndmedia.in',
    'email_space'           => 'space@sndmedia.in',
    'email_ads'             => 'ads@sndmedia.in',
    'studio_rate_display'   => 'Rates From ₹3,500/hr',
    'media_kit_pdf_url'     => '',
    'sensex_value'          => '84,210',
    'sensex_change'         => '+0.42%',
    'sensex_direction'      => 'up',
    'nifty_value'           => '25,830',
    'nifty_change'          => '+0.38%',
    'nifty_direction'       => 'up',
    'market_auto_feed'      => '1',
    'market_feed_status'    => 'cached',
    'ad_leaderboard_image'  => '',
    'ad_leaderboard_url'    => '',
    'ad_leaderboard_label'  => 'Advertise on SND Media',
    'ad_leaderboard_active' => '0',
    'ad_sidebar_image'      => '',
    'ad_sidebar_url'        => '',
    'ad_sidebar_label'      => 'Commercial Space Availability',
    'ad_sidebar_active'     => '0',
    'policy_privacy'        => '<p>At SND Media (operating at sndmedia.in from the SND Public School Campus, Palwal, Haryana), we respect your personal privacy. We collect client enquiry details strictly for studio booking follow-up, newsletter delivery, and editorial feedback. No customer information is sold or shared with unauthorized commercial third parties.</p>',
    'policy_tos'            => '<p>Welcome to SND Media. By using our website and video streaming services, you agree to comply with our code of conduct and copyright standards. All editorial articles, broadcast reports, and multimedia graphics are owned by SND Media Pvt. Ltd. and protected under Indian intellectual property laws.</p>',
    'policy_rental_rules'   => '<p>Commercial studio rentals (Studio A Newsroom and Studio B Podcast Suite) are managed under direct booking agreements. Crews must follow campus security rules at SND Public School, Palwal. Equipment checkouts, power backup usage, and acoustic protocols must be signed off with the Space Manager before and after every shoot session.</p>',
    'policy_editorial_ethics'=> '<p>SND Media operates under strict human editorial verification. Our student correspondents and senior editors cross-verify factual sources prior to publishing. We do not deploy automated generative AI to write unverified news reports. Corrections are addressed transparently by our Senior Newsroom Desk.</p>'
];

/**
 * Retrieve all settings as key-value associative array
 * @param bool $force_refresh
 * @return array
 */
function get_all_settings($force_refresh = false) {
    global $DEFAULT_SETTINGS;

    if ($GLOBALS['__snd_system_settings_cache'] !== null && !$force_refresh) {
        return $GLOBALS['__snd_system_settings_cache'];
    }

    $settings = $DEFAULT_SETTINGS;
    $pdo = get_db_connection();

    if ($pdo) {
        try {
            $stmt = $pdo->query("SELECT setting_key, setting_value FROM system_settings");
            while ($row = $stmt->fetch()) {
                $settings[$row['setting_key']] = $row['setting_value'];
            }
        } catch (Exception $e) {
            error_log("Settings Load Exception: " . $e->getMessage());
        }
    }

    $GLOBALS['__snd_system_settings_cache'] = $settings;
    return $settings;
}

/**
 * Retrieve a specific setting value
 * @param string $key
 * @param mixed $default
 * @return mixed
 */
function get_setting($key, $default = '') {
    global $DEFAULT_SETTINGS;
    $settings = get_all_settings();

    if (isset($settings[$key])) {
        return $settings[$key];
    }
    if (isset($DEFAULT_SETTINGS[$key])) {
        return $DEFAULT_SETTINGS[$key];
    }
    return $default;
}

/**
 * Update or insert a setting in system_settings
 * @param string $key
 * @param string $value
 * @return bool
 */
function update_setting($key, $value) {
    $pdo = get_db_connection();
    if (!$pdo) return false;

    try {
        $stmt = $pdo->prepare("
            INSERT INTO system_settings (setting_key, setting_value, updated_at) 
            VALUES (:key, :value, NOW())
            ON DUPLICATE KEY UPDATE setting_value = :value_update, updated_at = NOW()
        ");
        $success = $stmt->execute([
            ':key'          => $key,
            ':value'        => $value,
            ':value_update' => $value
        ]);
        if ($success && $GLOBALS['__snd_system_settings_cache'] !== null) {
            $GLOBALS['__snd_system_settings_cache'][$key] = $value;
        }
        return $success;
    } catch (Exception $e) {
        error_log("Failed to update setting {$key}: " . $e->getMessage());
        return false;
    }
}

