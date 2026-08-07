# TotalCashPro — Email Setup (Hostinger SMTP)

Configure transactional email using **SMTP only**. Credentials must live in `.env` — never in source code.

## Required `.env` variables

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.hostinger.com
MAIL_PORT=587
MAIL_USERNAME=hello@yourdomain.com
MAIL_PASSWORD=your_email_account_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=hello@yourdomain.com
MAIL_FROM_NAME="TotalCashPro"
```

| Variable | Hostinger value | Notes |
|----------|-----------------|-------|
| `MAIL_MAILER` | `smtp` | Use SMTP transport in production |
| `MAIL_HOST` | `smtp.hostinger.com` | Hostinger SMTP server |
| `MAIL_PORT` | `587` | TLS (recommended). SSL alternative: `465` with `MAIL_ENCRYPTION=ssl` |
| `MAIL_USERNAME` | Full email address | e.g. `hello@yourdomain.com` |
| `MAIL_PASSWORD` | Mailbox password | From hPanel → Email → Manage → Password |
| `MAIL_ENCRYPTION` | `tls` | Use `ssl` only if port is `465` |
| `MAIL_FROM_ADDRESS` | Same as mailbox | Must match an existing Hostinger mailbox |
| `MAIL_FROM_NAME` | `TotalCashPro` | Display name recipients see |

## Hostinger steps

1. Log in to **hPanel** → **Emails** → create or select a mailbox (e.g. `hello@yourdomain.com`).
2. Note the **SMTP** settings (host `smtp.hostinger.com`, port `587`, encryption TLS).
3. Copy the mailbox password into `MAIL_PASSWORD` in `.env` on your server.
4. Set `MAIL_FROM_ADDRESS` to that mailbox address.
5. Run `php artisan config:clear` after changing `.env`.

## Verify sending

```bash
php artisan mail:send-test you@example.com
```

Uses your configured `MAIL_MAILER` (typically `smtp` from `.env`). No Mailtrap API key is required when using SMTP.

```php
Mail::raw('Test from TotalCashPro', fn ($m) => $m->to('you@example.com')->subject('SMTP test'));
```

Check the recipient inbox and Hostinger email logs if delivery fails.

## Local development

- **`MAIL_MAILER=log`** — writes emails to `storage/logs/laravel.log` (no SMTP needed).
- **`MAIL_MAILER=array`** — captures messages in memory (used by PHPUnit).
- **`MAIL_MAILER=mailtrap`** — optional Mailtrap SDK if you keep `MAILTRAP_API_KEY` set.

## Queue recommendation (production)

Set `QUEUE_CONNECTION=database` (or `redis`) and run:

```bash
php artisan queue:work
```

Password resets, OTP codes, and notifications implement `ShouldQueue` and send via the queue when not using `sync`.

## Security

- Do **not** commit `.env` or real passwords.
- Rotate mailbox passwords if exposed.
- Use SPF/DKIM in Hostinger DNS for deliverability.
