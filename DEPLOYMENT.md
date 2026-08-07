# Deployment Guide

## Pre-deployment checklist

- [ ] `APP_ENV=production`, `APP_DEBUG=false`
- [ ] Valid SSL certificate
- [ ] Database migrated (`php artisan migrate --force`)
- [ ] Assets built (`npm run build`)
- [ ] Queue worker running
- [ ] Scheduler cron configured
- [ ] Mail SMTP tested
- [ ] `/up` health check returns 200
- [ ] Backups configured (see [BACKUP_GUIDE.md](BACKUP_GUIDE.md))

## Server requirements

- PHP 8.3+ with required extensions
- Nginx or Apache with document root → `public/`
- MySQL 8
- Redis (recommended for cache/queue at scale)

## Deploy steps

```bash
git pull origin main
composer install --no-dev --optimize-autoloader
npm ci && npm run build
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
php artisan queue:restart
```

## Web server

Point the vhost to `/public`. Ensure `try_files` routes to `index.php`.

Example Nginx location block:

```nginx
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
```

## Hostinger / shared hosting

See `deploy/hostinger/` for a sample `public_html` index redirect pattern.

## Zero-downtime tips

1. Run migrations before switching traffic
2. Use `php artisan down --refresh=15` during maintenance
3. Restart queue workers after deploy

## Post-deploy verification

```bash
curl -f https://yourdomain.com/up
php artisan schedule:list
```

Smoke-test: login, cash-up, staff clock-in, report export.

## Rollback

1. Restore previous release tag
2. Restore database backup if schema changed
3. `php artisan config:clear && php artisan cache:clear`
