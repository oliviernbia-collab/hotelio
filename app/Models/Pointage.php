<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pointage extends Model
{
    protected $fillable = [
        'staff_id', 'hotel_id', 'date',
        'check_in', 'check_out', 'check_in_source', 'check_out_source',
        'late_minutes', 'notes',
    ];

    protected $casts = [
        'date'      => 'date',
        'check_in'  => 'datetime',
        'check_out' => 'datetime',
    ];

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    public function getWorkedMinutesAttribute(): ?int
    {
        if (!$this->check_in || !$this->check_out) return null;
        return (int) $this->check_in->diffInMinutes($this->check_out);
    }

    public function getStatusAttribute(): string
    {
        if (!$this->check_in) return 'absent';
        if ($this->late_minutes > 0) return 'retard';
        return 'present';
    }
}
