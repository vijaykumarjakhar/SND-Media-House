<?php
/**
 * SND Media — Dynamic XML Sitemap Generator
 * Conforms to Google Search Console XML Sitemap Protocol 0.9
 */

header('Content-Type: application/xml; charset=utf-8');

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/settings.php';
require_once __DIR__ . '/includes/functions.php';

$domain = get_setting('site_domain', 'sndmedia.in');
$base_url = 'https://' . rtrim($domain, '/');

$urls = [
    [
        'loc' => $base_url . '/',
        'lastmod' => date('Y-m-d'),
        'changefreq' => 'hourly',
        'priority' => '1.0'
    ],
    [
        'loc' => $base_url . '/studio-rental',
        'lastmod' => date('Y-m-d'),
        'changefreq' => 'daily',
        'priority' => '0.9'
    ],
    [
        'loc' => $base_url . '/legal/privacy-policy',
        'lastmod' => date('Y-m-d'),
        'changefreq' => 'monthly',
        'priority' => '0.5'
    ],
    [
        'loc' => $base_url . '/legal/terms-of-service',
        'lastmod' => date('Y-m-d'),
        'changefreq' => 'monthly',
        'priority' => '0.5'
    ],
    [
        'loc' => $base_url . '/legal/rental-rules',
        'lastmod' => date('Y-m-d'),
        'changefreq' => 'monthly',
        'priority' => '0.6'
    ],
    [
        'loc' => $base_url . '/legal/editorial-ethics',
        'lastmod' => date('Y-m-d'),
        'changefreq' => 'monthly',
        'priority' => '0.7'
    ]
];

$pdo = get_db_connection();
if ($pdo) {
    try {
        $stmt = $pdo->query("SELECT slug, updated_at, published_at FROM articles WHERE status = 'published' ORDER BY published_at DESC LIMIT 500");
        while ($row = $stmt->fetch()) {
            $lastmod = !empty($row['updated_at']) ? date('Y-m-d', strtotime($row['updated_at'])) : date('Y-m-d', strtotime($row['published_at']));
            $urls[] = [
                'loc' => $base_url . '/article/' . urlencode($row['slug']),
                'lastmod' => $lastmod,
                'changefreq' => 'weekly',
                'priority' => '0.8'
            ];
        }
    } catch (Exception $e) {
        // Silently continue with static URLs
    }
}

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<?php foreach ($urls as $u): ?>
  <url>
    <loc><?= htmlspecialchars($u['loc'], ENT_XML1, 'UTF-8') ?></loc>
    <lastmod><?= htmlspecialchars($u['lastmod'], ENT_XML1, 'UTF-8') ?></lastmod>
    <changefreq><?= htmlspecialchars($u['changefreq'], ENT_XML1, 'UTF-8') ?></changefreq>
    <priority><?= htmlspecialchars($u['priority'], ENT_XML1, 'UTF-8') ?></priority>
  </url>
<?php endforeach; ?>
</urlset>
