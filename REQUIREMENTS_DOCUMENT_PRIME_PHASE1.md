# Software Requirements Specification (SRS) & Project Scope Document
## Project: SND Media House — Digital Platform (Prime Edition)
**Client:** SND Media House (`sndmedia.in`)  
**Selected Base Template:** Concept 01 — SND Prime Broadcast (`concept_1_prime_broadcast.html`)  
**Phase:** Phase 1 (Production MVP — AI Features Deferred to Phase 2)  
**Date:** October 2026  
**Status:** Approved for Implementation  

---

## 1. Executive Summary & Objective

SND Media House is launching an integrated digital media portal (`https://sndmedia.in`) designed to serve three core business objectives:
1. **24/7 Digital News Publishing:** Continuous reporting across National, Business/Economy, Tech, and Exclusive investigative domains with high visual impact.
2. **YouTube Broadcast Synchronization:** Direct integration with SND's video broadcasting channel (`[@sndschoolmedia](https://www.youtube.com/@sndschoolmedia)`) to drive multi-platform viewership and live stream engagement.
3. **Commercial Studio & Ad Space Monetization:** Generating high-value B2B commercial leads for SND's physical studio spaces (Newsroom Floor, Chroma Stage, Podcast Suites) and media advertising inventory.

### Phase 1 Core Mandate:
As per client decision, **Artificial Intelligence (AI) features are strictly deferred to Phase 2**. Phase 1 will focus on a rock-solid, high-speed, human-editorial workflow, clean CMS administration, dynamic YouTube synchronization, and robust lead capture for studio rentals.

---

## 2. Phase 1 vs. Phase 2 Scope Boundary (AI Clarification)

| Feature / Capability | Phase 1 (Current Scope) | Phase 2 (Future Roadmap) |
| :--- | :--- | :--- |
| **News Summaries** | **Manual Editorial Highlights:** Editors write 2–3 key bullet points per major story via CMS. | **Automated AI TL;DR:** LLM-generated summaries and automated article synopses. |
| **Fact-Checking & Badges** | **Editorial Verification Tag:** Editorial staff flags articles as "Verified Report" or "Special Investigation". | **AI Fact-Checking Engine:** Automated cross-referencing against primary sources and claims databases. |
| **Audio Bulletins** | **Manual Podcast / Video Audio Embeds:** Embedded YouTube / SoundCloud clips. | **AI Voice Synthesizer:** Automated text-to-speech 60-second news audio briefs. |
| **Article Tagging & SEO** | **Standard Manual Taxonomy:** Human editors select category, subcategory, and meta keywords. | **Semantic AI Auto-Tagging:** Automated entity recognition and automated contextual linking. |
| **Content Recommendations** | **Category & Tag-based Related Stories:** Deterministic query for latest posts in same category. | **Personalized AI Recommendation Engine:** User reading history and behavioral machine learning. |

---

## 3. Brand & Visual Design Specifications

The platform will adhere strictly to the visual guidelines demonstrated in **Template 01 (SND Prime)**:
- **Design Philosophy:** Urgent, authoritative broadcast aesthetic (comparable to premier Indian national newsrooms like NDTV, India Today, and Times Now), accented with premium commercial leasing blocks.
- **Color Palette:**
  - `brand-red`: `#e11d48` (Primary breaking news red, ticker banner, accents)
  - `brand-crimson`: `#be123c` (Urgent badges, dark red gradients)
  - `brand-navy`: `#0f172a` (Secondary surfaces, cards)
  - `brand-dark`: `#080d1a` (Primary dark viewport background)
  - `brand-card`: `#131c31` (Container and card backgrounds)
  - `commercial-gold`: `#f59e0b` (Studio space on rent badges, commercial CTA buttons)
  - `commercial-amber`: `#d97706` (Hover states and pricing tags)
- **Typography:**
  - Headings: `Outfit`, sans-serif (Bold, punchy, high-impact headlines)
  - Body & UI: `Inter`, sans-serif (Clean legibility for dense news feeds)
  - Financial & Timestamps: `Space Grotesk` or Monospace

---

## 4. Detailed Functional Requirements

### 4.1. Global Header & Top Utility Bar
* **Top Utility Bar:**
  * **Edition & Date Stamp:** Dynamic Indian Standard Time (IST) date display with multi-city editions (e.g., "Sunday, 04 Oct 2026 | New Delhi • Mumbai • Bengaluru").
  * **Financial Indices Ticker:** Configurable display of benchmark market indicators (SENSEX, NIFTY) with change percentage and trend arrow indicators. *(Can be configured via CMS or connected to financial feed API)*.
  * **YouTube Channel Statistics & Subscribe CTA:** Direct clickable link to the YouTube channel with subscriber count badge.
* **Main Navigation Bar:**
  * Fixed / Sticky layout with frosted glassmorphism effect on scroll.
  * SND Media House logo and tagline ("BROADCAST • DIGITAL PORTAL • STUDIO INFRASTRUCTURE").
  * Primary navigation items:
    1. **Live Desk** (Urgent animated live indicator linking to breaking stream)
    2. **National**
    3. **Business & Tech**
    4. **YouTube TV** (Distinct red brand accent)
    5. **Rent Studio & Ad Space** (Prominent Amber pill badge)
  * Primary Header CTA: **"Book Studio Floor"** button triggering the rental modal.
  * Mobile Responsive Hamburger drawer containing all links and quick contact options.
* **24/7 Live Breaking News Ticker:**
  * Continuous horizontal marquee ticker running across the full viewport width.
  * Urgent "BREAKING 24x7" prefix badge with animated pulse indicator.
  * Hover-to-pause interaction allowing users to read active bulletins easily.
  * Managed via CMS with ability to set urgency levels (Breaking, Flash, Routine Notice).

---

### 4.2. Editorial News Desk & Articles Stream
* **Hero Lead Story (Spotlight Feature):**
  * High-resolution 16:9 featured imagery with dark vignette overlay.
  * Tag badges (e.g., "Special Report", "Economy & Infrastructure").
  * Metadata bar: Reporting desk / Author name, estimated read time, and "Updated X mins ago" timestamp.
  * Lead headline in responsive large typography with supporting 2-sentence summary.
  * **Manual Key Highlights Drawer (Phase 1):** Collapsible "Key Takeaways" box containing 3 verified bullet points curated by the editorial desk (replacing AI summary).
  * Video report indicator linking directly to the embedded video or YouTube segment.
* **Continuous News Desk Feed:**
  * Multi-tab category selector: "All", "National", "Business", "Tech", "Energy", "Exclusive", "World".
  * *(Canonical category taxonomy — applied consistently across nav tabs, article schema, and footer links: `national | business | tech | energy | exclusive | world`)*
  * Deep-link URL support: `index.php?cat={slug}` automatically activates the matching category tab on page load and scrolls to the News Desk.
  * 2-column responsive grid layout showcasing latest articles.
  * Compact horizontal cards featuring:
    * Thumbnail image with hover micro-zoom effect.
    * Category slug with color-coded classification.
    * 2-line truncated headline.
    * Publication timestamp, editorial desk tag, and optional human `verification_badge` (when not `'none'`).
* **Single Article Detail View Template:**
  * Full article layout with breadcrumbs (`Home > Category > Headline`).
  * Author byline with profile thumbnail, publish date, and update timestamp.
  * Social sharing buttons (WhatsApp, X / Twitter, Facebook, LinkedIn, Copy Link).
  * In-article media support (Full-width photo, YouTube video embed, photo galleries, blockquotes).
  * Contextual Commercial Ad Banner slot (Leaderboard & Sidebar MPU) for internal space leasing or external sponsors, driven by canonical `system_settings` keys (`ad_leaderboard_image`, `ad_leaderboard_url`, `ad_leaderboard_label`, `ad_leaderboard_active`, `ad_sidebar_image`, `ad_sidebar_url`, `ad_sidebar_label`, `ad_sidebar_active`) editable via the Admin CMS.
  * "Related News" grid displaying 3 recent stories from the same category.

---

### 4.3. SND YouTube TV & Broadcast Hub
* **Featured Embedded Player:**
  * Responsive 16:9 video player with custom privacy-enhanced embed (`youtube-nocookie.com`).
  * Live Broadcast badge with pulsing red indicator.
  * Direct "Subscribe" button opening channel in new tab.
* **Video Bulletins Playlist / Switcher:**
  * Interactive video list showcasing 3–5 recent bulletins or debate episodes.
  * Instant switching: Clicking any playlist thumbnail updates the primary video player without requiring a full page reload.
  * Title, thumbnail preview, timestamp, and approximate view count per item.
* **CMS Video Management:**
  * Ability for editors to paste any YouTube Video ID or URL to immediately update the main player or add to the playlist.
  * Support for both pre-recorded videos and live streams.

---

### 4.4. Commercial Media House Space & Studio Rentals (Revenue Driver)
This is a core USP distinguishing SND Media House from standard news blogs.

* **Spotlight Commercial Box (Sidebar Widget):**
  * High-visibility container framed in commercial gold/amber border.
  * Badge: "Commercial Space Available | Rates From ₹3,500/hr".
  * Quick facility badges:
    * Podcast Suite (Shure SM7B mics + 4K cams)
    * Studio Floor A (1800–2200 sq.ft + Chroma green screen)
    * Digital Ad Slots (Header & in-article banners)
    * OB Van & Live Uplink units
  * Primary Actions: **"Request Rate Card"** (opens modal) and **"Call Space Desk"** (direct `tel:` link).
* **Detailed Studio & Infrastructure Showcase Section:**
  * Dedicated full-width section with 3 comprehensive commercial service cards:
  * **Section Header CTA:** "Download Media Kit & Rate Card" button — in Phase 1, this opens the booking/enquiry modal. A CMS-uploadable PDF media kit file field (`media_kit_pdf_url` in `system_settings`) is provisioned; if a PDF URL is set by admin, the button triggers a direct download instead of the modal. If the field is empty, it defaults to opening the enquiry modal.
    1. **Studio A — Broadcast Newsroom & Debate Floor:**
       * Pricing indicator (e.g., ₹35,000 / Day).
       * Feature checklist: Acoustically treated 2,200 sq.ft stage, Blackmagic 4K Multicam, redundant fiber uplink, makeup and green room.
       * CTA: "Book Studio A Inspection".
    2. **Studio B — Multi-Cam Visual Podcast Suite:**
       * Pricing indicator (e.g., ₹3,500 / Hour).
       * Feature checklist: Shure SM7B mics, Sony FX3 PTZ cameras, sound engineer on duty, instant raw footage handover.
       * CTA: "Book Podcast Slot".
    3. **Media Inventory — Digital Ad Space & News Sponsorship:**
       * Pricing indicator (Custom packages).
       * Audience metrics callout (Monthly web pageviews, YouTube subscriber reach, physical billboard locations).
       * CTA: "Enquire For Ad Inventory".
* **Lead Capture & Booking Modal:**
  * Responsive pop-up modal accessible from any "Book" or "Rate Card" button.
  * Input fields:
    * Client Full Name *(Required, minimum 2 characters)*
    * Production House / Organization *(Optional)*
    * Email Address *(Required, validated)*
    * Phone / WhatsApp Number *(Required, Indian & International 10-digit validation)*
    * Facility Required *(Dropdown: Studio A Newsroom, Studio B Podcast Suite, Website Ad Banners, Billboard Space, Full Media Floor Monthly Lease)*
    * Target Date / Shoot Schedule *(Datepicker)*
    * Project Requirements & Scope *(Textarea for crew size, hours, equipment)*
  * Submission Actions:
    * Instant on-screen confirmation notice.
    * Email alert dispatch to `space@sndmedia.in`.
    * Optional direct WhatsApp redirection with pre-filled enquiry text for immediate interaction.
    * Lead stored in backend database for sales team follow-up.

---

### 4.5. Footer & Legal Infrastructure
* **Branding & About Summary:** Clear summary of SND Media House (`sndmedia.in`) operations.
* **Quick Navigation Columns:**
  * News categories directory.
  * Studio rental facility direct links.
  * Editorial and corporate headquarters address (`SND Public School, Palwal-Sohna Road, Village Meghpur, Palwal, Haryana — 121102`).
  * Direct contact numbers and official domain (`sndmedia.in`) desk emails (`newsdesk@sndmedia.in`, `space@sndmedia.in`, `ads@sndmedia.in`).
* **Compliance & Legal Pages:**
  * Terms of Service
  * Privacy Policy
  * Commercial Studio Rental Rules & Security Guidelines
  * Editorial Ethics & Corrections Policy

---

### 4.6. Role-Based Access Control (RBAC) Specification
To ensure strict security and separation of duties across newsroom journalists, administrative leadership, and commercial sales executives, the Admin CMS implements granular Role-Based Access Control (RBAC).

#### 4 User Roles Defined:
1. **Super Admin (Executive / System Admin):**
   * Unrestricted access across the entire platform.
   * Can create, edit, deactivate admin user accounts and assign roles.
   * Modifies global site settings, contact details, social links, and financial ticker overrides.
   * Access to raw system logs, database backups, and lead reports.
2. **Editor / Senior Newsroom Editor:**
   * Full editorial authority over content publishing.
   * Create, edit, approve, publish, and delete any news articles.
   * Manage 24x7 Breaking News Marquee ticker items (add, re-order, expire).
   * Update the YouTube Broadcast TV featured video and bulletin playlist.
   * *Restricted from:* User account management and core system settings.
3. **Reporter / Contributing Journalist:**
   * Authoring role for newsroom staff and stringers.
   * Create, write, and submit news articles (with 3-bullet Key Highlights) into "Draft / Pending Review" status.
   * Edit only their own submitted articles.
   * *Restricted from:* Direct publishing, breaking ticker, YouTube hub, and commercial leads.
4. **Space & Commercial Manager (Studio Leasing Desk):**
   * Dedicated operational role for SND's studio rental and advertising sales team.
   * View, filter, and manage incoming Studio Rental inquiries and B2B leads.
   * Update inquiry workflow status (`New` ➔ `Contacted` ➔ `Negotiating` ➔ `Booked` ➔ `Archived`).
   * Add internal follow-up notes and export lead sheets to CSV/Excel.
   * Edit commercial facility details, hourly/daily rate cards, and equipment lists.
   * *Restricted from:* Editorial news publishing, breaking ticker, and user management.

#### RBAC Permissions Matrix:

| Feature / Module | Super Admin | Editor | Reporter | Space Manager |
| :--- | :---: | :---: | :---: | :---: |
| **Manage Users & Assign Roles** | ✅ Full | ❌ | ❌ | ❌ |
| **Global Site Settings & Branding** | ✅ Full | ❌ | ❌ | ❌ |
| **Publish / Edit Any Article** | ✅ Full | ✅ Full | ❌ (Own Drafts Only) | ❌ |
| **24x7 Breaking Ticker Desk** | ✅ Full | ✅ Full | ❌ | ❌ |
| **YouTube TV Hub & Playlist** | ✅ Full | ✅ Full | ❌ | ❌ |
| **Studio Facilities & Pricing Editor** | ✅ Full | ❌ | ❌ | ✅ Full |
| **Commercial Leads CRM & Export** | ✅ Full | ❌ | ❌ | ✅ Full |

---

### 4.7. Dynamic Content Management (Admin Controllable)
**Zero Hardcoded Content:** Every visual element and configuration option visible on the public frontend must be dynamically editable by authorized admin users via the CMS:

1. **Header & Utility Bar:**
   * Edition locations text (e.g. `Palwal • New Delhi • NCR • National`).
   * Official `@sndschoolmedia` brand assets (`assets/images/snd-logo.svg`, `assets/images/snd-banner.svg`, `assets/images/snd-channel-banner.png`), YouTube handle label (`@sndschoolmedia`, linked to `https://www.youtube.com/@sndschoolmedia`), and subscriber count text (`3.36K Subs • 666+ Videos`).
   * Market Indices (Sensex / Nifty values and % changes can be manually updated or set to auto-API).
2. **24x7 Breaking Ticker Marquee:**
   * Add/edit/delete ticker bulletins, change urgency badge (Breaking / Flash / Exclusive), and toggle active/inactive status.
3. **News Articles & Editorial Spotlight:**
   * Featured hero lead story selection toggle and human editorial `verification_badge` (`none`, `verified_report`, `special_investigation`, `ground_report`).
   * Headline, category, slug, featured cover image URL or file upload (`assets/uploads/articles/`).
   * 3-bullet "Key Highlights" drawer per article.
   * Full body content with WYSIWYG / rich-text formatting (paragraphs, blockquotes, embeds).
4. **YouTube TV Broadcast Player:**
   * Primary featured video ID or URL (changes the live/promoted video immediately).
   * Playlist video bulletins (Video ID, title, view count/tag, sorting order).
5. **Commercial Spaces & Studio Leasing:**
   * Studio facility cards (Title, badge label, description, hourly/daily pricing e.g. `₹35,000 / Day`, thumbnail image upload/URL, specifications checklist, CTA button label, sort order, availability toggle).
   * In-article and sidebar commercial banner ads managed via `system_settings` keys (`ad_leaderboard_*` and `ad_sidebar_*`: image upload/URL, destination URL, alt/label text, active toggle).
6. **Footer & Compliance Blocks:**
   * Headquarters physical address (`SND Public School, Palwal-Sohna Road, Village Meghpur, Palwal, Haryana — 121102`), phone numbers, and operational desk emails (`space@sndmedia.in`, `newsdesk@sndmedia.in`, `ads@sndmedia.in`).
   * Social media links (YouTube `https://www.youtube.com/@sndschoolmedia`, Twitter/X, Instagram, LinkedIn, Facebook).
   * Legal policies (Privacy Policy, Terms of Service, Rental Guidelines, Editorial Ethics).
   * **Media Kit PDF URL** (`media_kit_pdf_url`): Optional URL or uploaded PDF path (`assets/uploads/docs/`) for a downloadable rate card. When populated, the "Download Media Kit & Rate Card" CTA on the studio section triggers a direct file download instead of the enquiry modal.

### 4.8. Legal Content Pages & Policy Route Specification
To close the legal/compliance gap in the project definition, the platform must include public policy pages for:
- `/legal/privacy-policy`
- `/legal/terms-of-service`
- `/legal/rental-rules`
- `/legal/editorial-ethics`

These clean routes must be handled via `.htaccess` (`RewriteRule ^legal/([a-z0-9-]+)$ legal.php?page=$1 [L,QSA]`) dispatching to `legal.php`, using content sourced from `system_settings` (`policy_privacy`, `policy_tos`, `policy_rental_rules`, `policy_editorial_ethics`), and must be linked from the footer and any compliance blocks. Each page must include the page title, a neutral editorial tone, and a sanitized rich-HTML policy body.

**HTML Sanitization vs. Escaping Standard:**
- **Plain-text fields** (headlines, summaries, names, addresses, ticker items, URLs, metadata) are escaped on output with `sanitize_output()` (`htmlspecialchars($str, ENT_QUOTES, 'UTF-8')`).
- **WYSIWYG rich-text fields** (`articles.content` and `policy_*`) are sanitized using `sanitize_rich_html()` — an HTML allowlist sanitizer permitting safe formatting tags (`<p>`, `<br>`, `<strong>`, `<em>`, `<ul>`, `<ol>`, `<li>`, `<h2>`, `<h3>`, `<h4>`, `<blockquote>`, `<a>`, `<img>`) while stripping `<script>`, `<iframe>`, `on*` event attributes, and `javascript:` URLs so editorial and legal formatting renders properly without XSS exposure.

The legal policy content must be editable via the Super Admin settings panel and reflect the approved client language.

### 4.9. Canonical Content Taxonomy & Settings Registry
The system must use a single canonical taxonomy for all article categories and navigation, with display labels mapped separately from internal slugs:

| Internal Code | Display Label |
| :--- | :--- |
| `national` | National |
| `business` | Business |
| `tech` | Tech |
| `energy` | Energy |
| `exclusive` | Exclusive |
| `world` | World |

The application must maintain a formal registry of all `system_settings` keys to prevent drift between admin UI, backend logic, and frontend rendering:
- **Utility Bar, Site Meta & Editions:** `site_meta_description`, `editions_text`, `yt_handle` (default `@sndschoolmedia`), `yt_subscriber_label`
- **Market Data:** `sensex_value`, `sensex_change`, `sensex_direction`, `nifty_value`, `nifty_change`, `nifty_direction`, `market_auto_feed`, `market_last_fetched`, `market_feed_status`
- **Contact & Offices:** `hq_address` (default `SND Public School, Palwal-Sohna Road, Village Meghpur, Palwal, Haryana — 121102`), `phone_studio` (default `+91-7206622711`), `phone_whatsapp` (default `+917206622711`), `email_newsdesk` (`newsdesk@sndmedia.in`), `email_space` (`space@sndmedia.in`), `email_ads` (`ads@sndmedia.in`)
- **Social Media Links:** `social_youtube` (default `https://www.youtube.com/@sndschoolmedia`), `social_twitter`, `social_instagram`, `social_linkedin`, `social_facebook`
- **Commercial Rates & Media Kit:** `studio_rate_display`, `media_kit_pdf_url`
- **Commercial Banner Ads:** `ad_leaderboard_image`, `ad_leaderboard_url`, `ad_leaderboard_label`, `ad_leaderboard_active`, `ad_sidebar_image`, `ad_sidebar_url`, `ad_sidebar_label`, `ad_sidebar_active`
- **Legal & Policy Pages:** `policy_privacy`, `policy_tos`, `policy_rental_rules`, `policy_editorial_ethics`

### 4.10. Accessibility & UX Baseline
The public site and admin CMS must meet a minimum accessibility baseline for usability and readability:
- Keyboard-accessible navigation and interactive modals.
- All interactive controls must have visible focus states and a minimum touch target of 44x44px.
- Text contrast should meet WCAG AA for standard body copy, buttons, and status badges.
- Form validation should provide text error messaging and not rely only on color.
- Motion should be reduced or disabled when `prefers-reduced-motion` is enabled.
- Screen-reader-friendly labels and semantic HTML must be used for all forms, buttons, and switches.

---

## 5. Non-Functional Requirements (NFR)

* **Performance & Speed:**
  * Core Web Vitals optimized (LCP < 2.0s, CLS < 0.05 on standard 4G mobile networks).
  * Lightweight vanilla CSS / Tailwind utility footprints; zero heavy bloated libraries.
  * Responsive image loading with explicit aspect ratios to avoid layout shifts.
* **Mobile Responsiveness:**
  * Strict mobile-first design tested across 360px to 4K resolutions.
  * Touch-friendly tap targets (minimum 44x44px for CTAs and navigation buttons).
* **SEO & Social Metadata:**
  * Dynamic OpenGraph (`og:image`, `og:title`, `og:description`) and Twitter Card markup for every article to maximize viral social sharing.
  * Structured Data Schema (canonical domain `https://sndmedia.in`):
    * `NewsArticle` schema for news reports.
    * `LocalBusiness` / `StudioOrRecordingStudio` schema for commercial space rental pages to dominate local search for studio rentals in Palwal, Haryana & Delhi NCR.
* **Security & Spam Protection:**
  * CSRF tokens on all form submissions.
  * Honeypot field and rate-limiting on the Studio Rental inquiry form to eliminate automated bot spam.
  * HTTPS enforcement with secure cookie headers.
* **Password Security Policy:**
  * All user passwords hashed using **PHP `password_hash()` with `PASSWORD_BCRYPT`** (cost factor ≥ 12).
  * Minimum password length: **10 characters**.
  * **Admin-initiated reset:** Super Admin can trigger a forced password reset for any user account via `admin/reset_password.php`. A unique cryptographic token (`bin2hex(random_bytes(32))`) is generated, stored in `password_reset_tokens`, and expires after **1 hour**.
  * **Self-service reset:** Users can request a reset link delivered to their registered email address.
  * Sessions are regenerated (`session_regenerate_id(true)`) on every successful login to prevent session fixation attacks.

---

## 6. Confirmed Technical Architecture & Specifications (Client Approved)

Following direct client confirmation, the system architecture for Phase 1 is locked in as follows:

### 6.1. Technology Stack
* **Backend Engine:** Custom **PHP (v8.x)** using a **Clean Modular Page-Controller + Shared Service/Helper Architecture** with native **PDO** for secure database access, prepared statements, and high throughput.
* **Architecture Pattern:**
  * **Page Controllers (`index.php`, `article.php`, `studio-rental.php`, `legal.php`, `admin/*.php`, `api/*.php`):** Handle request routing, parameter validation, authentication/RBAC guards, and prepare view data before rendering templates.
  * **Shared Config & Services (`config/`, `includes/`):** Reusable database connection (`config/database.php`), settings registry loader (`config/settings.php`), auth guards (`includes/auth.php`), SEO meta generator (`includes/seo.php`), and sanitizers/upload/YouTube/market helpers (`includes/functions.php`).
  * **UI Partials & Templates (`includes/header.php`, `includes/footer.php`, `includes/youtube_hub.php`, `includes/rental_modal.php`, `admin/layout.php`):** Clean presentation markup with zero business/SQL logic embedded in reusable partials.
* **Database:** **MySQL / MariaDB** (standard XAMPP environment).
* **Frontend Presentation:** Semantic HTML5, Tailwind CSS utilities (as established in Template Prime), Vanilla JavaScript (zero bloat, instant DOM updates).
* **Server Environment:** Apache on Windows/Linux (XAMPP-ready in local root `d:\PHP\WindowXAPP\htdocs\Websites\SND-Media-House`).

### 6.2. Financial Market Data Strategy (Sensex / Nifty)
* **Hybrid Free API + Manual Fallback Strategy:**
  1. System checks a lightweight free financial feed (e.g., Yahoo Finance public JSON endpoint or free market quote endpoint).
  2. Data is cached locally in the database/file for 15 minutes to prevent rate limits and ensure sub-second page loads.
  3. **Admin CMS Manual Override:** In case free external market feeds are down, weekend-closed, or throttled, editorial admins can manually enter SENSEX and NIFTY values and percentages via the admin settings panel.

### 6.3. YouTube Video Management Strategy (Zero Cost, No Paid API)
* **Manual Video ID / URL Admin Control:**
  * To avoid Google Cloud Console billing, quota limitations, and recurring API fees, Phase 1 implements a high-efficiency manual video manager.
  * Editorial admins simply paste the YouTube video link (e.g. `https://www.youtube.com/watch?v=...` or `https://youtu.be/...`) or 11-character Video ID directly in the Admin Panel.
  * The system automatically extracts the ID, fetches the standard YouTube thumbnail URL (`img.youtube.com/vi/{id}/hqdefault.jpg`), and immediately updates the featured player and recent bulletins list.

### 6.4. Commercial Studio Rental Booking & Lead Flow
* **Multi-Channel Notification Workflow:**
  1. **Database Logging:** Every submission is securely sanitized and saved in the `rental_enquiries` MySQL table with initial status `new` (full CRM pipeline: `new` ➔ `contacted` ➔ `negotiating` ➔ `booked` ➔ `archived` / `rejected`).
  2. **Email Dispatch:** Form automatically dispatches a structured notification email to the leasing desk (`space@sndmedia.in`).
  3. **Instant WhatsApp Pre-Filled Link:** Upon submission, the user receives an instant confirmation on-screen with an actionable **"Chat with Studio Space Manager on WhatsApp"** button (`https://wa.me/917206622711?text=Hello%20SND,%20I%20have%20submitted%20a%20booking%20enquiry%20for%20[Facility]%20on%20[Date]...`) for real-time closing.

---

## 7. Database Architecture & Schema (MySQL)

```sql
-- 1. User Authentication & Role-Based Access Control (RBAC)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    role ENUM('super_admin', 'editor', 'reporter', 'space_manager') NOT NULL DEFAULT 'reporter',
    is_active TINYINT(1) DEFAULT 1,
    last_login DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Editorial Articles Table
CREATE TABLE articles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    category ENUM('national', 'business', 'tech', 'energy', 'exclusive', 'world') NOT NULL,
    subcategory VARCHAR(100) NULL,
    tags VARCHAR(500) NULL COMMENT 'Comma-separated tag list e.g. "economy,budget,infrastructure"',
    meta_keywords VARCHAR(500) NULL COMMENT 'SEO meta keywords for the article',
    featured_image VARCHAR(500) NOT NULL,
    summary TEXT NOT NULL,
    highlight_1 VARCHAR(255) NULL,
    highlight_2 VARCHAR(255) NULL,
    highlight_3 VARCHAR(255) NULL,
    content LONGTEXT NOT NULL,
    author_id INT NULL,
    author_name VARCHAR(100) DEFAULT 'SND Special Desk',
    read_time VARCHAR(20) DEFAULT '4 min read',
    verification_badge ENUM('none', 'verified_report', 'special_investigation', 'ground_report') NOT NULL DEFAULT 'none',
    youtube_video_id VARCHAR(50) NULL,
    is_hero_lead TINYINT(1) DEFAULT 0,
    is_breaking TINYINT(1) DEFAULT 0,
    status ENUM('draft', 'published', 'archived') DEFAULT 'published',
    published_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. 24x7 Breaking News Marquee Ticker
CREATE TABLE breaking_ticker (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ticker_text VARCHAR(300) NOT NULL,
    link_url VARCHAR(500) NULL,
    urgency_level ENUM('breaking', 'flash', 'routine') DEFAULT 'breaking',
    is_active TINYINT(1) DEFAULT 1,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. YouTube Broadcast Playlist
CREATE TABLE youtube_bulletins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    video_id VARCHAR(50) NOT NULL,
    title VARCHAR(255) NOT NULL,
    thumbnail_url VARCHAR(500) NULL COMMENT 'Auto-generated from YouTube CDN: https://img.youtube.com/vi/{id}/hqdefault.jpg — overridable via admin',
    views_label VARCHAR(50) DEFAULT 'Live / Recent',
    is_main_featured TINYINT(1) DEFAULT 0,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 5. Commercial Studio Rental Facilities & Inventory
CREATE TABLE studio_facilities (
    id INT AUTO_INCREMENT PRIMARY KEY,
    facility_code VARCHAR(50) UNIQUE NOT NULL,
    name VARCHAR(150) NOT NULL,
    badge_label VARCHAR(100) NULL COMMENT 'e.g. "Floor Space • Studio A"',
    description TEXT NULL,
    category ENUM('studio', 'podcast', 'ad_space', 'ob_van') NOT NULL,
    rate_display VARCHAR(100) NOT NULL,
    square_feet INT NULL,
    thumbnail_url VARCHAR(500) NULL,
    specifications JSON NULL COMMENT 'Feature checklist array, e.g. ["Blackmagic URSA 4K Multicam", "Gigabit Fiber Uplink", "Makeup Lounge"]',
    cta_text VARCHAR(100) DEFAULT 'Book Facility',
    sort_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 6. Studio Rental Leads & Inquiries
CREATE TABLE rental_enquiries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_name VARCHAR(150) NOT NULL,
    production_house VARCHAR(150) NULL,
    email VARCHAR(150) NOT NULL,
    phone VARCHAR(30) NOT NULL,
    facility_requested VARCHAR(100) NOT NULL,
    target_date DATE NOT NULL,
    requirement_details TEXT NULL,
    notes TEXT NULL COMMENT 'Internal sales follow-up notes visible only to admin staff',
    ip_address VARCHAR(45) NULL,
    status ENUM('new', 'contacted', 'negotiating', 'booked', 'archived', 'rejected') DEFAULT 'new',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 7. System & Dynamic Site Settings
CREATE TABLE system_settings (
    setting_key VARCHAR(100) PRIMARY KEY,
    setting_value TEXT NOT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 8. Password Reset Tokens
CREATE TABLE password_reset_tokens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    token VARCHAR(255) NOT NULL COMMENT 'Cryptographically random token (bin2hex 32 bytes)',
    expires_at DATETIME NOT NULL COMMENT 'Token valid for 1 hour from generation',
    is_used TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

---

## 8. Directory & File Structure Plan

```text
SND-Media-House/
│
├── config/
│   ├── .htaccess             # Deny direct HTTP access to config directory
│   ├── database.php          # PDO connection & environment credentials
│   └── settings.php          # Dynamic config loader (contact, WhatsApp, social links, ads)
│
├── includes/
│   ├── auth.php              # RBAC middleware (require_login, require_role, active check)
│   ├── header.php            # Dynamic sticky header, utility bar, marquee ticker
│   ├── footer.php            # Dynamic media house footer & compliance links
│   ├── seo.php               # OpenGraph, Twitter Cards & JSON-LD structured data helper
│   ├── youtube_hub.php       # Dynamic 16:9 player and bulletin switcher
│   ├── rental_modal.php      # Commercial rental lead capture modal
│   └── functions.php         # Slug generators, plain/rich HTML sanitizers, uploads, time ago helpers
│
├── admin/                    # Role-Based Editorial & Leads CMS
│   ├── layout.php            # Shared dark admin shell, role-aware sidebar & top bar
│   ├── login.php             # Authentication & session start
│   ├── logout.php            # Secure session destroy
│   ├── reset_password.php    # Admin-initiated & self-service password reset (token-based, BCRYPT re-hash)
│   ├── index.php             # Role-aware dashboard (editorial stats + rental leads)
│   ├── articles.php          # News management (Add/Edit with 3-bullet highlights & upload)
│   ├── ticker.php            # 24x7 Breaking News Marquee manager
│   ├── youtube.php           # Featured video & playlist editor
│   ├── facilities.php        # Commercial space rate card & studio specs editor
│   ├── leads.php             # Studio rental inquiries CRM & CSV export
│   ├── users.php             # [Super Admin] User creation & role management
│   └── settings.php          # [Super Admin] Dynamic site-wide content, ads, PDF kit & legal policy editor
│
├── api/
│   ├── submit_rental.php     # Lead capture endpoint (sanitization, DB insert, email, WhatsApp)
│   ├── market_rates.php      # Cached Sensex/Nifty helper & Super Admin manual refresh
│   ├── toggle_ticker.php     # AJAX breaking ticker active/inactive toggle
│   └── update_lead_status.php # AJAX studio lead CRM status updater
│
├── assets/
│   ├── css/                  # Compiled/custom styles (`custom.css`)
│   ├── js/                   # Live player switcher, modal handler, form validation (`main.js`)
│   ├── images/               # Logos, studio floor plans, static brand assets
│   └── uploads/              # CMS-uploaded files (`articles/`, `facilities/`, `docs/`, `ads/`)
│
├── .htaccess                 # HTTPS redirect, security headers, `/legal/{slug}` rewrite, no dir listing
├── index.php                 # Prime Broadcast Homepage (dynamic PHP version)
├── article.php               # Single news report detail view
├── studio-rental.php         # Dedicated Space on Rent & Rate Card landing page
├── legal.php                 # Public Legal & Policy router (`/legal/*`)
└── REQUIREMENTS_DOCUMENT_PRIME_PHASE1.md # Approved specifications
```

---

## 9. Next Steps for Development Execution

> **Pre-Build Cleanup:** Before converting `concept_1_prime_broadcast.html` to PHP, remove all prototype-only and Phase 2 (AI) presentation artifacts so none leak into production:
> 1. **Concept Navigation Switcher Banner:** Remove the top `<div>` linking between Concept 01 / 02 / 03 and the pitch deck.
> 2. **Top Utility Bar AI Pill:** Replace `"AI Smart Summaries Active"` with `"Editorial Desk • Verified Reporting"` (or omit).
> 3. **Main Nav AI Link:** Remove/replace the `"AI 60s Brief"` (`#ai-digest`) navigation link.
> 4. **Hero Summary Drawer:** Rename `"SND AI Smart Briefing"` / `"Show AI Summary"` to `"Key Takeaways"` / `"Show Key Takeaways"`.
> 5. **Article Verification Badges:** Replace `"AI Fact-Checked"` badges with human editorial `verification_badge` values (`Verified Report`, `Special Investigation`, `Ground Report`).
> 6. **Public YouTube Test Input:** Remove the `"Test Upload YouTube Link in Player"` input box from the public sidebar widget (video management belongs strictly in `admin/youtube.php`).

1. **Database Setup:** Initialize the MySQL database schema (including `users`, `password_reset_tokens`, and RBAC tables) and seed default super admin credentials and sample articles.
2. **Modular PHP Conversion:** Convert `concept_1_prime_broadcast.html` into modular PHP (`index.php`, `includes/header.php`, `includes/footer.php`) pulling dynamic content from the database.
3. **Studio Rental Lead Flow:** Implement `api/submit_rental.php` with MySQL persistence, email alerts, and the pre-filled WhatsApp redirection.
4. **Role-Based CMS Admin Panel:** Create the administrative portal with RBAC session checks (`admin/login.php`, `admin/articles.php`, `admin/ticker.php`, `admin/leads.php`, `admin/users.php`, `admin/settings.php`, `admin/reset_password.php`).
5. **Hardening, Staff Walkthrough & Production Launch:** Full mobile/responsive audit across 360px–4K, security scan (CSRF, honeypot, SQL injection checks), editorial team onboarding session, and go-live deployment to production server.
