<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * 1 admin, 3 students and 2 external users. Every password is "password".
 */
class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->admin()->create([
            'name' => 'Sports Centre Admin',
            'email' => 'admin@gmi.test',
            'phone' => '03-89141000',
        ]);

        $students = [
            ['name' => 'Ahmad Faris', 'email' => 'student@gmi.test', 'student_id' => 'GMI2023001'],
            ['name' => 'Nurul Aina', 'email' => 'aina@gmi.test', 'student_id' => 'GMI2023002'],
            ['name' => 'Lim Wei Jie', 'email' => 'weijie@gmi.test', 'student_id' => 'GMI2023003'],
        ];

        foreach ($students as $student) {
            User::factory()->student()->create($student);
        }

        User::factory()->external()->create(['name' => 'Siti Rahmah (alumni)', 'email' => 'external@gmi.test']);
        User::factory()->external()->create(['name' => 'Rajesh Kumar', 'email' => 'rajesh@gmi.test']);
    }
}
