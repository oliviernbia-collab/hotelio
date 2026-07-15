<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password',
        'role', 'status', 'hotels',
        'permissions', 'staff_id',
        'two_fa_secret', 'two_fa_enabled',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
        'permissions'       => 'array',
    ];

    public function staff()
    {
        // Certains comptes (créés depuis l'app mobile) n'ont pas `users.staff_id`
        // renseigné — on résout donc via `staff.user_id`, toujours fiable des deux côtés.
        return $this->hasOne(Staff::class, 'user_id');
    }

    public function favoriteHotels()
    {
        return $this->belongsToMany(Hotel::class, 'favorites')->withPivot('created_at');
    }

    /** IDs des hôtels auxquels cet utilisateur a accès */
    public function getHotelIds(): \Illuminate\Support\Collection
    {
        if ($this->role === 'admin') {
            return \App\Models\Hotel::pluck('id');
        }
        if ($this->role === 'prestataire') {
            return \App\Models\Hotel::where('user_id', $this->id)->pluck('id');
        }
        if ($this->role === 'employe' && $this->staff) {
            return collect([$this->staff->hotel_id])->filter();
        }
        return collect();
    }

    /** Hôtel unique de l'employé (null si pas employe) */
    public function getEmployeeHotel(): ?\App\Models\Hotel
    {
        if ($this->role !== 'employe') return null;
        return $this->staff?->hotel;
    }

    /** Vérifie si l'utilisateur a un droit spécifique */
    public function hasPermission(string $perm): bool
    {
        if (in_array($this->role, ['admin', 'prestataire'])) {
            return true; // Accès total
        }
        return in_array($perm, $this->permissions ?? []);
    }

    /** Liste des permissions disponibles avec leurs libellés */
    public static function availablePermissions(): array
    {
        return [
            'reservations' => ['label' => 'Réservations',          'icon' => 'fa-calendar-check',    'color' => '#8b5cf6'],
            'rooms'        => ['label' => 'Chambres',               'icon' => 'fa-bed',                'color' => '#06b6d4'],
            'clients'      => ['label' => 'Clients',                'icon' => 'fa-users',              'color' => '#10b981'],
            'staff'        => ['label' => 'Personnel',              'icon' => 'fa-user-tie',           'color' => '#f59e0b'],
            'calendar'     => ['label' => 'Calendrier',             'icon' => 'fa-calendar-days',      'color' => '#3b82f6'],
            'menu'         => ['label' => 'Menu & Commandes',       'icon' => 'fa-utensils',           'color' => '#ef4444'],
            'pools'        => ['label' => 'Piscines',               'icon' => 'fa-water-ladder',       'color' => '#0ea5e9'],
            'expenses'     => ['label' => 'Charges',                'icon' => 'fa-money-bill-wave',    'color' => '#84cc16'],
            'boutique'     => ['label' => 'Boutique',               'icon' => 'fa-store',              'color' => '#f77f00'],
            'reports'      => ['label' => 'Rapports',               'icon' => 'fa-chart-bar',          'color' => '#6366f1'],
            'billing'      => ['label' => 'Facturation',            'icon' => 'fa-file-invoice',       'color' => '#ec4899'],
            'tickets'      => ['label' => 'Tickets',                'icon' => 'fa-ticket',             'color' => '#a78bfa'],
        ];
    }
}
