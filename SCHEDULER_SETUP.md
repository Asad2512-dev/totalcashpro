# Scheduler Setup

Scheduled tasks run via Laravel's scheduler. **One cron entry** is required on the server.

## Cron entry

```
* * * * * cd /path/to/totalcashpro && php artisan schedule:run >> /dev/null 2>&1
```

## Scheduled commands

| Command | Schedule | Purpose |
|---------|----------|---------|
| `finance:generate-recurring-bills` | Daily 06:00 | Auto-create bills from templates |
| `billing:send-trial-reminders` | Daily 08:00 | Trial ending emails |
| `billing:process-expired-subscriptions` | Daily 01:00 | Subscription expiry handling |
| `billing:process-expired-trials` | Daily 02:00 | Trial expiry handling |
| `security:prune` | Weekly | Prune old security logs |

## Verify

```bash
php artisan schedule:list
php artisan schedule:run -v
```

## Manual run (testing)

```bash
php artisan finance:generate-recurring-bills
php artisan billing:send-trial-reminders
```
