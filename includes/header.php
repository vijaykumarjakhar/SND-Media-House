<?php
/**
 * SND Media — Modular Header Component
 * Implements smooth responsive broadcast layout with zero multi-line wrap
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/settings.php';
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/seo.php';

$settings = get_all_settings();
$pdo = get_db_connection();

// Fetch active breaking tickers
$tickers = [];
if ($pdo) {
    try {
        $stmt = $pdo->query("SELECT * FROM breaking_ticker WHERE is_active = 1 ORDER BY sort_order ASC, id DESC LIMIT 10");
        $tickers = $stmt->fetchAll();
    } catch (Exception $e) {
        $tickers = [];
    }
}

if (empty($tickers)) {
    $tickers = [
        ['ticker_text' => 'CENTRE UNVEILS RS 1.2 LAKH CR HIGH-SPEED DIGITAL INFRASTRUCTURE CORRIDOR CONNECTING TIER-2 CITIES', 'link_url' => 'article.php?slug=centre-unveils-1-2-lakh-cr-digital-corridor', 'urgency_level' => 'breaking'],
        ['ticker_text' => "SND EXCLUSIVE: INSIDE INDIA'S NEXT-GEN EV MANUFACTURING GIGA-FACTORIES", 'link_url' => '#business', 'urgency_level' => 'flash'],
        ['ticker_text' => 'STUDIO SPACE NOTICE: SND SOUTH STUDIO 02 & PODCAST SUITE NOW OPEN FOR BOOKINGS', 'link_url' => 'studio-rental.php', 'urgency_level' => 'routine'],
        ['ticker_text' => 'GLOBAL MARKETS: ASIAN INDICES RALLY ON ROBUST MANUFACTURING DATA', 'link_url' => '#business', 'urgency_level' => 'routine']
    ];
}

$page_title = $page_title ?? 'National News & Media Network';
$active_nav = $active_nav ?? '';
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  
  <?php
    // Dynamic base path ensures relative assets & clean links resolve properly on all rewritten routes
    $script_dir = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');
    $base_href = (!empty($script_dir) ? $script_dir : '') . '/';
  ?>
  <base href="<?= htmlspecialchars($base_href, ENT_QUOTES, 'UTF-8') ?>">
  
  <?php 
    render_seo_tags([
        'title' => $page_title ?? null,
        'description' => $seo_desc ?? null,
        'type' => $seo_type ?? 'website',
        'article' => $seo_article ?? null,
        'canonical' => $canonical_url ?? null,
        'image' => $seo_image ?? null,
        'noindex' => $noindex ?? false,
        'breadcrumbs' => $breadcrumbs ?? null,
        'keywords' => $seo_keywords ?? null
    ]); 
  ?>

  <link rel="icon" type="image/svg+xml" href="assets/images/snd-logo.svg">
  <link rel="alternate icon" href="favicon.ico">
  <link rel="apple-touch-icon" href="assets/images/snd-logo.svg">

  <!-- High-Performance Pre-compiled Static Tailwind CSS (Zero Runtime Overhead) -->
  <link rel="stylesheet" href="assets/css/tailwind.min.css">
  <link rel="stylesheet" href="assets/css/custom.css">

  <!-- Google Fonts & FontAwesome 6 (Preconnected for Speed) -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@700;800;900&family=Merriweather:ital,wght@0,300;0,400;0,700;1,300&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <script src="assets/js/main.js" defer></script>

  <style>
    @keyframes ticker {
      0% { transform: translate3d(0, 0, 0); }
      100% { transform: translate3d(-50%, 0, 0); }
    }
    .animate-marquee {
      display: inline-flex;
      white-space: nowrap;
      animation: ticker 42s linear infinite;
    }
    .animate-marquee:hover {
      animation-play-state: paused;
    }
    .glassmorphism {
      background: rgba(7, 11, 20, 0.94);
      backdrop-filter: blur(12px);
      -webkit-backdrop-filter: blur(12px);
    }
    .custom-scrollbar::-webkit-scrollbar {
      width: 5px;
      height: 5px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
      background: #334155;
      border-radius: 4px;
    }
  </style>
</head>
<body class="bg-brand-dark text-slate-100 font-sans antialiased selection:bg-amber-500 selection:text-black">

  <!-- TOP UTILITY INFO BAR -->
  <div class="bg-slate-950 border-b border-slate-800 text-[11px] text-slate-300 py-1.5 px-4 overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between gap-3 flex-nowrap whitespace-nowrap min-w-0">
      
      <!-- Left: Location & Date -->
      <div class="flex items-center gap-3 shrink-0">
        <span class="flex items-center gap-1.5 text-amber-400 font-semibold">
          <i class="fa-solid fa-location-dot"></i> <?= sanitize_output($settings['editions_text'] ?? 'Palwal • New Delhi • NCR') ?>
        </span>
        <span class="hidden sm:inline text-slate-600">|</span>
        <span class="hidden sm:inline text-slate-400 font-medium">
          <i class="fa-regular fa-clock mr-1"></i> <?= date('l, d F Y') ?> IST
        </span>
      </div>

      <!-- Center: Stock Market Rates -->
      <div class="hidden lg:flex items-center gap-4 shrink-0 font-mono text-[10.5px]">
        <div class="flex items-center gap-1.5">
          <span class="text-slate-400">SENSEX</span>
          <span class="font-bold text-white"><?= sanitize_output($settings['sensex_value'] ?? '84,210') ?></span>
          <span class="<?= ($settings['sensex_direction'] ?? 'up') === 'up' ? 'text-emerald-400' : 'text-rose-400' ?> flex items-center text-[10px]">
            <i class="fa-solid <?= ($settings['sensex_direction'] ?? 'up') === 'up' ? 'fa-arrow-trend-up' : 'fa-arrow-trend-down' ?> mr-0.5"></i><?= sanitize_output($settings['sensex_change'] ?? '+0.42%') ?>
          </span>
        </div>
        <span class="text-slate-700">&bull;</span>
        <div class="flex items-center gap-1.5">
          <span class="text-slate-400">NIFTY 50</span>
          <span class="font-bold text-white"><?= sanitize_output($settings['nifty_value'] ?? '25,830') ?></span>
          <span class="<?= ($settings['nifty_direction'] ?? 'up') === 'up' ? 'text-emerald-400' : 'text-rose-400' ?> flex items-center text-[10px]">
            <i class="fa-solid <?= ($settings['nifty_direction'] ?? 'up') === 'up' ? 'fa-arrow-trend-up' : 'fa-arrow-trend-down' ?> mr-0.5"></i><?= sanitize_output($settings['nifty_change'] ?? '+0.38%') ?>
          </span>
        </div>
      </div>

      <!-- Right: Channel & Verification Badge -->
      <div class="flex items-center gap-3 shrink-0">
        <span class="hidden md:inline-flex items-center gap-1 text-[10px] text-amber-300 bg-amber-400/10 px-2 py-0.5 rounded border border-amber-400/20">
          <i class="fa-solid fa-shield-halved text-amber-400"></i> Human Verified Editorial Desk
        </span>
        <a href="<?= sanitize_output($settings['social_youtube'] ?? 'https://www.youtube.com/@sndschoolmedia') ?>" target="_blank" rel="noopener noreferrer" class="text-rose-400 hover:text-rose-300 flex items-center gap-1 font-semibold transition">
          <i class="fa-brands fa-youtube text-sm"></i> <span><?= sanitize_output($settings['yt_handle'] ?? '@sndschoolmedia') ?></span>
        </a>
      </div>

    </div>
  </div>

  <!-- MAIN STICKY HEADER -->
  <header class="sticky top-0 z-50 glassmorphism border-b border-slate-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex items-center justify-between py-3 gap-3 md:gap-4 flex-nowrap min-w-0">
        
        <!-- Logo & Brand -->
        <a href="./" class="flex items-center gap-2.5 sm:gap-3 group shrink-0 min-w-0">
          <img src="https://yt3.googleusercontent.com/SrQdaVJkuZ6tMqEnEEOeFdW1MWR_YVT7xPSXR_skO36sMI48AkqieclVh7TZRgd49ZePERdz=s160-c-k-c0x00ffffff-no-rj" onerror="this.onerror=null;this.src='assets/images/snd-logo.svg'" alt="SND Media Official Logo" class="w-10 h-10 sm:w-11 sm:h-11 rounded-full object-cover border-2 border-amber-400 bg-white shadow-lg shadow-amber-500/20 group-hover:scale-105 transition shrink-0">
          <div class="min-w-0">
            <div class="flex items-center gap-1.5 whitespace-nowrap">
              <span class="font-heading font-extrabold text-xl sm:text-2xl tracking-tight text-white group-hover:text-amber-300 transition"><span class="text-amber-400">SND</span> MEDIA</span>
            </div>
            <p class="hidden 2xl:block text-[10px] text-slate-400 font-medium tracking-wide whitespace-nowrap">SPEAK. NARRATE. DISCOVER. &bull; WHERE STUDENTS LEAD MEDIA</p>
          </div>
        </a>

        <!-- Desktop Navigation Links -->
        <nav class="hidden xl:flex items-center gap-4 2xl:gap-6 text-sm font-semibold text-slate-300 shrink-0 whitespace-nowrap">
          <a href="./#breaking" class="text-white hover:text-brand-red transition flex items-center gap-1.5 <?= $active_nav === 'live' ? 'text-brand-red' : '' ?>">
            <span class="w-2 h-2 rounded-full bg-brand-red animate-ping"></span> Live Desk
          </a>
          <a href="category/national" class="hover:text-brand-red transition <?= $active_nav === 'national' ? 'text-brand-red' : '' ?>">National</a>
          <a href="category/business" class="hover:text-brand-red transition <?= $active_nav === 'business' ? 'text-brand-red' : '' ?>">Business & Tech</a>
          <a href="./#youtube-hub" class="hover:text-brand-red transition flex items-center gap-1.5 text-rose-400 <?= $active_nav === 'youtube' ? 'text-rose-300' : '' ?>">
            <i class="fa-brands fa-youtube"></i> YouTube TV
          </a>
          <a href="studio-rental" class="hover:text-amber-300 transition flex items-center gap-1.5 text-amber-400 bg-amber-400/10 hover:bg-amber-400/20 px-3 py-1.5 rounded-full border border-amber-400/40 <?= $active_nav === 'rental' ? 'bg-amber-400/25 ring-1 ring-amber-400 text-amber-300' : '' ?>">
            <i class="fa-solid fa-building-columns"></i> Rent Studio & Ad Space
          </a>
          <a href="./#studio-showcase" class="hover:text-amber-300 transition flex items-center gap-1.5 text-slate-300 <?= $active_nav === 'showcase' ? 'text-amber-300' : '' ?>">
            <i class="fa-solid fa-photo-film text-amber-400"></i> Studio Showcase
          </a>
        </nav>

        <!-- Right Mobile Menu Button (Eliminated 'Book Studio Floor' button per request) -->
        <div class="flex items-center gap-2 shrink-0 whitespace-nowrap">
          <button id="mobile-menu-btn" onclick="toggleMobileMenu()" class="xl:hidden text-slate-300 hover:text-white p-2 text-xl focus:outline-none" aria-label="Toggle Menu">
            <i class="fa-solid fa-bars" id="mobile-menu-icon"></i>
          </button>
        </div>

      </div>
    </div>

    <!-- Responsive Mobile Navigation Drawer -->
    <div id="mobile-nav-drawer" class="hidden xl:hidden border-t border-slate-800 bg-slate-950/95 backdrop-blur px-4 py-4 transition-all duration-300">
      <nav class="flex flex-col gap-3 text-sm font-semibold text-slate-300">
        <a href="./#breaking" onclick="toggleMobileMenu()" class="text-white hover:text-brand-red flex items-center gap-2 py-1">
          <span class="w-2 h-2 rounded-full bg-brand-red"></span> Live Desk 24x7
        </a>
        <a href="category/national" onclick="toggleMobileMenu()" class="hover:text-brand-red py-1">National News & Politics</a>
        <a href="category/business" onclick="toggleMobileMenu()" class="hover:text-brand-red py-1">Business & Tech</a>
        <a href="./#youtube-hub" onclick="toggleMobileMenu()" class="hover:text-brand-red text-rose-400 flex items-center gap-2 py-1">
          <i class="fa-brands fa-youtube"></i> YouTube TV (@sndschoolmedia)
        </a>
        <a href="studio-rental" onclick="toggleMobileMenu()" class="text-amber-400 flex items-center gap-2 py-1">
          <i class="fa-solid fa-building-columns"></i> Rent Studio & Ad Space
        </a>
        <a href="./#studio-showcase" onclick="toggleMobileMenu()" class="hover:text-amber-300 flex items-center gap-2 py-1">
          <i class="fa-solid fa-photo-film text-amber-400"></i> Studio Showcase
        </a>
        <div class="pt-3 border-t border-slate-800 flex flex-col gap-2">
          <button onclick="toggleMobileMenu(); openRentalModal('Mobile Navigation')" class="w-full text-center bg-gradient-to-r from-amber-500 to-amber-600 text-slate-950 font-bold text-xs py-2.5 rounded-md shadow">
            <i class="fa-solid fa-key mr-1"></i> Book Studio Floor Inspection
          </button>
          <a href="tel:<?= sanitize_output($settings['phone_studio'] ?? '+917206622711') ?>" class="text-center text-xs text-slate-400 py-1 hover:text-white">
            <i class="fa-solid fa-phone mr-1 text-amber-400"></i> Studio Desk: <?= sanitize_output($settings['phone_studio'] ?? '+91-7206622711') ?>
          </a>
        </div>
      </nav>
    </div>
  </header>

  <!-- BREAKING NEWS MARQUEE TICKER (High-Impact Broadcast Red Banner) -->
  <div id="breaking" class="bg-brand-red text-white border-y border-rose-700 flex items-stretch overflow-hidden h-9 shadow-md">
    <div class="bg-brand-crimson text-white text-[11px] font-black uppercase tracking-wider px-4 flex items-center gap-2 shrink-0 z-10 shadow-lg">
      <span class="w-2.5 h-2.5 rounded-full bg-white animate-pulse"></span>
      <span>BREAKING 24x7</span>
    </div>
    <div class="overflow-hidden relative flex-1 flex items-center">
      <div id="ticker-marquee" class="animate-marquee text-xs font-semibold text-rose-100 flex items-center">
        <?php foreach ($tickers as $tk): ?>
          <a href="<?= sanitize_output($tk['link_url'] ?? '#') ?>" class="hover:text-amber-300 inline-flex items-center gap-2 mr-10 transition">
            <span class="text-amber-300 font-bold">&bull;</span>
            <strong class="text-white uppercase"><?= sanitize_output($tk['ticker_text']) ?></strong>
          </a>
        <?php endforeach; ?>
        <!-- Duplicate loop for smooth infinite marquee wrap -->
        <?php foreach ($tickers as $tk): ?>
          <a href="<?= sanitize_output($tk['link_url'] ?? '#') ?>" class="hover:text-amber-300 inline-flex items-center gap-2 mr-10 transition">
            <span class="text-amber-300 font-bold">&bull;</span>
            <strong class="text-white uppercase"><?= sanitize_output($tk['ticker_text']) ?></strong>
          </a>
        <?php endforeach; ?>
      </div>
    </div>
    <!-- Interactive Pause/Play Ticker Control -->
    <div class="hidden sm:flex items-center px-3 bg-brand-crimson/95 border-l border-rose-700 text-rose-200 hover:text-white text-xs shrink-0 z-10">
      <button type="button" onclick="toggleTickerPlay()" class="p-1 focus:outline-none flex items-center gap-1 cursor-pointer" title="Pause or Resume Ticker" aria-label="Pause or Resume Ticker">
        <i class="fa-solid fa-pause text-[10px]" id="ticker-play-icon"></i>
      </button>
    </div>
  </div>
