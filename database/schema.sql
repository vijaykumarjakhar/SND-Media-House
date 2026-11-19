-- ==============================================================================
-- SND MEDIA — DATABASE SCHEMA & SEED DATA (PHASE 1)
-- Database Name: sndmedia_db
-- Character Set: utf8mb4 / utf8mb4_unicode_ci
-- Engine: InnoDB
-- ==============================================================================

SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;

CREATE DATABASE IF NOT EXISTS `sndmedia_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `sndmedia_db`;

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS `password_reset_tokens`;
DROP TABLE IF EXISTS `rental_enquiries`;
DROP TABLE IF EXISTS `studio_facilities`;
DROP TABLE IF EXISTS `youtube_bulletins`;
DROP TABLE IF EXISTS `breaking_ticker`;
DROP TABLE IF EXISTS `articles`;
DROP TABLE IF EXISTS `users`;
DROP TABLE IF EXISTS `system_settings`;
SET FOREIGN_KEY_CHECKS = 1;

-- ------------------------------------------------------------------------------
-- 1. Users Table (Authentication & RBAC)
-- ------------------------------------------------------------------------------
CREATE TABLE `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(50) NOT NULL UNIQUE,
    `email` VARCHAR(100) NOT NULL UNIQUE,
    `password_hash` VARCHAR(255) NOT NULL,
    `full_name` VARCHAR(100) NOT NULL,
    `role` ENUM('super_admin', 'editor', 'reporter', 'space_manager') NOT NULL DEFAULT 'reporter',
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `last_login` DATETIME NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_users_role` (`role`),
    INDEX `idx_users_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 2. Articles Table (Editorial Content Management)
-- ------------------------------------------------------------------------------
CREATE TABLE `articles` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(255) NOT NULL UNIQUE,
    `category` ENUM('national', 'business', 'tech', 'energy', 'exclusive', 'world') NOT NULL,
    `subcategory` VARCHAR(100) NULL,
    `tags` VARCHAR(500) NULL COMMENT 'Comma-separated tag list e.g. "economy,budget,infrastructure"',
    `meta_keywords` VARCHAR(500) NULL COMMENT 'SEO meta keywords for the article',
    `featured_image` VARCHAR(500) NOT NULL,
    `summary` TEXT NOT NULL,
    `highlight_1` VARCHAR(255) NULL,
    `highlight_2` VARCHAR(255) NULL,
    `highlight_3` VARCHAR(255) NULL,
    `content` LONGTEXT NOT NULL,
    `author_id` INT NULL,
    `author_name` VARCHAR(100) DEFAULT 'SND Special Desk',
    `read_time` VARCHAR(20) DEFAULT '4 min read',
    `verification_badge` ENUM('none', 'verified_report', 'special_investigation', 'ground_report', 'editorial_analysis') NOT NULL DEFAULT 'none',
    `youtube_video_id` VARCHAR(50) NULL,
    `is_hero_lead` TINYINT(1) DEFAULT 0,
    `is_breaking` TINYINT(1) DEFAULT 0,
    `status` ENUM('draft', 'review', 'published', 'archived') NOT NULL DEFAULT 'published',
    `published_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_articles_cat_status` (`category`, `status`),
    INDEX `idx_articles_hero` (`is_hero_lead`),
    INDEX `idx_articles_published` (`published_at`),
    CONSTRAINT `fk_articles_author` FOREIGN KEY (`author_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 3. Breaking Ticker Table (24x7 Marquee Bulletins)
-- ------------------------------------------------------------------------------
CREATE TABLE `breaking_ticker` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `ticker_text` VARCHAR(300) NOT NULL,
    `link_url` VARCHAR(500) NULL,
    `urgency_level` ENUM('breaking', 'flash', 'routine') NOT NULL DEFAULT 'breaking',
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `sort_order` INT NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_ticker_active_sort` (`is_active`, `sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 4. YouTube Bulletins Table (YouTube Broadcast Hub & Playlist)
-- ------------------------------------------------------------------------------
CREATE TABLE `youtube_bulletins` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `video_id` VARCHAR(50) NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `category_badge` VARCHAR(100) DEFAULT 'SPECIAL REPORT',
    `duration` VARCHAR(50) DEFAULT '12:40',
    `view_count` VARCHAR(50) DEFAULT 'Live / Recent',
    `thumbnail_url` VARCHAR(500) NULL COMMENT 'Auto-generated from https://img.youtube.com/vi/{id}/hqdefault.jpg unless overridden',
    `views_label` VARCHAR(50) DEFAULT 'Live / Recent',
    `is_main_featured` TINYINT(1) NOT NULL DEFAULT 0,
    `is_featured` TINYINT(1) NOT NULL DEFAULT 0,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `sort_order` INT NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_yt_featured` (`is_main_featured`),
    INDEX `idx_yt_is_featured` (`is_featured`),
    INDEX `idx_yt_sort` (`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 5. Studio Facilities Table (Commercial Spaces on Rent & Inventory)
-- ------------------------------------------------------------------------------
CREATE TABLE `studio_facilities` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `facility_code` VARCHAR(50) NOT NULL UNIQUE,
    `name` VARCHAR(150) NOT NULL,
    `facility_name` VARCHAR(150) NULL,
    `badge_label` VARCHAR(100) NULL COMMENT 'e.g. "Floor Space • Studio A"',
    `description` TEXT NULL,
    `short_description` TEXT NULL,
    `category` ENUM('studio', 'podcast', 'ad_space', 'ob_van') NOT NULL DEFAULT 'studio',
    `hourly_rate` DECIMAL(10,2) DEFAULT 0.00,
    `half_day_rate` DECIMAL(10,2) DEFAULT 0.00,
    `full_day_rate` DECIMAL(10,2) DEFAULT 0.00,
    `rate_display` VARCHAR(100) NOT NULL,
    `square_feet` INT NULL,
    `thumbnail_url` VARCHAR(500) NULL,
    `specifications` JSON NULL COMMENT 'Array of spec bullet strings',
    `cta_text` VARCHAR(100) DEFAULT 'Book Facility',
    `sort_order` INT NOT NULL DEFAULT 0,
    `is_available` TINYINT(1) NOT NULL DEFAULT 1,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_facilities_active` (`is_active`, `sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 6. Rental Enquiries Table (Commercial CRM & Leads)
-- ------------------------------------------------------------------------------
CREATE TABLE `rental_enquiries` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `client_name` VARCHAR(150) NOT NULL,
    `company_name` VARCHAR(150) NULL,
    `production_house` VARCHAR(150) NULL,
    `email` VARCHAR(150) NOT NULL,
    `phone` VARCHAR(30) NOT NULL,
    `facility_needed` VARCHAR(150) NULL,
    `facility_requested` VARCHAR(150) NULL,
    `preferred_date` DATE NULL,
    `target_date` DATE NULL,
    `slot_duration` VARCHAR(100) NULL,
    `requirement_details` TEXT NULL,
    `notes` TEXT NULL COMMENT 'Internal sales follow-up notes visible only to admin staff',
    `ip_address` VARCHAR(45) NULL,
    `status` ENUM('new', 'contacted', 'quoted', 'negotiating', 'booked', 'archived', 'rejected') NOT NULL DEFAULT 'new',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_enquiries_status` (`status`),
    INDEX `idx_enquiries_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 7. System Settings Table (Canonical Settings Registry)
-- ------------------------------------------------------------------------------
CREATE TABLE `system_settings` (
    `setting_key` VARCHAR(100) PRIMARY KEY,
    `setting_value` TEXT NOT NULL,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 8. Password Reset Tokens Table (1-Hour Expiry Cryptographic Tokens)
-- ------------------------------------------------------------------------------
CREATE TABLE `password_reset_tokens` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `token` VARCHAR(255) NOT NULL,
    `expires_at` DATETIME NOT NULL,
    `is_used` TINYINT(1) NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_tokens_lookup` (`token`, `is_used`, `expires_at`),
    CONSTRAINT `fk_tokens_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==============================================================================
-- SEED DATA
-- ==============================================================================

-- 1. Default Staff Users (Password for all seeded accounts: AdminPassword123!)
INSERT INTO `users` (`username`, `email`, `password_hash`, `full_name`, `role`, `is_active`) VALUES
('admin', 'admin@sndmedia.in', '$2y$12$hC4qi.WfOco.wvV7Jjeo4OhGpcQSY373BGrH3R7H/OfaC3mT9jFzO', 'SND Chief Executive', 'super_admin', 1),
('editor', 'editor@sndmedia.in', '$2y$12$hC4qi.WfOco.wvV7Jjeo4OhGpcQSY373BGrH3R7H/OfaC3mT9jFzO', 'Senior Newsroom Editor', 'editor', 1),
('reporter', 'reporter@sndmedia.in', '$2y$12$hC4qi.WfOco.wvV7Jjeo4OhGpcQSY373BGrH3R7H/OfaC3mT9jFzO', 'Special Investigative Reporter', 'reporter', 1),
('spacemanager', 'space@sndmedia.in', '$2y$12$hC4qi.WfOco.wvV7Jjeo4OhGpcQSY373BGrH3R7H/OfaC3mT9jFzO', 'Studio & Space Manager', 'space_manager', 1);

-- 2. System Settings (41 Official Canonical Settings Registry Keys)
INSERT INTO `system_settings` (`setting_key`, `setting_value`) VALUES
('site_name', 'SND Media'),
('site_domain', 'sndmedia.in'),
('site_tagline', 'Where Students Lead Media — Speak. Narrate. Discover.'),
('site_meta_description', 'SND Media (sndmedia.in) — 24/7 Digital Newsroom, Official @sndschoolmedia Broadcasts & Commercial Production Studio Rentals at SND Public School Campus, Palwal.'),
('editions_text', 'Palwal • New Delhi • NCR • National'),
('yt_handle', '@sndschoolmedia'),
('yt_subscriber_label', '3.36K Subs • 666+ Videos'),
('social_youtube', 'https://www.youtube.com/@sndschoolmedia'),
('social_twitter', 'https://twitter.com/sndschoolmedia'),
('social_instagram', 'https://instagram.com/sndschoolmedia'),
('social_linkedin', 'https://linkedin.com/company/sndmedia'),
('social_facebook', 'https://facebook.com/sndschoolmedia'),
('hq_address', 'SND Public School, Palwal-Sohna Road, Village Meghpur, Palwal, Haryana — 121102'),
('phone_studio', '+91-7206622711'),
('phone_whatsapp', '+917206622711'),
('email_newsdesk', 'newsdesk@sndmedia.in'),
('email_space', 'space@sndmedia.in'),
('email_ads', 'ads@sndmedia.in'),
('studio_rate_display', 'Rates From ₹3,500/hr'),
('media_kit_pdf_url', ''),
('sensex_value', '84,210'),
('sensex_change', '+0.42%'),
('sensex_direction', 'up'),
('nifty_value', '25,830'),
('nifty_change', '+0.38%'),
('nifty_direction', 'up'),
('market_auto_feed', '1'),
('market_last_fetched', NOW()),
('market_feed_status', 'cached'),
('ad_leaderboard_image', ''),
('ad_leaderboard_url', ''),
('ad_leaderboard_label', 'Advertise on SND Media'),
('ad_leaderboard_active', '0'),
('ad_sidebar_image', ''),
('ad_sidebar_url', ''),
('ad_sidebar_label', 'Commercial Space Availability'),
('ad_sidebar_active', '0'),
('policy_privacy', '<p>At SND Media (operating at sndmedia.in from the SND Public School Campus, Palwal, Haryana), we respect your personal privacy. We collect client enquiry details strictly for studio booking follow-up, newsletter delivery, and editorial feedback. No customer information is sold or shared with unauthorized commercial third parties.</p>'),
('policy_tos', '<p>Welcome to SND Media. By using our website and video streaming services, you agree to comply with our code of conduct and copyright standards. All editorial articles, broadcast reports, and multimedia graphics are owned by SND Media Pvt. Ltd. and protected under Indian intellectual property laws.</p>'),
('policy_rental_rules', '<p>Commercial studio rentals (Studio A Newsroom and Studio B Podcast Suite) are managed under direct booking agreements. Crews must follow campus security rules at SND Public School, Palwal. Equipment checkouts, power backup usage, and acoustic protocols must be signed off with the Space Manager before and after every shoot session.</p>'),
('policy_editorial_ethics', '<p>SND Media operates under strict human editorial verification. Our student correspondents and senior editors cross-verify factual sources prior to publishing. We do not deploy automated generative AI to write unverified news reports. Corrections are addressed transparently by our Senior Newsroom Desk.</p>');

-- 3. Breaking Ticker Marquee Items (Linked to Active Portal Routes)
INSERT INTO `breaking_ticker` (`ticker_text`, `link_url`, `urgency_level`, `is_active`, `sort_order`) VALUES
('CENTRE UNVEILS RS 1.2 LAKH CR HIGH-SPEED DIGITAL INFRASTRUCTURE CORRIDOR CONNECTING TIER-2 CITIES', 'article/centre-unveils-1-2-lakh-cr-digital-corridor', 'breaking', 1, 1),
('SND EXCLUSIVE: INSIDE INDIA''S NEXT-GEN EV MANUFACTURING GIGA-FACTORIES', 'article/india-next-gen-ev-battery-gigafactories-ncr-corridor', 'flash', 1, 2),
('ISRO ANNOUNCES ADVANCED AI WEATHER SATELLITE LAUNCH SCHEDULED FOR NEXT FORTNIGHT', 'article/isro-next-gen-weather-satellite-ai-forecasting-launch', 'routine', 1, 3),
('STUDIO SPACE NOTICE: SND STUDIO FLOOR A & PODCAST SUITE B NOW OPEN FOR BOOKINGS', 'studio-rental', 'breaking', 1, 4),
('GLOBAL MARKETS: ASIAN INDICES RALLY ON ROBUST MANUFACTURING DATA', 'index.php#news-desk', 'routine', 1, 5);

-- 4. YouTube Bulletins (Official @sndschoolmedia Videos & Live Streams)
INSERT INTO `youtube_bulletins` (`video_id`, `title`, `category_badge`, `duration`, `view_count`, `thumbnail_url`, `views_label`, `is_main_featured`, `is_featured`, `is_active`, `sort_order`) VALUES
('25tvHivopPE', 'GST Reform: Court Approval May Be Required Before Taxpayer Arrests', 'SPECIAL REPORT', '12:40', '1.1K views', 'https://img.youtube.com/vi/25tvHivopPE/hqdefault.jpg', '1.1K views • 1 month ago', 1, 1, 1, 1),
('h05GJIxJ3X4', 'LIVE | District Badminton Championship 2026 | Palwal | Day 1 | SND Media', 'SPORTS LIVE', '45:15', '1.4K views', 'https://img.youtube.com/vi/h05GJIxJ3X4/hqdefault.jpg', 'Streamed Live • 1.4K views', 0, 0, 1, 2),
('93D_Razv-nM', '95% Marks, But Not Ready for Life? Rethinking India''s Education System', 'EDUCATION PODCAST', '18:22', '2.5K views', 'https://img.youtube.com/vi/93D_Razv-nM/hqdefault.jpg', '1 month ago • 58 views', 0, 0, 1, 3),
('qjp36XeVlAQ', 'BRICS Summit 2026: Delhi में बदल रहा Global Power Balance | Modi, Xi & Putin', 'WORLD DESK', '14:50', '3.1K views', 'https://img.youtube.com/vi/qjp36XeVlAQ/hqdefault.jpg', '2 months ago • 67 views', 0, 0, 1, 4),
('Wl2bQlbmysk', 'Palwal Taekwondo Championship 2025 | LIVE from S.N.D. Public School', 'CAMPUS LIVE', '32:10', '446 views', 'https://img.youtube.com/vi/Wl2bQlbmysk/hqdefault.jpg', 'Streamed Live • 446 views', 0, 0, 1, 5);

-- 5. Studio Facilities (Commercial Production Spaces & Ad Inventory)
INSERT INTO `studio_facilities` (`facility_code`, `name`, `facility_name`, `badge_label`, `description`, `short_description`, `category`, `hourly_rate`, `half_day_rate`, `full_day_rate`, `rate_display`, `square_feet`, `thumbnail_url`, `specifications`, `cta_text`, `sort_order`, `is_available`, `is_active`) VALUES
('studio_a', 'Broadcast Newsroom & Debate Floor', 'Broadcast Newsroom & Chroma Floor (Studio A)', 'Floor Space • Studio A', 'Acoustically isolated 2,200 sq.ft stage equipped with 4-camera teleprompter setups, automated lighting grid, green room suites, and high-speed fiber uplink.', '2,200 sq.ft acoustically treated stage equipped with motorized grid lighting, cyclorama green screen, teleprompters, and 4K multi-cam SDI routing.', 'studio', 7500.00, 26000.00, 48000.00, 'Rates From ₹7,500/hr', 2200, 'https://images.unsplash.com/photo-1598488035139-bdbb2231ce04?auto=format&fit=crop&w=600&q=80', '["Blackmagic URSA 4K Multicam Set", "Gigabit Fiber Redundant Uplink", "Dedicated Anchor & Guest Makeup Lounge", "Full Chroma Key Wall & Cyclorama"]', 'Book Studio A Inspection', 1, 1, 1),
('studio_b', 'Multi-Cam Visual Podcast Studio', 'Multi-Cam Visual Podcast Studio (Studio B)', 'Acoustic Suite • Studio B', 'Ideal for vodcasts, YouTube interviews, roundtable debates, and audio masterings with professional sound engineers on call.', 'Designed for conversational talk shows, corporate leadership interviews, and video podcasts. Shure SM7B microphones, Rodecaster Pro II, and PTZ cameras.', 'podcast', 3500.00, 12000.00, 22000.00, 'Rates From ₹3,500/hr', 450, 'https://images.unsplash.com/photo-1590602847861-f357a9332bbc?auto=format&fit=crop&w=600&q=80', '["Shure SM7B Broadcast Mics (Up to 6)", "Sony FX3 4K Cinema PTZ Angles", "Instant Raw SSD Handover or Live Mix", "Dedicated Audio Engineer on Duty"]', 'Book Podcast Slot', 2, 1, 1),
('ad_space', 'Digital Ad Space & Campus Billboards', 'Outdoor Billboards & Web Ad Space', 'Media Inventory • Advertising', 'Monetize your brand across SND''s multi-million audience. Rent top billboard banners, YouTube video sponsorships, and advertorials.', 'Prime outdoor hoardings along high-traffic Palwal-Sohna highway frontage at SND Campus, paired with digital website leaderboard slots reaching Haryana and NCR.', 'ad_space', 0.00, 0.00, 0.00, 'Custom Packages', NULL, 'https://images.unsplash.com/photo-1542744094-3a31f272c490?auto=format&fit=crop&w=600&q=80', '["45,000+ Highway Traffic Vehicles / Day", "666+ Broadcast Videos (@sndschoolmedia)", "Prime Billboard Hoardings at Palwal Campus", "Multi-Platform Sponsorship Packages"]', 'Enquire For Ad Inventory', 3, 1, 1);

-- 6. Initial News Articles (All 6 Categories Covered + 1 Editorial Review Story)
INSERT INTO `articles` (`title`, `slug`, `category`, `subcategory`, `tags`, `meta_keywords`, `featured_image`, `summary`, `highlight_1`, `highlight_2`, `highlight_3`, `content`, `author_id`, `author_name`, `read_time`, `verification_badge`, `youtube_video_id`, `is_hero_lead`, `is_breaking`, `status`, `published_at`) VALUES
('Centre Unveils ₹1.2 Lakh Cr Digital Corridor Connecting Tier-2 Industrial Hubs', 'centre-unveils-1-2-lakh-cr-digital-corridor', 'national', 'Infrastructure', 'infrastructure,budget,tier-2,technology', 'digital corridor, infrastructure investment, tier 2 cities', 'https://images.unsplash.com/photo-1570168007204-dfb528c6958f?auto=format&fit=crop&w=1400&q=85', 'The union cabinet today sanctioned an ambitious multi-modal fiber and highway network linking 40 emerging industrial clusters directly to dedicated freight lines.', '₹1.2 Lakh Cr Outlay: Key connectivity lines identified around NCR, Bengaluru, Mumbai-Pune, and Hyderabad peripheral corridors.', 'Zero-Latency Fiber Mesh: Over 24,000 km of carrier-grade optical highway to boost AI and semiconductor manufacturing clusters.', 'Public-Private Consortiums: Execution slated in three fast-track phases under Gati Shakti oversight.', '<p>The Union Cabinet today approved the ambitious Digital Industrial Corridor Framework, pledging ₹1.2 lakh crore toward multi-modal connectivity, carrier-grade fiber backbones, and smart freight integration across 40 emerging Tier-2 hubs.</p><p>Speaking at the national press briefing in New Delhi, the infrastructure task force emphasized that Tier-2 cities in Haryana, Uttar Pradesh, Maharashtra, and Karnataka will benefit from direct zero-latency links to central cloud hubs.</p><blockquote>"Regional manufacturing can only compete globally when digital infrastructure matches physical speed," the announcement noted.</blockquote><p>SND Media ground reporters visited the pilot junction on the Palwal-Sohna corridor, where initial survey milestones have already been reached.</p>', 1, 'SND Investigative Bureau', '4 MIN READ', 'special_investigation', '25tvHivopPE', 1, 1, 'published', NOW()),

('GST Reform: Court Approval May Be Required Before Taxpayer Arrests', 'gst-reform-court-approval-before-arrests', 'business', 'Tax Policy', 'gst,tax-reform,business,finance', 'gst reform, tax arrests, court sanction, business news', 'https://img.youtube.com/vi/25tvHivopPE/hqdefault.jpg', 'Under proposed legislative amendments, investigating authorities may soon require prior judicial sanction before initiating arrests in alleged tax default cases.', 'Judicial Oversight: Proposed amendments mandate magistrate sanction prior to coercive tax enforcement actions.', 'Threshold Revision: The minimum disputed tax quantum for custodial actions raised significantly.', 'Industry Welcome: Federation of trade associations commends the balanced safeguard against arbitrary summons.', '<p>In what is being termed a milestone relief for enterprise and small businesses, the Ministry of Finance has drafted recommendations mandating court approval before tax enforcement agencies can effect arrests under GST provisions.</p><p>The move aims to differentiate between procedural accounting mismatches and wilful fraudulent evasion, establishing clear safeguards for entrepreneurs.</p>', 2, 'Senior Business Desk', '3 MIN READ', 'verified_report', '25tvHivopPE', 0, 0, 'published', NOW() - INTERVAL 2 HOUR),

('ISRO Schedules Next-Gen AI Weather & Disaster-Forecasting Satellite Launch', 'isro-next-gen-weather-satellite-ai-forecasting-launch', 'tech', 'Space & AI', 'isro,space,weather-satellite,ai-forecasting', 'isro launch, ai weather satellite, disaster prediction, space tech india', 'https://images.unsplash.com/photo-1451187580459-43490279c0fa?auto=format&fit=crop&w=1400&q=85', 'India''s space agency is preparing a dedicated meteorological satellite fitted with edge-AI atmospheric sensors to improve cyclone and heatwave alerts across northern plains.', 'Sub-Hour Alert Precision: Dual-band radiometers will capture hyper-local convective cloud shifts every 10 minutes.', 'Onboard Edge Telemetry: Automated anomaly filtering speeds up warnings to state disaster response cells across Haryana and NCR.', 'Open Research Feed: Student weather clubs and agricultural universities will receive structured telemetry streams.', '<p>The Indian Space Research Organisation (ISRO) has confirmed final integration milestones for its upcoming meteorological payload designed to pair high-resolution geostationary imaging with real-time predictive modeling.</p><p>Mission planners noted that faster thermal and moisture sweeps will directly assist district administrations and agrarian belts across Haryana and western Uttar Pradesh during extreme summer and monsoon transitions.</p><blockquote>"Minutes matter when tracking sudden squall lines and flash flood cells," mission scientists shared during the technical briefing.</blockquote><p>Academic institutions participating in India''s student science networks are slated to receive open-access atmospheric datasets shortly after orbital calibration.</p>', 3, 'SND Tech & Science Desk', '4 MIN READ', 'verified_report', NULL, 0, 0, 'published', NOW() - INTERVAL 3 HOUR),

('Palwal District Badminton Championship 2026 Concludes at SND Academy', 'palwal-district-badminton-championship-2026', 'national', 'Sports', 'sports,badminton,palwal,snd-academy', 'palwal badminton, district championship, snd public school', 'https://img.youtube.com/vi/h05GJIxJ3X4/hqdefault.jpg', 'Over 180 junior and senior shuttlers competed in the annual district tournament broadcast live on SND Media across two thrilling days.', 'Record Participation: 180+ players from 22 regional clubs competed in singles and doubles categories.', 'Live Multi-Cam Broadcast: SND Media students streamed all court finals in full high definition.', 'State Qualifiers: Top 4 seeded finalists earned spots representing the district in the upcoming Haryana State Games.', '<p>The Palwal District Badminton Championship 2026 concluded with electric final matches hosted at the indoor sports complex of SND Public School, Palwal.</p><p>SND Media broadcasted the entire tournament live on YouTube (@sndschoolmedia), managed from camera switching to commentary by Mass Media Studies students.</p>', 3, 'SND Sports Desk', '2 MIN READ', 'ground_report', 'h05GJIxJ3X4', 0, 0, 'published', NOW() - INTERVAL 5 HOUR),

('Inside India''s Next-Gen EV & Battery Giga-Factories Reshaping the Delhi-NCR Industrial Belt', 'india-next-gen-ev-battery-gigafactories-ncr-corridor', 'energy', 'Clean Energy & EV', 'ev,battery-gigafactory,clean-energy,ncr', 'ev manufacturing india, battery gigafactory, delhi ncr industrial corridor', 'https://images.unsplash.com/photo-1593941707882-a5bba14938c7?auto=format&fit=crop&w=1400&q=85', 'New lithium-iron-phosphate cell lines and solar microgrids along southern NCR expressways are accelerating domestic electric commercial vehicle production.', 'Localized Cell Chemistry: Next-phase factories target 65% domestic cathode and thermal-pack sourcing by 2027.', 'Solar-Powered Assembly: Captive rooftop arrays and battery storage banks cut industrial grid draw during peak shifts.', 'Skilled Youth Pipeline: Polytechnic and school vocational labs across Palwal-Faridabad are training diagnostic technicians.', '<p>India''s transition toward localized electric mobility is gaining industrial scale across southern Haryana and the Delhi-NCR belt, where newly commissioned battery assembly lines are integrating captive renewable microgrids.</p><p>Plant engineers touring the southern corridor highlighted faster thermal testing rigs and automated cell-grading units designed specifically for Indian summer operating conditions.</p><blockquote>"Reliable domestic cell packs are the true inflection point for commercial fleet electrification," industry analysts told SND Media.</blockquote><p>Regional technical institutes are aligning curriculum modules with high-voltage safety and battery management diagnostics to prepare graduating youth.</p>', 2, 'SND Energy & Mobility Desk', '4 MIN READ', 'editorial_analysis', NULL, 0, 0, 'published', NOW() - INTERVAL 8 HOUR),

('Rethinking India''s Education System: Moving Beyond 95% Marks to Practical Readiness', 'rethinking-indias-education-system-practical-skills', 'exclusive', 'Education & Society', 'education,skills,youth,future-of-work', 'indian education system, practical skills, youth employment', 'https://img.youtube.com/vi/93D_Razv-nM/hqdefault.jpg', 'Educationalists and student panels examine why high test scores must be paired with real-world creative media and technical vocational exposure.', 'Skills Mismatch: Industry recruiters cite a widening gap between textbook memorization and hands-on vocational competence.', 'Practical Media Curriculum: Hands-on student broadcasting exemplified at SND Media prepares youth for modern digital careers.', 'Holistic Assessment: NEP 2020 initiatives gain momentum as schools incorporate multimedia and civic reasoning labs.', '<p>In an insightful roundtable debate, educators and student journalists from SND Media explored the urgent need to bridge academic test metrics with practical vocational readiness.</p><p>The feature podcast highlights student projects that translate classroom theory into live news production, digital video editing, and public broadcasting.</p>', 2, 'SND Editorial Special', '5 MIN READ', 'special_investigation', '93D_Razv-nM', 0, 0, 'published', NOW() - INTERVAL 1 DAY),

('BRICS Summit 2026: Delhi Takes Center Stage in Global Economic Realignment', 'brics-summit-2026-delhi-global-economic-realignment', 'world', 'Geopolitics', 'brics,geopolitics,diplomacy,world-economy', 'brics summit 2026, global power balance, delhi summit', 'https://img.youtube.com/vi/qjp36XeVlAQ/hqdefault.jpg', 'Heads of state gather in New Delhi to formalize local-currency settlement pacts and digital trade corridors across developing economies.', 'Local Currency Settlement: Groundwork finalized for cross-border bilateral digital payment frameworks.', 'Strategic Energy Pacts: Renewables and critical minerals supply agreements headline the summit agenda.', 'Global Multi-Polarity: Emerging economies articulate a unified voice on technology transfer and fair trade tariffs.', '<p>The BRICS Summit 2026 convened in New Delhi with international attention focused on alternative settlement mechanisms, green transition pacts, and digital trade networks.</p><p>SND Media special desk provides comprehensive analysis and expert breakdown of the diplomatic communique.</p>', 1, 'SND World Bureau', '4 MIN READ', 'verified_report', 'qjp36XeVlAQ', 0, 0, 'published', NOW() - INTERVAL 2 DAY),

('Palwal-Sohna Smart Mobility Corridor: Ground Survey Report on Regional Logistics', 'palwal-sohna-smart-mobility-corridor-ground-survey', 'national', 'Regional Infrastructure', 'palwal,sohna,logistics,ground-report', 'palwal sohna corridor, haryana infrastructure, logistics survey', 'https://images.unsplash.com/photo-1513828583688-c52646db42da?auto=format&fit=crop&w=1400&q=85', 'Field correspondents inspect upcoming highway interchanges and fiber ducting work connecting Palwal industrial estates to Gurugram and Sohna.', 'Interchange Progress: Elevated ramps near Village Meghpur approach structural completion ahead of winter targets.', 'Freight Time Savings: Commercial transporters estimate 35% shorter transit loops toward the KMP Expressway.', 'Digital Wayfinding: Automated traffic telemetry poles slated for installation in Phase 2 rollout.', '<p>Field reporters from SND Media traveled along the Palwal-Sohna highway stretch to document civil construction milestones, utility ducting, and commuter feedback from local logistics operators.</p><p>This draft ground report is currently queued in the editorial review pipeline for senior desk fact-checking prior to public broadcast.</p>', 3, 'Special Investigative Reporter', '3 MIN READ', 'ground_report', NULL, 0, 0, 'review', NULL);

-- 7. Commercial Rental Enquiries (Studio Space CRM Seed Pipeline)
INSERT INTO `rental_enquiries` (`client_name`, `company_name`, `production_house`, `email`, `phone`, `facility_needed`, `facility_requested`, `preferred_date`, `target_date`, `slot_duration`, `requirement_details`, `notes`, `ip_address`, `status`) VALUES
('Rahul Sharma', 'Creative Studios India', 'Creative Studios India', 'rahul@creativestudios.com', '+919876543210', 'Studio A - Newsroom Stage (2200 sq ft)', 'Studio A - Newsroom Stage (2200 sq ft)', '2026-12-01', '2026-12-01', 'Full Day (8-10 Hours)', '4K multi-camera corporate leadership shoot with teleprompter operator.', 'Need 4K multi-cam setup for corporate leadership shoot.', '127.0.0.1', 'new'),
('Ananya Verma', 'NCR FinTech Insights', 'NCR FinTech Insights', 'ananya@ncrfintech.in', '+919811223344', 'Studio B - Multi-Cam Podcast Suite', 'Studio B - Multi-Cam Podcast Suite', '2026-12-05', '2026-12-05', 'Half Day (4 Hours)', '6-episode founder podcast series with 4 Shure SM7B mics and raw 4K SSD handover.', '6-episode founder podcast series with 4 Shure SM7B mics and raw 4K SSD handover.', '127.0.0.1', 'quoted'),
('Vikram Malhotra', 'Apex Mobility Motors', 'Apex Mobility Motors', 'vikram.m@apexmobility.in', '+919810556677', 'Outdoor Campus Billboard & Ad Hoardings', 'Outdoor Campus Billboard & Ad Hoardings', '2026-12-10', '2026-12-10', 'Monthly Contract', 'Palwal-Sohna highway 40x20 gantry hoarding + website leaderboard campaign.', 'Palwal-Sohna highway 40x20 gantry hoarding + website leaderboard campaign.', '127.0.0.1', 'booked');
