<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ExpenseCategory>
 */
class ExpenseCategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $categories = ['Groceries', 'Utilities', 'Transportation', 'Entertainment', 'Miscellaneous', 'Education', 'Healthcare', 'Personal Care', 'Home Improvement', 'Travel', 'Other'];

        return [
            'name' => $this->faker->randomElement($categories),
        ];
    }
}
