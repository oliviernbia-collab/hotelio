<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pool extends Model
{
    use HasFactory;

    protected $fillable = [
        'hotel_id',
        'name',
        'status',
        'area',
        'depth',
        'temperature',
        'description',
    ];

    protected $casts = [
        'area' => 'integer',
        'depth' => 'decimal:1',
        'temperature' => 'decimal:1',
    ];

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }
}
