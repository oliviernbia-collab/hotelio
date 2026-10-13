<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StaffSchedule extends Model
{
    protected $fillable = [
        'staff_id', 'hotel_id', 'date', 'shift',
        'start_time', 'end_time', 'poste', 'notes',
    ];

    protected $casts = ['date' => 'date'];

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    public function getShiftLabelAttribute(): string
    {
        return match($this->shift) {
            'matin'       => 'Matin (06h–14h)',
            'apres_midi'  => 'Après-midi (14h–22h)',
            'nuit'        => 'Nuit (22h–06h)',
            'repos'       => 'Repos',
            default       => $this->shift,
        };
    }
}
