# Installation Guide

## Requirements

- PHP 8.3+ with extensions: `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `bcmath`
- Composer 2.x
- Node.js 20+ and npm
- MySQL 8 (or MariaDB 10.6+)

## Steps

1. **Clone and install dependencies**

```bash
composer install
npm install
```

2. **Environment**

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` for database, mail and `APP_URL`. See [ENVIRONMENT_SETUP.md](ENVIRONMENT_SETUP.md).

3. **Database**

```bash
php artisan migrate --seed
```

Seeders create roles, plans, and optional demo data.

4. **Frontend assets**

```bash
npm run build
```

For development: `npm run dev`

5. **Run**

```bash
php artisan serve
```

With MAMP, point the vhost document root to `/public`.

6. **Queue worker** (recommended)

```bash
php artisan queue:work --tries=3
```

See [QUEUE_SETUP.md](QUEUE_SETUP.md).

7. **Scheduler** (production)

Add to crontab:

```
* * * * * cd /path/to/totalcashpro && php artisan schedule:run >> /dev/null 2>&1
```

See [SCHEDULER_SETUP.md](SCHEDULER_SETUP.md).

## Verify installation

```bash
php artisan test
php artisan mail:send-test you@example.com
curl -f http://127.0.0.1:8000/up
```

## Troubleshooting

| Issue | Fix |
|-------|-----|
| 500 on first load | Run `php artisan config:clear` and check `storage/` permissions |
| Vite assets missing | Run `npm run build` |
| Mail fails | Check [EMAIL_SETUP.md](EMAIL_SETUP.md) and `storage/logs/laravel.log` |
| Queue emails not sending | Start `queue:work` |
