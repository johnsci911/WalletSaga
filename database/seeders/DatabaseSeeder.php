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
            'name'  => 'John Karlo',
            'email' => 'johnkarlo.315@gmail.com'
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

        $dates = [];
        $startDate = now()->startOfDay();
        for ($day = 0; $day < 7; $day++) {
            $entriesPerDay = rand(3, 5);
            for ($entry = 0; $entry < $entriesPerDay; $entry++) {
                $dates[] = $startDate->copy()->addDays($day)->addSeconds(rand(0, 86399))->timestamp;
            }
        }
        sort($dates);


        foreach ($dates as $date) {
            EarningCategory::factory()->create(['name' => array_rand($earningCategories)])
                ->each(function ($earningCategory) use ($user, $date) {
                    Earning::factory(3)->create([
                        'earning_categories_id' => $earningCategory->id,
                        'user_id'               => $user->id,
                        'created_at'            => date('Y-m-d H:i:s', $date)
                    ]);
                });
            ExpenseCategory::factory()->create(['name' => array_rand($expenseCategories)])
                ->each(function ($earningCategory) use ($user, $date) {
                    Expense::factory(1)->create([
                        'expense_categories_id' => $earningCategory->id,
                        'user_id'               => $user->id,
                        'created_at'            => date('Y-m-d H:i:s', $date)
                    ]);
                });
        }
    }
}
