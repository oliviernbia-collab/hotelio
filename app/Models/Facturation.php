<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Facturation extends Model
{
    use HasFactory;

    protected $table = 'facturations';

    protected $fillable = [
        'numero',
        'client',
        'prestation',
        'date_facture',
        'date_echeance',
        'montant',
        'montant_paye',
        'statut',
        'mode_paiement',
        'notes',
        'lignes',
    ];

    /**
     * Cast automatiques des types.
     */
    protected $casts = [
        'date_facture'  => 'date',
        'date_echeance' => 'date',
        'montant'       => 'decimal:2',
        'montant_paye'  => 'decimal:2',
        'lignes'        => 'array',   // JSON <-> array PHP automatiquement
    ];

    /**
     * Valeurs par défaut.
     */
    protected $attributes = [
        'montant_paye' => 0,
        'statut'       => 'pending',
        'lignes'       => '[]',
    ];

    /* ── Scopes utiles ── */

    public function scopePayees($query)
    {
        return $query->where('statut', 'paid');
    }

    public function scopeImpayees($query)
    {
        return $query->whereIn('statut', ['pending', 'overdue']);
    }

    public function scopeEnRetard($query)
    {
        return $query->where('statut', 'overdue');
    }

    /* ── Accesseurs ── */

    /**
     * Calcule le solde restant dû.
     */
    public function getSoldeAttribute(): float
    {
        return max(0, (float) $this->montant - (float) $this->montant_paye);
    }

    /**
     * Vérifie si la facture est en retard.
     */
    public function getIsOverdueAttribute(): bool
    {
        return $this->statut === 'pending' && $this->date_echeance?->isPast();
    }
}
