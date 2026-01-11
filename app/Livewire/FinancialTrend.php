<?php

namespace App\Livewire;

use App\Models\Earning;
use App\Models\Expense;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class FinancialTrend extends Component
{
    public $dailyBalances;

    public function render()
    {
        return view('livewire.financial-trend');
    }

    public function mount()
    {
        $this->dailyBalances = $this->getDailyBalances();
    }

    public function getEarningsInDateRange($startDate, $endDate)
    {
        return Earning::where('user_id', Auth::id())
            ->whereBetween('date', [$startDate, $endDate])
            ->get();
    }

    public function getExpensesInDateRange($startDate, $endDate)
    {
        return Expense::where('user_id', Auth::id())
            ->whereBetween('date', [$startDate, $endDate])
            ->get();
    }

    public function getDailyBalances()
    {
        $startDate = now()->startOfMonth();
        $endDate = now()->endOfMonth();

        $earnings = $this->getEarningsInDateRange($startDate, $endDate);
        $expenses = $this->getExpensesInDateRange($startDate, $endDate);

        // Find the last date with actual data
        $lastEarningDate = $earnings->max('date');
        $lastExpenseDate = $expenses->max('date');

        // Use the most recent date between earnings and expenses, or today if no data exists
        $lastDataDate = max($lastEarningDate, $lastExpenseDate);

        if ($lastDataDate) {
            $endDate = min(Carbon::parse($lastDataDate), now()->endOfMonth());
        } else {
            // If no data exists, just show today
            $endDate = now();
        }

        $dailyBalances = [];
        $cumulativeBalance = 0;

        // Calculate the initial balance (all earnings and expenses before the start date)
        $initialEarnings = Earning::where('user_id', Auth::id())
            ->where('date', '<', $startDate)
            ->sum('amount');
        $initialExpenses = Expense::where('user_id', Auth::id())
            ->where('date', '<', $startDate)
            ->sum('amount');
        $cumulativeBalance = $initialEarnings - $initialExpenses;

        for ($date = $startDate->copy(); $date <= $endDate; $date->addDay()) {
            $currentDate = $date->format('M d');

            $dailyEarnings = $earnings->where('date', '>=', $date->startOfDay()->format('Y-m-d H:i:s'))
                ->where('date', '<', $date->copy()->addDay()->startOfDay()->format('Y-m-d H:i:s'))
                ->sum('amount');

            $dailyExpenses = $expenses->where('date', '>=', $date->startOfDay()->format('Y-m-d H:i:s'))
                ->where('date', '<', $date->copy()->addDay()->startOfDay()->format('Y-m-d H:i:s'))
                ->sum('amount');

            $cumulativeBalance += $dailyEarnings - $dailyExpenses;

            $dailyBalances[] = [
                'date' => $currentDate,
                'earnings' => $dailyEarnings,
                'expenses' => $dailyExpenses,
                'balance' => $cumulativeBalance
            ];
        }

        return $dailyBalances;
    }
}
