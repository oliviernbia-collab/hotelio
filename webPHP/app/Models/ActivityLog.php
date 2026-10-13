<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id', 'user_name', 'user_role',
        'action', 'model_type', 'model_id',
        'description', 'ip_address',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function record(string $action, string $description, $model = null): void
    {
        try {
            static::create([
                'user_id'    => Auth::id(),
                'user_name'  => Auth::user()?->name ?? 'Système',
                'user_role'  => Auth::user()?->role ?? null,
                'action'     => $action,
                'model_type' => $model ? class_basename($model) : null,
                'model_id'   => $model?->id ?? null,
                'description'=> $description,
                'ip_address' => request()->ip(),
            ]);
        } catch (\Throwable) {
            // Ne jamais bloquer l'application à cause du log
        }
    }

    public static function actionLabel(string $action): string
    {
        return match($action) {
            'created'  => 'Création',
            'updated'  => 'Modification',
            'deleted'  => 'Suppression',
            'login'    => 'Connexion',
            'logout'   => 'Déconnexion',
            'viewed'   => 'Consultation',
            'paid'     => 'Paiement',
            'reserved' => 'Réservation',
            'ordered'  => 'Commande',
            default    => ucfirst($action),
        };
    }

    public static function actionColor(string $action): string
    {
        return match($action) {
            'created'  => 'var(--ci-green)',
            'updated'  => 'var(--ci-orange)',
            'deleted'  => '#e63946',
            'login'    => '#3b82f6',
            'logout'   => 'var(--text-muted)',
            'paid'     => 'var(--ci-green)',
            'reserved' => '#8b5cf6',
            'ordered'  => '#f59e0b',
            default    => 'var(--text-secondary)',
        };
    }

    public static function actionIcon(string $action): string
    {
        return match($action) {
            'created'  => 'fa-plus-circle',
            'updated'  => 'fa-pen-to-square',
            'deleted'  => 'fa-trash',
            'login'    => 'fa-right-to-bracket',
            'logout'   => 'fa-right-from-bracket',
            'viewed'   => 'fa-eye',
            'paid'     => 'fa-credit-card',
            'reserved' => 'fa-calendar-check',
            'ordered'  => 'fa-bag-shopping',
            default    => 'fa-circle-info',
        };
    }
}
