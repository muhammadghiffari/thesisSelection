<?php

namespace Database\Seeders;

use App\Models\LiveActivity;
use App\Models\Student;
use App\Models\ThesisTitle;
use Illuminate\Database\Seeder;

class LiveActivitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create some random live activities
        LiveActivity::factory()
            ->count(15)
            ->create();

        // Create specific live activities for selected students
        $students = Student::inRandomOrder()->take(5)->get();
        $thesisTitles = ThesisTitle::where('status', 'Available')->inRandomOrder()->take(10)->get();

        foreach ($students as $student) {
            // Create viewing activity
            LiveActivity::create([
                'student_id'      => $student->id,
                'thesis_title_id' => null,
                'action'          => 'viewing thesis list',
                'ip_address'      => fake()->ipv4(),
            ]);

            // Create searching activity
            LiveActivity::create([
                'student_id'      => $student->id,
                'thesis_title_id' => null,
                'action'          => 'searching thesis',
                'ip_address'      => fake()->ipv4(),
            ]);

            // Create thesis viewing activity for 2 random thesis titles
            foreach ($thesisTitles->random(2) as $thesisTitle) {
                LiveActivity::create([
                    'student_id'      => $student->id,
                    'thesis_title_id' => $thesisTitle->id,
                    'action'          => 'viewing details',
                    'ip_address'      => fake()->ipv4(),
                ]);
            }
        }

        // Create some anonymous activities (no student_id)
        foreach ($thesisTitles->random(3) as $thesisTitle) {
            LiveActivity::create([
                'student_id'      => null,
                'thesis_title_id' => $thesisTitle->id,
                'action'          => 'viewing details',
                'ip_address'      => fake()->ipv4(),
            ]);
        }
    }
}
