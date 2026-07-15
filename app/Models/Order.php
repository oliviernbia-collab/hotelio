<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'hotel_id',
        'client_id',
        'menu_item_id',
        'quantity',
        'total',
        'status',
        'notes',
        'ordered_at',
        'delivered_at',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'total' => 'integer',
        'ordered_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function menuItem()
    {
        return $this->belongsTo(MenuItem::class);
    }
}
