<?php
/**
 * SND Media — Legal Policies & Compliance Router
 * Handles Privacy Policy, Terms of Service, Rental Agreement Rules & Editorial Ethics Code
 */

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/settings.php';
require_once __DIR__ . '/includes/functions.php';

$policy_key = isset($_GET['policy']) ? trim($_GET['policy']) : (isset($_GET['page']) ? trim($_GET['page']) : 'privacy');

// Normalize policy aliases
$aliases = [
    'privacy-policy' => 'privacy',
    'terms-of-service' => 'tos',
    'terms' => 'tos',
    'rental-rules' => 'rental-rules',
    'rental' => 'rental-rules',
    'editorial-ethics' => 'editorial-ethics',
    'ethics' => 'editorial-ethics'
];

if (isset($aliases[$policy_key])) {
    $policy_key = $aliases[$policy_key];
}

$policy_configs = [
    'privacy' => [
        'title' => 'Privacy Policy',
        'setting_key' => 'policy_privacy',
        'icon' => 'fa-shield-halved',
        'subtitle' => 'How SND Media collects, protects, and handles subscriber and studio rental data.'
    ],
    'tos' => [
        'title' => 'Terms of Service',
        'setting_key' => 'policy_tos',
        'icon' => 'fa-file-contract',
        'subtitle' => 'Rules and terms governing digital news consumption, comments, and media streaming.'
    ],
    'rental-rules' => [
        'title' => 'Commercial Rental Rules & Agreement',
        'setting_key' => 'policy_rental_rules',
        'icon' => 'fa-building-shield',
        'subtitle' => 'Campus safety, equipment security, acoustic guidelines, and booking deposit policies.'
    ],
    'editorial-ethics' => [
        'title' => 'Editorial Ethics & Human Verification Code',
        'setting_key' => 'policy_editorial_ethics',
        'icon' => 'fa-scale-balanced',
        'subtitle' => 'Our commitment to 100% human-verified reporting, fact-checking, and zero unverified AI news.'
    ]
];

$active_policy = $policy_configs[$policy_key] ?? $policy_configs['privacy'];
$policy_content = get_setting($active_policy['setting_key']);

$page_title = $active_policy['title'] . ' | Compliance';
$seo_desc = $active_policy['subtitle'];
$active_nav = 'legal';

require_once __DIR__ . '/includes/header.php';
?>

<main class="max-w-4xl mx-auto px-4 py-10">

  <!-- Policy Header -->
  <div class="mb-8">
    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-slate-800 text-amber-400 text-xs font-bold uppercase tracking-wider mb-3">
      <i class="fa-solid <?= $active_policy['icon'] ?>"></i> Legal &amp; Governance Desk
    </div>
    <h1 class="text-3xl sm:text-4xl font-extrabold text-white font-heading">
      <?= sanitize_output($active_policy['title']) ?>
    </h1>
    <p class="text-sm text-slate-400 mt-2">
      <?= sanitize_output($active_policy['subtitle']) ?>
    </p>
    <div class="text-xs text-slate-500 mt-2">
      Last Revised &bull; SND Public School Campus, Palwal, Haryana &bull; Effective Phase 1 (2026)
    </div>
  </div>

  <!-- Policy Navigation Tabs -->
  <div class="flex flex-wrap gap-2 mb-8 border-b border-slate-800 pb-4 text-xs font-bold">
    <?php foreach ($policy_configs as $k => $cfg): ?>
      <a href="legal/<?= $k ?>" class="px-3.5 py-2 rounded-lg transition flex items-center gap-1.5 <?= $k === $policy_key ? 'bg-amber-500 text-slate-950 shadow' : 'bg-slate-900 text-slate-300 hover:bg-slate-800 hover:text-white border border-slate-800' ?>">
        <i class="fa-solid <?= $cfg['icon'] ?>"></i>
        <span><?= sanitize_output($cfg['title']) ?></span>
      </a>
    <?php endforeach; ?>
  </div>

  <!-- Policy Text Body -->
  <div class="bg-brand-card p-6 sm:p-10 rounded-2xl border border-slate-800 shadow-xl prose prose-invert max-w-none text-slate-200 text-sm leading-relaxed font-sans">
    <?= sanitize_rich_html($policy_content) ?>
  </div>

  <!-- Contact & Legal Inquiries -->
  <div class="mt-8 p-5 bg-slate-950 rounded-xl border border-slate-800 text-xs text-slate-400 flex flex-col sm:flex-row items-center justify-between gap-4">
    <div>
      <h4 class="font-bold text-white">Questions regarding this policy?</h4>
      <p class="mt-0.5">Contact our Compliance &amp; Legal Desk at SND Public School Campus, Palwal.</p>
    </div>
    <a href="mailto:<?= sanitize_output($settings['email_newsdesk'] ?? 'newsdesk@sndmedia.in') ?>" class="bg-slate-800 hover:bg-slate-700 text-slate-200 px-4 py-2 rounded-lg border border-slate-700 font-semibold transition shrink-0">
      Contact Legal Desk
    </a>
  </div>

</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
