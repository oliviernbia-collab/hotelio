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

    protected $appends = ['photo_url'];

    public function getPhotoUrlAttribute(): ?string
    {
        if (! $this->photo) {
            return null;
        }
        if (str_starts_with($this->photo, 'http')) {
            return $this->photo;
        }
        return url('storage/' . $this->photo);
    }

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
