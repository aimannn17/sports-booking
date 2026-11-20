<?php

namespace Database\Factories;

use App\Enums\SlotStatus;
use App\Models\Facility;
use App\Models\TimeSlot;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TimeSlot>
 */
class TimeSlotFactory extends Factory
{
    protected static int $counter = 0;

    public function definition(): array
    {
        // Walk through hours then days so slots that share a facility never collide.
        $n = static::$counter++;
        $hour = 8 + ($n % 14);

        return [
            'facility_id' => Facility::factory(),
            'slot_date' => today()->addDays(2 + intdiv($n, 14)),
            'start_time' => sprintf('%02d:00:00', $hour),
            'end_time' => sprintf('%02d:00:00', $hour + 1),
            'status' => SlotStatus::Available,
        ];
    }

    public function booked(): static
    {
        return $this->state(fn (array $attributes) => ['status' => SlotStatus::Booked]);
    }

    public function blocked(): static
    {
        return $this->state(fn (array $attributes) => ['status' => SlotStatus::Blocked]);
    }

    public function past(): static
    {
        return $this->state(fn (array $attributes) => ['slot_date' => today()->subDays(3)]);
    }
}
