# WadiNada Hostinger Deployment

This app stores customer identity details and Kuwait ID images. Keep dashboard access limited to trusted staff accounts.

## Recommended Hostinger Setup

Use Hostinger hosting with SSH access and MySQL/MariaDB. Hostinger's Laravel deployment guide recommends preparing the server, deploying code, configuring `.env`, running Composer, setting permissions, and running artisan commands.

Official Hostinger guide:
https://www.hostinger.com/tutorials/how-to-deploy-laravel/

## Before Upload

Run locally:

```bash
cd /Users/rahmanzeb/WadiNada
php artisan test
npm run build
```

Do not upload these:

```text
.env
node_modules/
vendor/
storage/logs/*.log
```

Upload these important generated assets:

```text
public/build/
public/.user.ini
```

## Hostinger hPanel Steps

1. Create a MySQL database in hPanel.
2. Create a database user and password.
3. Note the database host, database name, username, and password.
4. Enable SSH access for the hosting account.
5. Set PHP version to a Laravel-compatible version, preferably PHP 8.3 or newer.
6. In PHP Options / MultiPHP INI Editor, set:

```ini
upload_max_filesize=10M
post_max_size=25M
max_file_uploads=20
max_input_vars=5000
max_execution_time=120
max_input_time=120
```

## File Layout

Best layout if Hostinger lets you point the domain document root to `public`:

```text
domains/your-domain.com/WadiNada/
domains/your-domain.com/WadiNada/public/
```

The domain document root should be:

```text
WadiNada/public
```

If Hostinger shared hosting does not let you change the document root, tell me before moving files. Laravel should not be deployed by exposing the project root.

## Server Commands

SSH into Hostinger, then from the project root:

```bash
cp .env.hostinger.example .env
```

Edit `.env` with real Hostinger values:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

DB_CONNECTION=mysql
DB_HOST=your_hostinger_mysql_host
DB_PORT=3306
DB_DATABASE=your_hostinger_database
DB_USERNAME=your_hostinger_database_user
DB_PASSWORD=your_hostinger_database_password
```

Then run:

```bash
composer install --no-dev --optimize-autoloader
php artisan key:generate
php artisan migrate --force
php artisan storage:link
php artisan app:create-staff-user --role=admin
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

The user creation command will ask for name, email, and password if you do not pass them as options.

## Permissions

Make sure these are writable by PHP:

```text
storage/
bootstrap/cache/
```

Typical permissions:

```bash
chmod -R 775 storage bootstrap/cache
```

## Post-Deployment Check

Open:

```text
https://your-domain.com/upload-limits
```

Expected:

```json
{
  "upload_max_filesize": "10M",
  "post_max_size": "25M"
}
```

Then test:

1. `/` redirects to `/login` when logged out.
2. Admin can log in.
3. Dashboard opens after login.
4. Create a batch purchase with multiple product variants.
5. Scan or paste IMEIs/serial numbers and confirm the received units appear in Inventory Lookup.
6. Scan one received unit at checkout and confirm its status changes from available to sold.
