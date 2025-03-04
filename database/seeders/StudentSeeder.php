<?php

namespace Database\Seeders;

use App\Models\Student;
use Illuminate\Database\Seeder;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 50 students
        Student::factory()
            ->count(50)
            ->create();

        // Create 10 students who have already selected a thesis
        Student::factory()
            ->count(10)
            ->create([
                'has_selected' => true,
            ]);

        // Create 5 students who have reset their selection
        Student::factory()
            ->count(5)
            ->create([
                'has_selected' => true,
                'has_reset'    => true,
            ]);
    }
}
