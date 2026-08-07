# TotalCashPro

TotalCashPro is a multi-tenant SaaS platform for UK restaurants, cafés, takeaways, pubs and retail businesses. It covers cash-up, attendance, payroll, finance, inventory, suppliers, HR, CRM and reporting in one application.

## Panels

| Panel | URL prefix | Users |
|-------|------------|-------|
| Marketing | `/` | Public |
| Super Admin | `/super-admin` | Platform operators |
| Business Admin | `/business-admin` | Organisation owners and managers |
| Staff | `/staff` | Front-line staff (PWA-ready) |

## Tech stack

- Laravel 12, PHP 8.3+
- Blade + Tailwind CSS + Alpine.js
- MySQL 8
- Repository + Service layer architecture
- Queued notifications and scheduled commands

## Quick start

See [INSTALL.md](INSTALL.md) for local setup and [ENVIRONMENT_SETUP.md](ENVIRONMENT_SETUP.md) for `.env` configuration.

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
npm install && npm run build
php artisan serve
```

Default local URL: `http://127.0.0.1:8000`

## Documentation

| Guide | Purpose |
|-------|---------|
| [INSTALL.md](INSTALL.md) | Local installation |
| [ENVIRONMENT_SETUP.md](ENVIRONMENT_SETUP.md) | Environment variables |
| [EMAIL_SETUP.md](EMAIL_SETUP.md) | SMTP / mail configuration |
| [QUEUE_SETUP.md](QUEUE_SETUP.md) | Queue workers |
| [SCHEDULER_SETUP.md](SCHEDULER_SETUP.md) | Cron / scheduler |
| [DEPLOYMENT.md](DEPLOYMENT.md) | Production deployment |
| [BACKUP_GUIDE.md](BACKUP_GUIDE.md) | Backups and recovery |
| [USER_GUIDE.md](USER_GUIDE.md) | End-user overview |
| [BUSINESS_ADMIN_GUIDE.md](BUSINESS_ADMIN_GUIDE.md) | Business Admin panel |
| [STAFF_GUIDE.md](STAFF_GUIDE.md) | Staff panel |
| [SUPER_ADMIN_GUIDE.md](SUPER_ADMIN_GUIDE.md) | Super Admin panel |

## Testing

```bash
php artisan test
```

## Release status

See [TOTALCASHPRO_V1_RELEASE_REPORT.md](TOTALCASHPRO_V1_RELEASE_REPORT.md) for v1.0 completion summary.

## Billing note

Stripe payment processing is intentionally deferred to v1.1. Plan selection and billing architecture are in place; checkout is not yet live.
