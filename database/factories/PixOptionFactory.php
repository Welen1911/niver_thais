<?php

namespace Database\Factories;

use App\Models\PixOption;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PixOption>
 */
class PixOptionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->word(),
            'description' => $this->faker->sentence(),
            'photo' => $this->faker->imageUrl(),
            'value' => $this->faker->randomFloat(2, 10, 100),
            'is_available' => $this->faker->boolean()
        ];
    }
}
