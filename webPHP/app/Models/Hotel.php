<?php

namespace App\Models;

use App\Models\Expense;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\Pool;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\Staff;
use App\Models\Ticket;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class Hotel extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'short_name',
        'city',
        'commune',
        'quartier',
        'stars',
        'rooms',
        'category',
        'status',
        'rating',
        'price',
        'emoji',
        'color',
        'manager',
        'phone',
        'email',
        'revenue',
        'occupancy',
        'checkins',
        'checkouts',
        'description',
        'services',
        'image',
        'opened_at',
        'latitude',
        'longitude',
    ];

    protected $appends = ['image_url'];

    public function getImageUrlAttribute(): ?string
    {
        if (! $this->image) return null;
        if (str_starts_with($this->image, 'http')) return $this->image;
        return url('storage/' . $this->image);
    }

    protected $casts = [
        'stars' => 'integer',
        'rooms' => 'integer',
        'rating' => 'decimal:1',
        'price' => 'integer',
        'revenue' => 'integer',
        'occupancy' => 'integer',
        'checkins' => 'integer',
        'checkouts' => 'integer',
        'services' => 'array',
        'opened_at' => 'date',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function rooms()
    {
        return $this->hasMany(Room::class);
    }

    public function staff()
    {
        return $this->hasMany(Staff::class);
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    public function pools()
    {
        return $this->hasMany(Pool::class);
    }

    /** Services additionnels (hotel_services) — nommé différemment de la colonne JSON `services` (équipements). */
    public function additionalServices()
    {
        return $this->hasMany(HotelService::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function menuItems()
    {
        return $this->hasMany(MenuItem::class);
    }

    public function favoritedByUsers()
    {
        return $this->belongsToMany(User::class, 'favorites')->withPivot('created_at');
    }

    public function generatedRooms(): Collection
    {
        $types = ['Standard', 'Deluxe', 'Suite', 'Suite Présidentielle'];
        $statuses = ['disponible', 'occupée', 'maintenance'];
        $rooms = collect();
        $count = 0;
        $floors = (int) ceil($this->rooms / 16);

        for ($floor = 1; $floor <= $floors; $floor++) {
            $max = min(16, $this->rooms - ($floor - 1) * 16);
            for ($number = 1; $number <= $max; $number++) {
                $type = $types[min($floor - 1, count($types) - 1)];
                $multiplier = $floor === 1 ? 0.7 : ($floor === $floors ? 1.8 : 1.0);
                $status = $statuses[$count % count($statuses)];
                $rooms->push((object) [
                    'id' => ++$count,
                    'number' => sprintf('%s%02d', $floor, $number),
                    'floor' => $floor,
                    'type' => $type,
                    'status' => $status,
                    'price' => (int) round($this->price * $multiplier),
                ]);
            }
        }

        return $rooms;
    }
}
