<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    use HasFactory;

    protected $fillable = [
        'hotel_id',
        'first_name',
        'last_name',
        'role',
        'phone',
        'email',
        'hired_at',
        'status',
        'salary',
        'photo',
        'user_id',
    ];

    protected $casts = [
        'hired_at' => 'date',
        'salary' => 'integer',
    ];

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
