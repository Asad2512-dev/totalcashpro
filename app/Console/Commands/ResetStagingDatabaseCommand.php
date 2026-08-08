<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;

final class ResetStagingDatabaseCommand extends Command
{
    protected $signature = 'db:reset-staging
                            {--force : Skip confirmation prompt}
                            {--no-backup : Skip database backup}
                            {--seed : Run realistic Harbour Kitchen seeder after migrate:fresh}';

    protected $description = 'SAFE STAGING ONLY: backup, migrate:fresh, and seed realistic Harbour Kitchen data';

    public function handle(): int
    {
        $env = (string) config('app.env');
        $connection = (string) config('database.default');
        $driver = (string) config("database.connections.{$connection}.driver");
        $host = (string) config("database.connections.{$connection}.host");
        $database = (string) config("database.connections.{$connection}.database");

        $this->components->twoColumnDetail('APP_ENV', $env);
        $this->components->twoColumnDetail('DB connection', $connection);
        $this->components->twoColumnDetail('DB driver', $driver);
        $this->components->twoColumnDetail('DB host', $host);
        $this->components->twoColumnDetail('DB name', $database);

        if ($env === 'production') {
            $this->components->error('BLOCKED: APP_ENV=production. This command must not run against production.');

            return self::FAILURE;
        }

        if (! in_array($env, ['local', 'staging', 'development', 'testing'], true)) {
            $this->components->warn("Environment '{$env}' is not in the approved list (local/staging/development/testing).");
            if (! $this->option('force') && ! $this->confirm('Continue anyway?')) {
                return self::SUCCESS;
            }
        }

        if (! $this->option('force') && ! $this->confirm('This will DESTROY all data and run migrate:fresh. Continue?')) {
            $this->components->warn('Aborted.');

            return self::SUCCESS;
        }

        $backupPath = null;
        if (! $this->option('no-backup') && $driver === 'mysql') {
            $backupPath = $this->createMysqlBackup($host, $database);
        } elseif (! $this->option('no-backup') && $driver === 'sqlite') {
            $backupPath = $this->createSqliteBackup($database);
        }

        $this->components->info('Running migrate:fresh...');
        $migrate = Artisan::call('migrate:fresh', ['--force' => true]);
        if ($migrate !== 0) {
            $this->components->error('migrate:fresh failed.');

            return self::FAILURE;
        }

        if ($this->option('seed')) {
            $this->components->info('Seeding staging database...');
            Artisan::call('db:seed', [
                '--class' => 'Database\\Seeders\\StagingDatabaseSeeder',
                '--force' => true,
            ]);
            $this->line(Artisan::output());
        }

        $this->components->info('Staging database reset complete.');
        if ($backupPath !== null) {
            $this->components->twoColumnDetail('Backup saved', $backupPath);
        }

        return self::SUCCESS;
    }

    private function createMysqlBackup(string $host, string $database): ?string
    {
        $dir = storage_path('backups');
        File::ensureDirectoryExists($dir);

        $timestamp = now()->format('Y-m-d_His');
        $path = "{$dir}/{$database}_{$timestamp}.sql";

        $user = (string) config('database.connections.mysql.username');
        $password = (string) config('database.connections.mysql.password');
        $port = (string) config('database.connections.mysql.port', '3306');

        $command = sprintf(
            'mysqldump -h%s -P%s -u%s %s %s > %s',
            escapeshellarg($host),
            escapeshellarg($port),
            escapeshellarg($user),
            $password !== '' ? '-p'.escapeshellarg($password) : '',
            escapeshellarg($database),
            escapeshellarg($path),
        );

        $result = Process::run($command);
        if (! $result->successful()) {
            $this->components->warn('Backup skipped: mysqldump failed (is mysqldump installed?).');

            return null;
        }

        return $path;
    }

    private function createSqliteBackup(string $database): ?string
    {
        if (! file_exists($database)) {
            return null;
        }

        $dir = storage_path('backups');
        File::ensureDirectoryExists($dir);
        $timestamp = now()->format('Y-m-d_His');
        $path = "{$dir}/sqlite_{$timestamp}.sqlite";
        copy($database, $path);

        return $path;
    }
}
