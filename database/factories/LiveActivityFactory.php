<?php

namespace Database\Factories;

use App\Models\LiveActivity;
use App\Models\Student;
use App\Models\ThesisTitle;
use Illuminate\Database\Eloquent\Factories\Factory;

class LiveActivityFactory extends Factory
{
    protected $model = LiveActivity::class;

    public function definition(): array
    {
        $actions = [
            'viewing thesis list',
            'selecting thesis',
            'searching thesis',
            'on selection page',
            'viewing details'
        ];

        $hasStudent = fake()->boolean(80);
        $hasThesis = fake()->boolean(60);

        return [
            'student_id'      => $hasStudent ? Student::factory() : null,
            'thesis_title_id' => $hasThesis ? ThesisTitle::factory() : null,
            'action'          => fake()->randomElement($actions),
            'ip_address'      => fake()->ipv4(),
        ];
    }
}
