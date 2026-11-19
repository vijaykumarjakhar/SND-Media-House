<?php
/**
 * SND Media — Primary Public Portal (Homepage)
 * Broadcast newsroom, official YouTube TV network & commercial studio facilities
 */

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/settings.php';
require_once __DIR__ . '/includes/functions.php';

$settings = get_all_settings();
$pdo = get_db_connection();

// 1. Fetch Hero Lead Article
$hero_article = null;
$secondary_articles = [];
$grid_articles = [];

if ($pdo) {
    try {
        // Hero article
        $stmt = $pdo->query("SELECT * FROM articles WHERE status = 'published' AND is_hero_lead = 1 ORDER BY published_at DESC LIMIT 1");
        $hero_article = $stmt->fetch();

        // If no hero designated, grab latest published
        if (!$hero_article) {
            $stmt = $pdo->query("SELECT * FROM articles WHERE status = 'published' ORDER BY published_at DESC LIMIT 1");
            $hero_article = $stmt->fetch();
        }

        // Secondary lead stories (exclude hero)
        $hero_id = $hero_article ? $hero_article['id'] : 0;
        $stmt = $pdo->prepare("SELECT * FROM articles WHERE status = 'published' AND id != :hid ORDER BY published_at DESC LIMIT 3");
        $stmt->execute([':hid' => $hero_id]);
        $secondary_articles = $stmt->fetchAll();

        // More articles for grid & continuous news desk feed
        $exclude_ids = array_merge([$hero_id], array_column($secondary_articles, 'id'));
        $in_clause = implode(',', array_map('intval', $exclude_ids));
        $stmt = $pdo->query("SELECT * FROM articles WHERE status = 'published' AND id NOT IN ($in_clause) ORDER BY published_at DESC LIMIT 20");
        $grid_articles = $stmt->fetchAll();

        // Feed articles (all published)
        $feedStmt = $pdo->query("SELECT * FROM articles WHERE status = 'published' ORDER BY published_at DESC LIMIT 24");
        $feed_articles = $feedStmt->fetchAll();
    } catch (Exception $e) {
        // Fallback handled below
    }
}

// Fallback data if DB is empty or offline
if (!$hero_article) {
    $hero_article = [
        'id' => 1,
        'title' => 'Centre Unveils ₹1.2 Lakh Cr Digital Corridor Connecting Tier-2 Industrial Hubs',
        'slug' => 'centre-unveils-1-2-lakh-cr-digital-corridor',
        'category' => 'national',
        'subcategory' => 'Infrastructure',
        'read_time' => '4 MIN READ',
        'published_at' => date('Y-m-d H:i:s'),
        'author_name' => 'SND Investigative Bureau',
        'featured_image' => 'https://images.unsplash.com/photo-1570168007204-dfb528c6958f?auto=format&fit=crop&w=1400&q=85',
        'summary' => 'The union cabinet today sanctioned an ambitious multi-modal fiber and highway network linking 40 emerging industrial clusters directly to dedicated freight lines.',
        'highlight_1' => '₹1.2 Lakh Cr Outlay: Key connectivity lines identified around NCR, Bengaluru, Mumbai-Pune, and Hyderabad peripheral corridors.',
        'highlight_2' => 'Zero-Latency Fiber Mesh: Over 24,000 km of carrier-grade optical highway to boost AI and semiconductor manufacturing clusters.',
        'highlight_3' => 'Public-Private Consortiums: Execution slated in three fast-track phases under Gati Shakti oversight.'
    ];
}

if (empty($secondary_articles)) {
    $secondary_articles = [
        [
            'id' => 2,
            'title' => 'GST Reform: Court Approval May Be Required Before Taxpayer Arrests',
            'slug' => 'gst-reform-court-approval-before-arrests',
            'category' => 'business',
            'subcategory' => 'Tax Policy',
            'read_time' => '3 MIN READ',
            'published_at' => date('Y-m-d H:i:s', strtotime('-2 hours')),
            'author_name' => 'Senior Business Desk',
            'featured_image' => 'https://img.youtube.com/vi/25tvHivopPE/hqdefault.jpg'
        ],
        [
            'id' => 3,
            'title' => 'Palwal District Badminton Championship 2026 Concludes at SND Academy',
            'slug' => 'palwal-district-badminton-championship-2026',
            'category' => 'national',
            'subcategory' => 'Sports',
            'read_time' => '2 MIN READ',
            'published_at' => date('Y-m-d H:i:s', strtotime('-5 hours')),
            'author_name' => 'SND Sports Desk',
            'featured_image' => 'https://img.youtube.com/vi/h05GJIxJ3X4/hqdefault.jpg'
        ],
        [
            'id' => 4,
            'title' => "Rethinking India's Education System: Practical Readiness Over Memorization",
            'slug' => 'rethinking-indias-education-system-practical-skills',
            'category' => 'exclusive',
            'subcategory' => 'Education',
            'read_time' => '5 MIN READ',
            'published_at' => date('Y-m-d H:i:s', strtotime('-1 day')),
            'author_name' => 'SND Editorial Special',
            'featured_image' => 'https://img.youtube.com/vi/93D_Razv-nM/hqdefault.jpg'
        ]
    ];
}

$page_title = 'National News, Broadcast Network & Production Hub';
$seo_desc = 'SND Media — 24/7 Digital Newsroom, Official @sndschoolmedia Broadcasts & Commercial Production Studio Rentals at SND Public School Campus, Palwal (Delhi NCR).';
$canonical_url = 'https://' . ($settings['site_domain'] ?? 'sndmedia.in') . '/';
$seo_keywords = 'SND Media, SND News, Palwal news, Haryana news, national breaking news, broadcast studio rental, Delhi NCR studio space, live news stream';
$active_nav = 'live';

require_once __DIR__ . '/includes/header.php';
?>

<!-- MAIN CONTAINER (Centered Grid with Standard Side Breathing Space) -->
<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

  <!-- HERO SECTION: LEAD BROADCAST STORY + SIDEBAR -->
  <section class="grid grid-cols-1 lg:grid-cols-12 gap-6 mb-12">
    
    <!-- Left 8 Cols: Main Hero Article Card -->
    <div class="lg:col-span-8">
      <article class="bg-brand-card rounded-2xl overflow-hidden border border-slate-800 hover:border-slate-700 transition shadow-2xl group flex flex-col justify-between h-full">
        <div>
          <!-- Hero Image with Live Pill -->
          <div class="relative aspect-video overflow-hidden bg-slate-900">
            <img src="<?= sanitize_output($hero_article['featured_image']) ?>" alt="<?= sanitize_output($hero_article['title']) ?>" class="w-full h-full object-cover group-hover:scale-105 transition duration-700">
            <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/40 to-transparent"></div>
            
            <div class="absolute top-4 left-4 flex flex-wrap gap-2">
              <span class="bg-brand-red text-white text-[11px] font-black uppercase tracking-wider px-3 py-1 rounded shadow-lg flex items-center gap-1.5">
                <span class="w-2 h-2 rounded-full bg-white animate-ping"></span> SPECIAL INVESTIGATION
              </span>
              <span class="bg-slate-900/80 backdrop-blur text-slate-300 text-[11px] font-bold px-2.5 py-1 rounded border border-slate-700">
                <?= sanitize_output($hero_article['subcategory'] ?? 'National') ?>
              </span>
            </div>

            <div class="absolute bottom-4 left-4 right-4">
              <h1 class="text-2xl sm:text-3xl md:text-4xl font-extrabold text-white leading-tight font-heading group-hover:text-amber-300 transition">
                <a href="article/<?= sanitize_output($hero_article['slug']) ?>">
                  <?= sanitize_output($hero_article['title']) ?>
                </a>
              </h1>
            </div>
          </div>

          <!-- Hero Body & Human Editorial 3-Bullet Highlights -->
          <div class="p-5 sm:p-6">
            <p class="text-sm text-slate-300 leading-relaxed font-serif">
              <?= sanitize_output($hero_article['summary']) ?>
            </p>

            <!-- Human Editorial 3-Bullet Highlights Drawer (Phase 1 Requirement) -->
            <div class="mt-5 p-4 rounded-xl bg-slate-900/90 border-l-4 border-amber-400 border border-slate-800">
              <div class="flex items-center justify-between mb-2">
                <span class="text-[11px] font-extrabold uppercase text-amber-400 tracking-wider flex items-center gap-1.5">
                  <i class="fa-solid fa-list-check"></i> Editorial 3-Bullet Highlights
                </span>
                <span class="text-[10px] text-slate-500 font-semibold uppercase">Human Verified</span>
              </div>
              <ul class="space-y-2 text-xs text-slate-300">
                <li class="flex items-start gap-2">
                  <span class="text-amber-400 font-bold mt-0.5">&bull;</span>
                  <span><?= sanitize_output($hero_article['highlight_1'] ?? 'Key corridor framework targets Tier-2 industrial clusters across Haryana and NCR.') ?></span>
                </li>
                <li class="flex items-start gap-2">
                  <span class="text-amber-400 font-bold mt-0.5">&bull;</span>
                  <span><?= sanitize_output($hero_article['highlight_2'] ?? 'Dedicated multi-modal freight connectivity slated across 24,000 km.') ?></span>
                </li>
                <li class="flex items-start gap-2">
                  <span class="text-amber-400 font-bold mt-0.5">&bull;</span>
                  <span><?= sanitize_output($hero_article['highlight_3'] ?? 'Ground survey milestone established along the Palwal-Sohna industrial axis.') ?></span>
                </li>
              </ul>
            </div>
          </div>
        </div>

        <!-- Hero Footer -->
        <div class="px-5 sm:px-6 pb-6 pt-3 border-t border-slate-800/80 flex items-center justify-between text-xs text-slate-400">
          <div class="flex items-center gap-2">
            <span class="w-6 h-6 rounded-full bg-amber-400/20 text-amber-400 flex items-center justify-center font-bold text-[10px]">SND</span>
            <span class="font-medium text-slate-300"><?= sanitize_output($hero_article['author_name']) ?></span>
            <span>&bull;</span>
            <span><?= time_ago($hero_article['published_at']) ?></span>
          </div>
          <a href="article/<?= sanitize_output($hero_article['slug']) ?>" class="text-amber-400 hover:text-amber-300 font-bold flex items-center gap-1">
            Read Full Story <i class="fa-solid fa-arrow-right text-[10px]"></i>
          </a>
        </div>
      </article>
    </div>

    <!-- Right 4 Cols: Top Stories Rail & Studio Callout -->
    <div class="lg:col-span-4 flex flex-col gap-6">
      
      <!-- Top Stories Rail -->
      <div class="bg-brand-card rounded-2xl p-5 border border-slate-800">
        <div class="flex items-center justify-between pb-3 border-b border-slate-800 mb-4">
          <span class="text-xs font-black uppercase text-white tracking-wider flex items-center gap-1.5">
            <i class="fa-solid fa-bolt text-amber-400"></i> Top Developing Stories
          </span>
          <span class="text-[10px] bg-slate-800 text-slate-300 px-2 py-0.5 rounded font-mono">LIVE FEED</span>
        </div>

        <div class="space-y-4">
          <?php foreach ($secondary_articles as $sec): ?>
            <article class="group pb-3 border-b border-slate-800/70 last:border-0 last:pb-0">
              <a href="article/<?= sanitize_output($sec['slug']) ?>" class="flex gap-3">
                <img src="<?= sanitize_output($sec['featured_image']) ?>" alt="<?= sanitize_output($sec['title']) ?>" class="w-20 h-16 rounded-lg object-cover shrink-0 bg-slate-900 border border-slate-800 group-hover:scale-105 transition">
                <div class="min-w-0">
                  <span class="text-[9px] font-black uppercase text-amber-400"><?= sanitize_output($sec['subcategory'] ?? 'NATIONAL') ?></span>
                  <h3 class="text-xs font-bold text-slate-200 group-hover:text-amber-300 transition line-clamp-2 leading-snug mt-0.5">
                    <?= sanitize_output($sec['title']) ?>
                  </h3>
                  <div class="text-[10px] text-slate-500 mt-1 flex items-center gap-1">
                    <span><?= sanitize_output($sec['read_time'] ?? '3 MIN') ?></span>
                    <span>&bull;</span>
                    <span><?= time_ago($sec['published_at']) ?></span>
                  </div>
                </div>
              </a>
            </article>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- Commercial Studio Space Callout Banner -->
      <div class="bg-gradient-to-br from-amber-950/40 via-brand-card to-slate-900 rounded-2xl p-5 border-2 border-amber-500/40 shadow-xl relative overflow-hidden">
        <div class="flex items-center justify-between mb-2">
          <span class="bg-amber-400 text-slate-950 text-[10px] font-black uppercase px-2 py-0.5 rounded shadow">
            COMMERCIAL RENTAL
          </span>
          <span class="text-amber-400 font-mono text-xs font-bold"><?= sanitize_output($settings['studio_rate_display'] ?? 'From ₹3,500/hr') ?></span>
        </div>
        <h3 class="font-heading text-base font-extrabold text-white leading-snug">
          Rent SND Media Studio & Production Facilities
        </h3>
        <p class="text-xs text-slate-300 mt-1.5 leading-relaxed">
          Soundproof Chroma Stage A, Podcast Suite Studio B, and Outdoor Campus Billboard slots available at SND Campus, Palwal (Delhi NCR).
        </p>
        <div class="pt-3 flex gap-2">
          <button onclick="openRentalModal('Studio Floor Booking')" class="bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs px-3.5 py-2 rounded-lg transition shadow flex-1 text-center">
            <i class="fa-solid fa-key mr-1"></i> Book Floor
          </button>
          <a href="studio-rental" class="bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs px-3 py-2 rounded-lg border border-slate-700 transition">
            View Rates
          </a>
        </div>
      </div>

    </div>
  </section>

  <!-- CONTINUOUS NEWS DESK FEED & CATEGORY FILTERING (US-05 / TASK-S2-04) -->
  <section id="news-desk" class="py-8 border-t border-slate-800">
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-6">
      <div>
        <div class="flex items-center gap-2">
          <span class="w-2.5 h-2.5 rounded-full bg-amber-400"></span>
          <span class="text-xs font-black uppercase text-amber-400 tracking-wider">CONTINUOUS EDITORIAL WIRE</span>
        </div>
        <h2 class="text-2xl md:text-3xl font-extrabold text-white mt-1">
          SND Newsroom Continuous Feed
        </h2>
        <p class="text-xs text-slate-400 mt-1">
          Human-verified ground reporting, economic policies, sports tournaments &amp; investigative desks.
        </p>
      </div>

      <!-- Category Filter Tabs -->
      <div class="flex flex-wrap gap-1.5 text-xs font-semibold">
        <button onclick="filterCategory('all', this)" data-category-target="all" class="category-tab-btn px-3 py-1.5 rounded-lg transition bg-amber-500 text-slate-950 font-bold">
          All Stories
        </button>
        <button onclick="filterCategory('national', this)" data-category-target="national" class="category-tab-btn px-3 py-1.5 rounded-lg transition bg-slate-900 text-slate-400 hover:text-white">
          National
        </button>
        <button onclick="filterCategory('business', this)" data-category-target="business" class="category-tab-btn px-3 py-1.5 rounded-lg transition bg-slate-900 text-slate-400 hover:text-white">
          Business
        </button>
        <button onclick="filterCategory('tech', this)" data-category-target="tech" class="category-tab-btn px-3 py-1.5 rounded-lg transition bg-slate-900 text-slate-400 hover:text-white">
          Tech
        </button>
        <button onclick="filterCategory('energy', this)" data-category-target="energy" class="category-tab-btn px-3 py-1.5 rounded-lg transition bg-slate-900 text-slate-400 hover:text-white">
          Energy
        </button>
        <button onclick="filterCategory('exclusive', this)" data-category-target="exclusive" class="category-tab-btn px-3 py-1.5 rounded-lg transition bg-slate-900 text-slate-400 hover:text-white">
          Exclusive
        </button>
        <button onclick="filterCategory('world', this)" data-category-target="world" class="category-tab-btn px-3 py-1.5 rounded-lg transition bg-slate-900 text-slate-400 hover:text-white">
          World
        </button>
      </div>
    </div>

    <!-- 2-Column Articles Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4" id="news-desk-grid">
      <?php 
      $display_feed = !empty($feed_articles) ? $feed_articles : array_merge([$hero_article], $secondary_articles);
      foreach ($display_feed as $art): 
        $cat_colors = [
          'national'  => 'text-sky-400 bg-sky-950/60 border-sky-800/50',
          'business'  => 'text-emerald-400 bg-emerald-950/60 border-emerald-800/50',
          'tech'      => 'text-amber-400 bg-amber-950/60 border-amber-800/50',
          'energy'    => 'text-cyan-400 bg-cyan-950/60 border-cyan-800/50',
          'exclusive' => 'text-rose-400 bg-rose-950/60 border-rose-800/50',
          'world'     => 'text-indigo-400 bg-indigo-950/60 border-indigo-800/50'
        ];
        $badge_style = $cat_colors[$art['category']] ?? 'text-slate-300 bg-slate-800 border-slate-700';
      ?>
        <article class="news-desk-card bg-brand-card p-4 rounded-xl border border-slate-800 hover:border-slate-700 transition group flex gap-3.5" data-category="<?= sanitize_output($art['category']) ?>">
          <a href="article/<?= sanitize_output($art['slug']) ?>" class="w-28 h-24 rounded-lg overflow-hidden shrink-0 bg-slate-900 border border-slate-800 block">
            <img src="<?= sanitize_output($art['featured_image']) ?>" alt="<?= sanitize_output($art['title']) ?>" class="w-full h-full object-cover group-hover:scale-105 group-hover:opacity-90 transition duration-300">
          </a>
          <div class="min-w-0 flex-1 flex flex-col justify-between">
            <div>
              <div class="flex items-center gap-2 mb-1 flex-wrap">
                <span class="text-[9px] font-black uppercase px-2 py-0.5 rounded border <?= $badge_style ?>">
                  <?= sanitize_output($art['category']) ?>
                </span>
                <?php if (!empty($art['verification_badge']) && $art['verification_badge'] !== 'none'): ?>
                  <span class="text-[9px] font-semibold text-slate-400 bg-slate-900 px-1.5 py-0.5 rounded">
                    <?= strtoupper(str_replace('_', ' ', $art['verification_badge'])) ?>
                  </span>
                <?php endif; ?>
              </div>
              <h3 class="text-xs sm:text-sm font-bold text-white group-hover:text-amber-300 transition line-clamp-2 leading-snug">
                <a href="article/<?= sanitize_output($art['slug']) ?>">
                  <?= sanitize_output($art['title']) ?>
                </a>
              </h3>
            </div>
            <div class="text-[10px] text-slate-500 mt-2 flex items-center justify-between">
              <span><?= sanitize_output($art['author_name'] ?? 'SND Desk') ?> &bull; <?= time_ago($art['published_at']) ?></span>
              <a href="article/<?= sanitize_output($art['slug']) ?>" class="text-amber-400 hover:text-amber-300 font-semibold">
                Read &rsaquo;
              </a>
            </div>
          </div>
        </article>
      <?php endforeach; ?>
    </div>

    <!-- Empty State -->
    <div id="news-desk-empty" class="hidden text-center py-10 bg-brand-card rounded-xl border border-slate-800 text-slate-400 text-xs">
      <i class="fa-solid fa-newspaper text-2xl text-slate-600 mb-2"></i>
      <p>No stories currently filed under this category.</p>
    </div>
  </section>

  <!-- YOUTUBE BROADCAST SECTION -->
  <?php require_once __DIR__ . '/includes/youtube_hub.php'; ?>

  <!-- STUDIO SHOWCASE SECTION (COMMERCIAL FACILITIES) -->
  <section id="studio-showcase" class="py-12">
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-8">
      <div>
        <span class="text-xs font-black uppercase text-amber-400 tracking-wider flex items-center gap-1.5">
          <i class="fa-solid fa-building-columns"></i> COMMERCIAL SPACE ON RENT
        </span>
        <h2 class="text-2xl md:text-3xl font-extrabold text-white mt-1">
          Studio Space On Rent & Production Facilities
        </h2>
        <p class="text-xs text-slate-400 mt-1 max-w-xl">
          High-bandwidth acoustic facilities for broadcast journalists, podcast creators, OTT film units, and commercial brand advertisers.
        </p>
      </div>
      <div>
        <a href="studio-rental" class="bg-brand-red hover:bg-rose-700 text-white font-bold text-xs px-4 py-2.5 rounded-lg transition inline-flex items-center gap-2 shadow-md">
          <span>View Full Studio Specs & Rate Card</span>
          <i class="fa-solid fa-arrow-right text-[10px]"></i>
        </a>
      </div>
    </div>

    <!-- 3 Core Space Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      
      <!-- Facility Card 1: Studio A -->
      <div class="bg-brand-card rounded-2xl overflow-hidden border border-slate-800 hover:border-amber-400/50 transition group flex flex-col justify-between">
        <div>
          <div class="relative aspect-video overflow-hidden bg-slate-900">
            <img src="https://images.unsplash.com/photo-1598488035139-bdbb2231ce04?auto=format&fit=crop&w=800&q=80" alt="Studio A Newsroom Floor" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
            <span class="absolute top-3 left-3 bg-amber-400 text-slate-950 font-black text-[10px] px-2 py-0.5 rounded shadow">STUDIO A</span>
            <span class="absolute bottom-3 right-3 bg-black/80 font-mono text-[11px] text-amber-300 px-2 py-0.5 rounded font-bold">₹7,500/hr</span>
          </div>
          <div class="p-5">
            <h3 class="font-bold text-base text-white group-hover:text-amber-300 transition">Main Newsroom & Chroma Stage</h3>
            <p class="text-xs text-slate-400 mt-1.5 leading-relaxed">
              2,200 sq.ft acoustically treated stage equipped with motorized grid lighting, cyclorama green screen, teleprompters, and 4K multi-cam SDI routing.
            </p>
            <div class="flex flex-wrap gap-1.5 mt-3 text-[10px] text-slate-300">
              <span class="bg-slate-900 px-2 py-0.5 rounded border border-slate-800">Cyclorama Chroma</span>
              <span class="bg-slate-900 px-2 py-0.5 rounded border border-slate-800">4K SDI Switcher</span>
              <span class="bg-slate-900 px-2 py-0.5 rounded border border-slate-800">Cool LED Grid</span>
            </div>
          </div>
        </div>
        <div class="p-5 pt-0">
          <button onclick="openRentalModal('Studio A - Newsroom Stage')" class="w-full bg-slate-800 hover:bg-amber-500 hover:text-slate-950 text-slate-200 font-bold text-xs py-2 rounded-lg border border-slate-700 hover:border-transparent transition">
            Book Studio A
          </button>
        </div>
      </div>

      <!-- Facility Card 2: Studio B -->
      <div class="bg-brand-card rounded-2xl overflow-hidden border border-slate-800 hover:border-amber-400/50 transition group flex flex-col justify-between">
        <div>
          <div class="relative aspect-video overflow-hidden bg-slate-900">
            <img src="https://images.unsplash.com/photo-1590602847861-f357a9332bbc?auto=format&fit=crop&w=800&q=80" alt="Studio B Podcast Suite" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
            <span class="absolute top-3 left-3 bg-amber-400 text-slate-950 font-black text-[10px] px-2 py-0.5 rounded shadow">STUDIO B</span>
            <span class="absolute bottom-3 right-3 bg-black/80 font-mono text-[11px] text-amber-300 px-2 py-0.5 rounded font-bold">₹3,500/hr</span>
          </div>
          <div class="p-5">
            <h3 class="font-bold text-base text-white group-hover:text-amber-300 transition">Multi-Cam Podcast Recording Suite</h3>
            <p class="text-xs text-slate-400 mt-1.5 leading-relaxed">
              Designed for conversational talk shows, corporate leadership interviews, and video podcasts. Shure SM7B microphones, Rodecaster Pro II, and PTZ cameras.
            </p>
            <div class="flex flex-wrap gap-1.5 mt-3 text-[10px] text-slate-300">
              <span class="bg-slate-900 px-2 py-0.5 rounded border border-slate-800">4x Shure SM7B</span>
              <span class="bg-slate-900 px-2 py-0.5 rounded border border-slate-800">Rodecaster Pro II</span>
              <span class="bg-slate-900 px-2 py-0.5 rounded border border-slate-800">4K PTZ Cams</span>
            </div>
          </div>
        </div>
        <div class="p-5 pt-0">
          <button onclick="openRentalModal('Studio B - Multi-Cam Podcast Suite')" class="w-full bg-slate-800 hover:bg-amber-500 hover:text-slate-950 text-slate-200 font-bold text-xs py-2 rounded-lg border border-slate-700 hover:border-transparent transition">
            Book Studio B
          </button>
        </div>
      </div>

      <!-- Facility Card 3: Ad Space -->
      <div class="bg-brand-card rounded-2xl overflow-hidden border border-slate-800 hover:border-amber-400/50 transition group flex flex-col justify-between">
        <div>
          <div class="relative aspect-video overflow-hidden bg-slate-900">
            <img src="https://images.unsplash.com/photo-1542744094-3a31f272c490?auto=format&fit=crop&w=800&q=80" alt="Outdoor Billboard & Digital Ads" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
            <span class="absolute top-3 left-3 bg-amber-400 text-slate-950 font-black text-[10px] px-2 py-0.5 rounded shadow">MEDIA SLOTS</span>
            <span class="absolute bottom-3 right-3 bg-black/80 font-mono text-[11px] text-amber-300 px-2 py-0.5 rounded font-bold">Custom Quote</span>
          </div>
          <div class="p-5">
            <h3 class="font-bold text-base text-white group-hover:text-amber-300 transition">Outdoor Billboards & Digital Banners</h3>
            <p class="text-xs text-slate-400 mt-1.5 leading-relaxed">
              Prime outdoor hoardings along high-traffic Palwal-Sohna highway frontage at SND Campus, paired with digital website leaderboard slots reaching Haryana and NCR.
            </p>
            <div class="flex flex-wrap gap-1.5 mt-3 text-[10px] text-slate-300">
              <span class="bg-slate-900 px-2 py-0.5 rounded border border-slate-800">Highway Frontage</span>
              <span class="bg-slate-900 px-2 py-0.5 rounded border border-slate-800">Leaderboard Web Ads</span>
              <span class="bg-slate-900 px-2 py-0.5 rounded border border-slate-800">Event Sponsorship</span>
            </div>
          </div>
        </div>
        <div class="p-5 pt-0">
          <button onclick="openRentalModal('Outdoor Campus Billboard & Ad Hoardings')" class="w-full bg-slate-800 hover:bg-amber-500 hover:text-slate-950 text-slate-200 font-bold text-xs py-2 rounded-lg border border-slate-700 hover:border-transparent transition">
            Enquire Ad Space
          </button>
        </div>
      </div>

    </div>
  </section>

</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
