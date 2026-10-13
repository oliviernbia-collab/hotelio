<?php

namespace App\Providers;

use App\Observers\ActivityObserver;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        Paginator::defaultView('vendor.pagination.hotelio');

        $models = [
            \App\Models\Hotel::class,
            \App\Models\Room::class,
            \App\Models\Reservation::class,
            \App\Models\Staff::class,
            \App\Models\Client::class,
            \App\Models\Product::class,
            \App\Models\Commande::class,
            \App\Models\User::class,
        ];

        foreach ($models as $model) {
            if (class_exists($model)) {
                $model::observe(ActivityObserver::class);
            }
        }

        // Log connexion
        \Illuminate\Support\Facades\Event::listen(
            \Illuminate\Auth\Events\Login::class,
            function ($event) {
                \App\Models\ActivityLog::record('login',
                    'Connexion de ' . $event->user->name . ' (' . $event->user->role . ')'
                );
            }
        );

        // Log déconnexion
        \Illuminate\Support\Facades\Event::listen(
            \Illuminate\Auth\Events\Logout::class,
            function ($event) {
                \App\Models\ActivityLog::record('logout',
                    'Déconnexion de ' . ($event->user?->name ?? 'Inconnu')
                );
            }
        );
    }
}
