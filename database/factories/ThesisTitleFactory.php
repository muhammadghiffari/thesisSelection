<?php

namespace Database\Factories;

use App\Models\ThesisTitle;
use Illuminate\Database\Eloquent\Factories\Factory;

class ThesisTitleFactory extends Factory
{
    protected $model = ThesisTitle::class;

    public function definition(): array
    {
        $topics = ['Basic Science'];

        $statuses = ['Available', 'In Selection', 'Unavailable'];
        $weights = [70, 20, 10]; // Weighted probabilities

        return [
            'title'       => fake()->sentence(6, true),
            'description' => fake()->paragraph(3, true),
            'topic'       => fake()->randomElement($topics),
            'status'      => fake()->randomElement($statuses, $weights),
        ];
    }

    public function available(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'Available',
            ];
        });
    }

    public function inSelection(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'In Selection',
            ];
        });
    }

    public function unavailable(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'Unavailable',
            ];
        });
    }
}
