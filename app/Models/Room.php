<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    // Cycle de vie conforme au mémoire (diagramme d'états-transitions)
    const STATUS_LIBRE        = 'disponible';
    const STATUS_RESERVEE     = 'réservée';
    const STATUS_OCCUPEE      = 'occupée';
    const STATUS_NETTOYAGE    = 'en_nettoyage';
    const STATUS_HORS_SERVICE = 'hors_service';

    const STATUSES = [
        self::STATUS_LIBRE        => 'Libre / Disponible',
        self::STATUS_RESERVEE     => 'Réservée',
        self::STATUS_OCCUPEE      => 'Occupée',
        self::STATUS_NETTOYAGE    => 'En nettoyage',
        self::STATUS_HORS_SERVICE => 'Hors service',
    ];

    protected $fillable = [
        'hotel_id',
        'number',
        'floor',
        'type',
        'status',
        'price',
        'capacity',
        'superficie_m2',
        'description',
    ];

    protected $casts = [
        'price' => 'integer',
        'capacity' => 'integer',
    ];

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }
}
