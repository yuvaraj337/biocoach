# Jerin’s Coaching — cPanel Shared Hosting Deployment Guide

**Institution:** Jerin’s Coaching  
**Founder:** Dr. Tanjina Jerin  
**Tagline:** Learn Without Fear  
**Application Stack:** Laravel 11 / PHP 8.2+ / MySQL / Tailwind CSS (Pre-compiled with Vite) / Alpine.js  

This guide provides step-by-step instructions for deploying the **Jerin’s Coaching** platform to a standard cPanel shared hosting environment with zero assumptions of root access, SSH, or Node.js on the production server.

---

## 1. Prerequisites & Hosting Environment

### A. PHP Version & Required Extensions
In cPanel, open **Select PHP Version** (or **MultiPHP Manager**):
- **PHP Version:** Select **PHP 8.2** or **PHP 8.3**.
- **Extensions:** Ensure the following extensions are enabled:
  - `bcmath`
  - `ctype`
  - `curl`
  - `dom` / `xml`
  - `fileinfo` (Required for secure payment proof & assignment PDF uploads)
  - `gd` or `imagick` (Required for course thumbnails & avatars)
  - `json`
  - `mbstring` (Critical for proper Bangla Unicode character processing)
  - `openssl`
  - `pdo_mysql`
  - `tokenizer`
  - `zip`

### B. PHP Configuration Settings (`php.ini` or cPanel PHP Options)
Adjust the following limits in cPanel under **PHP INI Editor**:
```ini
upload_max_filesize = 32M
post_max_size = 40M
memory_limit = 256M
max_execution_time = 300
max_input_time = 300
```

---

## 2. Directory Structure & Document Root Placement

To prevent exposing `.env`, credentials, vendor dependencies, and private uploads to the public web, **never** put the entire Laravel application inside `public_html`.

### Recommended Folder Layout
```text
/home/username/
├── jerins_coaching_app/            <-- [Private Laravel Root: Above web root]
│   ├── app/
│   ├── bootstrap/
│   ├── config/
│   ├── database/
│   ├── lang/
│   ├── routes/
│   ├── storage/
│   ├── vendor/
│   └── .env                       <-- Completely inaccessible from browser
│
└── public_html/                    <-- [Public Web Root]
    ├── build/                      <-- Pre-compiled CSS & JS from Vite
    ├── images/                     <-- Logo, hero images, branding
    ├── uploads/                    <-- Public uploads (if symlinked)
    ├── .htaccess                   <-- Routing & security headers
    └── index.php                   <-- Modified entry point
```

### Modifying `public_html/index.php`
Edit `/home/username/public_html/index.php` so its path pointers reference the private application directory:

```php
<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../jerins_coaching_app/storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../jerins_coaching_app/vendor/autoload.php';

// Bootstrap Laravel and handle the request...
(require_once __DIR__.'/../jerins_coaching_app/bootstrap/app.php')
    ->handleRequest(Request::capture());
```

---

## 3. Local Preparation Before Uploading

Shared hosting typically lacks Node.js or high memory limits. Always prepare and compile assets on your local workstation before uploading.

### Step 1: Pre-compile Frontend Assets
Run the local Vite build:
```bash
npm install
npm run build
```
This produces optimized production assets in `public/build/` (manifest, CSS, and JS).

### Step 2: Install Production Composer Dependencies
```bash
composer install --no-dev --optimize-autoloader
```

### Step 3: Package the Files
Create a ZIP archive of `jerins_coaching_app/`:
- **Include:** `app`, `bootstrap`, `config`, `database`, `lang`, `resources`, `routes`, `storage`, `vendor`, `composer.json`, `artisan`.
- **Exclude:** `node_modules`, `.git`, `.github`, `tests`, local SQLite files (`database/database.sqlite`).

---

## 4. Database Setup & Configuration

1. In cPanel, navigate to **MySQL® Database Wizard**.
2. **Create Database:** e.g., `username_jerincoach`.
3. **Create Database User:** e.g., `username_jcdbuser` with a strong 24+ character password.
4. **Grant All Privileges:** Assign all privileges on the database to the created user.
5. **Collation:** Ensure `utf8mb4_unicode_ci` is selected for complete Bangla Unicode text fidelity.

---

## 5. Production Environment Configuration (`.env`)

In `/home/username/jerins_coaching_app/`, create or update `.env`:

```ini
APP_NAME="Jerin's Coaching"
APP_ENV=production
APP_KEY=base64:YOUR_GENERATED_APP_KEY_HERE
APP_DEBUG=false
APP_URL=https://jerinscoaching.com
APP_TIMEZONE=Asia/Dhaka
APP_LOCALE=bn
APP_FALLBACK_LOCALE=en

LOG_CHANNEL=daily
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=username_jerincoach
DB_USERNAME=username_jcdbuser
DB_PASSWORD="your_strong_mysql_password"

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

# ----------------------------------------------------------------
# Bangladesh Payment Gateway Configuration (SSLCommerz)
# ----------------------------------------------------------------
SSLCOMMERZ_SANDBOX=false
SSLCOMMERZ_STORE_ID=your_registered_sslcommerz_store_id
SSLCOMMERZ_STORE_PASSWORD=your_registered_sslcommerz_store_pass
SSLCOMMERZ_CURRENCY=BDT

# ----------------------------------------------------------------
# Email & SMTP Settings (Bangladesh Localized Notifications)
# ----------------------------------------------------------------
MAIL_MAILER=smtp
MAIL_HOST=mail.jerinscoaching.com
MAIL_PORT=465
MAIL_USERNAME=info@jerinscoaching.com
MAIL_PASSWORD="your_email_password"
MAIL_ENCRYPTION=ssl
MAIL_FROM_ADDRESS="info@jerinscoaching.com"
MAIL_FROM_NAME="Jerin's Coaching"
```

---

## 6. Permissions, Symlinks, & Migrations

### A. Directory Permissions
Ensure write permissions for Laravel logs, framework caches, and file uploads:
```bash
chmod -R 775 /home/username/jerins_coaching_app/storage
chmod -R 775 /home/username/jerins_coaching_app/bootstrap/cache
```

### B. Storage Link
If cPanel Terminal is available:
```bash
cd /home/username/jerins_coaching_app
php artisan storage:link
```
*If Terminal is disabled by your host, create a temporary route in `routes/web.php` or execute via a one-off cPanel Cron Job:*
```bash
/usr/local/bin/php /home/username/jerins_coaching_app/artisan storage:link
```

### C. Run Database Migrations & Seeds
In cPanel Terminal:
```bash
cd /home/username/jerins_coaching_app
php artisan migrate --force
php artisan db:seed --force
```

### D. Optimize Configuration & Routes
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## 7. HTTPS & SSL Certificate Configuration

1. In cPanel, navigate to **SSL/TLS Status**.
2. Select your domain (`jerinscoaching.com` and `www.jerinscoaching.com`) and click **Run AutoSSL** to generate a free Let's Encrypt / cPanel SSL certificate.
3. Verify that your `/home/username/public_html/.htaccess` enforces HTTPS:

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    # Force HTTPS
    RewriteCond %{HTTPS} off
    RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

    # Handle Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
```

---

## 8. cPanel Cron Job (Scheduled Tasks)

The system manages upcoming live class notifications, voucher expirations, and cache cleanups via Laravel Scheduler.

1. In cPanel, open **Cron Jobs**.
2. Set Schedule: **Once Per Minute** (`* * * * *`).
3. Set Command:
```bash
/usr/local/bin/php /home/username/jerins_coaching_app/artisan schedule:run >> /dev/null 2>&1
```
*(Note: Replace `/usr/local/bin/php` with your specific PHP 8.2+ path shown in cPanel).*

---

## 9. Payment Gateway Setup (SSLCommerz & Mobile Banking)

### A. SSLCommerz Production Transition
1. Register a merchant account at [SSLCommerz](https://sslcommerz.com/).
2. Submit your Trade License, TIN, and business verification documents to SSLCommerz.
3. Upon approval, obtain your **Store ID** and **Store Password**.
4. In `.env`:
   - Set `SSLCOMMERZ_SANDBOX=false`.
   - Add your `SSLCOMMERZ_STORE_ID` and `SSLCOMMERZ_STORE_PASSWORD`.
5. Register the IPN callback URL in your SSLCommerz Merchant Dashboard:
   - `https://yourdomain.com/payments/sslcommerz/ipn`

### B. bKash, Nagad, & Rocket Manual Payment Configuration
1. Log in to the Admin Panel as Super Admin (`/admin`).
2. Navigate to **Payment Settings** (`/admin/payment-settings`).
3. Enter the official Merchant or Personal numbers and step-by-step instructions for:
   - **bKash Merchant / Personal:** Dial `*247#` or use bKash App -> Send Money / Payment.
   - **Nagad Merchant / Personal:** Dial `*167#` or use Nagad App.
   - **Rocket / Bank Transfer:** Enter Account Name, Number, Branch, and Routing details.
4. Save settings. When students check out, these exact details and copy buttons will be displayed in BDT (`৳`).

---

## 10. Backup & Disaster Recovery

1. **Database Backups:**
   - In cPanel, click **Backup Wizard** -> **Backup** -> **MySQL Databases** to download a `.sql.gz` dump.
   - Alternatively, schedule an automated weekly database backup via cron:
     ```bash
     mysqldump -u username_jcdbuser -p'your_password' username_jerincoach > /home/username/backups/db_$(date +\%F).sql
     ```
2. **File Backups:** Download regular ZIP backups of `/home/username/jerins_coaching_app/storage/app/` containing student assignment submissions and payment proofs.
3. **Troubleshooting Logs:**
   - Application errors: `/home/username/jerins_coaching_app/storage/logs/laravel.log`
   - Web server errors: cPanel -> **Errors** (under Metrics).
