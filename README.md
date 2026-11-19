# SND Media (`sndmedia.in`) — Phase 1 Digital Newsroom & Studio Portal

**Where Students Lead Media — Speak. Narrate. Discover.**

SND Media is a PHP 8 / MySQL production web platform operating out of **SND Public School Campus, Palwal-Sohna Road, Village Meghpur, Palwal, Haryana (Delhi NCR)**. It combines a 24/7 digital newsroom, the official `@sndschoolmedia` YouTube broadcast hub, commercial production studio rental booking & CRM, and a role-based administrative CMS.

---

## Key Features

- **Public Broadcast Newsroom (`index.php`, `article.php`):**
  - Hero lead story with **Human Editorial 3-Bullet Highlights** and verification badges (`Verified Report`, `Special Investigation`, `Ground Report`, `Editorial Analysis`).
  - Continuous news desk feed with instant client-side category filtering across 6 desks (`National`, `Business`, `Tech`, `Energy`, `Exclusive`, `World`).
  - 24/7 breaking news marquee ticker (`breaking_ticker`) with urgency badges and pause/play controls.
- **Official `@sndschoolmedia` YouTube Broadcast Hub (`includes/youtube_hub.php`):**
  - Embedded privacy-enhanced player (`youtube-nocookie.com`) paired with a curated broadcast playlist synced from the CMS.
- **Commercial Studio Space & Rate Estimator (`studio-rental.php`, `includes/rental_modal.php`):**
  - Interactive floor & duration estimator for **Studio A (2,200 sq. ft Chroma Newsroom)**, **Studio B (Multi-Cam Podcast Suite)**, and **Campus Billboards / Web Ad Inventory**.
  - Rate-limited booking enquiry modal with instant **WhatsApp Leasing Desk** handoff (`api/submit_rental.php`).
- **Role-Based Admin CMS & CRM (`admin/`):**
  - Protected portal (`super_admin`, `editor`, `reporter`, `space_manager`) with BCRYPT password hashing, CSRF protection, session role verification, and 1-hour token-based password recovery (`admin/reset_password.php`).
  - Editorial authoring, breaking ticker controls, YouTube broadcast manager, studio facility rate cards, commercial leads CRM with CSV export, and dynamic global site settings (`system_settings`).
- **Market Ticker API & SEO (`api/market_rates.php`, `sitemap.php`, `includes/seo.php`):**
  - Live/cached **SENSEX** and **NIFTY 50** index feeds, dynamic XML sitemap (`/sitemap.xml` rewritten to `sitemap.php`), OpenGraph/Twitter meta tags, and JSON-LD structured data.

---

## Tech Stack

- **Backend:** PHP 8.1+ (PDO MySQL, strict prepared statements)
- **Database:** MySQL 8.0+ / MariaDB 10.4+ (`utf8mb4_unicode_ci`)
- **Web Server:** Apache 2.4+ (`mod_rewrite`, `mod_headers`, `mod_deflate`)
- **Frontend:** Pre-compiled Tailwind CSS (`assets/css/tailwind.min.css`), custom CSS (`assets/css/custom.css`), vanilla JavaScript (`assets/js/main.js`), FontAwesome 6, Google Fonts

---

## Project Structure

```text
SND-Media-House/
├── .htaccess                  # Apache security headers, Gzip, extensionless routing
├── 404.php                    # Custom 404 error handler
├── README.md
├── REQUIREMENTS_DOCUMENT_PRIME_PHASE1.md
├── TASKS_PRIME_PHASE1.md
├── USER_STORIES_PRIME_PHASE1.md
├── article.php                # Single story view + highlights + related stories
├── index.php                  # Public newsroom homepage
├── legal.php                  # Dynamic privacy, TOS, rental rules & editorial ethics
├── robots.txt                 # Crawler exclusions & sitemap pointer
├── sitemap.php                # Dynamic XML sitemap generator
├── studio-rental.php          # Commercial studio showcase & rate estimator
├── admin/
│   ├── articles.php           # Story composer, 3-bullet highlights & publishing status
│   ├── facilities.php         # Studio A/B & ad inventory rate card manager
│   ├── index.php              # Role-aware operations dashboard
│   ├── layout.php             # Admin shell, sidebar & flash notifications
│   ├── leads.php              # Studio rental CRM, filters, WhatsApp & CSV export
│   ├── login.php              # Staff login
│   ├── logout.php             # Session teardown
│   ├── reset_password.php     # 1-hour token password recovery
│   ├── settings.php           # Global site settings registry & media kit upload
│   ├── ticker.php             # Breaking marquee manager
│   ├── users.php              # Staff RBAC user management (super_admin only)
│   └── youtube.php            # YouTube broadcast bulletins manager
├── api/
│   ├── market_rates.php       # SENSEX / NIFTY feed endpoint & cache updater
│   ├── submit_rental.php      # Studio booking lead intake (CSRF + honeypot + rate limit)
│   ├── toggle_ticker.php      # Toggle ticker active state
│   └── update_lead_status.php # Update rental enquiry CRM pipeline status
├── assets/
│   ├── css/                   # tailwind.min.css, custom.css
│   ├── images/                # snd-logo.svg, snd-banner.svg
│   ├── js/                    # main.js
│   └── uploads/               # ads/, articles/, docs/, facilities/ (ignored except .gitkeep)
├── config/
│   ├── .htaccess              # Deny direct web access to config directory
│   ├── database.php           # PDO connection with env-var overrides
│   └── settings.php           # Canonical 40-key baseline + DB settings loader/cache
├── database/
│   └── schema.sql             # Complete MySQL schema & UTF-8 seed data
├── docs/
│   ├── EDITORIAL_QUICKSTART_GUIDE.md
│   ├── PRODUCTION_DEPLOYMENT_GUIDE.md
│   ├── SPACE_MANAGEMENT_CRM_GUIDE.md
│   └── SUPER_ADMIN_CHECKLIST_AND_SETTINGS_REGISTRY.md
└── includes/
    ├── auth.php               # RBAC session guards
    ├── footer.php             # Global footer & scripts
    ├── functions.php          # Sanitization, CSRF, uploads, slug & YouTube helpers
    ├── header.php             # Utility bar, market ticker, breaking marquee & nav
    ├── rental_modal.php       # Studio booking modal & AJAX submission
    ├── seo.php                # Meta tags & Schema.org JSON-LD
    └── youtube_hub.php        # Dynamic YouTube broadcast hub component
```

---

## Local Setup (XAMPP) & Configuration

1. **Clone into your Apache webroot** (for local XAMPP subdirectory setup, e.g. `htdocs/Websites/SND-Media-House/`):
   ```bash
   git clone https://github.com/vijaykumarjakhar/SND-Media-House.git
   ```
2. **Import the database schema & seed data with UTF-8 (`utf8mb4`):**
   ```bash
   mysql --default-character-set=utf8mb4 -u root -p < database/schema.sql
   ```
   Or from inside the MySQL client:
   ```sql
   SOURCE database/schema.sql;
   ```
3. **Configure database credentials:**
   `config/database.php` reads optional environment variables (`DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, `DB_PASS`) and falls back to standard local XAMPP defaults:
   - Host: `127.0.0.1`
   - Port: `3306`
   - Database: `sndmedia_db`
   - User: `root` (empty password)
4. **Adjust `.htaccess` for production root domain (`sndmedia.in`):**
   `.htaccess` is currently configured for the local subdirectory `/Websites/SND-Media-House/`. When deploying to the root virtual host, update lines 56 and 67–70 in `.htaccess` to `/404.php` and `RewriteBase /` (see `docs/PRODUCTION_DEPLOYMENT_GUIDE.md`).
5. **Upload permissions (Linux/production):**
   Ensure `assets/uploads/` subdirectories (`ads`, `articles`, `docs`, `facilities`) are writable by the web server (`chmod -R 775 assets/uploads`).

---

## Default Seeded Staff Accounts & RBAC Matrix

All 4 seeded staff accounts in `database/schema.sql` share the temporary initial password: **`AdminPassword123!`**

| Username | Email | Role | Access Scope |
|---|---|---|---|
| `admin` | `admin@sndmedia.in` | `super_admin` | Full CMS, Staff RBAC (`users.php`), Global Settings (`settings.php`), Studio CRM & Editorial |
| `editor` | `editor@sndmedia.in` | `editor` | Publish/edit/delete articles, manage breaking ticker & YouTube broadcasts |
| `reporter` | `reporter@sndmedia.in` | `reporter` | Draft/submit own articles for editorial review (`review`), manage YouTube broadcasts |
| `spacemanager` | `space@sndmedia.in` | `space_manager` | Studio facilities/rate cards (`facilities.php`) & commercial rental CRM (`leads.php`) |

> **Security Warning:** Log into `/admin/login` and change all seeded account passwords immediately in `admin/users.php` before deploying to any public or production server.

---

## Routes & API Reference

### Public & Admin Routes
| Route | Target | Description |
|---|---|---|
| `/` | `index.php` | Homepage newsroom, hero lead, continuous feed, YouTube hub & studio showcase |
| `/article/{slug}` | `article.php?slug=...` | Full story view with 3-bullet highlights & related articles |
| `/category/{cat}` | `index.php?cat=...` | Category-filtered homepage route |
| `/studio-rental` | `studio-rental.php` | Studio A/B & billboard rate estimator and technical specifications |
| `/legal/{page}` | `legal.php?page=...` | `privacy-policy`, `terms-of-service`, `rental-rules`, `editorial-ethics` |
| `/sitemap.xml` | `sitemap.php` | Dynamic Google Search Console XML sitemap |
| `/admin/login` | `admin/login.php` | Role-based staff CMS login |

### JSON API Endpoints
| Endpoint | Method | Access | Purpose |
|---|---|---|---|
| `api/market_rates.php` | `GET` | Public | Returns cached or refreshed (`?action=refresh` / `?force=1`) SENSEX & NIFTY quotes |
| `api/submit_rental.php` | `POST` | Public | Validates CSRF + honeypot + IP rate limit (5/hr), stores lead & returns WhatsApp link |
| `api/toggle_ticker.php` | `GET` | `super_admin`, `editor` | Toggles `breaking_ticker.is_active` with CSRF verification |
| `api/update_lead_status.php` | `POST` | `super_admin`, `space_manager` | Updates `rental_enquiries.status` (`new` → `booked` / `archived` / `rejected`) |

---

## Database Schema Summary

`database/schema.sql` provisions `sndmedia_db` with 8 InnoDB tables and initial seed records:

| Table | Seeded Rows | Purpose |
|---|---|---|
| `users` | 4 | RBAC staff accounts (`super_admin`, `editor`, `reporter`, `space_manager`) |
| `articles` | 8 | 7 published stories across all 6 categories (`national`, `business`, `tech`, `energy`, `exclusive`, `world`) + 1 `review` draft |
| `breaking_ticker` | 5 | Live marquee alerts linked to active portal routes |
| `youtube_bulletins` | 5 | Curated `@sndschoolmedia` videos & live streams |
| `studio_facilities` | 3 | `studio_a`, `studio_b`, and `ad_space` inventory & pricing |
| `rental_enquiries` | 3 | Sample CRM pipeline leads (`new`, `quoted`, `booked`) |
| `system_settings` | 41 | Canonical branding, contact, market feed, ad slot & legal policy settings |
| `password_reset_tokens` | 0 | 1-hour cryptographic password reset tokens |

---

## Additional Documentation

- [Production Deployment Guide](docs/PRODUCTION_DEPLOYMENT_GUIDE.md)
- [Editorial Quickstart Guide](docs/EDITORIAL_QUICKSTART_GUIDE.md)
- [Space Management & CRM Guide](docs/SPACE_MANAGEMENT_CRM_GUIDE.md)
- [Super Admin Checklist & Settings Registry](docs/SUPER_ADMIN_CHECKLIST_AND_SETTINGS_REGISTRY.md)
