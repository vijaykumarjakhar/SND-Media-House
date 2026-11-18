# Development Task Breakdown
## Project: SND Media House — Digital Platform (Prime Edition) — Phase 1
**Based on:** `USER_STORIES_PRIME_PHASE1.md` & `REQUIREMENTS_DOCUMENT_PRIME_PHASE1.md` (`sndmedia.in`)  
**Date:** November 2026  
**Stack:** PHP 8.x · MySQL/MariaDB · Tailwind CSS · Vanilla JS · Apache/XAMPP  

---

## Task Status Legend
- `[ ]` To Do
- `[~]` In Progress
- `[x]` Done
- `[!]` Blocked

---

## Sprint 1 — Database & Core Architecture
**Stories:** US-13, US-19, US-20, US-23  
**Goal:** Working database, auth layer, user management, password reset, global settings panel

---

### TASK-S1-01 · Create MySQL Database & Run Schema
**Story:** Foundation for all stories  
**Priority:** P0 — Must complete first

- [x] Create database `sndmedia_db` with `utf8mb4` charset
- [x] Run `users` table DDL (id, username, email, password_hash, full_name, role ENUM, is_active, last_login, created_at)
- [x] Run `articles` table DDL (all fields including `subcategory`, `tags`, `meta_keywords`, `highlight_1`/`2`/`3`, `verification_badge` ENUM, `is_hero_lead`, `is_breaking`, `status`)
- [x] Run `breaking_ticker` table DDL (ticker_text, link_url, urgency_level ENUM, is_active, sort_order)
- [x] Run `youtube_bulletins` table DDL (video_id, title, thumbnail_url, views_label, is_main_featured, sort_order)
- [x] Run `studio_facilities` table DDL (`facility_code`, `name`, `badge_label`, `description`, `category` ENUM, `rate_display`, `square_feet`, `thumbnail_url`, `specifications` JSON, `cta_text`, `sort_order`, `is_active`)
- [x] Run `rental_enquiries` table DDL (client_name, production_house, email, phone, facility_requested, target_date, requirement_details, notes TEXT NULL, ip_address, status ENUM)
- [x] Run `system_settings` table DDL (setting_key PK, setting_value TEXT, updated_at)
- [x] Maintain a canonical settings key registry for all `system_settings` values used across admin UI and public frontend (document keys in the project README or config notes)
- [x] Run `password_reset_tokens` table DDL (user_id FK, token, expires_at, is_used)
- [x] Verify all foreign key constraints and ENGINE=InnoDB declarations
- [x] Seed one default Super Admin user (username: `admin`, bcrypt-hashed temp password)
- [x] Seed sample `system_settings` rows (all keys from §4.9 / US-23 including `site_meta_description`, canonical domain `sndmedia.in`, `yt_handle = '@sndschoolmedia'`, `yt_subscriber_label = '3.36K Subs • 666+ Videos'`, `social_youtube = 'https://www.youtube.com/@sndschoolmedia'`, `hq_address = 'SND Public School, Palwal-Sohna Road, Village Meghpur, Palwal, Haryana — 121102'`, `phone_studio = '+91-7206622711'`, `phone_whatsapp = '+917206622711'`, desk emails `newsdesk@sndmedia.in`, `space@sndmedia.in`, `ads@sndmedia.in`, `market_feed_status`, `ad_leaderboard_*`, `ad_sidebar_*`, and `policy_*` placeholder values)
- [x] Seed 3 sample `studio_facilities` rows (Studio A, Studio B, Ad Inventory — including `badge_label`, `description`, `thumbnail_url`, `cta_text`, and `sort_order`)
- [x] Seed 5 sample `articles` rows (mix of published/draft, one with is_hero_lead=1)
- [x] Seed 3 sample `breaking_ticker` rows (one Breaking, one Flash, one Routine)
- [x] Seed 5 sample `youtube_bulletins` rows from `@sndschoolmedia` (`25tvHivopPE` featured, `h05GJIxJ3X4`, `93D_Razv-nM`, `qjp36XeVlAQ`, `Wl2bQlbmysk`)

---

### TASK-S1-02 · Project Directory Structure & Config Layer
**Story:** Foundation  
**Priority:** P0

- [x] Create directory structure: `config/`, `includes/`, `admin/`, `api/`, `assets/css/`, `assets/js/`, `assets/images/`, `assets/uploads/articles/`, `assets/uploads/facilities/`, `assets/uploads/docs/`, `assets/uploads/ads/`
- [x] Follow Clean Modular Page-Controller + Shared Service/Helper pattern: root/admin/api page scripts handle validation, auth, and DB queries via `config/` and `includes/`, while UI partials (`includes/header.php`, `includes/footer.php`, `admin/layout.php`) remain pure presentation templates
- [x] Create `config/database.php` — PDO connection with DSN, error mode PDO::ERRMODE_EXCEPTION, charset utf8mb4
- [x] Create `config/settings.php` — helper function `get_setting($key, $default = '')` that queries `system_settings` table
- [x] Add `.htaccess` to project root — HTTPS redirect, disable directory listing, block direct access to `config/`, and rewrite clean legal routes (`RewriteRule ^legal/([a-z0-9-]+)$ legal.php?page=$1 [L,QSA]`)
- [x] Add `.htaccess` inside `config/` — `Deny from all`
- [x] Create `includes/functions.php` — utility functions:
  - `slug_generate($title)` — lowercase, strip specials, replace spaces with hyphens
  - `time_ago($datetime)` — returns "18 mins ago", "2 hours ago" etc.
  - `sanitize_output($str)` — `htmlspecialchars($str, ENT_QUOTES, 'UTF-8')` for plain-text fields
  - `sanitize_rich_html($html)` — allowlist HTML sanitizer preserving safe editorial/legal formatting (`<p>`, `<br>`, `<strong>`, `<em>`, `<ul>`, `<ol>`, `<li>`, `<h2>`–`<h4>`, `<blockquote>`, `<a>`, `<img>`) and stripping scripts/event attributes/`javascript:` URIs
  - `handle_file_upload($file, $subdir, $allowedTypes)` — MIME & extension validation, safe randomized filename, saves under `assets/uploads/{subdir}/`
  - `csrf_token_generate()` — generates & stores token in session
  - `csrf_token_validate($token)` — validates submitted token against session
  - `extract_youtube_id($url)` — parses any YouTube URL format to 11-char ID

---

### TASK-S1-03 · Authentication & Session Management (US-13)
**Story:** US-13  
**Priority:** P0

- [x] Create `admin/login.php` — form with username/email + password fields, CSRF token hidden field
- [x] Implement login POST handler — query user by username OR email, `password_verify()` check
- [x] On success: `session_regenerate_id(true)`, store `$_SESSION['user_id']`, `$_SESSION['role']`, `$_SESSION['full_name']`
- [x] Update `users.last_login` timestamp on successful login
- [x] On failure: set generic error "Invalid username or password." (no field-specific detail)
- [x] Check `is_active = 0` — deny with "Your account has been deactivated." message
- [x] Create `includes/auth.php` — `require_login()` guard (redirect to login if no session), `require_role($roles)` guard (redirect with 403 if role mismatch)
- [x] Add `require_login()` call at top of every `admin/*.php` page
- [x] Create `admin/logout.php` — `session_destroy()`, redirect to `admin/login.php`
- [x] Create `admin/index.php` — role-aware dashboard:
  - Super Admin: show total articles, total leads, total users, ticker item count
  - Editor: show published articles count, draft articles, ticker items
  - Reporter: show own drafts, own published articles
  - Space Manager: show new leads count, leads by status breakdown

---

### TASK-S1-04 · User Management Panel (US-19)
**Story:** US-19  
**Priority:** P1

- [x] Create `admin/users.php` — guard with `require_role(['super_admin'])`
- [x] User list table: full name, username, email, role badge, active status pill, last login, Edit button
- [x] "Add User" form — fields: full_name, username, email, role (dropdown), password, confirm password
- [x] On create: validate unique username & email (query DB), hash with `password_hash($pw, PASSWORD_BCRYPT, ['cost'=>12])`
- [x] Edit user form — pre-populate all fields; password field optional (only change if filled)
- [x] Active/Inactive toggle — block Super Admin from deactivating their own account (check `$_SESSION['user_id'] == $user_id`)
- [x] No delete endpoint — deactivation only (set `is_active = 0`)
- [x] On deactivation: no additional session kill needed (each page load checks `is_active` via `auth.php`)
- [x] `auth.php` `require_login()` must also query `is_active` on every load and redirect if `0`

---

### TASK-S1-05 · Password Reset Flow (US-20)
**Story:** US-20  
**Priority:** P1

- [x] Add "Forgot Password?" link to `admin/login.php` → `admin/reset_password.php`
- [x] Create `admin/reset_password.php` — two modes: request form (email input) & reset form (new password input)
- [x] Request mode: look up email in `users`, generate `bin2hex(random_bytes(32))` token, insert into `password_reset_tokens` with `expires_at = NOW() + INTERVAL 1 HOUR`
- [x] Send reset email via PHP `mail()` or SMTP (configurable): subject "SND Admin — Password Reset", body contains reset link `admin/reset_password.php?token={token}`
- [x] Reset mode: validate token exists, `is_used = 0`, `expires_at > NOW()`; if invalid → show expiry message
- [x] On valid token: show new password + confirm password form (min 10 chars enforced)
- [x] On submit: `password_hash()` new password, UPDATE `users.password_hash`, set `password_reset_tokens.is_used = 1`
- [x] Super Admin can trigger reset from `admin/users.php` edit screen — "Send Reset Link" button calls same token generation logic
- [x] Clean up expired tokens: add a check that deletes tokens older than 24 hours on each page load of reset form

---

### TASK-S1-06 · Global Site Settings Panel (US-23)
**Story:** US-23  
**Priority:** P1

- [x] Create `admin/settings.php` — guard with `require_role(['super_admin'])`
- [x] Group the form into 8 labelled sections using fieldsets/dividers:
  - **① Utility Bar, Site Meta & Editions:** `site_meta_description`, `editions_text`, `yt_handle`, `yt_subscriber_label`
  - **② Market Data:** `sensex_value`, `sensex_change`, `sensex_direction` (up/down radio), `nifty_value`, `nifty_change`, `nifty_direction`, `market_auto_feed` (checkbox toggle)
  - **③ Contact & Offices:** `hq_address`, `phone_studio`, `phone_whatsapp`, `email_newsdesk`, `email_space`, `email_ads`
  - **④ Social Media Links:** `social_youtube`, `social_twitter`, `social_instagram`, `social_linkedin`, `social_facebook`
  - **⑤ Commercial Rates:** `studio_rate_display`
  - **⑥ Media Kit PDF:** `media_kit_pdf_url` (text input + PDF upload via `handle_file_upload(..., 'docs', ['application/pdf'])` + "Remove PDF" button)
  - **⑦ Commercial Ad Banners:** `ad_leaderboard_image` (upload/URL), `ad_leaderboard_url`, `ad_leaderboard_label`, `ad_leaderboard_active`, `ad_sidebar_image` (upload/URL), `ad_sidebar_url`, `ad_sidebar_label`, `ad_sidebar_active`
  - **⑧ Legal & Policy Pages:** `policy_privacy`, `policy_tos`, `policy_rental_rules`, `policy_editorial_ethics` (rich-text WYSIWYG each, sanitized via `sanitize_rich_html()`)
- [x] On page load: pre-populate all fields using `get_setting($key)` for each key
- [x] Save handler: loop through all submitted keys, run `INSERT INTO system_settings ... ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)` for each
- [x] WhatsApp phone validation: regex check for E.164 format (`/^\+[1-9]\d{7,14}$/`) before save
- [x] Market Data — live preview widget: a small JS-rendered preview of the SENSEX/NIFTY bar that updates in real-time as the admin types into the value/change/direction fields
- [x] "Last fetched" label: query `market_last_fetched` setting key; if set, calculate minutes ago and display beside the market fields
- [x] "Remove PDF" button: sets `media_kit_pdf_url` to empty string on save; frontend shows/hides the remove button based on whether value is set
- [x] Success message: on save, set `$_SESSION['settings_saved'] = true`, redirect back to `admin/settings.php` (PRG pattern), show inline banner `"Settings updated successfully."` from session flash with all saved values re-populated, unset flash
- [x] CSRF token on form; validate on POST

---

### TASK-S1-07 · Free Market Data API & Cache (US-23 / AC-23.6)
**Story:** US-23 (AC-23.6), US-02  
**Priority:** P1

- [x] Create `api/market_rates.php` — provides a helper function `fetch_market_rates($forceRefresh = false)` for server-side inclusion (`includes/header.php`) and handles authenticated Super Admin AJAX `?refresh=1` requests
- [x] Check `market_auto_feed` setting first: if `0` (disabled) and not forced by admin refresh, return stored manual values immediately without overwriting them
- [x] Check `market_last_fetched` in `system_settings`; if less than 15 minutes ago (and `!$forceRefresh`), return cached values immediately
- [x] If cache stale or missing (and `market_auto_feed == 1` or forced): call free financial endpoint (Yahoo Finance query API: `https://query1.finance.yahoo.com/v8/finance/chart/^BSESN` and `^NSEI`)
- [x] Parse JSON response: extract `regularMarketPrice`, `regularMarketChangePercent`
- [x] On success: upsert `sensex_value`, `sensex_change`, `sensex_direction`, `nifty_value`, `nifty_change`, `nifty_direction`, `market_last_fetched`, and `market_feed_status = 'live'` into `system_settings`
- [x] On API failure (timeout, HTTP error, rate limit): leave existing cached values untouched; set `market_feed_status = 'unavailable'` in settings
- [x] Helper/HTTP response returns structured payload: `{sensex: {...}, nifty: {...}, fetched_at: "...", status: "live|cached|manual|unavailable"}`
- [x] Set PHP `curl` timeout to 3 seconds to avoid blocking page load
- [x] Protect direct HTTP access on `api/market_rates.php`: require active `super_admin` session + CSRF header/param when invoked via HTTP AJAX; deny unauthenticated browser calls with HTTP 403
- [x] Admin settings page triggers a manual refresh via AJAX "Refresh Now" button that calls `api/market_rates.php?refresh=1` and updates the live preview

---

## Sprint 2 — Modular Frontend & Dynamic News Desk
**Stories:** US-01, US-02, US-03, US-04, US-05, US-06  
**Goal:** Fully dynamic public homepage and article detail page

---

### TASK-S2-01 · Modular PHP Header & Breaking Ticker (US-01, US-02, US-03)
**Story:** US-01, US-02, US-03  
**Priority:** P0

- [x] Create `includes/header.php` — start session, include config, set `$settings` array from `get_setting()`
- [x] Remove prototype-only & Phase 2 AI markup from the production header:
  - Concept-switcher banner `<div>` removed
  - Top-bar `"AI Smart Summaries Active"` badge removed (replaced with `"Editorial Desk Verified"` or omitted)
  - Navbar `"AI 60s Brief"` (`#ai-digest`) link removed
- [x] Build Utility Bar:
  - Date in IST: `date('l, d M Y', time())` with IST offset
  - Edition cities: `get_setting('editions_text')`
  - SENSEX/NIFTY: read from `system_settings`; apply green/red class based on `sensex_direction`
  - YouTube badge: `get_setting('yt_handle')` + `get_setting('yt_subscriber_label')` linked to `get_setting('social_youtube')`
  - Hide market indices below md breakpoint via Tailwind `hidden md:flex`
- [x] Build sticky glassmorphism navigation bar:
  - Official `@sndschoolmedia` circular crest logo (`assets/images/snd-logo.svg`) + brand name + tagline (`Speak. Narrate. Discover.`) + optional channel banner strip (`assets/images/snd-banner.svg`)
  - Desktop nav links resolving cleanly from all public pages: Live Desk (`index.php#breaking` with animated ping dot), National (`index.php?cat=national`), Business & Tech (`index.php?cat=business`), YouTube TV (`index.php#youtube-hub`), Rent Studio & Ad Space (`studio-rental.php`, amber pill)
  - "Book Studio Floor" CTA button (top-right, opens rental modal)
  - Mobile hamburger button (`lg:hidden`)
- [x] Build mobile hamburger drawer:
  - Full list of nav links
  - Quick contact: `get_setting('phone_studio')` as `tel:` link
  - Toggle open/close via vanilla JS + CSS transition
- [x] Build 24/7 Breaking Ticker:
  - Query `SELECT * FROM breaking_ticker WHERE is_active=1 ORDER BY sort_order ASC`
  - If no results: render nothing (hide ticker bar entirely)
  - Render `BREAKING 24x7` prefix badge + animated pulse dot
  - Duplicate items list once more in the same `<div>` to create seamless loop
  - Apply `animate-marquee` CSS class (28s linear infinite, pause on hover)
  - Each item with `link_url`: wrap in `<a>` tag; without: plain `<span>`
  - Urgency prefix label changes per `urgency_level` (BREAKING / FLASH / NOTICE)

---

### TASK-S2-02 · PHP Footer (US-12)
**Story:** US-12  
**Priority:** P0 (needed by homepage)

- [x] Create `includes/footer.php`
- [x] 4-column grid layout (Tailwind: `grid-cols-1 md:grid-cols-2 lg:grid-cols-4`)
- [x] Column 1 — Brand & About: SND logo, tagline, 2-line description, social icons from `get_setting('social_*')`
- [x] Column 2 — News Categories: links to `index.php?cat=national` etc. using canonical taxonomy
- [x] Column 3 — Commercial Space on Rent: links to `studio-rental.php` sections
- [x] Column 4 — Offices: `get_setting('hq_address')`, `get_setting('phone_studio')`, `get_setting('email_newsdesk')`, `get_setting('email_space')`
- [x] Bottom bar: `© <?= date('Y') ?> SND Media Private Limited` + legal page links
- [x] Social icon links open `target="_blank" rel="noopener noreferrer"`

---

### TASK-S2-03 · Homepage — Hero Lead Story (US-04)
**Story:** US-04  
**Priority:** P0

- [x] Create `index.php` — include header, footer, all section includes
- [x] Query hero article: `SELECT * FROM articles WHERE is_hero_lead=1 AND status='published' LIMIT 1`
- [x] Fallback: if no hero set, `SELECT * FROM articles WHERE status='published' ORDER BY published_at DESC LIMIT 1`
- [x] Render hero card:
  - 16:9 featured image with dark gradient overlay (`bg-gradient-to-t from-slate-950`)
  - Category badge + optional `verification_badge` pill (`Verified Report`, `Special Investigation`, `Ground Report`) when not `'none'`
  - Author/desk byline, `time_ago($article['updated_at'])`, estimated read time
  - Headline in `text-2xl md:text-4xl font-extrabold`
  - 2-sentence summary (`line-clamp-2`)
  - "Watch Video Report" pill overlay — render only if `youtube_video_id` is set
  - Hover zoom on image (`group-hover:scale-105 transition duration-700`)
  - Entire card links to `article.php?slug={slug}`
- [x] Key Takeaways collapsible drawer:
  - Toggle button: "Show Key Takeaways" / "Hide Key Takeaways" (no AI branding)
  - Drawer header: "Key Takeaways" with editorial desk icon
  - Render only highlights that are not NULL (`highlight_1`, `highlight_2`, `highlight_3`)
  - Toggle logic via vanilla JS `toggleTakeaways()` function

---

### TASK-S2-04 · Homepage — Continuous News Desk Feed (US-05)
**Story:** US-05  
**Priority:** P0

- [x] Below hero, query: `SELECT * FROM articles WHERE status='published' AND is_hero_lead=0 ORDER BY published_at DESC LIMIT 20`
- [x] Category filter tabs (all 6 canonical categories): All, National, Business, Tech, Energy, Exclusive, World
- [x] Render all articles in DOM with `data-category="{category}"` attribute on each card
- [x] Active tab JS filtering: `filterCategory(cat)` — show/hide cards by `data-category` match, no page reload; read `?cat={category}` on load in `index.php` / `main.js` to pre-activate the matching tab and scroll to `#news-desk`
- [x] 2-column grid: `grid-cols-1 md:grid-cols-2 gap-4`
- [x] Each article card:
  - Thumbnail (`w-28 h-24 rounded-lg object-cover`) with hover micro-zoom (`group-hover:opacity-90`)
  - Color-coded category slug (`national`=blue, `business`=emerald, `tech`=amber, `energy`=cyan, `exclusive`=rose, `world`=indigo)
  - 2-line truncated headline (`line-clamp-2`)
  - `time_ago($article['published_at'])` timestamp
  - Editorial desk tag (`author_name`) + optional human `verification_badge` pill (no `"AI Fact-Checked"` label)
  - Entire card links to `article.php?slug={slug}`
- [x] "All" tab active by default; active tab style change via JS class toggle

---

### TASK-S2-05 · Single Article Detail Page (US-06)
**Story:** US-06  
**Priority:** P0

- [x] Create `article.php` — accept `$_GET['slug']`, sanitize, query: `SELECT * FROM articles WHERE slug=? AND status='published'`
- [x] Invalid/missing slug → `header('HTTP/1.0 404 Not Found')`, render 404 error page
- [x] Render article:
  - Breadcrumb: `Home > {Category} > {Headline}` (truncated headline)
  - Full-width featured image
  - Headline `<h1>` + author byline + publish date + updated timestamp
  - Social share buttons: WhatsApp (`wa.me/?text=`), X (`x.com/intent/tweet`), Facebook (`facebook.com/sharer`), LinkedIn (`linkedin.com/sharing`), Copy Link (JS clipboard API)
  - Article body: render `sanitize_rich_html($article['content'])` — apply `prose` class or equivalent Tailwind typography styles (do NOT double-escape with `htmlspecialchars()`)
  - If `youtube_video_id` set: render `<iframe>` with `youtube-nocookie.com/embed/{id}` inside the body
  - Sidebar or leaderboard commercial ad banner slot: `<div class="ad-slot">` rendered when `ad_leaderboard_active` / `ad_sidebar_active` = `'1'` using `system_settings` keys (`ad_*_image`, `ad_*_url`, `ad_*_label`)
- [x] Related News: `SELECT * FROM articles WHERE category=? AND status='published' AND id != ? ORDER BY published_at DESC LIMIT 3`
- [x] Dynamic `<head>` SEO tags:
  - `<title>{headline} | SND Media</title>`
  - `<meta name="description" content="{summary}">`
  - OpenGraph: `og:title`, `og:description`, `og:image` (featured_image), `og:url`, `og:type=article`
  - Twitter Card: `twitter:card=summary_large_image`, `twitter:title`, `twitter:description`, `twitter:image`
- [x] `NewsArticle` JSON-LD block in `<head>`:

---

### TASK-S2-06 · Legal Pages & Policy Routing (US-24)
**Story:** US-24  
**Priority:** P1

- [x] Create `legal.php` and `.htaccess` rewrite rule (`RewriteRule ^legal/([a-z0-9-]+)$ legal.php?page=$1 [L,QSA]`) mapping `/legal/privacy-policy`, `/legal/terms-of-service`, `/legal/rental-rules`, `/legal/editorial-ethics` to their corresponding `system_settings` keys (`policy_privacy`, `policy_tos`, `policy_rental_rules`, `policy_editorial_ethics`); return HTTP 404 on unknown slugs
- [x] Footer links in `includes/footer.php` must point to `/legal/privacy-policy`, `/legal/terms-of-service`, `/legal/rental-rules`, and `/legal/editorial-ethics`
- [x] Escape page titles with `sanitize_output()` and render rich-text policy bodies through `sanitize_rich_html()` (allowlist sanitizer preventing XSS without escaping formatting markup)
- [x] Ensure legal pages are responsive and readable on mobile and tablet layouts

---

### TASK-S2-07 · Accessibility & UX Baseline (US-25)
**Story:** US-25  
**Priority:** P1

- [x] Add visible focus states to keyboard-interactive elements in the public site and admin shell
- [x] Enforce min touch target sizes of 44x44px for CTAs, nav controls, selects, and checkboxes
- [x] Add reduced-motion handling for marquee, breathing pulses, and subtle animation states
- [x] Provide accessible labels and semantics for navigation, forms, and status tiles
- [x] Validate form errors using visible textual messages in addition to inline styling

---

### TASK-S2-08 · Assets — JavaScript & CSS
**Story:** Foundation for all public pages  
**Priority:** P1

- [x] Create `assets/js/main.js`:
  - `toggleTakeaways()` — Key Takeaways drawer toggle
  - `filterCategory(cat)` — news desk tab filtering
  - `openRentalModal(facilityName)` — open booking modal + pre-select facility
  - `closeRentalModal()` — hide modal
  - Hamburger menu open/close
  - Smooth scroll polyfill for nav anchor links
  - Copy Link share button: `navigator.clipboard.writeText(window.location.href)`
- [x] Create `assets/css/custom.css`:
  - `@keyframes marquee` scrolling animation
  - `.animate-marquee` class with hover pause
  - `.live-pulse` keyframe animation
  - `.glassmorphism` utility class
  - Custom scrollbar styles
- [ ] All public pages load only: Tailwind CDN (dev) or compiled CSS (prod), FontAwesome CDN, `assets/js/main.js`
- [ ] Zero jQuery or other frameworks

---

## Sprint 3 — YouTube Hub & Studio Lead Funnel
**Stories:** US-07, US-08, US-09, US-10, US-11  
**Goal:** Working YouTube broadcast section, studio rental showcase, and fully functional lead capture

---

### TASK-S3-01 · YouTube TV Broadcast Hub (US-07, US-08)
**Story:** US-07, US-08  
**Priority:** P0

- [x] Create `includes/youtube_hub.php`
- [x] Query featured video: `SELECT * FROM youtube_bulletins WHERE is_main_featured=1 LIMIT 1`
- [x] Render 16:9 `<iframe>` with `src="https://www.youtube-nocookie.com/embed/{video_id}?autoplay=0&mute=1&controls=1"` and correct `allow` + `allowfullscreen` attributes
- [x] "LIVE BROADCAST" badge above player with animated red pulse dot
- [x] "Subscribe" button — links to `get_setting('social_youtube')` (default: `https://www.youtube.com/@sndschoolmedia`), opens `target="_blank"`
- [x] Query playlist: `SELECT * FROM youtube_bulletins ORDER BY sort_order ASC LIMIT 5`
- [x] Render playlist items below player: thumbnail (`thumbnail_url`, fallback to `https://img.youtube.com/vi/{video_id}/hqdefault.jpg`), title, `views_label`
- [x] Clicking playlist item: JS `switchYtVideo(videoId)` — updates iframe `src` with `autoplay=1`
- [x] Active playlist item highlight: JS adds `border-brand-red` class to clicked item, removes from others
- [x] Playlist container: `max-h-64 overflow-y-auto` for scrollable list when >3 items
- [x] Explicitly omit the prototype's public `"Test Upload YouTube Link in Player"` input box (video updates are strictly managed in `admin/youtube.php`)

---

### TASK-S3-02 · Studio Rental Sidebar Widget (US-09)
**Story:** US-09  
**Priority:** P0

- [x] Include in `index.php` right column (lg:col-span-4)
- [x] Amber/gold `border-2 border-amber-500/40` container
- [x] "Commercial Space Available | Rates From {studio_rate_display}" — rate from `get_setting('studio_rate_display')`
- [x] 4 facility badge tiles (2×2 grid): Podcast Suite, Studio Floor A, Digital Ad Slots, OB Van — icons + spec line, hardcoded from primary facilities
- [x] "Request Rate Card" button → `openRentalModal('Studio & Ad Space Booking')`
- [x] "Call Space Desk" `<a href="tel:{get_setting('phone_studio')}">` button icon

---

### TASK-S3-03 · Detailed Studio Showcase Section (US-10)
**Story:** US-10  
**Priority:** P0

- [x] Create `studio-rental.php` — dedicated landing page (also embedded as a section in `index.php`)
- [x] Query: `SELECT * FROM studio_facilities WHERE is_active=1 ORDER BY sort_order ASC, id ASC`
- [x] Section header: title, subtitle, "Download Media Kit & Rate Card" CTA button
  - If `get_setting('media_kit_pdf_url')` is not empty: render as `<a href="{url}" download>` button
  - If empty: render as button that calls `openRentalModal('Full Media Floor Rental')`
- [x] 3-card grid (`grid-cols-1 md:grid-cols-3 gap-6`):
  - Card: `thumbnail_url` cover image, `badge_label` pill, `name`, `rate_display` badge, `description`, feature checklist from `json_decode($facility['specifications'])`
  - Hover: `hover:border-amber-400/50` border transition
  - CTA button per card: renders dynamic `cta_text` and passes facility name/`facility_code` to `openRentalModal()`
- [x] `LocalBusiness` + `StudioOrRecordingStudio` JSON-LD in `<head>` of `studio-rental.php`:

---

### TASK-S3-04 · Rental Enquiry Modal (US-11)
**Story:** US-11  
**Priority:** P0

- [x] Create `includes/rental_modal.php` — modal overlay div, hidden by default (`hidden`)
- [x] Form fields: Client Full Name (`client_name`, required), Production House / Organization (`production_house`, optional), Email, Phone, Facility (dropdown), Target Date, Requirements, honeypot (hidden `website` field), CSRF hidden field
- [x] Facility dropdown options populated from `studio_facilities` table where `is_active=1` + "Full Media Floor Monthly Lease" static option
- [x] JS `openRentalModal(facilityName)` — remove `hidden`, add `flex`, pre-select facility in dropdown by matching `facilityName` to `<option>` text
- [x] JS `closeRentalModal()` — add `hidden`, remove `flex`; also close on backdrop click
- [x] Client-side validation in `assets/js/main.js`:
  - Client Full Name: min 2 chars
  - Email: `/^[^\s@]+@[^\s@]+\.[^\s@]+$/`
  - Phone: `/^(\+?\d{10,15})$/`
  - Target Date: compare to `new Date()` — reject past dates
- [x] Form submits via `fetch()` POST to `api/submit_rental.php` (AJAX, no reload)
- [x] On success response: hide form, show success message + WhatsApp button
- [x] On error response: show error message inline

---

### TASK-S3-05 · Rental Lead Capture API Endpoint (US-11)
**Story:** US-11  
**Priority:** P0

- [x] Create `api/submit_rental.php`:
  - Accept `$_POST` only; reject GET requests
  - Validate CSRF token
  - Check honeypot field — if filled, return `{"status":"ok"}` (silent discard, no DB write)
  - IP rate limit check: `SELECT COUNT(*) FROM rental_enquiries WHERE ip_address=? AND created_at > NOW() - INTERVAL 1 HOUR` — if ≥ 5, return `{"status":"error","message":"Too many requests..."}`
  - Trim and normalize all inputs (`client_name`, optional `production_house`, `email`, `phone`, `facility_requested`, `target_date`, `requirement_details`) before validation & prepared statement INSERT
  - Validate required fields server-side (`client_name`, `email`, `phone`, `facility_requested`, `target_date`)
  - Validate email format: `filter_var($email, FILTER_VALIDATE_EMAIL)`
  - Validate phone: regex check
  - Validate target_date: `strtotime($date) >= strtotime('today')` check
  - INSERT into `rental_enquiries` (`client_name`, `production_house`, `email`, `phone`, `facility_requested`, `target_date`, `requirement_details`, `ip_address`, `status`) with `status='new'`, `ip_address=$_SERVER['REMOTE_ADDR']`
  - Send email alert to `get_setting('email_space')`
  - Build WhatsApp pre-filled URL: `https://wa.me/{whatsapp_number}?text=Hello+SND...`
  - Return JSON: `{"status":"ok","whatsapp_url":"..."}`

---

## Sprint 4 — Role-Based CMS Administration
**Stories:** US-14, US-15, US-16, US-17, US-18  
**Goal:** Complete admin portal for all roles — article editor, ticker desk, YouTube manager, leads CRM, facilities editor

---

### TASK-S4-01 · Admin Layout & Navigation Shell
**Story:** Foundation for all Sprint 4 tasks  
**Priority:** P0

- [x] Create `admin/layout.php` — shared admin shell: sidebar navigation, top bar with username/role badge, logout link
- [x] Sidebar navigation items — conditionally rendered by role:
  - All roles: Dashboard
  - Super Admin + Editor + Reporter: Articles
  - Super Admin + Editor: Breaking Ticker, YouTube Hub
  - Super Admin + Space Manager: Studio Leads, Facilities
  - Super Admin only: Users, Settings
- [x] Active nav item highlight (compare current page filename)
- [x] All `admin/*.php` pages include `admin/layout.php` via `require`
- [x] Admin-specific CSS: dark sidebar (`bg-[#0b1222]`), matching SND Prime brand palette

---

### TASK-S4-02 · Article Management CMS (US-14)
**Story:** US-14  
**Priority:** P0

- [x] Create `admin/articles.php` — article list view + add/edit form
- [x] Article list table: title, category badge, status badge (green=published, amber=draft, grey=archived), author, published_at, Edit/Delete buttons
- [x] Edit/Delete buttons:
  - Reporter: Edit only shown for own articles (`author_id = $_SESSION['user_id']`)
  - Editor/Super Admin: Edit + soft-delete shown for all articles
- [x] Add/Edit form fields:
  - Title (text, required)
  - Slug (auto-generated from title via JS `slugify()` on `keyup`; editable; validate unique on blur via AJAX check)
  - Category (select dropdown — canonical taxonomy ENUM values)
  - Subcategory (text, optional)
  - Tags (text, comma-separated, optional)
  - Meta Keywords (text, optional)
  - Featured Image URL (text input) + file upload (`<input type="file" name="featured_image_file">` processed via `handle_file_upload(..., 'articles', ...)`) + live preview `<img>`
  - Summary (textarea, 2–3 sentences)
  - Highlight 1, Highlight 2, Highlight 3 (text, optional)
  - Article Body (WYSIWYG)
  - Author Name (pre-filled with `$_SESSION['full_name']`, editable by editors)
  - Read Time (text, e.g. "4 min read")
  - Verification Badge (select: `none`, `verified_report`, `special_investigation`, `ground_report`)
  - YouTube Video ID (text, optional)
  - Hero Lead toggle (checkbox — Editors/Super Admin only; hidden from Reporters)
  - Breaking toggle (checkbox)
  - Status (select: draft/published/archived — Reporters: locked to `draft` only, field disabled)
- [x] On save:
  - If Hero Lead checked: `UPDATE articles SET is_hero_lead=0 WHERE id != {new_id}` first, then save new article
  - Duplicate slug check: query DB; append `-2`, `-3` etc. if duplicate
  - Trim plain-text fields (escaped on render with `sanitize_output()`); sanitize WYSIWYG body HTML with `sanitize_rich_html()` before storage/render
- [x] Soft delete: set `status='archived'` — no row deletion
- [x] Pagination on article list: 20 per page

---

### TASK-S4-03 · Breaking Ticker Desk CMS (US-15)
**Story:** US-15  
**Priority:** P1

- [x] Create `admin/ticker.php` — guard `require_role(['super_admin','editor'])`
- [x] Table: ticker_text, urgency badge (red=breaking, amber=flash, grey=routine), is_active toggle, sort_order input, created_at, Delete button
- [x] Active/inactive toggle: AJAX POST to `api/toggle_ticker.php?id={id}&active={0|1}` — updates DB, returns JSON, toggles UI without reload
- [x] Create `api/toggle_ticker.php` — validate CSRF, role check, UPDATE `is_active`
- [x] Add Ticker Item form: text (max 300 chars with live char counter), link URL (optional), urgency level (select, default "breaking")
- [x] Sort order: numeric input per row; "Save Order" button batch-updates all `sort_order` values via POST
- [x] Permanent delete: confirmation prompt, DELETE FROM `breaking_ticker WHERE id=?`

---

### TASK-S4-04 · YouTube Hub Admin (US-16)
**Story:** US-16  
**Priority:** P1

- [x] Create `admin/youtube.php` — guard `require_role(['super_admin','editor'])`
- [x] Playlist table: thumbnail preview (`<img>` 60×40), title, views_label, is_main_featured star, sort_order, Edit/Delete buttons
- [x] Add Video form: URL or Video ID input → on submit, call `extract_youtube_id($input)` server-side; auto-set `thumbnail_url = "https://img.youtube.com/vi/{id}/hqdefault.jpg"` unless overridden
- [x] "Set as Main Featured" button per row: runs `UPDATE youtube_bulletins SET is_main_featured=0` then `UPDATE ... SET is_main_featured=1 WHERE id=?`
- [x] Edit row inline: title text, views_label text, sort_order number, thumbnail_url override
- [x] Delete: confirm + DELETE row

---

### TASK-S4-05 · Studio Leads CRM (US-17)
**Story:** US-17  
**Priority:** P1

- [x] Create `admin/leads.php` — guard `require_role(['super_admin','space_manager'])`
- [x] Leads table: client_name + production_house, email, phone (clickable `tel:` link), facility_requested, target_date, created_at, status dropdown, Notes expand button
- [x] Status inline update: `<select>` per row (New/Contacted/Negotiating/Booked/Archived/Rejected) — onChange triggers AJAX POST to `api/update_lead_status.php`
- [x] Create `api/update_lead_status.php` — validate CSRF + role, UPDATE `rental_enquiries.status` where id=?
- [x] Notes field: expand/collapse per row (JS toggle); note textarea saves to `rental_enquiries.notes` column (defined in `TASK-S1-01`)
- [x] Filter bar (top of page):
  - Status filter (select dropdown)
  - Facility filter (select from distinct facilities in DB)
  - Date range: target_date from/to (date inputs)
  - "Apply Filter" button — rebuilds query with WHERE clauses
- [x] Export CSV button: generate CSV from current filter results with all columns; set headers `Content-Type: text/csv`, `Content-Disposition: attachment; filename="leads_{date}.csv"`
- [x] Pagination: 25 per page

---

### TASK-S4-06 · Studio Facilities Editor (US-18)
**Story:** US-18  
**Priority:** P1

- [x] Create `admin/facilities.php` — guard `require_role(['super_admin','space_manager'])`
- [x] Facilities table: `facility_code`, `name`, `badge_label`, category badge, `rate_display`, `square_feet`, `sort_order`, `is_active` toggle, Edit button
- [x] Edit/Add form: `facility_code`, `name`, `badge_label`, `description` (textarea), `category` (select), `rate_display` (text, e.g. "₹35,000 / Day"), `square_feet` (number), `thumbnail_url` (URL input + file upload to `assets/uploads/facilities/`), `cta_text` (e.g. "Book Studio A Inspection"), `sort_order` (number), `is_active` toggle
- [x] Specifications list editor:
  - Dynamically add/remove text input rows (JS `addSpecRow()` / `removeSpecRow()`)
  - On save: collect all row values into PHP array, `json_encode()`, save to `specifications` column
  - On load: `json_decode($facility['specifications'])` → populate rows
- [x] `is_active` toggle: AJAX update same pattern as ticker toggle

---

## Sprint 5 — Hardening, SEO & Production Launch
**Stories:** US-12, US-21, US-22  
**Goal:** Complete SEO meta layer, security hardening, performance audit, staff onboarding, go-live

---

### TASK-S5-01 · SEO & Structured Data (US-22)
**Story:** US-22  
**Priority:** P0

- [x] Create `includes/seo.php` — accepts `$seo` array, renders all meta tags
- [x] Homepage (`index.php`) SEO: title `"SND Media (sndmedia.in) | National News, Broadcast & Studio Rentals — Palwal & Delhi NCR"`, description from `system_settings`
- [x] Article page (`article.php`) SEO: dynamic title `{headline} | SND Media`, description from `summary`
- [x] Studio page (`studio-rental.php`) SEO: title `"Studio On Rent in Palwal & Delhi NCR | SND Media Production Facilities"`, targeted description
- [x] All pages: OpenGraph block (`og:title`, `og:description`, `og:image`, `og:url`, `og:type`, `og:site_name`)
- [x] All pages: Twitter Card block (`twitter:card=summary_large_image`, `twitter:title`, `twitter:description`, `twitter:image`)
- [x] `article.php`: inject `NewsArticle` JSON-LD (headline, datePublished, dateModified, author name, image URL, publisher org)
- [x] `studio-rental.php`: inject `LocalBusiness` + `StudioOrRecordingStudio` JSON-LD (`name`, `address`: `SND Public School, Palwal-Sohna Road, Village Meghpur, Palwal, Haryana 121102`, `telephone`, `priceRange`, `url`: `https://sndmedia.in`)
- [x] Verify JSON-LD with Google Rich Results Test

---

### TASK-S5-02 · Security Hardening (US-22)
**Story:** US-22  
**Priority:** P0

- [x] All database queries use prepared statements with parameter binding (zero string concatenation)
- [x] CSRF protection on every form POST (login, password reset, settings, articles, ticker, youtube, facilities, leads, rental booking)
- [x] HTML input sanitization: `sanitize_output()` for plain-text strings, `sanitize_rich_html()` for rich-text
- [x] File upload restrictions: strict MIME validation (JPEG, PNG, WebP for images; PDF for docs), randomized filename hashing, blocked script extensions
- [x] Session security: `cookie_httponly = 1`, `use_only_cookies = 1`, `session_regenerate_id()` on login
- [x] Security headers in `.htaccess`: `X-Content-Type-Options: nosniff`, `X-Frame-Options: SAMEORIGIN`, `X-XSS-Protection: 1; mode=block`, `Referrer-Policy: strict-origin-when-cross-origin`
- [x] Audit all DB queries — confirm 100% use PDO prepared statements with parameter binding; zero string concatenation
- [x] Audit all output — confirm `sanitize_output()` / `htmlspecialchars()` applied to every rendered plain-text variable and `sanitize_rich_html()` applied to WYSIWYG fields (`articles.content`, `policy_*`)
- [x] Add HTTP security headers to `.htaccess` (nosniff, SAMEORIGIN, Referrer-Policy, static asset caching)
- [x] HTTPS redirect and clean URL rewrite rules in `.htaccess`
- [x] Confirm CSRF token validation on every admin POST form and `api/submit_rental.php`
- [x] Confirm honeypot field present in rental modal and validated in `api/submit_rental.php`
- [x] Confirm IP rate limiting in `api/submit_rental.php` (max 5/IP/hour)
- [x] Set `session.cookie_httponly = 1` and `session.cookie_secure = 1` in PHP config / `functions.php`
- [x] Confirm all `admin/*.php` pages have `require_login()` and appropriate `require_role()` calls

---

### TASK-S5-03 · Performance Optimisation (US-21)
**Story:** US-21  
**Priority:** P1

- [x] Performance baseline audited: zero blocking scripts, zero jQuery overhead
- [x] Add explicit `width`, `height` attributes to `<img>` tags in PHP templates
- [x] Hero image: `loading="eager"`; all other images: `loading="lazy"`
- [x] All YouTube iframes: set explicit `aspect-ratio: 16/9` via CSS to prevent CLS
- [x] Count total DB queries on homepage — stayed strictly under 5 batch queries with indexes
- [x] Pure vanilla JavaScript in `assets/js/main.js` with zero external bloat
- [x] Add `Cache-Control: max-age=3600, public` header for static assets via `.htaccess`

---

### TASK-S5-04 · Mobile & Responsive Audit
**Story:** US-21 / general  
**Priority:** P1

- [x] Test homepage across mobile and desktop viewport profiles (360px, 768px, 1280px)
- [x] Verify hamburger drawer opens/closes on mobile and touch targets are ≥ 44×44px
- [x] Verify ticker renders without overflow on mobile (smooth continuous marquee with pause on hover)
- [x] Verify news desk continuous wire collapses gracefully to 1 column on mobile
- [x] Verify studio showcase collapses cleanly to 1 column on mobile
- [x] Verify rental modal scrolls properly on mobile height-constrained screens
- [x] Verify YouTube iframe 16:9 ratio maintained across all widths
- [x] Verify footer responsive breakdown (4-col → 2-col → 1-col)
- [x] Verify article page typography and 3-bullet Highlights drawer legible on mobile

---

### TASK-S5-05 · Cross-Browser & Functionality QA
**Story:** All  
**Priority:** P1

- [x] Verify marquee animation pauses on hover in all browsers
- [x] Verify YouTube playlist player switcher works via iframe src update
- [x] Verify rental modal opens, validates, submits to database, and shows direct WhatsApp link
- [x] Verify copy-to-clipboard share button with fallback handling
- [x] Verify category tab filter works smoothly with instant DOM filtering
- [x] Verify 404 page (`404.php`) renders for invalid article slugs with recovery links
- [x] Verify admin login redirect works for unauthenticated users
- [x] Verify RBAC role restrictions across Super Admin, Editor, Reporter, and Space Manager
- [x] Verify market rate fallback behavior when API is offline

---

### TASK-S5-06 · Staff Onboarding & Documentation
**Story:** Sprint 5 delivery  
**Priority:** P2

- [x] Write 1-page quick-start guide for Editors: `docs/EDITORIAL_QUICKSTART_GUIDE.md`
- [x] Write 1-page quick-start guide for Space Managers: `docs/SPACE_MANAGEMENT_CRM_GUIDE.md`
- [x] Write Super Admin setup checklist: `docs/SUPER_ADMIN_CHECKLIST_AND_SETTINGS_REGISTRY.md`
- [x] Document all `system_settings` keys and their effect on the public site
- [x] Document Editorial Verification standard (100% human takeaways, zero unverified AI stories)

---

### TASK-S5-07 · Production Deployment
**Story:** Sprint 5 delivery  
**Priority:** P0

- [x] Production deployment checklist documented: `docs/PRODUCTION_DEPLOYMENT_GUIDE.md`
- [x] Live local database `sndmedia_db` connected on `localhost:3306` with user `root`
- [x] DDL schema created with 8 InnoDB tables, utf8mb4 collation, and seed datasets
- [x] Create robots exclusion protocol (`robots.txt`) with sitemap location
- [x] Create dynamic XML sitemap (`sitemap.php`) and static XML sitemap (`sitemap.xml`)
- [x] Verify JSON-LD Schema.org (`NewsMediaOrganization`, `NewsArticle`, `LocalBusiness`)
- [x] Confirm rental enquiry leads funnel end-to-end (modal → API → DB insert → WhatsApp link)

---

## Task Summary by Sprint

| Sprint | Tasks | Key Deliverable |
|--------|-------|-----------------|
| **Sprint 1** | TASK-S1-01 → S1-07 | Database, auth, user mgmt, password reset, global settings, free market API |
| **Sprint 2** | TASK-S2-01 → S2-08 | Public homepage, ticker, article detail, legal routes, accessibility baseline, assets/JS |
| **Sprint 3** | TASK-S3-01 → S3-05 | YouTube hub, studio showcase, rental modal, lead capture API |
| **Sprint 4** | TASK-S4-01 → S4-06 | Full admin CMS: articles, ticker, YouTube, leads CRM, facilities |
| **Sprint 5** | TASK-S5-01 → S5-07 | SEO, security hardening, performance audit, QA, deployment |

**Total Tasks: 33 | Total Checklist Items: 252**
