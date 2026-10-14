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
        'jeko_reference',
        'jeko_payment_request_id',
        'nationality',
        'checkin_at',
        'checkout_at',
        'notes',
        'created_at',
        'commission_rate',
        'commission_amount',
        'payout_transfer_id',
        'payout_status',
        'payout_failure_reason',
        'cancelled_at',
        'cancellation_fee_amount',
        'admin_fee_amount',
        'refund_amount',
        'refund_transfer_id',
        'refund_status',
        'refund_failure_reason',
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
        'commission_rate' => 'decimal:2',
        'commission_amount' => 'integer',
        'cancelled_at' => 'datetime',
        'cancellation_fee_amount' => 'integer',
        'admin_fee_amount' => 'integer',
        'refund_amount' => 'integer',
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
