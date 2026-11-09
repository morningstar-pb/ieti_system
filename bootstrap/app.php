<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Storage;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Register custom alias
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            '2fa' => \App\Http\Middleware\Check2FA::class,
            'prevent-back-history' => \App\Http\Middleware\PreventBackHistory::class,
        ]);
    })
    ->withSchedule(function (Schedule $schedule) {
        // Run automated database backup daily at midnight
        $schedule->call(function () {
            $filename = 'auto-db-backup-' . date('Y-m-d_H-i-s') . '.sql';
            $backupPath = storage_path('app/backups/' . $filename);

            $database = config('database.connections.mysql.database');
            $username = config('database.connections.mysql.username');
            $password = config('database.connections.mysql.password');
            $host = config('database.connections.mysql.host');
            $port = config('database.connections.mysql.port');

            $command = sprintf(
                'mysqldump --user=%s --password=%s --host=%s --port=%s %s > "%s"',
                escapeshellarg($username),
                escapeshellarg($password),
                escapeshellarg($host),
                escapeshellarg($port),
                escapeshellarg($database),
                $backupPath
            );

            exec($command);

            // Auto-deletion: Clean up backups older than 30 days
            $files = Storage::disk('local')->files('backups');
            $expirationTime = now()->subDays(30)->timestamp;

            foreach ($files as $file) {
                if (Storage::disk('local')->lastModified($file) < $expirationTime) {
                    Storage::disk('local')->delete($file);
                }
            }
        })->daily();
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();