<?php

namespace Database\Factories;

use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClientFactory extends Factory
{
    protected $model = Client::class;

    public function definition(): array
    {
        return [
            'code'       => strtoupper(fake()->unique()->bothify('CLT-####')),
            'first_name' => fake()->firstName(),
            'last_name'  => fake()->lastName(),
            'email'      => fake()->unique()->safeEmail(),
            'phone'      => '+225 ' . fake()->numerify('## ## ## ## ##'),
            'city'       => 'Abidjan',
            'status'     => 'actif',
        ];
    }
}
