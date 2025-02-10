<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EarningCategory>
 */
class EarningCategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $categories = ['Salary', 'Bonus', 'Commission', 'Dividend', 'Interest'];

        return [
            'name' => $this->faker->randomElement($categories),
        ];
    }
}
