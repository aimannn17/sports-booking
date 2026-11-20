<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database. Order matters: parents before children.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            FacilityTypeSeeder::class,
            FacilitySeeder::class,
            FacilityScheduleSeeder::class,
            TimeSlotSeeder::class,
            BookingSeeder::class,
        ]);
    }
}
