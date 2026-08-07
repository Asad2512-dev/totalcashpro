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

Sign in at `/login` — your role opens the correct panel after authentication.

## Demo credentials

> **Security:** These accounts are created by `php artisan db:seed`. Change all passwords before using on a public/production server.

### Super Admin

| Email | Password | Panel |
|-------|----------|-------|
| `admin@totalcashpro.com` | `admin123` | `/super-admin` |

### Harbour Kitchen Group (primary demo business)

**Business Admin**

| Name | Email | Password | Panel |
|------|-------|----------|-------|
| Ava Morgan | `ava@harbourkitchen.test` | `password` | `/business-admin` |

**Staff (web login)** — all use password `password`, panel `/staff`

| Name | Email | PIN (kiosk) | Branch |
|------|-------|-------------|--------|
| Jamie Cole | `staff.harbour-kitchen-group@totalcashpro.test` | `1000` | Dockside |
| Priya Shah | `priya@harbourkitchen.test` | `1001` | Dockside |
| Marcus Lee | `marcus@harbourkitchen.test` | `1002` | Dockside |
| Sofia Reed | `sofia@harbourkitchen.test` | `1122` | Harbour Central |
| Noah Blake | `noah@harbourkitchen.test` | `1234` | Harbour Central |

**Smart Kiosk** — create/open a kiosk under **People → Smart Kiosks** in Business Admin. Staff clock in/out with their **4-digit PIN** only. Start/close the kiosk session with the business admin email and password above.

### Other demo business admins

All use password `password` and open `/business-admin`.

| Organisation | Admin name | Email |
|--------------|--------------|-------|
| Oak Street Bakery | Tom Reed | `tom@oakstreet.test` |
| Northbridge Retail | Sara Khan | `sara@northbridge.test` |
| Riverbend Cafe | James Cole | `james@riverbend.test` |
| Cedar Hospitality | Mia Chen | `mia@cedar.test` |
| Summit Pantry | Noah Price | `noah@summitpantry.test` |
| Greenfield Markets | Ellie Brooks | `ellie@greenfield.test` |
| Lakeside Deli | Chris Patel | `chris@lakeside.test` |

### Other demo staff logins

One generic staff account per organisation (password `password`, panel `/staff`):

| Organisation | Email |
|--------------|-------|
| Harbour Kitchen Group | `staff.harbour-kitchen-group@totalcashpro.test` |
| Oak Street Bakery | `staff.oak-street-bakery@totalcashpro.test` |
| Northbridge Retail | `staff.northbridge-retail@totalcashpro.test` |
| Riverbend Cafe | `staff.riverbend-cafe@totalcashpro.test` |
| Cedar Hospitality | `staff.cedar-hospitality@totalcashpro.test` |
| Summit Pantry | `staff.summit-pantry@totalcashpro.test` |
| Greenfield Markets | `staff.greenfield-markets@totalcashpro.test` |
| Lakeside Deli | `staff.lakeside-deli@totalcashpro.test` |

### Seed on server

```bash
php artisan migrate --force
php artisan db:seed --force
```

See [DEPLOYMENT.md](DEPLOYMENT.md) for production notes. On live, prefer seeding only required classes instead of full demo data.

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
