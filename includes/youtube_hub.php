<?php
/**
 * SND Media — Dynamic YouTube Broadcast Hub Component
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/settings.php';
require_once __DIR__ . '/functions.php';

$settings = get_all_settings();
$pdo = get_db_connection();

$bulletins = [];
if ($pdo) {
    try {
        $stmt = $pdo->query("SELECT * FROM youtube_bulletins WHERE is_active = 1 ORDER BY is_featured DESC, sort_order ASC, id DESC LIMIT 6");
        $bulletins = $stmt->fetchAll();
    } catch (Exception $e) {
        $bulletins = [];
    }
}

if (empty($bulletins)) {
    $bulletins = [
        [
            'video_id' => '25tvHivopPE',
            'title' => 'GST Reform: Court Approval Before Taxpayer Arrests — Business Bulletin',
            'category_badge' => 'SPECIAL REPORT',
            'duration' => '12:40',
            'view_count' => '1.8K views',
            'is_featured' => 1
        ],
        [
            'video_id' => 'h05GJIxJ3X4',
            'title' => 'Palwal District Badminton Championship 2026 — Live Finals Coverage',
            'category_badge' => 'SPORTS LIVE',
            'duration' => '45:15',
            'view_count' => '4.2K views',
            'is_featured' => 0
        ],
        [
            'video_id' => '93D_Razv-nM',
            'title' => "Rethinking India's Education System — Student Voices & NEP 2020",
            'category_badge' => 'EDUCATION PODCAST',
            'duration' => '18:22',
            'view_count' => '2.5K views',
            'is_featured' => 0
        ],
        [
            'video_id' => 'qjp36XeVlAQ',
            'title' => 'BRICS Summit 2026 Special: Delhi Global Economic Realignment',
            'category_badge' => 'WORLD DESK',
            'duration' => '14:50',
            'view_count' => '3.1K views',
            'is_featured' => 0
        ]
    ];
}

$featured_video = $bulletins[0];
?>
<!-- YOUTUBE BROADCAST NETWORK SECTION -->
<section id="youtube-hub" class="py-12 bg-slate-950 border-t border-b border-slate-900">
  <div class="max-w-7xl mx-auto px-4">
    
    <!-- Section Header -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-6">
      <div>
        <div class="flex items-center gap-2">
          <span class="w-2.5 h-2.5 rounded-full bg-rose-500 animate-ping"></span>
          <span class="text-xs font-black uppercase text-rose-500 tracking-wider">OFFICIAL YOUTUBE BROADCAST HUB</span>
        </div>
        <h2 class="text-2xl md:text-3xl font-extrabold text-white mt-1">
          Watch SND Media Channel Broadcasts
        </h2>
        <p class="text-xs text-slate-400 mt-1">
          High-definition daily bulletins, student podcasts, sports tournaments & investigative documentaries.
        </p>
      </div>

      <div class="flex items-center gap-3">
        <a href="<?= sanitize_output($settings['social_youtube'] ?? 'https://www.youtube.com/@sndschoolmedia') ?>" target="_blank" rel="noopener noreferrer" class="bg-brand-red hover:bg-rose-700 text-white font-bold text-xs px-4 py-2 rounded-lg flex items-center gap-2 shadow-lg shadow-rose-900/30 transition transform active:scale-95">
          <i class="fa-brands fa-youtube text-base"></i>
          <span>Subscribe (<?= sanitize_output($settings['yt_subscriber_label'] ?? '3.36K Subs') ?>)</span>
        </a>
      </div>
    </div>

    <!-- YouTube Main Player + Playlist Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 bg-brand-card rounded-2xl border border-slate-800 p-4 md:p-6 shadow-2xl">
      
      <!-- Left 8 Cols: Main Video Screen -->
      <div class="lg:col-span-8 flex flex-col justify-between">
        <div class="relative w-full aspect-video rounded-xl overflow-hidden bg-black border border-slate-700/80 shadow-inner">
          <iframe id="main-youtube-player" class="w-full h-full" src="https://www.youtube-nocookie.com/embed/<?= sanitize_output($featured_video['video_id']) ?>?rel=0&enablejsapi=1" title="SND Media YouTube Player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
        </div>
        <div class="mt-4">
          <div class="flex items-center gap-2 mb-1.5">
            <span id="player-badge" class="bg-rose-600/20 text-rose-400 text-[10px] font-extrabold uppercase px-2 py-0.5 rounded border border-rose-500/30">
              <?= sanitize_output($featured_video['category_badge'] ?? 'FEATURED BROADCAST') ?>
            </span>
            <span class="text-slate-500 text-xs">&bull;</span>
            <span class="text-xs text-slate-400"><i class="fa-solid fa-satellite-dish mr-1 text-rose-500"></i> Broadcasted on <strong class="text-slate-300">@sndschoolmedia</strong></span>
          </div>
          <h3 id="player-title" class="text-lg md:text-xl font-extrabold text-white leading-snug">
            <?= sanitize_output($featured_video['title']) ?>
          </h3>
        </div>
      </div>

      <!-- Right 4 Cols: Video Playlist -->
      <div class="lg:col-span-4 flex flex-col">
        <div class="flex items-center justify-between pb-3 border-b border-slate-800 mb-3">
          <span class="text-xs font-bold text-slate-300 uppercase tracking-wider flex items-center gap-1.5">
            <i class="fa-solid fa-list-ul text-amber-400"></i> Recent Broadcasts
          </span>
          <span class="text-[11px] text-slate-500"><?= count($bulletins) ?> Videos</span>
        </div>

        <div class="space-y-2.5 overflow-y-auto max-h-[380px] custom-scrollbar pr-1" id="youtube-playlist-container">
          <?php foreach ($bulletins as $idx => $vid): ?>
            <div onclick="switchVideo('<?= sanitize_output($vid['video_id']) ?>', '<?= addslashes(sanitize_output($vid['title'])) ?>', '<?= sanitize_output($vid['category_badge'] ?? 'BROADCAST') ?>', this)" 
                 class="playlist-item <?= $idx === 0 ? 'bg-slate-800/80 border-amber-400/50' : 'bg-slate-900/60 border-slate-800' ?> hover:bg-slate-800 border p-2 rounded-xl cursor-pointer transition flex gap-3 group">
              <div class="relative w-24 aspect-video shrink-0 rounded-lg overflow-hidden bg-black">
                <img src="https://img.youtube.com/vi/<?= sanitize_output($vid['video_id']) ?>/mqdefault.jpg" alt="<?= sanitize_output($vid['title']) ?>" class="w-full h-full object-cover group-hover:scale-105 transition">
                <div class="absolute inset-0 bg-black/30 group-hover:bg-transparent transition flex items-center justify-center">
                  <i class="fa-solid fa-play text-white text-xs opacity-80 group-hover:opacity-100 group-hover:scale-110 transition"></i>
                </div>
                <?php if (!empty($vid['duration'])): ?>
                  <span class="absolute bottom-1 right-1 bg-black/80 text-[9px] font-mono text-white px-1 rounded"><?= sanitize_output($vid['duration']) ?></span>
                <?php endif; ?>
              </div>
              <div class="min-w-0 flex-1 flex flex-col justify-center">
                <span class="text-[9px] font-extrabold uppercase text-amber-400 line-clamp-1"><?= sanitize_output($vid['category_badge'] ?? 'BROADCAST') ?></span>
                <h4 class="text-xs font-bold text-slate-200 group-hover:text-amber-300 transition line-clamp-2 leading-snug mt-0.5">
                  <?= sanitize_output($vid['title']) ?>
                </h4>
              </div>
            </div>
          <?php endforeach; ?>
        </div>

        <div class="pt-3 mt-auto border-t border-slate-800 text-center">
          <a href="<?= sanitize_output($settings['social_youtube'] ?? 'https://www.youtube.com/@sndschoolmedia') ?>" target="_blank" rel="noopener noreferrer" class="text-xs text-amber-400 hover:text-amber-300 font-semibold inline-flex items-center gap-1.5 transition">
            <span>Explore All 666+ Videos on YouTube</span>
            <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
          </a>
        </div>
      </div>

    </div>

  </div>
</section>

<script>
  function switchVideo(videoId, title, badge, el) {
    const player = document.getElementById('main-youtube-player');
    const titleEl = document.getElementById('player-title');
    const badgeEl = document.getElementById('player-badge');

    if (player) {
      player.src = `https://www.youtube-nocookie.com/embed/${videoId}?rel=0&autoplay=1`;
    }
    if (titleEl) titleEl.textContent = title;
    if (badgeEl) badgeEl.textContent = badge;

    // Toggle playlist active styles
    document.querySelectorAll('.playlist-item').forEach(item => {
      item.classList.remove('bg-slate-800/80', 'border-amber-400/50');
      item.classList.add('bg-slate-900/60', 'border-slate-800');
    });

    if (el) {
      el.classList.remove('bg-slate-900/60', 'border-slate-800');
      el.classList.add('bg-slate-800/80', 'border-amber-400/50');
    }
  }
</script>
