<?php

namespace Database\Factories;

use App\Models\Hotel;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class HotelFactory extends Factory
{
    protected $model = Hotel::class;

    public function definition(): array
    {
        return [
            'user_id'    => User::factory(),
            'name'       => fake()->company() . ' Hotel',
            'short_name' => fake()->word(),
            'city'       => fake()->city(),
            'quartier'   => 'Plateau',
            'stars'      => fake()->numberBetween(2, 5),
            'rooms'      => fake()->numberBetween(10, 50),
            'category'   => 'Hôtel',
            'status'     => 'Ouvert',
            'rating'     => fake()->randomFloat(1, 3.0, 5.0),
            'price'      => fake()->numberBetween(30000, 200000),
        ];
    }
}
