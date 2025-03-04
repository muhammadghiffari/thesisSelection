<?php

namespace Database\Factories;

use App\Models\ActivityLog;
use App\Models\Student;
use App\Models\ThesisSelection;
use App\Models\ThesisTitle;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ActivityLogFactory extends Factory
{
    protected $model = ActivityLog::class;

    public function definition(): array
    {
        $loggableTypes = [
            Student::class,
            ThesisTitle::class,
            ThesisSelection::class,
            User::class,
        ];

        $loggableType = fake()->randomElement($loggableTypes);

        // Create a related model based on the chosen type
        $loggableId = null;
        if ($loggableType === Student::class) {
            $loggableId = Student::factory()->create()->id;
            $actions = ['login', 'logout', 'select thesis', 'reset selection'];
        } elseif ($loggableType === ThesisTitle::class) {
            $loggableId = ThesisTitle::factory()->create()->id;
            $actions = ['created', 'updated', 'deleted', 'status changed'];
        } elseif ($loggableType === ThesisSelection::class) {
            $loggableId = ThesisSelection::factory()->create()->id;
            $actions = ['created', 'approved', 'rejected', 'deleted'];
        } else {
            $loggableId = User::factory()->create()->id;
            $actions = ['login', 'logout', 'create user', 'update settings'];
        }

        $action = fake()->randomElement($actions);

        return [
            'loggable_type' => $loggableType,
            'loggable_id'   => $loggableId,
            'action'        => $action,
            'description'   => fake()->sentence(),
            'ip_address'    => fake()->ipv4(),
        ];
    }
}
