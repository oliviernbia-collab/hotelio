<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

    class Commande extends Model
{
    protected $fillable = [
        'user_id', 'total', 'statut', 'mode_paiement', 'transaction_id', 'payment_reference',
        'jeko_reference', 'jeko_payment_request_id',
        'cancelled_at', 'cancellation_fee_amount', 'admin_fee_amount', 'refund_amount',
        'refund_transfer_id', 'refund_status', 'refund_failure_reason',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'cancellation_fee_amount' => 'integer',
        'admin_fee_amount' => 'integer',
        'refund_amount' => 'integer',
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

