<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LigneCommande extends Model
{
    protected $fillable = [
        'commande_id',
        'product_id',
        'quantite',
        'prix',
        'commission_rate',
        'commission_amount',
        'payout_transfer_id',
        'payout_status',
        'payout_failure_reason',
    ];

    protected $casts = [
        'commission_rate'   => 'float',
        'commission_amount' => 'integer',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function commande()
    {
        return $this->belongsTo(Commande::class);
    }
}
