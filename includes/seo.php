<?php
/**
 * SND Media — Enterprise SEO & Structured Data (JSON-LD) Engine
 * Implements Google Search Essentials, OpenGraph 2.0, Twitter Cards & Schema.org 2026 specs
 */

require_once __DIR__ . '/../config/settings.php';
require_once __DIR__ . '/functions.php';

function render_seo_tags($params = []) {
    $settings = get_all_settings();
    $site_name = $settings['site_name'] ?? 'SND Media';
    $site_domain = $settings['site_domain'] ?? 'sndmedia.in';
    $base_url = 'https://' . rtrim($site_domain, '/');

    // Title construction
    $raw_title = !empty($params['title']) ? trim($params['title']) : 'National News & Media Network';
    if (stripos($raw_title, $site_name) === false) {
        $meta_title = $raw_title . ' | ' . $site_name;
    } else {
        $meta_title = $raw_title;
    }

    // Description normalization (160 characters max, single line, no markup)
    $raw_desc = !empty($params['description']) ? $params['description'] : ($settings['site_meta_description'] ?? 'SND Media — 24/7 Digital Newsroom, Official @sndschoolmedia Broadcasts & Commercial Production Studio Rentals at SND Public School Campus, Palwal (Delhi NCR).');
    $clean_desc = preg_replace('/\s+/', ' ', trim(strip_tags((string)$raw_desc)));
    if (mb_strlen($clean_desc) > 165) {
        $meta_description = mb_substr($clean_desc, 0, 160) . '...';
    } else {
        $meta_description = $clean_desc;
    }

    // Canonical URL
    if (!empty($params['canonical'])) {
        $canonical = $params['canonical'];
    } else {
        $req_path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
        $canonical = $base_url . ($req_path === '/index.php' ? '/' : $req_path);
    }

    // Robots directive
    $is_noindex = !empty($params['noindex']);
    $robots = $is_noindex ? 'noindex, nofollow' : 'index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1';

    // Social Media Image
    $image = !empty($params['image']) ? $params['image'] : $base_url . '/assets/images/snd-banner.svg';
    if (!preg_match('#^https?://#i', $image)) {
        $image = $base_url . '/' . ltrim($image, '/');
    }

    $type = !empty($params['type']) ? $params['type'] : 'website';
    $keywords = !empty($params['keywords']) ? $params['keywords'] : 'SND Media, SND News, Palwal News, Haryana Media, Commercial Studio Rental, Podcast Studio Delhi NCR, SND Public School Media';
    ?>

    <!-- Primary Metadata -->
    <title><?= sanitize_output($meta_title) ?></title>
    <meta name="description" content="<?= sanitize_output($meta_description) ?>">
    <meta name="keywords" content="<?= sanitize_output($keywords) ?>">
    <meta name="robots" content="<?= sanitize_output($robots) ?>">
    <meta name="author" content="SND Media House">
    <link rel="canonical" href="<?= sanitize_output($canonical) ?>">

    <!-- Open Graph / Facebook -->
    <meta property="og:locale" content="en_IN">
    <meta property="og:site_name" content="<?= sanitize_output($site_name) ?>">
    <meta property="og:title" content="<?= sanitize_output($meta_title) ?>">
    <meta property="og:description" content="<?= sanitize_output($meta_description) ?>">
    <meta property="og:url" content="<?= sanitize_output($canonical) ?>">
    <meta property="og:type" content="<?= $type === 'article' ? 'article' : ($type === 'service' ? 'business.business' : 'website') ?>">
    <meta property="og:image" content="<?= sanitize_output($image) ?>">
    <meta property="og:image:secure_url" content="<?= sanitize_output($image) ?>">
    <meta property="og:image:alt" content="<?= sanitize_output($meta_title) ?>">

    <!-- Twitter Cards -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:site" content="<?= sanitize_output($settings['yt_handle'] ?? '@sndschoolmedia') ?>">
    <meta name="twitter:creator" content="<?= sanitize_output($settings['yt_handle'] ?? '@sndschoolmedia') ?>">
    <meta name="twitter:title" content="<?= sanitize_output($meta_title) ?>">
    <meta name="twitter:description" content="<?= sanitize_output($meta_description) ?>">
    <meta name="twitter:image" content="<?= sanitize_output($image) ?>">
    <meta name="twitter:image:alt" content="<?= sanitize_output($meta_title) ?>">

    <?php if ($type === 'article' && !empty($params['article'])): 
        $art = $params['article'];
    ?>
    <!-- Article Specific Social Meta -->
    <meta property="article:published_time" content="<?= !empty($art['published_at']) ? date('c', strtotime($art['published_at'])) : date('c') ?>">
    <meta property="article:modified_time" content="<?= !empty($art['updated_at']) ? date('c', strtotime($art['updated_at'])) : date('c') ?>">
    <meta property="article:section" content="<?= sanitize_output($art['category'] ?? 'National') ?>">
    <meta property="article:author" content="<?= sanitize_output($art['author_name'] ?? 'SND News Desk') ?>">
    <?php endif; ?>

    <!-- Schema 1: NewsMediaOrganization & Broadcast Network -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "NewsMediaOrganization",
      "@id": "<?= $base_url ?>/#organization",
      "name": "<?= addslashes($site_name) ?>",
      "alternateName": "SND Media House",
      "legalName": "SND Media Pvt. Ltd.",
      "url": "<?= $base_url ?>",
      "logo": {
        "@type": "ImageObject",
        "url": "<?= $base_url ?>/assets/images/snd-logo.svg",
        "width": 240,
        "height": 240
      },
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
      "geo": {
        "@type": "GeoCoordinates",
        "latitude": 28.1487,
        "longitude": 77.3320
      },
      "contactPoint": {
        "@type": "ContactPoint",
        "telephone": "<?= addslashes($settings['phone_studio'] ?? '+91-7206622711') ?>",
        "contactType": "newsroom",
        "email": "<?= addslashes($settings['email_newsdesk'] ?? 'newsdesk@sndmedia.in') ?>",
        "areaServed": "IN",
        "availableLanguage": ["en", "hi"]
      },
      "publishingPrinciples": "<?= $base_url ?>/legal/editorial-ethics",
      "correctionsPolicy": "<?= $base_url ?>/legal/editorial-ethics"
    }
    </script>

    <!-- Schema 2: WebSite with Sitelinks SearchBox -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "WebSite",
      "@id": "<?= $base_url ?>/#website",
      "url": "<?= $base_url ?>/",
      "name": "<?= addslashes($site_name) ?>",
      "description": "<?= addslashes($clean_desc) ?>",
      "publisher": {
        "@id": "<?= $base_url ?>/#organization"
      },
      "inLanguage": "en-IN",
      "potentialAction": {
        "@type": "SearchAction",
        "target": "<?= $base_url ?>/index.php?q={search_term_string}",
        "query-input": "required name=search_term_string"
      }
    }
    </script>

    <?php 
    // Schema 3: NewsArticle (for News Content)
    if ($type === 'article' && !empty($params['article'])): 
        $art = $params['article'];
    ?>
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "NewsArticle",
      "mainEntityOfPage": {
        "@type": "WebPage",
        "@id": "<?= $canonical ?>"
      },
      "headline": "<?= addslashes($art['title'] ?? '') ?>",
      "description": "<?= addslashes($art['summary'] ?? '') ?>",
      "image": [
        "<?= addslashes($art['featured_image'] ?? $image) ?>"
      ],
      "datePublished": "<?= !empty($art['published_at']) ? date('c', strtotime($art['published_at'])) : date('c') ?>",
      "dateModified": "<?= !empty($art['updated_at']) ? date('c', strtotime($art['updated_at'])) : date('c') ?>",
      "author": {
        "@type": "Person",
        "name": "<?= addslashes($art['author_name'] ?? 'SND News Desk') ?>",
        "jobTitle": "Journalist / News Editor",
        "worksFor": {
          "@id": "<?= $base_url ?>/#organization"
        }
      },
      "publisher": {
        "@id": "<?= $base_url ?>/#organization"
      },
      "articleSection": "<?= addslashes($art['category'] ?? 'National') ?>",
      "inLanguage": "en-IN",
      "speakable": {
        "@type": "SpeakableSpecification",
        "cssSelector": [".article-headline", ".article-summary"]
      }
    }
    </script>
    <?php endif; ?>

    <?php 
    // Schema 4: LocalBusiness & Studio Infrastructure (for Studio Rental page)
    if ($type === 'service'): 
    ?>
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "LocalBusiness",
      "@id": "<?= $base_url ?>/studio-rental#facility",
      "name": "SND Media Commercial Studios & Production Floors",
      "alternateName": "SND Media Production Hub Palwal",
      "description": "High-definition soundproof commercial production studios, podcast suites, 4K multi-cam chroma stages, and outdoor billboard rentals at SND Campus, Palwal (Delhi NCR).",
      "url": "<?= $base_url ?>/studio-rental",
      "telephone": "<?= addslashes($settings['phone_studio'] ?? '+91-7206622711') ?>",
      "priceRange": "₹₹",
      "image": "<?= $base_url ?>/assets/images/snd-banner.svg",
      "address": {
        "@type": "PostalAddress",
        "streetAddress": "Palwal-Sohna Road, Village Meghpur",
        "addressLocality": "Palwal",
        "addressRegion": "Haryana",
        "postalCode": "121102",
        "addressCountry": "IN"
      },
      "geo": {
        "@type": "GeoCoordinates",
        "latitude": 28.1487,
        "longitude": 77.3320
      },
      "openingHoursSpecification": [
        {
          "@type": "OpeningHoursSpecification",
          "dayOfWeek": ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"],
          "opens": "07:00",
          "closes": "22:00"
        }
      ],
      "hasOfferCatalog": {
        "@type": "OfferCatalog",
        "name": "Studio Production Services & Space Hire",
        "itemListElement": [
          {
            "@type": "Offer",
            "itemOffered": {
              "@type": "Service",
              "name": "Production Studio Floor 01 (Multi-Cam 4K)",
              "description": "Soundproof acoustic 1,800 sq.ft stage with motorized grid, Blackmagic ATEM 4K switcher, and green room."
            },
            "price": "3500",
            "priceCurrency": "INR"
          },
          {
            "@type": "Offer",
            "itemOffered": {
              "@type": "Service",
              "name": "Acoustic Podcast Suite (4-Mic Rode Setup)",
              "description": "Broadcast-quality sound isolation suite for podcasts, talk shows, and audiobooks."
            },
            "price": "1800",
            "priceCurrency": "INR"
          },
          {
            "@type": "Offer",
            "itemOffered": {
              "@type": "Service",
              "name": "Outdoor Highway Billboard Display",
              "description": "High-visibility billboard placement at SND Campus gate along the Palwal-Sohna Highway."
            },
            "price": "15000",
            "priceCurrency": "INR"
          }
        ]
      }
    }
    </script>
    <?php endif; ?>

    <?php 
    // Schema 5: BreadcrumbList Schema (if breadcrumbs provided or dynamic)
    if (!empty($params['breadcrumbs']) && is_array($params['breadcrumbs'])): 
    ?>
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "BreadcrumbList",
      "itemListElement": [
        <?php foreach ($params['breadcrumbs'] as $idx => $crumb): ?>
        {
          "@type": "ListItem",
          "position": <?= $idx + 1 ?>,
          "name": "<?= addslashes($crumb['name']) ?>",
          "item": "<?= addslashes($crumb['url']) ?>"
        }<?= $idx < count($params['breadcrumbs']) - 1 ? ',' : '' ?>
        <?php endforeach; ?>
      ]
    }
    </script>
    <?php endif; ?>

<?php
}
