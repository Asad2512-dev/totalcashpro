# Queue Setup

TotalCashPro queues email notifications, webhooks prep, and other async work.

## Configuration

In `.env`:

```
QUEUE_CONNECTION=database
```

Tables: `jobs`, `job_batches`, `failed_jobs` (from default Laravel migrations).

For production at scale, use Redis:

```
QUEUE_CONNECTION=redis
REDIS_HOST=127.0.0.1
```

## Run a worker

Development:

```bash
php artisan queue:work --tries=3 --timeout=90
```

Production (Supervisor example):

```ini
[program:totalcashpro-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/totalcashpro/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/log/totalcashpro-worker.log
```

## After deploy

```bash
php artisan queue:restart
```

## Failed jobs

```bash
php artisan queue:failed
php artisan queue:retry all
```

## What is queued

- Staff invitation emails
- OTP and security notifications
- Leave / shift swap notifications
- Trial and subscription emails
- Recurring bill generation notifications
