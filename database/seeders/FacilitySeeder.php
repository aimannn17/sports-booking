<?php

namespace Database\Seeders;

use App\Models\Facility;
use App\Models\FacilityType;
use Illuminate\Database\Seeder;

class FacilitySeeder extends Seeder
{
    public function run(): void
    {
        $type = FacilityType::pluck('id', 'name');

        $facilities = [
            ['Badminton', 'Badminton Court A', 'Sports Hall, Level 1', 20, true, 'Wooden floor, 2 nets. Bring your own racquet.'],
            ['Badminton', 'Badminton Court B', 'Sports Hall, Level 1', 20, true, 'Next to Court A.'],
            ['Badminton', 'Badminton Court C', 'Sports Hall, Level 2', 20, false, 'Closed for floor repairs.'],
            ['Futsal', 'Futsal Court', 'Outdoor Complex', 60, true, 'Covered court with floodlights.'],
            ['Tennis', 'Tennis Court 1', 'Outdoor Complex', 40, true, 'Hard court. Floodlights until 10 pm.'],
            ['Volleyball', 'Volleyball Court', 'Sports Hall, Level 2', 30, true, null],
        ];

        foreach ($facilities as [$typeName, $name, $location, $fee, $active, $description]) {
            Facility::create([
                'facility_type_id' => $type[$typeName],
                'name' => $name,
                'location' => $location,
                'external_fee' => $fee,
                'is_active' => $active,
                'description' => $description,
            ]);
        }
    }
}
