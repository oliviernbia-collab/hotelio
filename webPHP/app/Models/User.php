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
        'name', 'email', 'password', 'avatar',
        'role', 'status', 'hotels',
        'permissions', 'staff_id',
        'two_fa_secret', 'two_fa_enabled',
        'payout_method', 'payout_identifier', 'jeko_contact_id',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
        'permissions'       => 'array',
        'payout_identifier' => 'array',
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

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    /** Abonnement le plus récent du prestataire (toutes statuts confondus) */
    public function currentSubscription(): ?Subscription
    {
        return $this->subscriptions()->latest()->first();
    }

    public function payouts()
    {
        return $this->hasMany(Payout::class);
    }

    /**
     * Montant dû au prestataire sur les réservations : somme (total − commission) des
     * réservations payées, non annulées, apportées par la plateforme (commissionnables), sur
     * tous ses hôtels — à l'exclusion de celles déjà reversées automatiquement avec succès
     * (virement Jèko). Ce montant est donc le solde qui reste éventuellement à régler
     * manuellement (fallback), pas le total historique : la plupart des réservations sont
     * normalement déjà soldées par le virement automatique déclenché à l'encaissement
     * (voir DashboardController::triggerOwnerPayout()).
     */
    public function totalOwedFromReservations(): int
    {
        $hotelIds = \App\Models\Hotel::where('user_id', $this->id)->pluck('id');

        return (int) \App\Models\Reservation::whereIn('hotel_id', $hotelIds)
            ->where('paid', true)
            ->where('status', '!=', 'annulée')
            ->whereIn('channel', ['Site web', 'App mobile'])
            ->where(function ($q) {
                $q->whereNull('payout_status')->orWhere('payout_status', '!=', 'success');
            })
            ->selectRaw('COALESCE(SUM(total - COALESCE(commission_amount, 0)), 0) as owed')
            ->value('owed');
    }

    /**
     * Même logique que totalOwedFromReservations() mais pour les achats boutique
     * (lignes de commande des produits dont ce prestataire est propriétaire).
     */
    public function totalOwedFromCommandes(): int
    {
        return (int) \App\Models\LigneCommande::whereHas('product', fn ($q) => $q->where('user_id', $this->id))
            ->whereHas('commande', fn ($q) => $q->where('statut', 'payé'))
            ->where(function ($q) {
                $q->whereNull('payout_status')->orWhere('payout_status', '!=', 'success');
            })
            ->selectRaw('COALESCE(SUM((quantite * prix) - COALESCE(commission_amount, 0)), 0) as owed')
            ->value('owed');
    }

    /** Montant total dû, réservations + achats confondus. */
    public function totalOwed(): int
    {
        return $this->totalOwedFromReservations() + $this->totalOwedFromCommandes();
    }

    /** Solde restant à verser = montant dû − versements déjà enregistrés */
    public function payoutBalance(): int
    {
        return $this->totalOwed() - (int) $this->payouts()->sum('amount');
    }

    /**
     * Un virement automatique Jèko a-t-il échoué pour ce prestataire (réservation ou achat) ?
     * Le versement manuel (Payout) n'est proposé que dans ce cas — sinon tout se règle
     * automatiquement au moment du paiement.
     */
    public function hasFailedAutomaticPayout(): bool
    {
        $hotelIds = \App\Models\Hotel::where('user_id', $this->id)->pluck('id');

        $hasFailedReservation = \App\Models\Reservation::whereIn('hotel_id', $hotelIds)
            ->where('payout_status', 'error')
            ->exists();

        if ($hasFailedReservation) {
            return true;
        }

        return \App\Models\LigneCommande::whereHas('product', fn ($q) => $q->where('user_id', $this->id))
            ->where('payout_status', 'error')
            ->exists();
    }

    public function hasPayoutContact(): bool
    {
        return ! empty($this->jeko_contact_id);
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
            'services'     => ['label' => 'Services additionnels',  'icon' => 'fa-concierge-bell',     'color' => '#14b8a6'],
            'expenses'     => ['label' => 'Charges',                'icon' => 'fa-money-bill-wave',    'color' => '#84cc16'],
            'boutique'     => ['label' => 'Boutique',               'icon' => 'fa-store',              'color' => '#f77f00'],
            'reports'      => ['label' => 'Rapports',               'icon' => 'fa-chart-bar',          'color' => '#6366f1'],
            'billing'      => ['label' => 'Facturation',            'icon' => 'fa-file-invoice',       'color' => '#ec4899'],
            'tickets'      => ['label' => 'Tickets',                'icon' => 'fa-ticket',             'color' => '#a78bfa'],
        ];
    }
}
