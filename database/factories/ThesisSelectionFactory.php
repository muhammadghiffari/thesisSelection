<?php

namespace Database\Factories;

use App\Models\Student;
use App\Models\ThesisSelection;
use App\Models\ThesisTitle;
use Illuminate\Database\Eloquent\Factories\Factory;

class ThesisSelectionFactory extends Factory
{
    protected $model = ThesisSelection::class;

    public function definition(): array
    {
        $statuses = ['Pending', 'Approved', 'Rejected'];
        $weights = [50, 40, 10]; // Weighted probabilities

        return [
            'student_id'      => Student::factory(),
            'thesis_title_id' => ThesisTitle::factory(),
            'ip_address'      => fake()->ipv4(),
            'status'          => fake()->randomElement($statuses, $weights),
        ];
    }

    public function pending(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'Pending',
            ];
        });
    }

    public function approved(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'Approved',
            ];
        });
    }

    public function rejected(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'Rejected',
            ];
        });
    }
}
