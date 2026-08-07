# Backup Guide

## What to back up

| Asset | Method |
|-------|--------|
| Database | Daily mysqldump |
| `storage/app` | File sync (uploads, receipts) |
| `.env` | Secure secrets store (not in git) |

## Database backup

```bash
mysqldump -u root -p totalcashpro > backup-$(date +%F).sql
```

Automate with cron:

```
0 2 * * * mysqldump -u backup_user -p'***' totalcashpro | gzip > /backups/totalcashpro-$(date +\%F).sql.gz
```

## Restore

```bash
mysql -u root -p totalcashpro < backup-2026-08-07.sql
```

## File storage

```bash
tar -czf storage-app-$(date +%F).tar.gz storage/app
```

## Retention

- Daily backups: keep 7 days
- Weekly backups: keep 4 weeks
- Monthly backups: keep 12 months

## Pre-migration backup

Always backup before `php artisan migrate --force` in production.

## Testing restores

Quarterly: restore to a staging server and verify login, reports, and finance data.
