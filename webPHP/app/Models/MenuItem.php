<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'hotel_id',
        'name',
        'item_type',
        'category',
        'price',
        'stock',
        'emoji',
        'description',
        'available',
    ];

    protected $casts = [
        'price' => 'integer',
        'stock' => 'integer',
        'available' => 'boolean',
    ];

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }
}
