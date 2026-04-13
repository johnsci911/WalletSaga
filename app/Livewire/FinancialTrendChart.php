<?php

namespace App\Livewire;

use App\Models\Earning;
use App\Models\Expense;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class FinancialTrendChart extends Component
{
    public $dailyBalances;

    public function render()
    {
        return view('livewire.financial-trend-chart');
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

        $lastEarningDate = $earnings->max('date');
        $lastExpenseDate = $expenses->max('date');

        $lastDataDate = max(
            $lastEarningDate ? strtotime($lastEarningDate) : 0,
            $lastExpenseDate ? strtotime($lastExpenseDate) : 0
        );

        if ($lastDataDate) {
            $endDate = min(Carbon::createFromTimestamp($lastDataDate), now()->endOfMonth());
        } else {
            $endDate = now();
        }

        $dailyBalances = [];
        $cumulativeBalance = 0;

        $initialEarnings = Earning::where('user_id', Auth::id())
            ->where('date', '<', $startDate)
            ->sum('amount');
        $initialExpenses = Expense::where('user_id', Auth::id())
            ->where('date', '<', $startDate)
            ->sum('amount');
        $cumulativeBalance = $initialEarnings - $initialExpenses;

        for ($date = $startDate->copy(); $date <= $endDate; $date->addDay()) {
            $currentDate = $date->format('M d');

            $dayStart = $date->copy()->startOfDay();
            $dayEnd = $date->copy()->endOfDay();

            $dailyEarnings = $earnings
                ->filter(fn ($e) => Carbon::parse($e->date)->between($dayStart, $dayEnd))
                ->sum('amount');

            $dailyExpenses = $expenses
                ->filter(fn ($e) => Carbon::parse($e->date)->between($dayStart, $dayEnd))
                ->sum('amount');

            $cumulativeBalance += $dailyEarnings - $dailyExpenses;

            $dailyBalances[] = [
                'date' => $currentDate,
                'earnings' => $dailyEarnings,
                'expenses' => $dailyExpenses,
                'balance' => $cumulativeBalance,
            ];
        }

        return $dailyBalances;
    }
}