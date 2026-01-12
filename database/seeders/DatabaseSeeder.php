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
            'name'     => 'John Karlo',
            'email'    => 'johnkarlo.315@gmail.com',
            'password' => 'password',
        ]);

        $earningCategories = [
            'Salary',
            'Bonus',
            'Commission',
            'Dividend',
            'Interest',
            'Other'
        ];

        $expenseCategories = [
            'Groceries',
            'Utilities',
            'Transportation',
            'Entertainment',
            'Miscellaneous',
            'Education',
            'Healthcare',
            'Personal Care',
            'Home Improvement',
            'Travel',
            'Other'
        ];

        // Create all categories upfront
        foreach ($earningCategories as $category) {
            EarningCategory::create(['name' => $category]);
        }

        foreach ($expenseCategories as $category) {
            ExpenseCategory::create(['name' => $category]);
        }

        $dates = [];
        $startDate = now()->startOfDay();
        for ($day = 0; $day < 30; $day++) {
            $entriesPerDay = rand(3, 5);
            for ($entry = 0; $entry < $entriesPerDay; $entry++) {
                $dates[] = $startDate->copy()->subDays($day)->addSeconds(rand(0, 86399))->timestamp;
            }
        }
        sort($dates);

        $earningCategoryIds = EarningCategory::pluck('id')->toArray();
        $expenseCategoryIds = ExpenseCategory::pluck('id')->toArray();

        foreach ($dates as $date) {
            Earning::factory(3)->create([
                'earning_categories_id' => $earningCategoryIds[array_rand($earningCategoryIds)],
                'user_id'               => $user->id,
                'date'                  => date('Y-m-d H:i:s', $date)
            ]);

            Expense::factory(1)->create([
                'expense_categories_id' => $expenseCategoryIds[array_rand($expenseCategoryIds)],
                'user_id'               => $user->id,
                'date'                  => date('Y-m-d H:i:s', $date)
            ]);
        }

        // Seed dynamic goals using factory
        // Create 25 active goals with random progress
        \App\Models\Goal::factory(25)->create([
            'user_id' => $user->id,
        ]);

        // Create 3 completed goals
        \App\Models\Goal::factory(3)->completed()->create([
            'user_id' => $user->id,
        ]);

        // Create 2 overdue goals
        \App\Models\Goal::factory(2)->overdue()->create([
            'user_id' => $user->id,
        ]);
    }
}
