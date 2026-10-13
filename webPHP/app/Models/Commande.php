<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

    class Commande extends Model
{
    protected $fillable = [
        'user_id', 'total', 'statut', 'mode_paiement', 'transaction_id', 'payment_reference',
        'jeko_reference', 'jeko_payment_request_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function lignes()
    {
        return $this->hasMany(LigneCommande::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

