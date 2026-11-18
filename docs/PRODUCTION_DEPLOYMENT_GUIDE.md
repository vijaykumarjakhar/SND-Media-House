# SND Media — Production Deployment & Launch Guide
**Domain:** `https://sndmedia.in`  
**Application Stack:** PHP 8.1+, MySQL 8.0+ / MariaDB 10.4+, Apache with `mod_rewrite` & `mod_headers`  
**Database Name:** `sndmedia_db`  

---

## 1. Production Server Prerequisites
- PHP 8.1 or higher with extensions: `pdo_mysql`, `curl`, `json`, `mbstring`, `fileinfo`, `gd`.
- Apache 2.4+ with modules enabled:
  ```bash
  a2enmod rewrite
  a2enmod headers
  a2enmod expires
  systemctl restart apache2
  ```
- SSL/TLS certificate installed (Let's Encrypt / Certbot or commercial SSL).
- MySQL 8.0 or MariaDB 10.4 service running.

---

## 2. Code Deployment via Git / SFTP
1. Clone or upload the repository to the production webroot (e.g. `/var/www/html/` or `/home/user/public_html/`):
   ```bash
   cd /var/www/html
   git clone <repo_url> .
   ```
2. Set appropriate file permissions:
   ```bash
   chown -R www-data:www-data /var/www/html
   chmod -R 755 /var/www/html
   chmod -R 775 /var/www/html/assets/uploads
   ```

---

## 3. Production Database Setup
1. Log into production MySQL:
   ```bash
   mysql -u root -p
   ```
2. Create database and production service user:
   ```sql
   CREATE DATABASE sndmedia_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   CREATE USER 'snd_user'@'localhost' IDENTIFIED BY 'StrongProductionPasswordHere!';
   GRANT ALL PRIVILEGES ON sndmedia_db.* TO 'snd_user'@'localhost';
   FLUSH PRIVILEGES;
   EXIT;
   ```
3. Import the canonical schema and seed data:
   ```bash
   mysql -u snd_user -p sndmedia_db < database/schema.sql
   ```

---

## 4. Production Environment Configuration
1. Edit `config/database.php` (or configure system environment variables):
   ```php
   $host = getenv('DB_HOST') ?: 'localhost';
   $port = getenv('DB_PORT') ?: '3306';
   $dbname = getenv('DB_NAME') ?: 'sndmedia_db';
   $user = getenv('DB_USER') ?: 'snd_user';
   $pass = getenv('DB_PASS') ?: 'StrongProductionPasswordHere!';
   ```
2. Ensure `php.ini` production flags:
   ```ini
   display_errors = Off
   display_startup_errors = Off
   log_errors = On
   error_log = /var/log/php/error.log
   session.cookie_httponly = 1
   session.cookie_secure = 1
   session.use_only_cookies = 1
   ```

---

## 5. Web Server Configuration (`.htaccess`)
Ensure `.htaccess` RewriteBase matches the root `/` on the production virtual host:
```apache
RewriteEngine On
RewriteBase /
```
When moving from subdirectory `/Websites/SND-Media-House/` to root domain `sndmedia.in`, change:
```apache
ErrorDocument 404 /404.php
RewriteBase /
```

---

## 6. Post-Deployment Verification Checklist
1. Visit `https://sndmedia.in/` — Verify HTTPS green padlock, zero console errors, fast initial render.
2. Verify ticker marquee animation and market quotes bar.
3. Test category filters (`National`, `Business`, `Tech`, etc.) on `#news-desk`.
4. Test human 3-bullet Highlights drawer on the Hero story and single article view (`article.php?slug=...`).
5. Open Studio Rental page (`studio-rental.php`) and submit a test enquiry via the booking modal.
6. Verify CRM entry in `https://sndmedia.in/admin/leads.php`.
7. Verify sitemap accessibility at `https://sndmedia.in/sitemap.xml`.
8. Submit `https://sndmedia.in/sitemap.xml` to Google Search Console.
