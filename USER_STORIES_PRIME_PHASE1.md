# User Stories & Acceptance Criteria
## Project: SND Media House — Digital Platform (Prime Edition) — Phase 1
**Based on:** `REQUIREMENTS_DOCUMENT_PRIME_PHASE1.md` (`sndmedia.in`)  
**Date:** November 2026  
**Status:** Ready for Sprint Planning  

---

## Story Map Overview

| Epic | Stories |
|------|---------|
| EP-01: Breaking News Ticker | US-01 |
| EP-02: Global Header & Navigation | US-02, US-03 |
| EP-03: Editorial News Desk | US-04, US-05, US-06 |
| EP-04: YouTube Broadcast Hub | US-07, US-08 |
| EP-05: Commercial Studio Rentals | US-09, US-10, US-11 |
| EP-06: Footer & Legal | US-12 |
| EP-07: Role-Based Admin CMS | US-13, US-14, US-15, US-16, US-17, US-18, US-23 |
| EP-08: User Account & Security | US-19, US-20 |
| EP-09: Non-Functional & SEO | US-21, US-22 |
| EP-10: Legal Pages & Accessibility | US-24, US-25 |

---

## EP-01: Breaking News Ticker

---

### US-01 — 24/7 Breaking News Marquee Ticker

**As a** site visitor,  
**I want to** see a continuously scrolling breaking news ticker at the top of every page,  
**so that** I am immediately aware of the most urgent live news bulletins without having to scroll.

**Acceptance Criteria:**

- [ ] **AC-01.1** — The ticker renders a full-width red banner (`#e11d48`) immediately below the sticky navigation bar on all pages.
- [ ] **AC-01.2** — The ticker displays a "BREAKING 24x7" prefix badge on the left with an animated white pulse dot (`animate-pulse`).
- [ ] **AC-01.3** — Ticker items scroll horizontally in a smooth, continuous loop (CSS marquee animation, ~28s cycle).
- [ ] **AC-01.4** — When a user hovers over the ticker, the scroll animation pauses so the bulletin can be read in full.
- [ ] **AC-01.5** — Each ticker item can be one of three urgency levels: **Breaking**, **Flash**, or **Routine** — the label prefix updates accordingly.
- [ ] **AC-01.6** — Only ticker items with `is_active = 1` are fetched from the `breaking_ticker` table and displayed.
- [ ] **AC-01.7** — Items render in ascending `sort_order` sequence as defined in the database.
- [ ] **AC-01.8** — Ticker items with a `link_url` set are clickable and navigate to the linked article/page.
- [ ] **AC-01.9** — The ticker is fully visible and functional on mobile devices (360px viewport width and above).
- [ ] **AC-01.10** — When no active ticker items exist in the database, the ticker bar is hidden gracefully (no empty red bar shown).

---

## EP-02: Global Header & Navigation

---

### US-02 — Top Utility Bar (Date, Market Indices, YouTube Badge)

**As a** site visitor,  
**I want to** see the current date, market indices, and YouTube channel stats in the top bar,  
**so that** I get immediate context on the edition, market status, and channel reach before reading news.

**Acceptance Criteria:**

- [ ] **AC-02.1** — The utility bar displays the current date in IST format: `Day, DD Mon YYYY` (e.g., "Friday, 13 Nov 2026").
- [ ] **AC-02.2** — The active city editions string (e.g., "Palwal • New Delhi • NCR") is pulled dynamically from `system_settings` and is editable by Super Admin.
- [ ] **AC-02.3** — SENSEX and NIFTY values and percentage change are displayed with a trend arrow (↑ green for positive, ↓ red for negative).
- [ ] **AC-02.4** — Market data is sourced from the cached value in the database (refreshed every 15 minutes via `api/market_rates.php`). If the external feed is unavailable, the last cached value is shown without error.
- [ ] **AC-02.5** — The YouTube handle (`@sndschoolmedia`, linking to `https://www.youtube.com/@sndschoolmedia`) and subscriber count text are pulled from `system_settings` and are clickable, opening the YouTube channel in a new tab.
- [ ] **AC-02.6** — The utility bar is hidden on screens narrower than 768px (md breakpoint) to preserve mobile header space; date and editions remain visible.

---

### US-03 — Sticky Navigation Header

**As a** site visitor,  
**I want to** see a persistent sticky navigation bar that remains visible as I scroll,  
**so that** I can quickly jump to any section (news desk, YouTube TV, studio rentals) from anywhere on the page.

**Acceptance Criteria:**

- [ ] **AC-03.1** — The navigation bar sticks to the top of the viewport (`position: sticky; top: 0; z-index: 50`) when the user scrolls down.
- [ ] **AC-03.2** — The sticky nav applies a frosted glassmorphism effect (`backdrop-filter: blur(12px)` with semi-transparent background) when the page is scrolled.
- [ ] **AC-03.3** — The SND logo, brand name, and tagline ("BROADCAST • DIGITAL PORTAL • STUDIO INFRASTRUCTURE") are displayed.
- [ ] **AC-03.4** — Navigation links displayed: **Live Desk** (`index.php#breaking` with animated red ping dot), **National** (`index.php?cat=national`), **Business & Tech** (`index.php?cat=business`), **YouTube TV** (`index.php#youtube-hub`), **Rent Studio & Ad Space** (`studio-rental.php` or `#space-rental`, amber pill badge) — resolving cleanly from any page (`index.php`, `article.php`, `studio-rental.php`, `legal.php`).
- [ ] **AC-03.5** — The "Rent Studio & Ad Space" link renders with an amber/gold accent pill border to visually distinguish it as a commercial CTA.
- [ ] **AC-03.6** — The **"Book Studio Floor"** CTA button is visible in the top-right of the nav and triggers the rental enquiry modal on click.
- [ ] **AC-03.7** — On screens below the `lg` breakpoint (1024px), the desktop nav links are hidden and replaced by a hamburger menu button.
- [ ] **AC-03.8** — Tapping the hamburger button opens a full drawer containing all nav links and a quick contact option.
- [ ] **AC-03.9** — All nav anchor links scroll smoothly to their corresponding page sections when on the homepage, and navigate cleanly back to the homepage section when triggered from subpages.
- [ ] **AC-03.10** — Prototype-only and Phase 2 AI elements from `concept_1_prime_broadcast.html` — specifically the Concept Switcher Banner, the top-bar `"AI Smart Summaries Active"` pill, the `"AI 60s Brief"` nav link, `"AI Fact-Checked"` badges, and the public YouTube test-upload input — are **removed** from the production build.

---

## EP-03: Editorial News Desk & Articles

---

### US-04 — Hero Lead Story Spotlight

**As a** site visitor,  
**I want to** see a prominent featured hero article at the top of the homepage,  
**so that** the most important current story is immediately visible and engaging.

**Acceptance Criteria:**

- [ ] **AC-04.1** — The hero story is the article with `is_hero_lead = 1` in the `articles` table. If none is set, the most recently published article is used as fallback.
- [ ] **AC-04.2** — The hero card displays: full-width 16:9 featured image, category badge(s), author/desk byline, estimated read time, and "Updated X mins ago" timestamp.
- [ ] **AC-04.3** — The timestamp shows a human-readable relative time (e.g., "18 mins ago", "2 hours ago") computed server-side from `updated_at`.
- [ ] **AC-04.4** — The headline renders in large responsive typography (at least `text-2xl` on mobile, `text-4xl` on desktop).
- [ ] **AC-04.5** — A 2-sentence article summary is displayed below the headline.
- [ ] **AC-04.6** — A collapsible **"Key Takeaways"** drawer is present. The toggle button label reads "Show Key Takeaways" / "Hide Key Takeaways" (Phase 1 — no AI branding).
- [ ] **AC-04.7** — Clicking the toggle reveals up to 3 editorial bullet points (`highlight_1`, `highlight_2`, `highlight_3` from the `articles` table). Bullets with a NULL value are not rendered.
- [ ] **AC-04.8** — If the article has a `youtube_video_id` set, a "Watch Video Report" pill button overlay is shown on the hero image, linking to the YouTube hub section.
- [ ] **AC-04.9** — Hovering the hero image applies a subtle scale-up zoom transition (`scale-105`, `duration-700`).
- [ ] **AC-04.10** — The entire hero card is clickable and navigates to `article.php?slug={article-slug}`.

---

### US-05 — Continuous News Desk Feed

**As a** site visitor,  
**I want to** browse the latest news articles filtered by category,  
**so that** I can quickly find stories relevant to my interests (National, Business, Tech, etc.).

**Acceptance Criteria:**

- [ ] **AC-05.1** — The news desk section displays articles in a 2-column responsive grid (1 column on mobile).
- [ ] **AC-05.2** — Category filter tabs are displayed for all 6 canonical categories: **All**, **National**, **Business**, **Tech**, **Energy**, **Exclusive**, **World**. The "All" tab is active by default unless a `?cat={category}` query parameter is supplied.
- [ ] **AC-05.3** — Clicking a category tab filters the displayed articles to that category without a full page reload (client-side filter or AJAX call); loading `index.php?cat={category}` (e.g., from footer or nav links) pre-selects that tab and scrolls smoothly to the News Desk.
- [ ] **AC-05.4** — Each article card shows: thumbnail image, color-coded category slug, 2-line truncated headline, publication timestamp, editorial desk tag, and the article's `verification_badge` pill (`Verified Report`, `Special Investigation`, or `Ground Report`) when not `'none'`.
- [ ] **AC-05.5** — Hovering a card thumbnail applies a micro-zoom effect.
- [ ] **AC-05.6** — Clicking any article card navigates to `article.php?slug={slug}`.
- [ ] **AC-05.7** — Only articles with `status = 'published'` are shown to public visitors.
- [ ] **AC-05.8** — Articles are ordered by `published_at DESC` (newest first).
- [ ] **AC-05.9** — The hero lead article (`is_hero_lead = 1`) is excluded from the news desk grid to avoid duplication.

---

### US-06 — Single Article Detail View

**As a** site visitor,  
**I want to** read a full news article on a dedicated page,  
**so that** I can consume the complete story with all media, context, and sharing options.

**Acceptance Criteria:**

- [ ] **AC-06.1** — The article page (`article.php?slug={slug}`) renders when a valid slug is passed. An invalid or missing slug returns a 404 page.
- [ ] **AC-06.2** — Breadcrumb navigation is shown: `Home > {Category} > {Headline}`.
- [ ] **AC-06.3** — The author byline shows the author name, publish date, and last updated timestamp.
- [ ] **AC-06.4** — Social sharing buttons are present for: **WhatsApp**, **X (Twitter)**, **Facebook**, **LinkedIn**, and **Copy Link**. Each opens the correct share URL with the article title and URL pre-filled.
- [ ] **AC-06.5** — The article body supports: paragraphs, blockquotes, full-width images, and YouTube video embeds (via `youtube_video_id`).
- [ ] **AC-06.6** — At least one commercial ad banner slot (leaderboard or sidebar MPU) is rendered within or alongside the article body, sourced dynamically from `system_settings` (`ad_leaderboard_*` / `ad_sidebar_*` keys) when active.
- [ ] **AC-06.7** — A "Related News" section at the bottom displays up to 3 published articles from the same category, ordered by `published_at DESC`.
- [ ] **AC-06.8** — The page `<head>` contains dynamic OpenGraph tags (`og:title`, `og:description`, `og:image`) and Twitter Card meta tags populated from the article's title, summary, and featured image.
- [ ] **AC-06.9** — `NewsArticle` JSON-LD structured data is injected in the `<head>` with headline, datePublished, dateModified, author, and image.
- [ ] **AC-06.10** — The article page is fully responsive and readable on 360px mobile screens.

---

## EP-04: YouTube Broadcast Hub

---

### US-07 — Embedded YouTube TV Player

**As a** site visitor,  
**I want to** watch SND's YouTube broadcast directly on the site,  
**so that** I can consume video content without leaving the platform.

**Acceptance Criteria:**

- [ ] **AC-07.1** — The YouTube player renders as a responsive 16:9 `iframe` using the privacy-enhanced `youtube-nocookie.com` embed URL.
- [ ] **AC-07.2** — The player loads the video ID stored as `is_main_featured = 1` in the `youtube_bulletins` table.
- [ ] **AC-07.3** — A "LIVE BROADCAST" badge with an animated red pulse dot is displayed above the player.
- [ ] **AC-07.4** — A **"Subscribe"** button is visible next to the player heading and opens the SND YouTube channel in a new tab.
- [ ] **AC-07.5** — The player iframe has `allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"` and `allowfullscreen` attributes set.
- [ ] **AC-07.6** — The player does not autoplay on page load (autoplay=0 by default) to avoid unexpected sound.

---

### US-08 — Video Bulletin Playlist Switcher

**As a** site visitor,  
**I want to** click on a recent video bulletin thumbnail to load it in the main player,  
**so that** I can watch specific debate episodes or news reports without leaving the page.

**Acceptance Criteria:**

- [ ] **AC-08.1** — Up to 5 recent video bulletins from the `youtube_bulletins` table are listed below the main player, ordered by `sort_order ASC`.
- [ ] **AC-08.2** — Each playlist item shows: thumbnail image (from `thumbnail_url`, fallback to `https://img.youtube.com/vi/{video_id}/hqdefault.jpg`), title, and `views_label`.
- [ ] **AC-08.3** — Clicking a playlist item updates the main player `iframe` `src` to the selected `video_id` with `autoplay=1` — no full page reload occurs.
- [ ] **AC-08.4** — The currently active playlist item is visually highlighted (e.g., border or background change).
- [ ] **AC-08.5** — The playlist is scrollable if more than 3 items are shown, without overflowing the sidebar layout.

---

## EP-05: Commercial Studio Rentals

---

### US-09 — Studio Rental Spotlight Sidebar Widget

**As a** potential client visiting the news site,  
**I want to** see a prominent commercial callout for SND's studio spaces in the sidebar,  
**so that** I can quickly discover rental options and initiate an enquiry without searching.

**Acceptance Criteria:**

- [ ] **AC-09.1** — The commercial sidebar widget renders with a 2px amber/gold border (`#f59e0b`) to visually distinguish it from editorial content.
- [ ] **AC-09.2** — The widget displays the badge: "Commercial Space Available | Rates From ₹3,500/hr" (rate value pulled from `system_settings`).
- [ ] **AC-09.3** — Four facility badges are shown: Podcast Suite, Studio Floor A, Digital Ad Slots, OB Van & Live Uplink — each with an icon and brief spec line.
- [ ] **AC-09.4** — A **"Request Rate Card"** button is present and triggers the rental modal on click.
- [ ] **AC-09.5** — A **"Call Space Desk"** button renders as a `tel:` link with the configured phone number from `system_settings`.
- [ ] **AC-09.6** — The widget is visible in the right sidebar on desktop (≥1024px). On mobile, it stacks below the main content column.

---

### US-10 — Detailed Studio Showcase Section

**As a** production house or brand manager,  
**I want to** see detailed specifications and pricing for each SND studio facility,  
**so that** I can evaluate which space best fits my production requirements.

**Acceptance Criteria:**

- [ ] **AC-10.1** — The studio showcase section renders as a full-width section with active facility cards pulled dynamically from the `studio_facilities` table where `is_active = 1`, ordered by `sort_order ASC`.
- [ ] **AC-10.2** — Each facility card displays: `badge_label`, `thumbnail_url` cover image, facility `name`, pricing (`rate_display`), `description`, and the feature checklist from the `specifications` JSON array.
- [ ] **AC-10.3** — Each card renders its dynamic `cta_text` button (e.g., "Book Studio A Inspection", "Book Podcast Slot", "Enquire For Ad Inventory") that triggers the rental modal with the corresponding facility pre-selected.
- [ ] **AC-10.4** — Hovering a card applies a gold/amber border highlight (`border-amber-400/50`).
- [ ] **AC-10.5** — The **"Download Media Kit & Rate Card"** section header button behaves as follows:
  - If `media_kit_pdf_url` is set in `system_settings`, it triggers a direct file download.
  - If `media_kit_pdf_url` is empty/null, it opens the rental enquiry modal instead.
- [ ] **AC-10.6** — `LocalBusiness` / `StudioOrRecordingStudio` JSON-LD structured data is injected on the studio rental page (`studio-rental.php`) for local SEO.
- [ ] **AC-10.7** — The 3-card grid collapses to a single column on mobile.

---

### US-11 — Lead Capture & Booking Modal

**As a** potential client,  
**I want to** submit a studio rental enquiry via a modal form,  
**so that** SND's commercial team can contact me quickly with a rate card and availability.

**Acceptance Criteria:**

- [ ] **AC-11.1** — The rental modal opens from any "Book", "Request Rate Card", or "Enquire" CTA on the page.
- [ ] **AC-11.2** — The modal title dynamically updates to reflect the facility being enquired about (e.g., "Book: Studio A - Broadcast Newsroom").
- [ ] **AC-11.3** — The form contains all required and optional fields: Client Full Name (required), Production House / Organization (optional), Email (required), Phone/WhatsApp (required), Facility (dropdown), Target Date (date picker), and Requirements (textarea).
- [ ] **AC-11.4** — The **Facility dropdown** pre-selects the facility matching the button that triggered the modal.
- [ ] **AC-11.5** — Form validation:
  - Client Full Name: required, minimum 2 characters.
  - Email: required, valid email format.
  - Phone: required, accepts 10-digit Indian numbers or international format with `+` prefix.
  - Target Date: required, must not be a past date.
- [ ] **AC-11.6** — A honeypot hidden field is present in the form markup. If it is filled on submission, the request is silently rejected (HTTP 200 returned but no data saved).
- [ ] **AC-11.7** — A CSRF token is generated per session, included as a hidden field, and validated server-side on `api/submit_rental.php`.
- [ ] **AC-11.8** — On successful submission:
  - The enquiry is inserted into `rental_enquiries` (persisting `client_name`, `production_house`, `email`, `phone`, `facility_requested`, `target_date`, `requirement_details`, `ip_address`) with `status = 'new'`.
  - An email notification is dispatched to `space@sndmedia.in` with all submitted details.
  - An on-screen success message is shown: "Enquiry received! The SND Space Manager will connect within 15 minutes."
  - A WhatsApp pre-filled link button is shown: `https://wa.me/{whatsapp_number}?text=...` with the facility name and date pre-filled in the message.
- [ ] **AC-11.9** — The modal closes cleanly via the ✕ button or by clicking the backdrop overlay.
- [ ] **AC-11.10** — IP-based rate limiting: a maximum of 5 submissions per IP per hour is enforced server-side. Excess requests return a user-friendly error message.

---

## EP-06: Footer & Legal

---

### US-12 — Dynamic Footer

**As a** site visitor,  
**I want to** find SND's contact details, news categories, and legal links in the footer,  
**so that** I can quickly contact the right desk or access compliance pages.

**Acceptance Criteria:**

- [ ] **AC-12.1** — The footer displays 4 columns: Brand & About, News Categories, Commercial Space on Rent, Editorial & Studio Offices.
- [ ] **AC-12.2** — Headquarters address (`SND Public School, Palwal-Sohna Road, Village Meghpur, Palwal, Haryana — 121102`), phone numbers, and desk emails (`newsdesk@sndmedia.in`, `space@sndmedia.in`) are pulled dynamically from `system_settings`.
- [ ] **AC-12.3** — Social media icon links (YouTube, X/Twitter, Instagram, LinkedIn) are pulled from `system_settings` and open in a new tab.
- [ ] **AC-12.4** — News category links in the footer use the canonical taxonomy: National & Politics, Economy & Budget, Tech & AI Startups, World & Geopolitics, Special Ground Reports, Energy & Green.
- [ ] **AC-12.5** — Commercial Space on Rent links point to the studio showcase section or `studio-rental.php`.
- [ ] **AC-12.6** — The footer bottom bar contains: copyright notice with current year, and links to Privacy Policy, Terms of Service, Rental Agreement Rules, and Editorial Ethics Code.
- [ ] **AC-12.7** — The footer renders in a 4-column grid on desktop, collapsing to a 2-column grid on tablet and 1-column stack on mobile.

---

## EP-07: Role-Based Admin CMS

---

### US-13 — Admin Login & Session Management

**As an** admin user (any role),  
**I want to** securely log in to the CMS admin panel,  
**so that** I can manage content, leads, or settings according to my assigned role.

**Acceptance Criteria:**

- [ ] **AC-13.1** — The login page (`admin/login.php`) accepts username/email and password.
- [ ] **AC-13.2** — Passwords are verified using `password_verify()` against the `PASSWORD_BCRYPT` hash stored in the `users` table.
- [ ] **AC-13.3** — On successful login, `session_regenerate_id(true)` is called and the user's `role` and `id` are stored in the session.
- [ ] **AC-13.4** — On failed login (wrong credentials), a generic error message is shown: "Invalid username or password." No specific detail about which field was wrong.
- [ ] **AC-13.5** — The `last_login` timestamp is updated in the `users` table on every successful authentication.
- [ ] **AC-13.6** — Users with `is_active = 0` are denied login with the message: "Your account has been deactivated. Contact your administrator."
- [ ] **AC-13.7** — All admin pages (`admin/*.php`) include an `auth.php` guard that redirects unauthenticated requests to `admin/login.php`.
- [ ] **AC-13.8** — The dashboard (`admin/index.php`) shows a role-aware summary: editors see editorial stats; space managers see lead counts; super admins see both.
- [ ] **AC-13.9** — `admin/logout.php` destroys the session (`session_destroy()`) and redirects to `admin/login.php`.

---

### US-14 — Article Management (Editor & Reporter)

**As an** Editor or Reporter,  
**I want to** create, edit, and manage news articles in the CMS,  
**so that** published content on the public site stays current and accurate.

**Acceptance Criteria:**

- [ ] **AC-14.1** — The article editor form (`admin/articles.php`) includes fields for: Title, Slug (auto-generated from title, editable), Category (dropdown — canonical taxonomy), Subcategory, Tags, Meta Keywords, Featured Image (URL input or file upload to `assets/uploads/articles/`), Summary, Highlight 1/2/3, Body (rich-text WYSIWYG), Author Name, Read Time, Verification Badge (dropdown: `none`, `verified_report`, `special_investigation`, `ground_report`), YouTube Video ID, Hero Lead toggle, Breaking toggle, and Status.
- [ ] **AC-14.2** — The slug is auto-generated from the title (lowercase, hyphens, strip special characters) but can be manually overridden. Duplicate slugs are rejected with a validation error.
- [ ] **AC-14.3** — **Reporters** can only save articles with `status = 'draft'`. The Status field is locked for reporters.
- [ ] **AC-14.4** — **Editors** can save articles with any status (`draft`, `published`, `archived`).
- [ ] **AC-14.5** — Reporters can only edit their own articles (`author_id = session user id`). Attempting to edit another reporter's article redirects with an "Access Denied" message.
- [ ] **AC-14.6** — Editors can edit any article regardless of author.
- [ ] **AC-14.7** — Only one article can have `is_hero_lead = 1` at a time. Setting a new hero lead automatically unsets the previous one.
- [ ] **AC-14.8** — The article list view shows: title, category, status badge, author, published date, and Edit / Delete action buttons.
- [ ] **AC-14.9** — Deleting an article sets `status = 'archived'` (soft delete) rather than removing the database row.
- [ ] **AC-14.10** — Plain-text inputs are trimmed and escaped on output with `sanitize_output()` (`htmlspecialchars()`), while WYSIWYG article body HTML is cleaned with `sanitize_rich_html()` (allowlist sanitizer stripping scripts/event attributes) before rendering.

---

### US-15 — Breaking Ticker Desk (Editor & Super Admin)

**As an** Editor,  
**I want to** add, reorder, and expire breaking news ticker items in the CMS,  
**so that** the public-facing ticker always reflects the current news priority.

**Acceptance Criteria:**

- [ ] **AC-15.1** — The ticker manager (`admin/ticker.php`) lists all current ticker items with: text, urgency level badge, active status toggle, sort order, and created timestamp.
- [ ] **AC-15.2** — New ticker items can be added with: ticker text (max 300 chars), optional link URL, and urgency level (Breaking / Flash / Routine).
- [ ] **AC-15.3** — Urgency level defaults to "Breaking" for new items.
- [ ] **AC-15.4** — Active/inactive status can be toggled per item with a single click (AJAX toggle, no full page reload).
- [ ] **AC-15.5** — Sort order can be updated via a numeric input field or drag-and-drop reordering.
- [ ] **AC-15.6** — Ticker items can be deleted permanently from this screen.
- [ ] **AC-15.7** — Reporters and Space Managers are denied access to this page (redirected with "Access Denied").

---

### US-16 — YouTube Hub Admin (Editor & Super Admin)

**As an** Editor,  
**I want to** update the featured video and playlist in the CMS by pasting a YouTube URL,  
**so that** the broadcast hub on the public site always shows the latest content without touching code.

**Acceptance Criteria:**

- [ ] **AC-16.1** — The YouTube admin page (`admin/youtube.php`) shows a list of current playlist items with their title, thumbnail preview, `views_label`, featured status, and sort order.
- [ ] **AC-16.2** — An "Add Video" form accepts a full YouTube URL (`youtube.com/watch?v=...`, `youtu.be/...`, `/shorts/...`) or a raw 11-character Video ID.
- [ ] **AC-16.3** — The system auto-extracts the 11-character Video ID from any valid YouTube URL format using server-side parsing.
- [ ] **AC-16.4** — When a video is saved, `thumbnail_url` is auto-populated as `https://img.youtube.com/vi/{video_id}/hqdefault.jpg` unless overridden manually.
- [ ] **AC-16.5** — Setting a video as "Main Featured" (`is_main_featured = 1`) automatically demotes the previous featured video.
- [ ] **AC-16.6** — Sort order and `views_label` are editable per item.
- [ ] **AC-16.7** — Reporters and Space Managers are denied access to this page.

---

### US-17 — Studio Leads CRM (Space Manager & Super Admin)

**As a** Space & Commercial Manager,  
**I want to** view, filter, update, and export all studio rental enquiries,  
**so that** I can efficiently follow up with leads and track the sales pipeline.

**Acceptance Criteria:**

- [ ] **AC-17.1** — The leads dashboard (`admin/leads.php`) displays all enquiries from `rental_enquiries` ordered by `created_at DESC`.
- [ ] **AC-17.2** — Each row shows: client name, production house, email, phone, facility requested, target date, submission date, and current status badge.
- [ ] **AC-17.3** — The status can be updated inline or via a dropdown: `New → Contacted → Negotiating → Booked → Archived`. The change is saved immediately to the database.
- [ ] **AC-17.4** — An internal **notes field** is available per enquiry for the sales team to log follow-up actions. Notes are stored server-side and visible only in the admin panel.
- [ ] **AC-17.5** — A filter bar allows filtering enquiries by: **status**, **facility requested**, and **date range** (target_date or created_at).
- [ ] **AC-17.6** — A **"Export CSV"** button generates and downloads a `.csv` file of the filtered or all leads containing all columns.
- [ ] **AC-17.7** — Editors and Reporters are denied access to this page.

---

### US-18 — Studio Facilities & Rate Card Editor (Space Manager & Super Admin)

**As a** Space Manager,  
**I want to** update studio pricing, specifications, and availability in the CMS,  
**so that** the public-facing studio cards always show accurate, current rates and features.

**Acceptance Criteria:**

- [ ] **AC-18.1** — The facilities editor (`admin/facilities.php`) lists all entries from `studio_facilities` ordered by `sort_order ASC` with: name, `badge_label`, category, `rate_display`, `square_feet`, `sort_order`, active status, and Edit button.
- [ ] **AC-18.2** — Each facility can be edited to update: `name`, `badge_label`, `description`, `rate_display` (free text, e.g., "₹35,000 / Day"), `square_feet`, `specifications` JSON array (editable as a line-by-line list of features in the UI), `thumbnail_url` (URL input or file upload to `assets/uploads/facilities/`), `cta_text`, and `sort_order`.
- [ ] **AC-18.3** — The `is_active` toggle controls whether the facility card appears on the public site.
- [ ] **AC-18.4** — The `specifications` JSON is stored as a valid JSON array. The admin UI presents it as a simple list (add/remove lines); the backend serializes to JSON before saving.
- [ ] **AC-18.5** — Editors and Reporters are denied access to this page.

---

### US-23 — Global Site Settings Editor (Super Admin Only)

**As a** Super Admin,  
**I want to** edit all site-wide content and configuration values from a single settings panel,  
**so that** the portal's contact details, branding text, market data, social links, and commercial copy stay accurate without any code changes.

**Acceptance Criteria:**

- [ ] **AC-23.1** — The settings panel (`admin/settings.php`) is accessible only to `super_admin` role. All other roles are redirected with "Access Denied".
- [ ] **AC-23.2** — Settings are grouped into clearly labelled sections on the page:
  - **Utility Bar, Site Meta & Editions** — Default site meta description (`site_meta_description`), city editions text, YouTube handle, subscriber count label.
  - **Market Data** — Manual SENSEX value, SENSEX % change, NIFTY value, NIFTY % change, trend direction toggle (up/down per index), and auto-feed enable/disable toggle.
  - **Contact & Offices** — HQ address (default: `SND Public School, Palwal-Sohna Road, Village Meghpur, Palwal, Haryana — 121102`), studio booking phone number (default: `+91-7206622711`), WhatsApp number (default: `+917206622711`), `newsdesk@sndmedia.in`, `space@sndmedia.in`, `ads@sndmedia.in`.
  - **Social Media Links** — YouTube channel URL (default: `https://www.youtube.com/@sndschoolmedia`), Twitter/X profile URL, Instagram URL, LinkedIn URL, Facebook URL.
  - **Commercial Rates** — Starting rate display text (e.g., "₹3,500/hr") shown in the sidebar widget badge.
  - **Media Kit PDF** — Upload PDF (saved to `assets/uploads/docs/`) or paste URL for the downloadable rate card PDF (`media_kit_pdf_url`). Clear/remove option included.
  - **Commercial Ad Banners** — Leaderboard and Sidebar MPU ad slots (`ad_leaderboard_image` / `ad_sidebar_image` via upload or URL, destination URLs, alt labels, and active toggles).
  - **Legal & Policy Pages** — Editable body content (rich text) for Privacy Policy, Terms of Service, Rental Agreement Rules, and Editorial Ethics Code pages.
- [ ] **AC-23.3** — Each setting maps to a row in the `system_settings` table with a unique `setting_key`. Saving any section performs a MySQL `INSERT ... ON DUPLICATE KEY UPDATE` to upsert the values.
- [ ] **AC-23.4** — A canonical key registry is maintained for all `system_settings` records and is used consistently by admin UI, server code, and frontend rendering. Keys are documented and not duplicated across multiple naming patterns.
- [ ] **AC-23.5** — All fields are pre-populated with the current saved values on page load.
- [ ] **AC-23.6** — When **auto-feed (`market_auto_feed = 1`) is enabled**, the system calls a free financial API (e.g., Yahoo Finance public JSON endpoint) via `api/market_rates.php`, caches the result in `system_settings` for 15 minutes, and auto-populates the SENSEX and NIFTY fields with the latest fetched values and trend direction. When `market_auto_feed = 0`, manual admin values are preserved and never overwritten by the external feed. A "Last fetched: X mins ago" label is shown next to the fields. When the external feed is unavailable or returns an error, the last cached values are retained and a warning badge is displayed: "Live feed unavailable — showing cached data." The Market Data section also shows a live UI preview of how the SENSEX/NIFTY bar will render on the public site (green ↑ / red ↓) using the currently displayed values, updating in real-time as the admin edits fields.
- [ ] **AC-23.7** — WhatsApp number field validates to E.164 international format (e.g., `+917206622711`) with a clear error if invalid. Phone number field accepts 10-digit Indian or international `+` format.
- [ ] **AC-23.8** — The `media_kit_pdf_url` field shows the current filename/URL if set, with a "Remove PDF" button that clears the value and reverts the "Download Rate Card" CTA to modal-trigger mode.
- [ ] **AC-23.9** — All form submissions include a CSRF token validated server-side. Invalid requests are rejected with HTTP 403.
- [ ] **AC-23.10** — On successful save (via POST-Redirect-GET session flash or AJAX), an inline success banner is shown: "Settings updated successfully." and the form displays all updated saved values.
- [ ] **AC-23.11** — Changes to contact details, social links, and market data are reflected on the public site on the very next page load (no cache flush required beyond the 15-minute market data cache).

---

### US-24 — Public Legal Policy Pages

**As a** site visitor or compliance stakeholder,  
**I want to** access clearly defined legal and policy pages for the website and studio operations,  
**so that** I can review terms, privacy rules, and editorial standards before engaging with content or rentals.

**Acceptance Criteria:**

- [ ] **AC-24.1** — Public routes are available for: Privacy Policy (`/legal/privacy-policy`), Terms of Service (`/legal/terms-of-service`), Rental Rules (`/legal/rental-rules`), and Editorial Ethics (`/legal/editorial-ethics`).
- [ ] **AC-24.2** — Clean `/legal/{slug}` URLs are routed via `.htaccess` rewrite rules to `legal.php` and linked consistently from the footer and compliance blocks.
- [ ] **AC-24.3** — Legal page content is editable from the admin settings area (`policy_*` keys) and is rendered server-side.
- [ ] **AC-24.4** — Policy page titles are escaped with `sanitize_output()` (`htmlspecialchars()`), and rich-text HTML bodies are sanitized through `sanitize_rich_html()` (allowlist sanitizer stripping scripts/event handlers while preserving safe formatting tags) rather than double-escaping HTML markup.
- [ ] **AC-24.5** — The pages are fully responsive and readable on both desktop and mobile devices.

---

### US-25 — Accessibility & UX Baseline

**As a** user with standard web accessibility needs,  
**I want to** navigate the site and admin tools with clear focus states, readable contrast, and navigable forms,  
**so that** I can use the platform without frustration or exclusion.

**Acceptance Criteria:**

- [ ] **AC-25.1** — All interactive controls have visible keyboard focus states.
- [ ] **AC-25.2** — Minimum interactive target sizes and readable typography are enforced across the public and admin interfaces.
- [ ] **AC-25.3** — Form validation communicates errors in text as well as visual styling.
- [ ] **AC-25.4** — The platform respects reduced motion preferences for marquee and pulse animations.
- [ ] **AC-25.5** — Semantic HTML and labels are used across forms, buttons, and navigation components.

---

## EP-08: User Account & Security

---

### US-19 — User Management (Super Admin Only)

**As a** Super Admin,  
**I want to** create, edit, deactivate, and assign roles to admin users,  
**so that** only authorized staff can access the CMS with the correct level of permissions.

**Acceptance Criteria:**

- [ ] **AC-19.1** — The user management page (`admin/users.php`) is accessible only to users with `role = 'super_admin'`. All other roles are redirected with "Access Denied".
- [ ] **AC-19.2** — The user list shows: full name, username, email, role badge, active status, and last login timestamp.
- [ ] **AC-19.3** — Creating a new user requires: full name, username (unique), email (unique), role, and initial password. Password is hashed with `password_hash(..., PASSWORD_BCRYPT)` with cost factor ≥ 12 before storage.
- [ ] **AC-19.4** — Editing a user allows updating: full name, email, role, and active status. Password is only changed if the "New Password" field is filled.
- [ ] **AC-19.5** — A Super Admin cannot deactivate their own account.
- [ ] **AC-19.6** — Deactivating a user (`is_active = 0`) immediately invalidates any active sessions for that user (session check on every admin page load against `is_active`).
- [ ] **AC-19.7** — Deleting a user is not permitted; deactivation is the only supported action to preserve audit trail and foreign key integrity (`author_id` on articles).

---

### US-20 — Password Reset

**As an** admin user,  
**I want to** reset my password via a secure token-based flow,  
**so that** I can regain access to the CMS if I forget my credentials.

**Acceptance Criteria:**

- [ ] **AC-20.1** — A "Forgot Password?" link is present on the `admin/login.php` page.
- [ ] **AC-20.2** — Submitting the forgot password form with a registered email generates a unique token (`bin2hex(random_bytes(32))`), saves it in `password_reset_tokens` with `expires_at = NOW() + 1 HOUR`, and sends a reset link to the email.
- [ ] **AC-20.3** — The reset email contains a link in the format: `admin/reset_password.php?token={token}`.
- [ ] **AC-20.4** — Opening a valid, unexpired, unused token link renders the new password form.
- [ ] **AC-20.5** — Submitting the new password form updates the user's `password_hash` with a fresh BCRYPT hash and marks `is_used = 1` on the token.
- [ ] **AC-20.6** — New password must be at least 10 characters. Validation error shown if requirement is not met.
- [ ] **AC-20.7** — Expired tokens (past `expires_at`) or already-used tokens (`is_used = 1`) display: "This reset link has expired or already been used. Please request a new one."
- [ ] **AC-20.8** — A Super Admin can also trigger a password reset for any user from `admin/users.php` (admin-initiated flow — sends the reset email to that user's registered address).

---

## EP-09: Non-Functional & SEO

---

### US-21 — Performance & Core Web Vitals

**As a** site visitor on a mobile device,  
**I want** pages to load quickly and without layout jumps,  
**so that** I can read breaking news without frustration on slow connections.

**Acceptance Criteria:**

- [ ] **AC-21.1** — Largest Contentful Paint (LCP) is under **2.0 seconds** on a simulated 4G mobile connection (measured via Chrome Lighthouse or PageSpeed Insights).
- [ ] **AC-21.2** — Cumulative Layout Shift (CLS) score is below **0.05** — all images and embeds have explicit `width`/`height` or `aspect-ratio` set to prevent reflow.
- [ ] **AC-21.3** — No external JavaScript framework (React, Vue, Angular, jQuery) is loaded. All interactivity is vanilla JS.
- [ ] **AC-21.4** — Tailwind CSS is loaded via CDN in development; a purged/compiled CSS file is used in production to minimize payload.
- [ ] **AC-21.5** — All `<img>` tags include `width`, `height`, and `loading="lazy"` attributes (except the hero lead image, which uses `loading="eager"`).
- [ ] **AC-21.6** — Database queries use PDO prepared statements. The homepage must not execute more than **10 database queries** total.

---

### US-22 — SEO, Schema & Security Hardening

**As** SND's digital team,  
**I want** the platform to be discoverable on Google and secure from common web attacks,  
**so that** we rank highly for news searches and studio rental queries while protecting editorial and commercial data.

**Acceptance Criteria:**

- [ ] **AC-22.1** — Every public page has a unique, dynamically generated `<title>` and `<meta name="description">` tag.
- [ ] **AC-22.2** — All article pages include `NewsArticle` JSON-LD structured data (headline, datePublished, dateModified, author, image, publisher).
- [ ] **AC-22.3** — The `studio-rental.php` page includes `LocalBusiness` and `StudioOrRecordingStudio` JSON-LD structured data with name, address (`SND Public School, Palwal-Sohna Road, Village Meghpur, Palwal, Haryana — 121102`), telephone, priceRange, and canonical `url` (`https://sndmedia.in`).
- [ ] **AC-22.4** — All public pages include fully populated OpenGraph (`og:title`, `og:description`, `og:image`, `og:url`, `og:type`) and Twitter Card meta tags.
- [ ] **AC-22.5** — All database queries across the entire application use PDO prepared statements with bound parameters. No raw `$_GET` / `$_POST` values are interpolated directly into SQL strings.
- [ ] **AC-22.6** — All plain-text HTML output of user-generated or CMS-entered content is escaped with `sanitize_output()` (`htmlspecialchars()`), while WYSIWYG rich-text HTML (`articles.content`, `policy_*`) is cleaned with the `sanitize_rich_html()` allowlist sanitizer before rendering.
- [ ] **AC-22.7** — All admin form submissions include a CSRF token validated server-side. Requests failing CSRF validation are rejected with HTTP 403.
- [ ] **AC-22.8** — The rental enquiry form includes a hidden honeypot field. Bot submissions filling this field are silently discarded.
- [ ] **AC-22.9** — HTTP response headers include: `X-Content-Type-Options: nosniff`, `X-Frame-Options: SAMEORIGIN`, and `Referrer-Policy: strict-origin-when-cross-origin`.
- [ ] **AC-22.10** — HTTPS is enforced; HTTP requests are redirected to HTTPS via `.htaccess`.

---

## Appendix: Story → Sprint Mapping

| Sprint | Stories |
|--------|---------|
| **Sprint 1** — Database & Core Architecture | US-13 (auth layer), US-19 (users), US-20 (password reset), US-23 (global settings) |
| **Sprint 2** — Modular Frontend, News Desk & Legal/UX | US-01, US-02, US-03, US-04, US-05, US-06, US-12, US-24, US-25 |
| **Sprint 3** — YouTube Hub & Studio Lead Funnel | US-07, US-08, US-09, US-10, US-11 |
| **Sprint 4** — Role-Based CMS Administration | US-14, US-15, US-16, US-17, US-18 |
| **Sprint 5** — Hardening, SEO & Launch | US-21, US-22 |

---

*Total Stories: 25 | Total Acceptance Criteria: 193*
