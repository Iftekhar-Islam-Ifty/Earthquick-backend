# Earthquick (Nous Telos) — cPanel Production Deployment Guide

This comprehensive, step-by-step operational guide explains how to safely and securely deploy the **Earthquick (Nous Telos)** Laravel 11 e-commerce engine to a standard or shared cPanel hosting environment.

---

> Current application target: Laravel 12. This guide supersedes the historical Laravel 11 wording in its title and introduction.

## 1. Directory Structure & Security Architecture

In shared cPanel hosting, placing the entire Laravel project directly into `public_html` exposes sensitive files like `.env`, database configuration, migrations, and source code. 

To maintain enterprise-grade security, separate the core application files from the publicly accessible web directory:

```text
/home/cpanel_username/
├── earthquick_core/             <-- [NON-PUBLIC] Laravel core source code & .env
│   ├── app/
│   ├── bootstrap/
│   ├── config/
│   ├── database/
│   ├── routes/
│   ├── storage/
│   ├── vendor/
│   ├── .env
│   └── artisan
└── public_html/                 <-- [PUBLIC] Web server root (formerly Laravel /public)
    ├── css/
    ├── js/
    ├── images/
    ├── index.php                <-- Modified entrypoint
    ├── .htaccess                <-- URL rewrite & SSL enforcement
    └── robots.txt
```

---

## 2. Step-by-Step Deployment Workflow

### Step 1: Prepare the Project Archive
On your local machine or build workstation:
1. Ensure all local tests and optimizations pass:
   ```bash
   php artisan test
   ```
2. Clear local cache before packaging:
   ```bash
   php artisan config:clear
   php artisan route:clear
   php artisan view:clear
   php artisan cache:clear
   ```
3. Create a ZIP archive containing all project files **including the `vendor/` directory** if your cPanel plan does not include SSH access. (If SSH is available, you may run `composer install --optimize-autoloader --no-dev` directly on the server).

### Step 2: Upload Files to cPanel
1. Log in to your **cPanel Dashboard** and open **File Manager**.
2. Navigate to your home root directory (`/home/username/`).
3. Create a new directory named `earthquick_core`.
4. Upload your ZIP archive into `/home/username/earthquick_core` and extract it.
5. Move the entire contents of the project's `public/` directory (all CSS, JS, images, `.htaccess`, and `index.php`) into `/home/username/public_html/`.
6. You may safely delete the now-empty `public/` directory inside `earthquick_core`.

### Step 3: Configure `public_html/index.php` Entrypoint
Open `/home/username/public_html/index.php` in the cPanel File Editor and update the relative paths pointing to `autoload.php` and `app.php`:

```php
<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../earthquick_core/storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer Autoloader...
require __DIR__.'/../earthquick_core/vendor/autoload.php';

// Bootstrap Laravel and handle the request...
(require_once __DIR__.'/../earthquick_core/bootstrap/app.php')
    ->handleRequest(Request::capture());
```

---

## 3. MySQL Database Setup and Migration

1. In cPanel, navigate to **MySQL Databases**:
   - Create a database: `username_earthquick`
   - Create a user: `username_eq_user` with a strong password.
   - Add the user to the database and grant **ALL PRIVILEGES**.
2. Before running any command, take and verify a database backup. If SSH is available, run only the reviewed migrations:
   ```bash
    cd /home/username/earthquick_core
    php artisan migrate --force
    ```
3. Do not run development seeders against an existing production catalog. Product/vendor ownership must be reviewed and migrated deliberately; a seeder is not a production backfill tool.
4. If SSH is **not** available:
   - Export your local database `earthquick_db` to an `.sql` file using phpMyAdmin.
   - Open **phpMyAdmin** in cPanel, select `username_earthquick`, and click **Import**.

---

## 4. Production Environment Configuration (`.env`)

Create or edit `/home/username/earthquick_core/.env`:

```ini
APP_NAME=Earthquick
APP_ENV=production
APP_KEY=base64:YOUR_GENERATED_APP_KEY
APP_DEBUG=false
APP_URL=https://yourdomain.com

LOG_CHANNEL=daily
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=username_earthquick
DB_USERNAME=username_eq_user
DB_PASSWORD="YourStrongPasswordHere"

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=true
SESSION_PATH=/
SESSION_DOMAIN=null
SESSION_SECURE_COOKIE=true
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=lax

# Chattogram Shipping Rates Reference
SHIPPING_INSIDE_CTG=80
SHIPPING_OUTSIDE_CTG=150
```

> [!IMPORTANT]
> Always ensure `APP_DEBUG=false` in production. If `APP_DEBUG=true`, visitors encountering an error will see sensitive server environment variables, database passwords, and internal file paths.

---

## 5. Storage Symlink & Directory Permissions

### Storage Symlink
Product images and user uploads stored in `storage/app/public` must be accessible from the web root.

If SSH is available:
```bash
# Create the public storage link after verifying the paths.
php artisan storage:link
```

If SSH is unavailable, use the hosting provider's supported file/symlink tool. Do not create a temporary public route or upload a one-time PHP script for this task.

### Folder Permissions
Laravel requires write access to `storage` and `bootstrap/cache`:
```bash
chmod -R 775 /home/username/earthquick_core/storage
chmod -R 775 /home/username/earthquick_core/bootstrap/cache
```

---

## 6. Production Caching & Performance Tuning

Run these commands in the cPanel Terminal or via SSH to optimize route dispatching and template compilation:

```bash
cd /home/username/earthquick_core

# Cache configuration files into a single optimized file
php artisan config:cache

# Compile route registrations for microsecond routing
php artisan route:cache

# Pre-compile Blade templates into raw PHP views
php artisan view:cache
```

If you make code or `.env` modifications in the future, clear the caches before regenerating them:
```bash
php artisan optimize:clear
php artisan optimize
```

---

## 7. HTTPS & SSL Enforcement (`.htaccess`)

After the domain has a valid SSL certificate, ensure your production `/home/username/public_html/.htaccess` enforces canonical HTTPS and shields dotfiles. Do not add the HTTPS redirect to a local HTTP development environment.

```apache
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Force HTTPS Redirect
    RewriteCond %{HTTPS} off
    RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Handle X-XSRF-Token Header
    RewriteCond %{HTTP:x-xsrf-token} .
    RewriteRule .* - [E=HTTP_X_XSRF_TOKEN:%{HTTP:X-XSRF-Token}]

    # Prevent Direct Access to Hidden / Dot Files
    RewriteRule (^|/)\.(?!well-known) - [F]

    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
```

---

## 8. Scheduled Background Tasks (Cron Job)

If you plan to utilize Laravel's task scheduler (e.g., pruning old abandoned carts or archiving logs):
1. In cPanel, navigate to **Cron Jobs**.
2. Add a new cron job configured to run every minute (`* * * * *`):
   ```text
   cd /home/username/earthquick_core && php artisan schedule:run >> /dev/null 2>&1
   ```

---

## 9. Post-deployment Verification

1. Confirm `https://yourdomain.com/up` responds successfully and does not expose debug information.
2. Check public pages, login, cart, checkout, search and admin access over HTTPS.
3. Verify uploads resolve through `/storage` and no `.env`, log, or source file is web-accessible.
4. Complete real-browser QA on desktop and mobile before accepting orders.
5. Confirm the backup/restore process with a non-production copy before relying on it.

---

## 10. Troubleshooting & Common Scenarios

| Issue | Likely Root Cause | Resolution |
| :--- | :--- | :--- |
| **500 Internal Server Error (Blank Screen)** | File permissions or syntax error in `index.php` paths | Check `/home/username/earthquick_core/storage/logs/laravel.log`. Ensure `storage` is `chmod 775`. Verify paths in `public_html/index.php`. |
| **Missing CSS / JS / Images** | Assets referencing wrong base URL | Confirm `APP_URL=https://yourdomain.com` in `.env`. Ensure static files were placed in `public_html/` and not left in `earthquick_core/public/`. |
| **Database Connection Refused** | Incorrect DB credentials or host | In shared cPanel, `DB_HOST` is almost always `127.0.0.1` or `localhost`. Double check database user prefix (`username_dbname`). |
| **Changes to `.env` Not Reflected** | Configuration is cached | Execute `php artisan config:clear` or delete `bootstrap/cache/config.php`. |
| **403 Forbidden on `/admin`** | User does not have `is_admin = 1` | Check the `users` table via phpMyAdmin and ensure `is_admin` column is set to `1` for your account. |
