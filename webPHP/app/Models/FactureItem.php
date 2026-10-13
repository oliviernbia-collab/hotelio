<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FactureItem extends Model
{
    public function items()
        {
            return $this->hasMany(FactureItem::class);
        }

        public function facture()
    {
        return $this->belongsTo(Facturation::class, 'facturation_id');
    }
    
}
