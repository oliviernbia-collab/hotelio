<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'name'        => fake()->words(3, true),
            'price'       => fake()->numberBetween(1000, 50000),
            'stock'       => fake()->numberBetween(1, 100),
            'category'    => fake()->randomElement(['soin', 'alimentation', 'decoration', 'textile']),
            'emoji'       => '🛒',
            'description' => fake()->sentence(),
        ];
    }
}
