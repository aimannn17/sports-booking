<?php

namespace Database\Seeders;

use App\Models\FacilityType;
use Illuminate\Database\Seeder;

class FacilityTypeSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['Badminton', 'Futsal', 'Tennis', 'Volleyball'] as $name) {
            FacilityType::create(['name' => $name]);
        }
    }
}
