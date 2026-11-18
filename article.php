<?php
/**
 * SND Media — Single Article View
 * Features human editorial 3-bullet highlights, verification badge & social sharing
 */

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/settings.php';
require_once __DIR__ . '/includes/functions.php';

$slug = isset($_GET['slug']) ? trim($_GET['slug']) : '';

$article = null;
$related_articles = [];
$pdo = get_db_connection();

if ($pdo && !empty($slug)) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM articles WHERE slug = :slug AND status = 'published' LIMIT 1");
        $stmt->execute([':slug' => $slug]);
        $article = $stmt->fetch();

        if ($article) {
            // Fetch related articles
            $relStmt = $pdo->prepare("SELECT * FROM articles WHERE status = 'published' AND id != :id AND category = :cat ORDER BY published_at DESC LIMIT 3");
            $relStmt->execute([':id' => $article['id'], ':cat' => $article['category']]);
            $related_articles = $relStmt->fetchAll();

            // If not enough in category, fetch latest
            if (count($related_articles) < 3) {
                $relStmt2 = $pdo->prepare("SELECT * FROM articles WHERE status = 'published' AND id != :id ORDER BY published_at DESC LIMIT 3");
                $relStmt2->execute([':id' => $article['id']]);
                $related_articles = $relStmt2->fetchAll();
            }
        }
    } catch (Exception $e) {
        $article = null;
    }
}

// If slug was specifically requested but not found in DB, render 404
if (!empty($slug) && !$article) {
    include __DIR__ . '/404.php';
    exit;
}

// Fallback demo article if no slug provided and DB offline
if (!$article) {
    $article = [
        'id' => 1,
        'title' => 'Centre Unveils ₹1.2 Lakh Cr Digital Corridor Connecting Tier-2 Industrial Hubs',
        'slug' => 'centre-unveils-1-2-lakh-cr-digital-corridor',
        'category' => 'national',
        'subcategory' => 'Infrastructure',
        'read_time' => '4 MIN READ',
        'published_at' => date('Y-m-d H:i:s'),
        'author_name' => 'SND Investigative Bureau',
        'verification_badge' => 'special_investigation',
        'youtube_video_id' => '25tvHivopPE',
        'featured_image' => 'https://images.unsplash.com/photo-1570168007204-dfb528c6958f?auto=format&fit=crop&w=1400&q=85',
        'summary' => 'The union cabinet today sanctioned an ambitious multi-modal fiber and highway network linking 40 emerging industrial clusters directly to dedicated freight lines.',
        'highlight_1' => '₹1.2 Lakh Cr Outlay: Key connectivity lines identified around NCR, Bengaluru, Mumbai-Pune, and Hyderabad peripheral corridors.',
        'highlight_2' => 'Zero-Latency Fiber Mesh: Over 24,000 km of carrier-grade optical highway to boost AI and semiconductor manufacturing clusters.',
        'highlight_3' => 'Public-Private Consortiums: Execution slated in three fast-track phases under Gati Shakti oversight.',
        'content' => '<p class="text-base md:text-lg leading-relaxed text-slate-200 font-serif mb-6">The Union Cabinet today approved the ambitious Digital Industrial Corridor Framework, pledging ₹1.2 lakh crore toward multi-modal connectivity, carrier-grade fiber backbones, and smart freight integration across 40 emerging Tier-2 hubs.</p><p class="text-base leading-relaxed text-slate-300 font-serif mb-6">Speaking at the national press briefing in New Delhi, the infrastructure task force emphasized that Tier-2 cities in Haryana, Uttar Pradesh, Maharashtra, and Karnataka will benefit from direct zero-latency links to central cloud hubs.</p><blockquote class="my-8 p-5 rounded-r-xl border-l-4 border-amber-400 bg-slate-900/90 text-white italic font-serif text-lg leading-relaxed">"Regional manufacturing can only compete globally when digital infrastructure matches physical speed," the announcement noted.</blockquote><p class="text-base leading-relaxed text-slate-300 font-serif mb-6">SND Media ground reporters visited the pilot junction on the Palwal-Sohna corridor, where initial survey milestones have already been reached.</p>'
    ];
}

$page_title = $article['title'];
$seo_desc = $article['summary'];
$seo_type = 'article';
$seo_article = $article;
$active_nav = $article['category'];

require_once __DIR__ . '/includes/header.php';

$current_url = 'https://' . ($settings['site_domain'] ?? 'sndmedia.in') . '/article/' . urlencode($article['slug']);
?>

<main class="max-w-4xl mx-auto px-4 py-8">

  <!-- Breadcrumb -->
  <nav class="flex items-center gap-2 text-xs text-slate-400 mb-4 flex-wrap">
    <a href="index.php" class="hover:text-white">Home</a>
    <span>&rsaquo;</span>
    <a href="index.php#<?= sanitize_output($article['category']) ?>" class="hover:text-amber-400 uppercase font-bold text-amber-400">
      <?= sanitize_output($article['category']) ?>
    </a>
    <span>&rsaquo;</span>
    <span class="text-slate-300 truncate max-w-xs"><?= sanitize_output($article['subcategory'] ?? 'Special Report') ?></span>
  </nav>

  <!-- Article Header Meta -->
  <header class="mb-6">
    <div class="flex flex-wrap items-center gap-2.5 mb-3">
      <span class="bg-brand-red text-white text-[10px] font-black uppercase tracking-wider px-2.5 py-0.5 rounded shadow">
        <?= strtoupper(str_replace('_', ' ', $article['verification_badge'] ?? 'VERIFIED REPORT')) ?>
      </span>
      <span class="text-xs text-slate-400 flex items-center gap-1">
        <i class="fa-regular fa-clock"></i> <?= sanitize_output($article['read_time'] ?? '4 MIN READ') ?>
      </span>
      <span class="text-slate-600">&bull;</span>
      <span class="text-xs text-slate-400">Published <?= date('F d, Y', strtotime($article['published_at'])) ?></span>
    </div>

    <h1 class="text-2xl sm:text-3xl md:text-4xl font-extrabold text-white leading-tight font-heading">
      <?= sanitize_output($article['title']) ?>
    </h1>

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mt-4 pt-4 border-t border-slate-800 text-xs text-slate-400">
      <div class="flex items-center gap-3">
        <div class="w-9 h-9 rounded-full bg-amber-400/20 text-amber-400 flex items-center justify-center font-bold">
          <i class="fa-solid fa-feather-pointed"></i>
        </div>
        <div>
          <span class="block font-bold text-white"><?= sanitize_output($article['author_name']) ?></span>
          <span class="text-[11px] text-slate-500">SND Media Editorial Bureau &bull; Palwal Campus</span>
        </div>
      </div>

      <!-- Social Sharing Links -->
      <div class="flex items-center gap-2">
        <span class="text-[11px] text-slate-400 font-semibold mr-1">Share:</span>
        <a href="https://wa.me/?text=<?= rawurlencode($article['title'] . ' - ' . $current_url) ?>" target="_blank" rel="noopener noreferrer" class="w-8 h-8 rounded-lg bg-emerald-600/20 hover:bg-emerald-600 text-emerald-400 hover:text-white flex items-center justify-center transition" title="Share on WhatsApp">
          <i class="fa-brands fa-whatsapp"></i>
        </a>
        <a href="https://twitter.com/intent/tweet?text=<?= rawurlencode($article['title']) ?>&url=<?= rawurlencode($current_url) ?>" target="_blank" rel="noopener noreferrer" class="w-8 h-8 rounded-lg bg-sky-600/20 hover:bg-sky-600 text-sky-400 hover:text-white flex items-center justify-center transition" title="Share on Twitter/X">
          <i class="fa-brands fa-x-twitter"></i>
        </a>
        <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?= rawurlencode($current_url) ?>" target="_blank" rel="noopener noreferrer" class="w-8 h-8 rounded-lg bg-blue-600/20 hover:bg-blue-600 text-blue-400 hover:text-white flex items-center justify-center transition" title="Share on LinkedIn">
          <i class="fa-brands fa-linkedin"></i>
        </a>
        <button onclick="navigator.clipboard.writeText('<?= $current_url ?>'); alert('Article link copied to clipboard!');" class="w-8 h-8 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white flex items-center justify-center transition" title="Copy Link">
          <i class="fa-solid fa-link"></i>
        </button>
      </div>
    </div>
  </header>

  <!-- Editorial 3-Bullet Highlights Drawer (Phase 1 Mandate) -->
  <section class="mb-8 p-5 rounded-2xl bg-brand-card border-2 border-amber-500/40 shadow-xl relative overflow-hidden">
    <div class="flex items-center justify-between mb-3">
      <div class="flex items-center gap-2">
        <span class="w-2.5 h-2.5 rounded-full bg-amber-400"></span>
        <h2 class="text-xs font-black uppercase tracking-wider text-amber-400">Human Editorial 3-Bullet Highlights</h2>
      </div>
      <span class="text-[10px] text-slate-400 bg-slate-900 px-2 py-0.5 rounded border border-slate-800">
        <i class="fa-solid fa-user-check text-emerald-400 mr-1"></i> Staff Fact-Checked
      </span>
    </div>
    <ul class="space-y-2.5 text-xs text-slate-200">
      <li class="flex items-start gap-2.5">
        <span class="text-amber-400 font-black mt-0.5 text-sm">&bull;</span>
        <span class="leading-relaxed"><?= sanitize_output($article['highlight_1'] ?? 'Primary policy framework and infrastructure allocation confirmed.') ?></span>
      </li>
      <li class="flex items-start gap-2.5">
        <span class="text-amber-400 font-black mt-0.5 text-sm">&bull;</span>
        <span class="leading-relaxed"><?= sanitize_output($article['highlight_2'] ?? 'Key corridor routes connect northern industrial manufacturing nodes.') ?></span>
      </li>
      <li class="flex items-start gap-2.5">
        <span class="text-amber-400 font-black mt-0.5 text-sm">&bull;</span>
        <span class="leading-relaxed"><?= sanitize_output($article['highlight_3'] ?? 'Ground survey milestone established along the Palwal-Sohna industrial axis.') ?></span>
      </li>
    </ul>
  </section>

  <!-- Featured Media: Video or High-Res Image -->
  <div class="mb-8 rounded-2xl overflow-hidden bg-slate-900 border border-slate-800 shadow-2xl">
    <?php if (!empty($article['youtube_video_id'])): ?>
      <div class="relative w-full aspect-video">
        <iframe class="w-full h-full" src="https://www.youtube-nocookie.com/embed/<?= sanitize_output($article['youtube_video_id']) ?>?rel=0" title="SND Media Video Report" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
      </div>
      <div class="p-3 bg-slate-950 text-slate-400 text-xs flex items-center justify-between">
        <span><i class="fa-brands fa-youtube text-red-500 mr-1.5"></i> Video Bulletin Broadcast on <strong>@sndschoolmedia</strong></span>
        <a href="https://www.youtube.com/watch?v=<?= sanitize_output($article['youtube_video_id']) ?>" target="_blank" rel="noopener noreferrer" class="text-amber-400 hover:text-amber-300 font-semibold">Watch on YouTube &rsaquo;</a>
      </div>
    <?php else: ?>
      <img src="<?= sanitize_output($article['featured_image']) ?>" alt="<?= sanitize_output($article['title']) ?>" class="w-full max-h-[480px] object-cover">
    <?php endif; ?>
  </div>

  <!-- Full Article Body -->
  <article class="prose prose-invert max-w-none mb-12">
    <?= sanitize_rich_html($article['content']) ?>
  </article>

  <!-- Commercial Callout in Article -->
  <div class="bg-gradient-to-r from-slate-900 to-brand-card p-6 rounded-2xl border border-slate-800 mb-12 flex flex-col sm:flex-row items-center justify-between gap-4">
    <div>
      <span class="text-[10px] font-black uppercase text-amber-400 tracking-wider">CREATOR & BROADCAST INFRASTRUCTURE</span>
      <h3 class="text-base font-bold text-white mt-1">Need a professional studio space for your broadcast or podcast?</h3>
      <p class="text-xs text-slate-400 mt-1">Rent Studio A Chroma stage or Studio B Podcast suite at SND Campus, Palwal.</p>
    </div>
    <button onclick="openRentalModal('Article Studio Callout')" class="bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs px-4 py-2.5 rounded-lg shrink-0 transition shadow">
      Book Studio Floor
    </button>
  </div>

  <!-- Related Stories -->
  <?php if (!empty($related_articles)): ?>
    <section class="border-t border-slate-800 pt-8">
      <h3 class="text-base font-black uppercase text-white tracking-wider mb-4 flex items-center gap-2">
        <i class="fa-solid fa-newspaper text-amber-400"></i> Related Coverage
      </h3>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <?php foreach ($related_articles as $rel): ?>
          <a href="article/<?= sanitize_output($rel['slug']) ?>" class="bg-brand-card p-4 rounded-xl border border-slate-800 hover:border-slate-700 transition group flex flex-col justify-between">
            <div>
              <div class="relative aspect-video rounded-lg overflow-hidden bg-slate-900 mb-2.5">
                <img src="<?= sanitize_output($rel['featured_image']) ?>" alt="<?= sanitize_output($rel['title']) ?>" class="w-full h-full object-cover group-hover:scale-105 transition">
              </div>
              <span class="text-[9px] font-black uppercase text-amber-400"><?= sanitize_output($rel['subcategory'] ?? 'NEWS') ?></span>
              <h4 class="text-xs font-bold text-slate-200 group-hover:text-amber-300 transition line-clamp-2 mt-1">
                <?= sanitize_output($rel['title']) ?>
              </h4>
            </div>
            <div class="text-[10px] text-slate-500 mt-3 flex items-center justify-between">
              <span><?= sanitize_output($rel['read_time'] ?? '3 MIN') ?></span>
              <span class="text-amber-400 font-semibold group-hover:translate-x-0.5 transition">Read &rsaquo;</span>
            </div>
          </a>
        <?php endforeach; ?>
      </div>
    </section>
  <?php endif; ?>

</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
