# TotalCashPro

Complete Restaurant & Retail Operations Platform.

**Domain:** [https://totalcashpro.com](https://totalcashpro.com)

## Phase 1

This repository currently ships:

- A production-minded Laravel application foundation
- Clean architecture folders (Actions, DTOs, Repositories, Services, View Components, and more)
- Repository pattern base contracts and Eloquent implementation
- Service-layer marketing content
- A premium responsive marketing website
- Reusable Blade component library
- SEO essentials (`robots.txt`, sitemap placeholder, Open Graph, schema-ready layout)

Pricing model: **monthly SaaS subscriptions** — Basic **£19.99/month** and Professional **£29.99/month**. Access is requested via form; accounts are created manually after review.

Authentication, SaaS modules, payment checkout, APIs, and domain database tables are intentionally deferred to later phases.

## Stack

- Laravel 13 / PHP 8.4+
- MySQL via MAMP
- Vite
- Blade
- Tailwind CSS 4
- Alpine.js

## Local setup (MAMP)

1. Start **Apache** and **MySQL** in MAMP.
2. Set MAMP PHP to **8.4.1** (Preferences → PHP). Laravel 13 requires PHP 8.4+.
3. Confirm MySQL is on port **8889** (MAMP default).
4. Configure the app:

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
npm install
npm run build
```

Default MAMP database settings in `.env`:

```env
APP_URL=http://localhost:8888/totalcashpro/public
ASSET_URL=http://localhost:8888/totalcashpro/public
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=8889
DB_DATABASE=totalcashpro
DB_USERNAME=root
DB_PASSWORD=root
DB_SOCKET=/Applications/MAMP/tmp/mysql/mysql.sock
```

Open: [http://localhost:8888/totalcashpro/public](http://localhost:8888/totalcashpro/public)

For Vite hot reload while developing assets:

```bash
npm run dev
```

## Architecture notes

| Area | Location |
| --- | --- |
| Repository contracts | `app/Repositories/Contracts` |
| Eloquent repositories | `app/Repositories/Eloquent` |
| Application services | `app/Services` |
| Form requests | `app/Http/Requests` |
| Blade/view components | `app/View/Components` + `resources/views/components` |
| Marketing controllers | `app/Http/Controllers/Marketing` |
| Brand/SEO config | `config/totalcashpro.php` |

Bind new repository interfaces in `app/Providers/RepositoryServiceProvider.php`.

## Marketing routes

| Path | Name |
| --- | --- |
| `/` | `home` |
| `/about` | `about` |
| `/contact` | `contact` |
| `/privacy` | `privacy` |
| `/terms` | `terms` |
| `/login` | `login` (placeholder redirect) |
| `/get-started` | `register` (placeholder redirect) |
# totalcashpro
