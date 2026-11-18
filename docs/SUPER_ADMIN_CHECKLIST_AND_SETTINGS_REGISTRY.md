# SND Media — Super Admin Setup Checklist & System Settings Registry
**Target:** System Administrators & Technical Operations  
**Platform:** SND Media (`sndmedia.in`)  
**Database:** `sndmedia_db` (MySQL 8 / MariaDB)  

---

## Part 1: First-Time Super Admin Setup Checklist

- [ ] **Step 1: Secure the Root Super Admin Account**
  1. Log into `https://sndmedia.in/admin/login.php` with initial admin credentials:
     - Username: `admin`
     - Temporary Password: `AdminPassword123!`
  2. Navigate to **User Management** (`admin/users.php`).
  3. Edit user `admin` and update the password to a strong, high-entropy password (min 12 characters with symbols and numbers).
  4. Ensure your administrative email is set accurately to receive system alerts.

- [ ] **Step 2: Configure Global Brand & Contact Registry**
  1. Navigate to **Site Settings** (`admin/settings.php`).
  2. Verify all 8 configuration fieldsets:
     - HQ Physical Campus: `SND Public School, Palwal-Sohna Road, Village Meghpur, Palwal, Haryana — 121102`
     - Phone / WhatsApp: `+91-7206622711`
     - YouTube Link: `https://www.youtube.com/@sndschoolmedia`
     - Email Desks: `newsdesk@sndmedia.in`, `space@sndmedia.in`, `ads@sndmedia.in`

- [ ] **Step 3: Upload the Official Media Kit PDF**
  1. In `admin/settings.php` under Section ⑥, upload the current commercial rate-card PDF.
  2. Test the public download link on the header and studio rental pages.

- [ ] **Step 4: Configure Market Feed Mode**
  1. If internet access is enabled on the server, toggle **Market Auto-Feed** to enabled.
  2. Click **Refresh Quotes** to fetch real-time SENSEX and NIFTY indexes from Yahoo Finance.
  3. If running in an air-gapped or offline environment, toggle **Market Auto-Feed** to disabled and input manual index values (`SENSEX 81,450.20`, `NIFTY 24,820.15`).

- [ ] **Step 5: Verify SSL/TLS & HTTPS Configuration**
  1. Ensure your SSL certificate is installed on Apache/Nginx.
  2. Verify that visits to `http://sndmedia.in` automatically redirect to `https://sndmedia.in`.
  3. Verify that `session.cookie_secure` is automatically enabled over HTTPS.

---

## Part 2: Complete `system_settings` Key Registry

The `system_settings` table acts as the unified configuration layer for `sndmedia.in`. Below is the complete catalog of keys and their impact on the platform:

| Setting Key | Type | Default Value | Public Frontend Effect |
|-------------|------|---------------|------------------------|
| `site_name` | String | `SND Media` | Used across HTML `<title>`, header logo alt text, OpenGraph tags, schema JSON-LD. |
| `site_domain` | String | `sndmedia.in` | Canonical URL root for SEO, social share links, and sitemap generation. |
| `site_tagline` | String | `Where Students Lead Media` | Primary branding slogan displayed in the header subtitle. |
| `site_meta_description` | Text | National news, live broadcasts... | Default meta description for `index.php` and fallback for search engines. |
| `editions_text` | String | `EDITIONS: PALWAL • DELHI NCR • NATIONAL` | Displayed on the top utility bar above the primary navigation. |
| `yt_channel_url` | URL | `https://www.youtube.com/@sndschoolmedia` | Outbound links to YouTube channel on the header, broadcast hub, and footer. |
| `yt_handle` | String | `@sndschoolmedia` | Channel badge label in the top utility bar and broadcast widget. |
| `yt_subscriber_label` | String | `3.36K+ Subscribers • 666+ Videos` | Channel subscriber count pill on the homepage broadcast hub. |
| `hq_address` | String | `SND Public School, Palwal-Sohna Road...` | Rendered in the footer contact block and LocalBusiness schema JSON-LD. |
| `phone_studio` | Phone | `+91-7206622711` | Primary dialer link (`tel:+917206622711`) across header, footer, and modals. |
| `phone_whatsapp` | Phone | `+91-7206622711` | Target number for instant lead routing (`https://wa.me/917206622711`). |
| `email_newsdesk` | Email | `newsdesk@sndmedia.in` | Rendered in footer editorial bureau block and contact links. |
| `email_space` | Email | `space@sndmedia.in` | Destination for studio rental enquiries and commercial leads. |
| `email_ads` | Email | `ads@sndmedia.in` | Displayed for corporate advertising and sponsorship queries. |
| `studio_rate_display` | String | `₹35,000 / Day` | Headline commercial rate rendered on homepage banner and studio page. |
| `media_kit_pdf_url` | URL/Path | `assets/uploads/docs/snd_media_kit.pdf` | Linked from "Download Media Kit" button in header utility bar and footer. |
| `sensex_value` | String | `81,450.20` | Live SENSEX index price displayed on the ticker bar. |
| `sensex_change` | String | `+342.15 (+0.42%)` | SENSEX price change and percentage. |
| `sensex_direction` | Enum | `up` | Controls green (up) or red (down) arrow and text styling. |
| `nifty_value` | String | `24,820.15` | Live NIFTY 50 index price displayed on the ticker bar. |
| `nifty_change` | String | `+102.40 (+0.41%)` | NIFTY price change and percentage. |
| `nifty_direction` | Enum | `up` | Controls green (up) or red (down) arrow and text styling. |
| `market_auto_feed` | Boolean | `1` | Enables/disables automated Yahoo Finance background polling. |
| `market_last_fetched`| Timestamp | `2026-11-15 14:00:00` | Records last fetch time for 15-minute cache validation. |
| `ad_leaderboard_image`| URL/Path | `assets/img/ad_sample_leaderboard.webp` | Top header commercial ad banner (728×90 / responsive). |
| `ad_leaderboard_url` | URL | `https://sndmedia.in/studio-rental.php` | Click destination for top leaderboard ad. |
| `ad_leaderboard_active`| Boolean | `1` | Controls whether top ad banner renders or hides. |
| `policy_privacy` | Rich HTML | `<p>...</p>` | Rendered at `/legal/privacy-policy` route. |
| `policy_tos` | Rich HTML | `<p>...</p>` | Rendered at `/legal/terms-of-service` route. |
| `policy_rental_rules` | Rich HTML | `<p>...</p>` | Rendered at `/legal/rental-rules` route. |
| `policy_editorial_ethics`| Rich HTML | `<p>...</p>` | Rendered at `/legal/editorial-ethics` route. |
