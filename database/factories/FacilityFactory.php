<?php

namespace Database\Factories;

use App\Models\Facility;
use App\Models\FacilityType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Facility>
 */
class FacilityFactory extends Factory
{
    public function definition(): array
    {
        return [
            'facility_type_id' => FacilityType::factory(),
            'name' => 'Court '.fake()->randomLetter(),
            'location' => fake()->randomElement(['Sports Hall, Level 1', 'Sports Hall, Level 2', 'Outdoor Complex']),
            'external_fee' => fake()->randomElement([20, 30, 40, 60]),
            'is_active' => true,
            'description' => fake()->sentence(),
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => ['is_active' => false]);
    }
}
