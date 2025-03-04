<?php

namespace Database\Seeders;

use App\Models\ThesisTitle;
use Illuminate\Database\Seeder;

class ThesisTitleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create available thesis titles
        ThesisTitle::factory()
            ->count(30)
            ->available()
            ->create();

        // Create thesis titles that are in selection
        ThesisTitle::factory()
            ->count(15)
            ->inSelection()
            ->create();

        // Create unavailable thesis titles
        ThesisTitle::factory()
            ->count(10)
            ->unavailable()
            ->create();

        // Create predefined thesis titles for each topic
        $topics = [
            'Basic Science',
        ];

        foreach ($topics as $topic) {
            ThesisTitle::factory()
                ->count(3)
                ->create([
                    'topic'  => $topic,
                    'status' => 'Available',
                ]);
        }
    }
}
