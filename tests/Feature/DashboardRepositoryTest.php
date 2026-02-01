<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\ExpenseCategory;
use App\Repositories\DashboardRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Carbon\Carbon;

class DashboardRepositoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_update_expense_retains_time()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $category = ExpenseCategory::factory()->create();

        $repository = new DashboardRepository();

        $expense = $repository->createExpense([
            'date' => '2023-10-27T10:00',
            'amount' => 100,
            'category' => $category->id,
            'description' => 'Test Expense',
        ]);

        $this->assertEquals('10:00:00', Carbon::parse($expense->date)->format('H:i:s'));

        $updatedDate = '2023-10-28T14:30';
        $repository->updateExpense($expense->id, [
            'date' => $updatedDate,
            'amount' => 150,
            'category' => $category->id,
            'description' => 'Updated Expense',
        ]);

        $updatedExpense = $expense->fresh();
        
        $this->assertEquals('14:30:00', Carbon::parse($updatedExpense->date)->format('H:i:s'), 'Time component was lost during update');
        $this->assertEquals('2023-10-28', Carbon::parse($updatedExpense->date)->format('Y-m-d'));
    }

    public function test_update_earning_retains_time()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $category = \App\Models\EarningCategory::factory()->create();

        $repository = new DashboardRepository();

        $earning = $repository->createEarning([
            'date' => '2023-10-27T10:00',
            'amount' => 100,
            'category' => $category->id,
            'description' => 'Test Earning',
        ]);
        
        $updatedDate = '2023-10-28T14:30';
        $repository->updateEarning($earning->id, [
            'date' => $updatedDate,
            'amount' => 150,
            'category' => $category->id,
            'description' => 'Updated Earning',
        ]);

        $updatedEarning = $earning->fresh();
        
        $this->assertEquals('14:30:00', Carbon::parse($updatedEarning->date)->format('H:i:s'), 'Time component was lost during earning update');
        $this->assertEquals('2023-10-28', Carbon::parse($updatedEarning->date)->format('Y-m-d'));
    }

    public function test_create_earning_retains_time()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $category = \App\Models\EarningCategory::factory()->create();

        $repository = new DashboardRepository();

        $earning = $repository->createEarning([
            'date' => '2023-10-27T10:00',
            'amount' => 100,
            'category' => $category->id,
            'description' => 'Test Earning Creation',
        ]);

        $this->assertEquals('10:00:00', Carbon::parse($earning->date)->format('H:i:s'), 'Time component was lost during earning creation');
        $this->assertEquals('2023-10-27', Carbon::parse($earning->date)->format('Y-m-d'));
    }

    public function test_create_expense_retains_time()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $category = ExpenseCategory::factory()->create();

        $repository = new DashboardRepository();

        $expense = $repository->createExpense([
            'date' => '2023-10-27T10:00',
            'amount' => 100,
            'category' => $category->id,
            'description' => 'Test Expense Creation',
        ]);

        $this->assertEquals('10:00:00', Carbon::parse($expense->date)->format('H:i:s'), 'Time component was lost during expense creation');
        $this->assertEquals('2023-10-27', Carbon::parse($expense->date)->format('Y-m-d'));
    }
}
