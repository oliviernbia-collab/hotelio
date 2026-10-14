<?php

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withSchedule(function (Schedule $schedule): void {
        // Relance automatiquement les virements Jèko en échec (réservations, achats, remboursements),
        // sans intervention d'un admin — nécessite `php artisan schedule:run` dans le cron système.
        $schedule->command('payouts:retry-failed')->everyThirtyMinutes()->withoutOverlapping();

        // Libère les reversements prestataires une fois la fenêtre d'annulation dépassée
        // (filet de sécurité pour les check-in jamais pointés dans l'app).
        $schedule->command('payouts:release-due')->hourly()->withoutOverlapping();
    })
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role'  => \App\Http\Middleware\RoleMiddleware::class,
            '2fa'   => \App\Http\Middleware\TwoFAMiddleware::class,
        ]);
        $middleware->web(append: [
            \App\Http\Middleware\SetLocale::class,
            \App\Http\Middleware\TwoFAMiddleware::class,
            \App\Http\Middleware\SecurityHeaders::class,
        ]);
        $middleware->api(append: [
            \App\Http\Middleware\SecurityHeaders::class,
        ]);
        $middleware->statefulApi();
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
