<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LigneCommande extends Model
{
    protected $fillable = [
        'commande_id',
        'product_id',
        'quantite',
        'prix'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
