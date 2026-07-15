<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Hotel;
use App\Models\Reservation;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReservationFactory extends Factory
{
    protected $model = Reservation::class;

    public function definition(): array
    {
        $checkin  = fake()->dateTimeBetween('now', '+30 days');
        $checkout = fake()->dateTimeBetween($checkin, '+37 days');
        $nights   = max(1, (int) $checkin->diff($checkout)->days);
        $price    = fake()->numberBetween(20000, 80000);

        return [
            'code'       => strtoupper(fake()->unique()->bothify('RES-######')),
            'client_id'  => Client::factory(),
            'hotel_id'   => Hotel::factory(),
            'room'       => fake()->numerify('1##'),
            'checkin'    => $checkin->format('Y-m-d'),
            'checkout'   => $checkout->format('Y-m-d'),
            'nights'     => $nights,
            'price'      => $price,
            'total'      => $price * $nights,
            'status'     => 'en_attente',
            'channel'    => 'direct',
            'paid'       => false,
        ];
    }
}
