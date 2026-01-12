<?php

namespace Database\Factories;

use App\Models\Goal;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Goal>
 */
class GoalFactory extends Factory
{
    protected $model = Goal::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => fake()->words(3, true),
            'description' => fake()->sentence(),
            'target_amount' => fake()->randomFloat(2, 1000, 50000),
            'current_amount' => fake()->randomFloat(2, 0, 1000),
            'deadline' => fake()->dateTimeBetween('now', '+2 years'),
            'category' => fake()->randomElement(['Travel', 'Emergency', 'Purchase', 'Savings', 'Investment']),
            'is_completed' => false,
            'completed_at' => null,
        ];
    }

    /**
     * Indicate that the goal is completed.
     */
    public function completed(): static
    {
        return $this->state(fn(array $attributes) => [
            'is_completed' => true,
            'completed_at' => now(),
            'current_amount' => $attributes['target_amount'],
        ]);
    }

    /**
     * Indicate that the goal is overdue.
     */
    public function overdue(): static
    {
        return $this->state(fn(array $attributes) => [
            'deadline' => fake()->dateTimeBetween('-1 year', '-1 day'),
            'is_completed' => false,
        ]);
    }
}
