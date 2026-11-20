<?php

namespace Database\Factories;

use App\Models\Facility;
use App\Models\FacilitySchedule;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FacilitySchedule>
 */
class FacilityScheduleFactory extends Factory
{
    public function definition(): array
    {
        return [
            'facility_id' => Facility::factory(),
            'day_of_week' => fake()->numberBetween(0, 6),
            'open_time' => '08:00',
            'close_time' => '22:00',
            'slot_minutes' => 60,
        ];
    }
}
