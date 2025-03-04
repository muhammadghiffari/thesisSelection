<?php

namespace Database\Seeders;

use App\Models\ActivityLog;
use App\Models\Student;
use App\Models\ThesisSelection;
use App\Models\ThesisTitle;
use App\Models\User;
use Illuminate\Database\Seeder;

class ActivityLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create general random activity logs
        ActivityLog::factory()
            ->count(50)
            ->create();

        // Create specific logs for students
        $students = Student::inRandomOrder()->take(10)->get();
        foreach ($students as $student) {
            // Login activity
            ActivityLog::create([
                'loggable_type' => Student::class,
                'loggable_id'   => $student->id,
                'action'        => 'login',
                'description'   => "Student {$student->name} logged in",
                'ip_address'    => fake()->ipv4(),
            ]);

            // If student has selected a thesis, create a selection activity
            if ($student->has_selected) {
                $selection = ThesisSelection::where('student_id', $student->id)->first();
                if ($selection) {
                    ActivityLog::create([
                        'loggable_type' => ThesisSelection::class,
                        'loggable_id'   => $selection->id,
                        'action'        => 'select thesis',
                        'description'   => "Student {$student->name} selected thesis: {$selection->thesisTitle->title}",
                        'ip_address'    => fake()->ipv4(),
                    ]);
                }
            }
        }

        // Create specific logs for thesis titles
        $thesisTitles = ThesisTitle::inRandomOrder()->take(5)->get();
        foreach ($thesisTitles as $thesisTitle) {
            ActivityLog::create([
                'loggable_type' => ThesisTitle::class,
                'loggable_id'   => $thesisTitle->id,
                'action'        => 'status changed',
                'description'   => "Thesis title '{$thesisTitle->title}' status changed to {$thesisTitle->status}",
                'ip_address'    => fake()->ipv4(),
            ]);
        }

        // Create admin activity logs
        $users = User::all();
        foreach ($users as $user) {
            ActivityLog::create([
                'loggable_type' => User::class,
                'loggable_id'   => $user->id,
                'action'        => 'login',
                'description'   => "Admin {$user->name} logged in",
                'ip_address'    => fake()->ipv4(),
            ]);

            ActivityLog::create([
                'loggable_type' => User::class,
                'loggable_id'   => $user->id,
                'action'        => 'update settings',
                'description'   => "Admin {$user->name} updated system settings",
                'ip_address'    => fake()->ipv4(),
            ]);
        }
    }
}
