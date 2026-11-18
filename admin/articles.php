<?php
/**
 * SND Media — Editorial CMS: Articles Management
 * Role-Aware article authoring, 3-bullet highlights editor & publishing pipeline
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/settings.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/layout.php';

// RBAC: super_admin, editor, reporter
require_role(['super_admin', 'editor', 'reporter']);

$user_role = $_SESSION['user_role'];
$user_id   = $_SESSION['user_id'];
$user_name = $_SESSION['user_full_name'];
$pdo       = get_db_connection();

$action = $_GET['action'] ?? 'list';
$article_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$error = '';

// Handle POST: Create or Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && in_array($action, ['save_create', 'save_edit'])) {
    if (!csrf_token_validate($_POST['csrf_token'] ?? '')) {
        flash_set('error', 'Security token expired. Please retry.');
        header('Location: articles.php');
        exit;
    }

    $title              = trim($_POST['title'] ?? '');
    $slug               = slug_generate(trim($_POST['slug'] ?? '') ?: $title);
    $category           = trim($_POST['category'] ?? 'national');
    $subcategory        = trim($_POST['subcategory'] ?? '');
    $tags               = trim($_POST['tags'] ?? '');
    $meta_keywords      = trim($_POST['meta_keywords'] ?? '');
    $summary            = trim($_POST['summary'] ?? '');
    $highlight_1        = trim($_POST['highlight_1'] ?? '');
    $highlight_2        = trim($_POST['highlight_2'] ?? '');
    $highlight_3        = trim($_POST['highlight_3'] ?? '');
    $content            = trim($_POST['content'] ?? '');
    $read_time          = trim($_POST['read_time'] ?? '3 MIN READ');
    $verification_badge = trim($_POST['verification_badge'] ?? 'verified_report');
    $youtube_video_id   = trim($_POST['youtube_video_id'] ?? '');
    if (!empty($youtube_video_id)) {
        $extracted = extract_youtube_id($youtube_video_id);
        if ($extracted) $youtube_video_id = $extracted;
    }

    $is_hero_lead = isset($_POST['is_hero_lead']) ? 1 : 0;
    $is_breaking  = isset($_POST['is_breaking']) ? 1 : 0;

    // Status: Reporters can only draft or submit for review; Editors & Super Admins can publish
    $requested_status = trim($_POST['status'] ?? 'draft');
    if ($user_role === 'reporter') {
        $status = ($requested_status === 'published') ? 'review' : $requested_status;
    } else {
        $status = $requested_status;
    }

    // Handle featured image upload or URL
    $featured_image = trim($_POST['featured_image_url'] ?? '');
    if (isset($_FILES['featured_image_file']) && $_FILES['featured_image_file']['error'] !== UPLOAD_ERR_NO_FILE) {
        $upload_dir = __DIR__ . '/../assets/uploads/articles/';
        $upload = handle_file_upload($_FILES['featured_image_file'], $upload_dir, ['image/jpeg', 'image/png', 'image/webp'], 5 * 1024 * 1024);
        if ($upload['success']) {
            $featured_image = 'assets/uploads/articles/' . $upload['filename'];
        } else {
            $error = 'Image upload warning: ' . $upload['error'];
        }
    }

    if (empty($title)) {
        $error = 'Article title is required.';
    }

    if (empty($error) && $pdo) {
        try {
            // If designated as hero lead, remove hero flag from previous stories
            if ($is_hero_lead) {
                $pdo->exec("UPDATE articles SET is_hero_lead = 0");
            }

            if ($action === 'save_create') {
                $stmt = $pdo->prepare("
                    INSERT INTO articles 
                    (title, slug, category, subcategory, tags, meta_keywords, featured_image, summary, highlight_1, highlight_2, highlight_3, content, author_id, author_name, read_time, verification_badge, youtube_video_id, is_hero_lead, is_breaking, status, published_at, created_at, updated_at)
                    VALUES 
                    (:title, :slug, :cat, :subcat, :tags, :mkeywords, :img, :summary, :h1, :h2, :h3, :content, :auth_id, :auth_name, :rtime, :vbadge, :ytid, :hero, :brk, :status, :pub_at, NOW(), NOW())
                ");
                $published_at = ($status === 'published') ? date('Y-m-d H:i:s') : null;
                $stmt->execute([
                    ':title'      => $title,
                    ':slug'       => $slug,
                    ':cat'        => $category,
                    ':subcat'     => $subcategory,
                    ':tags'       => $tags,
                    ':mkeywords'  => $meta_keywords,
                    ':img'        => $featured_image ?: 'https://images.unsplash.com/photo-1585829365295-ab7cd400c167?auto=format&fit=crop&w=1200&q=80',
                    ':summary'    => $summary,
                    ':h1'         => $highlight_1,
                    ':h2'         => $highlight_2,
                    ':h3'         => $highlight_3,
                    ':content'    => $content,
                    ':auth_id'    => $user_id,
                    ':auth_name'  => $user_name,
                    ':rtime'      => $read_time,
                    ':vbadge'     => $verification_badge,
                    ':ytid'       => $youtube_video_id ?: null,
                    ':hero'       => $is_hero_lead,
                    ':brk'        => $is_breaking,
                    ':status'     => $status,
                    ':pub_at'     => $published_at
                ]);
                flash_set('success', 'Article created successfully!');
                header('Location: articles.php');
                exit;
            } else {
                // save_edit
                $target_id = (int)$_POST['id'];
                
                // Reporter ownership guard
                if ($user_role === 'reporter') {
                    $chk = $pdo->prepare("SELECT author_id FROM articles WHERE id = ?");
                    $chk->execute([$target_id]);
                    $auth = $chk->fetchColumn();
                    if ($auth != $user_id) {
                        flash_set('error', 'You can only edit your own assigned articles.');
                        header('Location: articles.php');
                        exit;
                    }
                }

                $stmt = $pdo->prepare("
                    UPDATE articles SET 
                    title = :title, slug = :slug, category = :cat, subcategory = :subcat, tags = :tags, meta_keywords = :mkeywords,
                    featured_image = :img, summary = :summary, highlight_1 = :h1, highlight_2 = :h2, highlight_3 = :h3, content = :content,
                    read_time = :rtime, verification_badge = :vbadge, youtube_video_id = :ytid, is_hero_lead = :hero, is_breaking = :brk,
                    status = :status, updated_at = NOW()
                    " . ($status === 'published' ? ", published_at = COALESCE(published_at, NOW())" : "") . "
                    WHERE id = :id
                ");
                $stmt->execute([
                    ':title'      => $title,
                    ':slug'       => $slug,
                    ':cat'        => $category,
                    ':subcat'     => $subcategory,
                    ':tags'       => $tags,
                    ':mkeywords'  => $meta_keywords,
                    ':img'        => $featured_image,
                    ':summary'    => $summary,
                    ':h1'         => $highlight_1,
                    ':h2'         => $highlight_2,
                    ':h3'         => $highlight_3,
                    ':content'    => $content,
                    ':rtime'      => $read_time,
                    ':vbadge'     => $verification_badge,
                    ':ytid'       => $youtube_video_id ?: null,
                    ':hero'       => $is_hero_lead,
                    ':brk'        => $is_breaking,
                    ':status'     => $status,
                    ':id'         => $target_id
                ]);
                flash_set('success', 'Article updated successfully!');
                header('Location: articles.php');
                exit;
            }
        } catch (Exception $e) {
            $error = 'Database error: ' . $e->getMessage();
        }
    }
}

// Handle Delete
if ($action === 'delete' && $article_id > 0) {
    if (!csrf_token_validate($_GET['token'] ?? '')) {
        flash_set('error', 'Invalid token.');
        header('Location: articles.php');
        exit;
    }
    if ($pdo) {
        if ($user_role === 'reporter') {
            flash_set('error', 'Reporters cannot delete articles. Request an editor.');
        } else {
            $stmt = $pdo->prepare("DELETE FROM articles WHERE id = ?");
            $stmt->execute([$article_id]);
            flash_set('success', 'Article removed.');
        }
    }
    header('Location: articles.php');
    exit;
}

// Render View
if ($action === 'create' || $action === 'edit') {
    $article = [
        'id' => 0, 'title' => '', 'slug' => '', 'category' => 'national', 'subcategory' => '',
        'tags' => '', 'meta_keywords' => '', 'featured_image' => '', 'summary' => '',
        'highlight_1' => '', 'highlight_2' => '', 'highlight_3' => '', 'content' => '',
        'read_time' => '3 MIN READ', 'verification_badge' => 'verified_report',
        'youtube_video_id' => '', 'is_hero_lead' => 0, 'is_breaking' => 0, 'status' => 'draft'
    ];

    if ($action === 'edit' && $article_id > 0 && $pdo) {
        $stmt = $pdo->prepare("SELECT * FROM articles WHERE id = ?");
        $stmt->execute([$article_id]);
        $found = $stmt->fetch();
        if ($found) {
            $article = $found;
        } else {
            flash_set('error', 'Article not found.');
            header('Location: articles.php');
            exit;
        }
    }

    render_admin_layout($action === 'create' ? 'Compose News Story' : 'Edit Story: ' . $article['title'], function() use ($action, $article, $error, $user_role) {
    ?>
    <div class="max-w-5xl mx-auto space-y-6">
        <div class="flex items-center justify-between">
            <a href="articles.php" class="text-xs text-slate-400 hover:text-white flex items-center gap-1.5">
                <i class="fa-solid fa-arrow-left"></i> Back to Newsroom Articles
            </a>
            <span class="text-xs text-amber-400 font-bold uppercase">
                <i class="fa-solid fa-feather-pointed mr-1"></i> Phase 1 Human Editorial Desk
            </span>
        </div>

        <?php if (!empty($error)): ?>
            <div class="p-4 rounded-xl bg-rose-500/20 text-rose-300 border border-rose-500/30 text-xs font-semibold">
                <?= sanitize_output($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="articles.php?action=<?= $action === 'create' ? 'save_create' : 'save_edit' ?>" enctype="multipart/form-data" class="space-y-6">
            <?= csrf_field() ?>
            <?php if ($action === 'edit'): ?>
                <input type="hidden" name="id" value="<?= $article['id'] ?>">
            <?php endif; ?>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                <!-- Main Editorial Column (8 cols) -->
                <div class="lg:col-span-8 space-y-5">
                    
                    <div class="bg-brand-card p-6 rounded-2xl border border-slate-800 space-y-4">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">Article Headline <span class="text-rose-500">*</span></label>
                            <input type="text" name="title" required value="<?= sanitize_output($article['title']) ?>" placeholder="e.g. Centre Unveils ₹1.2 Lakh Cr Digital Corridor Connecting Tier-2 Hubs" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-white text-base font-bold focus:outline-none focus:border-amber-400">
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">URL Slug (Leave blank to auto-generate)</label>
                            <input type="text" name="slug" value="<?= sanitize_output($article['slug']) ?>" placeholder="centre-unveils-1-2-lakh-cr-digital-corridor" class="w-full bg-slate-900/60 border border-slate-800 rounded-lg px-3 py-1.5 text-slate-300 font-mono text-xs focus:outline-none focus:border-amber-400">
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">Lead Deck / Summary (Lead Paragraph)</label>
                            <textarea name="summary" rows="3" placeholder="Brief 1-2 sentence lead deck summarizing the primary news developments..." class="w-full bg-slate-900 border border-slate-700 rounded-xl p-3 text-slate-200 text-xs focus:outline-none focus:border-amber-400"><?= sanitize_output($article['summary']) ?></textarea>
                        </div>
                    </div>

                    <!-- 3-Bullet Human Editorial Highlights Box (Mandatory Phase 1 Feature) -->
                    <div class="bg-gradient-to-br from-brand-card to-slate-900 p-6 rounded-2xl border-2 border-amber-500/40 shadow-xl space-y-3">
                        <div class="flex items-center justify-between pb-2 border-b border-slate-800">
                            <span class="text-xs font-black uppercase text-amber-400 tracking-wider flex items-center gap-1.5">
                                <i class="fa-solid fa-list-check"></i> Human Editorial 3-Bullet Highlights
                            </span>
                            <span class="text-[10px] text-slate-400 font-semibold bg-slate-900 px-2 py-0.5 rounded border border-slate-800">
                                Phase 1 Mandate (No AI)
                            </span>
                        </div>
                        <p class="text-[11px] text-slate-400">
                            Craft 3 concise, human-verified editorial takeaways that appear prominently in the gold Highlights drawer on the story view and homepage.
                        </p>

                        <div>
                            <label class="block text-[11px] font-bold text-amber-300 mb-1">Highlight Point 1</label>
                            <input type="text" name="highlight_1" value="<?= sanitize_output($article['highlight_1']) ?>" placeholder="e.g. ₹1.2 Lakh Cr Outlay: Key connectivity lines identified around NCR..." class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3 py-2 text-white text-xs focus:outline-none focus:border-amber-400">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-amber-300 mb-1">Highlight Point 2</label>
                            <input type="text" name="highlight_2" value="<?= sanitize_output($article['highlight_2']) ?>" placeholder="e.g. Zero-Latency Fiber Mesh: Over 24,000 km of carrier-grade optical highway..." class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3 py-2 text-white text-xs focus:outline-none focus:border-amber-400">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-amber-300 mb-1">Highlight Point 3</label>
                            <input type="text" name="highlight_3" value="<?= sanitize_output($article['highlight_3']) ?>" placeholder="e.g. Public-Private Consortiums: Execution slated in three fast-track phases..." class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3 py-2 text-white text-xs focus:outline-none focus:border-amber-400">
                        </div>
                    </div>

                    <!-- Full Article Body -->
                    <div class="bg-brand-card p-6 rounded-2xl border border-slate-800 space-y-2">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1">Full Article Content (HTML allowed)</label>
                        <p class="text-[11px] text-slate-400 mb-2">Use standard paragraph tags <code>&lt;p&gt;</code>, blockquotes <code>&lt;blockquote&gt;</code>, and subheadings <code>&lt;h3&gt;</code>.</p>
                        <textarea name="content" rows="12" placeholder="Write full investigative report here..." class="w-full bg-slate-900 border border-slate-700 rounded-xl p-4 text-slate-200 text-xs font-serif leading-relaxed focus:outline-none focus:border-amber-400"><?= sanitize_output($article['content']) ?></textarea>
                    </div>

                </div>

                <!-- Right Publishing Rail (4 cols) -->
                <div class="lg:col-span-4 space-y-5">
                    
                    <!-- Publishing Status Card -->
                    <div class="bg-brand-card p-5 rounded-2xl border border-slate-800 space-y-4">
                        <h4 class="text-xs font-black uppercase text-white tracking-wider pb-2 border-b border-slate-800">
                            Publishing Controls
                        </h4>

                        <div>
                            <label class="block text-xs font-bold text-slate-300 mb-1">Status</label>
                            <select name="status" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3 py-2 text-white text-xs focus:outline-none focus:border-amber-400">
                                <option value="draft" <?= $article['status'] === 'draft' ? 'selected' : '' ?>>Draft</option>
                                <option value="review" <?= $article['status'] === 'review' ? 'selected' : '' ?>>In Review / Editorial Check</option>
                                <?php if ($user_role !== 'reporter'): ?>
                                    <option value="published" <?= $article['status'] === 'published' ? 'selected' : '' ?>>Published Live</option>
                                    <option value="archived" <?= $article['status'] === 'archived' ? 'selected' : '' ?>>Archived</option>
                                <?php endif; ?>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-300 mb-1">Category</label>
                            <select name="category" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3 py-2 text-white text-xs focus:outline-none focus:border-amber-400">
                                <option value="national" <?= $article['category'] === 'national' ? 'selected' : '' ?>>National & Politics</option>
                                <option value="business" <?= $article['category'] === 'business' ? 'selected' : '' ?>>Business & Economy</option>
                                <option value="exclusive" <?= $article['category'] === 'exclusive' ? 'selected' : '' ?>>Exclusive Ground Report</option>
                                <option value="world" <?= $article['category'] === 'world' ? 'selected' : '' ?>>World & Geopolitics</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-300 mb-1">Subcategory / Beat</label>
                            <input type="text" name="subcategory" value="<?= sanitize_output($article['subcategory']) ?>" placeholder="e.g. Infrastructure, Sports, Tax Policy" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3 py-2 text-white text-xs focus:outline-none focus:border-amber-400">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-300 mb-1">Estimated Read Time</label>
                            <input type="text" name="read_time" value="<?= sanitize_output($article['read_time']) ?>" placeholder="4 MIN READ" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3 py-2 text-white text-xs focus:outline-none focus:border-amber-400">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-300 mb-1">Verification Stamp</label>
                            <select name="verification_badge" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3 py-2 text-white text-xs focus:outline-none focus:border-amber-400">
                                <option value="verified_report" <?= $article['verification_badge'] === 'verified_report' ? 'selected' : '' ?>>Verified Report</option>
                                <option value="special_investigation" <?= $article['verification_badge'] === 'special_investigation' ? 'selected' : '' ?>>Special Investigation</option>
                                <option value="ground_report" <?= $article['verification_badge'] === 'ground_report' ? 'selected' : '' ?>>Ground Report</option>
                                <option value="editorial_analysis" <?= $article['verification_badge'] === 'editorial_analysis' ? 'selected' : '' ?>>Editorial Analysis</option>
                            </select>
                        </div>

                        <!-- Special Toggles -->
                        <div class="pt-2 border-t border-slate-800 space-y-2">
                            <label class="flex items-center gap-2 text-xs text-slate-300 cursor-pointer">
                                <input type="checkbox" name="is_hero_lead" value="1" <?= $article['is_hero_lead'] ? 'checked' : '' ?> class="rounded text-amber-500 focus:ring-amber-400">
                                <span class="font-bold text-amber-300">Set as Homepage Hero Lead Story</span>
                            </label>
                            <label class="flex items-center gap-2 text-xs text-slate-300 cursor-pointer">
                                <input type="checkbox" name="is_breaking" value="1" <?= $article['is_breaking'] ? 'checked' : '' ?> class="rounded text-rose-500 focus:ring-rose-400">
                                <span class="font-bold text-rose-400">Mark as Breaking Flash Alert</span>
                            </label>
                        </div>

                        <div class="pt-2">
                            <button type="submit" class="w-full bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-slate-950 font-bold text-xs py-3 rounded-xl shadow-lg shadow-amber-500/20 transition transform active:scale-95 flex items-center justify-center gap-2">
                                <i class="fa-solid fa-floppy-disk"></i>
                                <span><?= $action === 'create' ? 'Publish / Save Story' : 'Update Story Changes' ?></span>
                            </button>
                        </div>
                    </div>

                    <!-- Media & Video Integration -->
                    <div class="bg-brand-card p-5 rounded-2xl border border-slate-800 space-y-4">
                        <h4 class="text-xs font-black uppercase text-white tracking-wider pb-2 border-b border-slate-800">
                            Media & Broadcast Link
                        </h4>

                        <div>
                            <label class="block text-xs font-bold text-slate-300 mb-1">YouTube Video ID / URL (@sndschoolmedia)</label>
                            <input type="text" name="youtube_video_id" value="<?= sanitize_output($article['youtube_video_id']) ?>" placeholder="e.g. 25tvHivopPE or full URL" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3 py-2 text-white text-xs focus:outline-none focus:border-amber-400 font-mono">
                            <p class="text-[10px] text-slate-500 mt-1">If provided, article hero renders embedded responsive video player.</p>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-300 mb-1">Featured Image URL</label>
                            <input type="text" name="featured_image_url" value="<?= sanitize_output($article['featured_image']) ?>" placeholder="https://..." class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3 py-2 text-white text-xs focus:outline-none focus:border-amber-400">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-300 mb-1">Or Upload Local Image</label>
                            <input type="file" name="featured_image_file" accept="image/png,image/jpeg,image/webp" class="w-full text-xs text-slate-400 file:mr-2 file:py-1.5 file:px-3 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-slate-800 file:text-slate-200 hover:file:bg-slate-700">
                        </div>
                    </div>

                </div>
            </div>
        </form>
    </div>
    <?php
    });
    exit;
}

// Default Action: List Articles
$articles = [];
if ($pdo) {
    try {
        $filter_cat = trim($_GET['cat'] ?? '');
        $filter_status = trim($_GET['status'] ?? '');
        $query = "SELECT * FROM articles WHERE 1=1";
        $params = [];

        if ($user_role === 'reporter') {
            $query .= " AND author_id = :uid";
            $params[':uid'] = $user_id;
        }

        if (!empty($filter_cat)) {
            $query .= " AND category = :cat";
            $params[':cat'] = $filter_cat;
        }

        if (!empty($filter_status)) {
            $query .= " AND status = :st";
            $params[':st'] = $filter_status;
        }

        $query .= " ORDER BY published_at DESC, id DESC LIMIT 50";
        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        $articles = $stmt->fetchAll();
    } catch (Exception $e) {
        $articles = [];
    }
}

render_admin_layout('Editorial Desk &bull; Article Management', function() use ($articles, $user_role) {
?>
<div class="space-y-6">
    <!-- Header with Create Action -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold text-white">Editorial Stories &amp; Reports</h2>
            <p class="text-xs text-slate-400 mt-0.5">Manage digital newsroom articles, 3-bullet highlights, and video bulletin attachments.</p>
        </div>
        <a href="articles.php?action=create" class="bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs px-4 py-2.5 rounded-xl shadow-lg shadow-amber-500/20 transition flex items-center gap-1.5 shrink-0">
            <i class="fa-solid fa-plus"></i> Compose New Story
        </a>
    </div>

    <!-- Articles Table Card -->
    <div class="bg-brand-card rounded-2xl border border-slate-800 overflow-hidden shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-900 text-slate-400 uppercase text-[10px] font-bold tracking-wider border-b border-slate-800">
                    <tr>
                        <th class="py-3 px-4">Story Headline</th>
                        <th class="py-3 px-4">Category</th>
                        <th class="py-3 px-4">Author</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4">Highlights</th>
                        <th class="py-3 px-4">Date</th>
                        <th class="py-3 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800 text-slate-300">
                    <?php if (empty($articles)): ?>
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-500 text-xs">
                                No articles found. Click "Compose New Story" to publish the first story.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($articles as $art): ?>
                            <tr class="hover:bg-slate-900/60 transition">
                                <td class="py-3 px-4 max-w-sm">
                                    <div class="flex items-center gap-2">
                                        <?php if ($art['is_hero_lead']): ?>
                                            <span class="bg-amber-400 text-slate-950 font-black text-[9px] px-1.5 py-0.5 rounded shadow">HERO</span>
                                        <?php endif; ?>
                                        <?php if ($art['is_breaking']): ?>
                                            <span class="bg-rose-600 text-white font-black text-[9px] px-1.5 py-0.5 rounded shadow animate-pulse">FLASH</span>
                                        <?php endif; ?>
                                        <a href="../article.php?slug=<?= sanitize_output($art['slug']) ?>" target="_blank" class="font-bold text-white hover:text-amber-400 line-clamp-2">
                                            <?= sanitize_output($art['title']) ?>
                                        </a>
                                    </div>
                                    <?php if (!empty($art['youtube_video_id'])): ?>
                                        <span class="inline-flex items-center gap-1 text-[10px] text-rose-400 mt-1">
                                            <i class="fa-brands fa-youtube"></i> <?= sanitize_output($art['youtube_video_id']) ?>
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-3 px-4 uppercase font-semibold text-[11px] text-slate-400">
                                    <?= sanitize_output($art['category']) ?>
                                    <?php if (!empty($art['subcategory'])): ?>
                                        <span class="block text-[10px] text-slate-500"><?= sanitize_output($art['subcategory']) ?></span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-3 px-4 text-slate-300"><?= sanitize_output($art['author_name']) ?></td>
                                <td class="py-3 px-4">
                                    <?php if ($art['status'] === 'published'): ?>
                                        <span class="bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 px-2 py-0.5 rounded font-bold text-[10px] uppercase">Published</span>
                                    <?php elseif ($art['status'] === 'review'): ?>
                                        <span class="bg-amber-500/20 text-amber-300 border border-amber-500/30 px-2 py-0.5 rounded font-bold text-[10px] uppercase">In Review</span>
                                    <?php else: ?>
                                        <span class="bg-slate-800 text-slate-400 border border-slate-700 px-2 py-0.5 rounded font-bold text-[10px] uppercase">Draft</span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-3 px-4">
                                    <?php if (!empty($art['highlight_1'])): ?>
                                        <span class="text-amber-400 font-bold" title="<?= sanitize_output($art['highlight_1']) ?>"><i class="fa-solid fa-circle-check text-xs"></i> 3/3 Set</span>
                                    <?php else: ?>
                                        <span class="text-slate-600"><i class="fa-regular fa-circle"></i> None</span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-3 px-4 text-slate-400 text-[11px] whitespace-nowrap">
                                    <?= date('d M Y', strtotime($art['published_at'] ?? $art['created_at'])) ?>
                                </td>
                                <td class="py-3 px-4 text-right whitespace-nowrap">
                                    <a href="articles.php?action=edit&id=<?= $art['id'] ?>" class="text-slate-300 hover:text-amber-400 px-2 py-1 transition" title="Edit Story">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <?php if ($user_role !== 'reporter'): ?>
                                        <a href="articles.php?action=delete&id=<?= $art['id'] ?>&token=<?= csrf_token_generate() ?>" onclick="return confirm('Permanently remove this article?');" class="text-slate-400 hover:text-rose-400 px-2 py-1 transition" title="Delete Story">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php
});
