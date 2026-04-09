<?php

namespace Database\Factories;

use App\Models\PixContribution;
use App\Models\PixOption;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PixContribution>
 */
class PixContributionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'pix_option_id' => PixOption::factory(),
            'guest_name' => $this->faker->name(),
            'confirmed' => $this->faker->boolean()
        ];
    }
}
