<?php

namespace App\Models;

use App\Models\Room;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'client_id',
        'hotel_id',
        'room_id',
        'room',
        'checkin',
        'checkout',
        'nights',
        'price',
        'total',
        'status',
        'channel',
        'paid',
        'payment_method',
        'nationality',
        'checkin_at',
        'checkout_at',
        'notes',
        'created_at',
    ];

    protected $casts = [
        'checkin' => 'date',
        'checkout' => 'date',
        'nights' => 'integer',
        'price' => 'integer',
        'total' => 'integer',
        'paid' => 'boolean',
        'checkin_at' => 'datetime',
        'checkout_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }
}
