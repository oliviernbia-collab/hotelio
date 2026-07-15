<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'hotel_id',
        'description',
        'amount',
        'expense_type',
        'occurred_at',
        'notes',
    ];

    protected $casts = [
        'amount' => 'integer',
        'occurred_at' => 'date',
    ];

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }
}
