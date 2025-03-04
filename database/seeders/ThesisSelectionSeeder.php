<?php

namespace Database\Seeders;

use App\Models\Student;
use App\Models\ThesisSelection;
use App\Models\ThesisTitle;
use Illuminate\Database\Seeder;

class ThesisSelectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get students who have selected a thesis
        $students = Student::where('has_selected', true)->get();

        foreach ($students as $student) {
            // Create a thesis selection for each student
            // First, get a thesis title that matches their topic preference
            $thesisTitle = ThesisTitle::where('topic', $student->thesis_topic)
                ->where(function($query) {
                    $query->where('status', 'In Selection')
                          ->orWhere('status', 'Unavailable');
                })
                ->inRandomOrder()
                ->first();

            // If no matching thesis title is found, get any thesis title
            if (!$thesisTitle) {
                $thesisTitle = ThesisTitle::where(function($query) {
                    $query->where('status', 'In Selection')
                          ->orWhere('status', 'Unavailable');
                })
                ->inRandomOrder()
                ->first();
            }

            // Create the thesis selection
            if ($thesisTitle) {
                $status = $student->has_reset ? 'Pending' : 'Approved';

                ThesisSelection::create([
                    'student_id' => $student->id,
                    'thesis_title_id' => $thesisTitle->id,
                    'ip_address' => fake()->ipv4(),
                    'status' => $status,
                ]);

                // Update thesis title status if approved
                if ($status === 'Approved') {
                    $thesisTitle->update(['status' => 'Unavailable']);
                } else {
                    $thesisTitle->update(['status' => 'In Selection']);
                }
            }
        }

        // Create some random thesis selections for demonstration
        ThesisSelection::factory()
            ->count(5)
            ->pending()
            ->create();

        ThesisSelection::factory()
            ->count(3)
            ->rejected()
            ->create();
    }
}
