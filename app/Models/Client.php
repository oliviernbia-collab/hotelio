<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'code',
        'first_name',
        'last_name',
        'email',
        'phone',
        'city',
        'quartier',
        'total_stays',
        'total_spend',
        'points_fidelite',
        'status',
        'note',
        'nationality',
        'last_visit',
    ];

    protected $casts = [
        'total_stays' => 'integer',
        'total_spend' => 'integer',
        'points_fidelite' => 'integer',
        'last_visit' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }
}
