<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductReservation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProductReservation>
 */
class ProductReservationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'guest_name' => $this->faker->name(),
            'quantity' => $this->faker->numberBetween(1, 5),
        ];
    }
}
