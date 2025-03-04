<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Student>
 */
class StudentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $topics = ['Basic Science'];

        $classes = ['REGULER', 'KKI'];

        return [
            'name'         => fake()->name(),
            'npm'          => fake()->unique()->numerify('20########'),
            'class'        => fake()->randomElement($classes),
            'thesis_topic' => fake()->randomElement($topics),
            'email'        => fake()->unique()->numerify('##########') . '@ui.ac.id',
            'token'        => strtoupper(Str::random(5)),
            'has_selected' => false,
            'has_reset'    => false,
        ];
    }
}
