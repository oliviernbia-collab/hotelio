<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HotelService extends Model
{
    use HasFactory;

    protected $table = 'hotel_services';

    protected $fillable = [
        'hotel_id',
        'name',
        'category',
        'description',
        'price',
        'emoji',
        'available',
    ];

    protected $casts = [
        'price'     => 'decimal:2',
        'available' => 'boolean',
    ];

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }
}
