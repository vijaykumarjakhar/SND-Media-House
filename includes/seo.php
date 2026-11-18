<?php
/**
 * SND Media — Dynamic SEO & Structured Data (JSON-LD) Generator
 */

require_once __DIR__ . '/../config/settings.php';
require_once __DIR__ . '/functions.php';

function render_seo_tags($params = []) {
    $settings = get_all_settings();
    $site_name = $settings['site_name'] ?? 'SND Media';
    $site_domain = $settings['site_domain'] ?? 'sndmedia.in';
    $base_url = 'https://' . $site_domain;

    $title = !empty($params['title']) ? $params['title'] . ' | ' . $site_name : $site_name . ' | National News & Media Network';
    $description = !empty($params['description']) ? $params['description'] : ($settings['site_meta_description'] ?? 'SND Media — 24/7 Digital Newsroom, Official @sndschoolmedia Broadcasts & Commercial Production Studio Rentals at SND Public School Campus, Palwal.');
    $canonical = !empty($params['canonical']) ? $params['canonical'] : $base_url . ($_SERVER['REQUEST_URI'] ?? '');
    $image = !empty($params['image']) ? $params['image'] : $base_url . '/assets/images/snd-banner.svg';
    $type = !empty($params['type']) ? $params['type'] : 'website';

    // Output Meta Tags
    ?>
    <title><?= sanitize_output($title) ?></title>
    <meta name="description" content="<?= sanitize_output($description) ?>">
    <link rel="canonical" href="<?= sanitize_output($canonical) ?>">

    <!-- OpenGraph Social Meta -->
    <meta property="og:site_name" content="<?= sanitize_output($site_name) ?>">
    <meta property="og:title" content="<?= sanitize_output($title) ?>">
    <meta property="og:description" content="<?= sanitize_output($description) ?>">
    <meta property="og:url" content="<?= sanitize_output($canonical) ?>">
    <meta property="og:type" content="<?= sanitize_output($type) ?>">
    <meta property="og:image" content="<?= sanitize_output($image) ?>">

    <!-- Twitter Card Meta -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:site" content="<?= sanitize_output($settings['yt_handle'] ?? '@sndschoolmedia') ?>">
    <meta name="twitter:title" content="<?= sanitize_output($title) ?>">
    <meta name="twitter:description" content="<?= sanitize_output($description) ?>">
    <meta name="twitter:image" content="<?= sanitize_output($image) ?>">

    <!-- Organization & Local Business JSON-LD -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "NewsMediaOrganization",
      "name": "<?= addslashes($site_name) ?>",
      "url": "<?= $base_url ?>",
      "logo": "<?= $base_url ?>/assets/images/snd-logo.svg",
      "sameAs": [
        "<?= addslashes($settings['social_youtube'] ?? 'https://www.youtube.com/@sndschoolmedia') ?>",
        "<?= addslashes($settings['social_twitter'] ?? '') ?>",
        "<?= addslashes($settings['social_linkedin'] ?? '') ?>"
      ],
      "address": {
        "@type": "PostalAddress",
        "streetAddress": "Palwal-Sohna Road, Village Meghpur",
        "addressLocality": "Palwal",
        "addressRegion": "Haryana",
        "postalCode": "121102",
        "addressCountry": "IN"
      },
      "contactPoint": {
        "@type": "ContactPoint",
        "telephone": "<?= addslashes($settings['phone_studio'] ?? '+91-7206622711') ?>",
        "contactType": "newsroom",
        "email": "<?= addslashes($settings['email_newsdesk'] ?? 'newsdesk@sndmedia.in') ?>"
      }
    }
    </script>
    <?php

    // If Article Schema requested
    if ($type === 'article' && !empty($params['article'])) {
        $art = $params['article'];
        ?>
        <script type="application/ld+json">
        {
          "@context": "https://schema.org",
          "@type": "NewsArticle",
          "headline": "<?= addslashes($art['title'] ?? '') ?>",
          "description": "<?= addslashes($art['summary'] ?? '') ?>",
          "image": ["<?= addslashes($art['featured_image'] ?? $image) ?>"],
          "datePublished": "<?= !empty($art['published_at']) ? date('c', strtotime($art['published_at'])) : date('c') ?>",
          "dateModified": "<?= !empty($art['updated_at']) ? date('c', strtotime($art['updated_at'])) : date('c') ?>",
          "author": {
            "@type": "Person",
            "name": "<?= addslashes($art['author_name'] ?? 'SND News Desk') ?>"
          },
          "publisher": {
            "@type": "NewsMediaOrganization",
            "name": "<?= addslashes($site_name) ?>",
            "logo": {
              "@type": "ImageObject",
              "url": "<?= $base_url ?>/assets/images/snd-logo.svg"
            }
          }
        }
        </script>
        <?php
    }
}
