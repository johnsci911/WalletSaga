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
        $goals = [
            ['name' => 'Emergency Fund', 'description' => 'Build a safety net to cover 6 months of living expenses for unexpected situations', 'category' => 'Emergency'],
            ['name' => 'Vacation to Japan', 'description' => 'Save for a 2-week trip including flights, accommodation, and spending money in Tokyo and Kyoto', 'category' => 'Travel'],
            ['name' => 'New MacBook Pro', 'description' => 'Purchase a high-performance laptop for software development and video editing', 'category' => 'Purchase'],
            ['name' => 'Investment Portfolio', 'description' => 'Build initial investment portfolio for long-term wealth building in index funds', 'category' => 'Investment'],
            ['name' => 'Home Down Payment', 'description' => 'Save for 20% down payment on a new home or condo in the suburbs', 'category' => 'Savings'],
            ['name' => 'Wedding Fund', 'description' => 'Save for dream wedding including venue, catering, and professional photography', 'category' => 'Savings'],
            ['name' => 'Car Purchase', 'description' => 'Buy a reliable vehicle for daily commute and long family road trips', 'category' => 'Purchase'],
            ['name' => 'Master\'s Degree', 'description' => 'Fund graduate school tuition and related educational supplies and books', 'category' => 'Savings'],
            ['name' => 'Retirement Fund', 'description' => 'Build retirement savings for financial security in later years with compounds', 'category' => 'Investment'],
            ['name' => 'Home Renovation', 'description' => 'Remodel kitchen and bathroom to increase home value and utility', 'category' => 'Savings'],
            ['name' => 'European Tour', 'description' => 'Travel across Europe visiting multiple countries over 3 productive weeks', 'category' => 'Travel'],
            ['name' => 'iPhone 16 Pro', 'description' => 'Upgrade to the latest smartphone with better camera and faster performance', 'category' => 'Purchase'],
            ['name' => 'Business Startup', 'description' => 'Save capital to launch own personal business or creative side hustle', 'category' => 'Investment'],
            ['name' => 'Debt Payoff', 'description' => 'Pay off all credit card debt and become completely debt-free by next year', 'category' => 'Emergency'],
            ['name' => 'Gaming Setup', 'description' => 'Build complete gaming PC setup with high-end monitor and accessories', 'category' => 'Purchase'],
            ['name' => 'Engagement Ring', 'description' => 'Save for a beautiful diamond ring for a special proposal moment', 'category' => 'Savings'],
            ['name' => 'Digital Camera', 'description' => 'Buy a professional mirrorless camera for high-quality photography', 'category' => 'Purchase'],
            ['name' => 'Furniture Set', 'description' => 'Upgrade living room furniture with a new sofa, coffee table, and rug', 'category' => 'Purchase'],
            ['name' => 'Language Course', 'description' => 'Enroll in an intensive language course to learn a new foreign language', 'category' => 'Savings'],
            ['name' => 'Stock Market Fund', 'description' => 'Allocate capital specifically for individual stock market opportunities', 'category' => 'Investment'],
        ];

        $selected = fake()->randomElement($goals);

        return [
            'user_id' => User::factory(),
            'name' => $selected['name'],
            'description' => $selected['description'],
            'target_amount' => fake()->randomFloat(2, 5000, 100000),
            'current_amount' => fake()->randomFloat(2, 0, 5000),
            'deadline' => fake()->dateTimeBetween('now', '+2 years'),
            'category' => $selected['category'],
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
