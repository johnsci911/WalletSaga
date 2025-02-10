<?php

namespace Database\Seeders;

use App\Models\Earning;
use App\Models\EarningCategory;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::factory()->create([
            'name'  => 'Test User',
            'email' => 'test@example.com'
        ]);

        $earningCategories = ['Salary', 'Bonus', 'Commission', 'Dividend', 'Interest', 'Other'];

        foreach ($earningCategories as $category) {
            EarningCategory::factory()->create(['name' => $category])
                ->each(function ($earningCategory) use ($user) {
                    Earning::factory(3)->create([
                        'earning_categories_id' => $earningCategory->id,
                        'user_id'               => $user->id
                    ]);
                });
        };

        $expenseCategories = ['Groceries', 'Utilities', 'Transportation', 'Entertainment', 'Miscellaneous', 'Education', 'Healthcare', 'Personal Care', 'Home Improvement', 'Travel', 'Other'];

        foreach ($expenseCategories as $category) {
            ExpenseCategory::factory()->create(['name' => $category])
                ->each(function ($earningCategory) use ($user) {
                    Expense::factory(1)->create([
                        'expense_categories_id' => $earningCategory->id,
                        'user_id'               => $user->id
                    ]);
                });
        }
    }
}
