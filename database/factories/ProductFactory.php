<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'photo' => $this->faker->imageUrl(640, 480, 'food', true), // Random food image
            'name' => $this->faker->word(),
            'description' => $this->faker->sentence(),
            'is_available' => $this->faker->boolean(80), // 80% chance of being available
            'stock' => $this->faker->numberBetween(0, 100),
        ];
    }
}
