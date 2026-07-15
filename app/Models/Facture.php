<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Facture extends Model
{
    protected $fillable = [
        'client',
        'hotel',
        'reservation_id',
        'numero',
        'date_facture',
        'total_ht',
        'tva',
        'taxe_hoteliere',
        'total_ttc',
        'statut',
        'mode_paiement',
        'notes',
    ];

    protected $casts = [
        'total_ht' => 'decimal:2',
        'tva' => 'decimal:2',
        'taxe_hoteliere' => 'decimal:2',
        'total_ttc' => 'decimal:2',
        'date_facture' => 'date',
    ];

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    public function items()
    {
        return $this->hasMany(FactureItem::class);
    }
}
