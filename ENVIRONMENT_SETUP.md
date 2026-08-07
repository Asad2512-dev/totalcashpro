# Environment Setup

Copy `.env.example` to `.env` and configure the following.

## Application

| Variable | Description |
|----------|-------------|
| `APP_NAME` | Display name (TotalCashPro) |
| `APP_ENV` | `local`, `staging`, or `production` |
| `APP_DEBUG` | `true` locally; **false** in production |
| `APP_URL` | Full base URL including scheme |

## Database

| Variable | Example |
|----------|---------|
| `DB_CONNECTION` | `mysql` |
| `DB_HOST` | `127.0.0.1` |
| `DB_PORT` | `3306` (MAMP: `8889`) |
| `DB_DATABASE` | `totalcashpro` |
| `DB_USERNAME` | `root` |
| `DB_PASSWORD` | your password |
| `DB_SOCKET` | MAMP socket path if needed |

## Mail (SMTP)

| Variable | Description |
|----------|-------------|
| `MAIL_MAILER` | `smtp` |
| `MAIL_HOST` | e.g. `smtp.hostinger.com` |
| `MAIL_PORT` | `465` (SSL) or `587` (TLS) |
| `MAIL_USERNAME` | SMTP username |
| `MAIL_PASSWORD` | SMTP password |
| `MAIL_ENCRYPTION` | `ssl` or `tls` |
| `MAIL_FROM_ADDRESS` | noreply@yourdomain.com |
| `MAIL_FROM_NAME` | `${APP_NAME}` |

Test: `php artisan mail:send-test you@example.com`

See [EMAIL_SETUP.md](EMAIL_SETUP.md) for Hostinger-specific notes.

## Session & cache

| Variable | Production recommendation |
|----------|---------------------------|
| `SESSION_DRIVER` | `database` or `redis` |
| `CACHE_STORE` | `redis` or `database` |
| `QUEUE_CONNECTION` | `database` or `redis` |

## Support

| Variable | Description |
|----------|-------------|
| `SUPPORT_EMAIL` | Address for contact form notifications |

## Security

- Never commit `.env` to version control
- Use strong `APP_KEY` (generated via `php artisan key:generate`)
- Set `APP_DEBUG=false` in production

## After changes

```bash
php artisan config:clear
php artisan cache:clear
```
