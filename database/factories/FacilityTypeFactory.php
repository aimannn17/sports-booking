<?php

namespace Database\Factories;

use App\Models\FacilityType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FacilityType>
 */
class FacilityTypeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->randomElement([
                'Badminton', 'Futsal', 'Tennis', 'Volleyball', 'Basketball',
                'Squash', 'Table Tennis', 'Netball', 'Sepak Takraw', 'Pickleball',
            ]).' '.fake()->unique()->numberBetween(1, 9999),
        ];
    }
}
