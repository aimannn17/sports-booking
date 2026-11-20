<?php

namespace Database\Seeders;

use App\Models\Facility;
use Illuminate\Database\Seeder;

/**
 * Open every day, 8 am to 10 pm. Futsal uses 90-minute slots (last one ends 9.30 pm).
 */
class FacilityScheduleSeeder extends Seeder
{
    public function run(): void
    {
        foreach (Facility::all() as $facility) {
            $minutes = str_contains($facility->name, 'Futsal') ? 90 : 60;

            foreach (range(0, 6) as $day) {
                $facility->schedules()->create([
                    'day_of_week' => $day,
                    'open_time' => '08:00',
                    'close_time' => $minutes === 90 ? '21:30' : '22:00',
                    'slot_minutes' => $minutes,
                ]);
            }
        }
    }
}
