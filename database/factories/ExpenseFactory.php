<?php

namespace Database\Factories;

use App\Models\ExpenseCategory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Expense>
 */
class ExpenseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id'               => User::factory(),
            'expense_categories_id' => ExpenseCategory::factory(),
            'amount'                => $this->faker->randomFloat(2, 1, 1000),
            'description'           => $this->faker->sentence,
            'date'                  => $this->faker->date('Y-m-d H:i:s'),
        ];
    }
}
