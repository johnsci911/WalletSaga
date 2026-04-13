<?php

namespace App\Livewire;

use App\Models\Earning;
use App\Models\Expense;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class YearlyFinancialTrend extends Component
{
    public $monthlyBalances;

    public function render()
    {
        return view('livewire.yearly-financial-trend');
    }

    public function mount()
    {
        $this->monthlyBalances = $this->getMonthlyBalances();
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

    public function getMonthlyBalances()
    {
        // Get data for the current year
        $startDate = now()->startOfYear();
        $endDate = now()->endOfYear();

        $earnings = $this->getEarningsInDateRange($startDate, $endDate);
        $expenses = $this->getExpensesInDateRange($startDate, $endDate);

        // Find the last date with actual data
        $lastEarningDate = $earnings->max('date');
        $lastExpenseDate = $expenses->max('date');

        // Use the most recent date between earnings and expenses, or today if no data exists
        $lastDataDate = max($lastEarningDate, $lastExpenseDate);

        if ($lastDataDate) {
            $endDate = min(Carbon::parse($lastDataDate), now()->endOfYear());
        } else {
            // If no data exists, just show today
            $endDate = now();
        }

        $monthlyBalances = [];
        $cumulativeBalance = 0;

        // Calculate the initial balance (all earnings and expenses before the start date)
        $initialEarnings = Earning::where('user_id', Auth::id())
            ->where('date', '<', $startDate)
            ->sum('amount');
        $initialExpenses = Expense::where('user_id', Auth::id())
            ->where('date', '<', $startDate)
            ->sum('amount');
        $cumulativeBalance = $initialEarnings - $initialExpenses;

        // Process each month
        for ($month = $startDate->copy(); $month <= $endDate; $month->addMonth()) {
            $currentMonth = $month->format('M Y');

            $monthlyStartDate = $month->startOfMonth();
            $monthlyEndDate = $month->endOfMonth();

            $monthlyEarnings = $earnings->where('date', '>=', $monthlyStartDate->format('Y-m-d H:i:s'))
                ->where('date', '<=', $monthlyEndDate->format('Y-m-d H:i:s'))
                ->sum('amount');

            $monthlyExpenses = $expenses->where('date', '>=', $monthlyStartDate->format('Y-m-d H:i:s'))
                ->where('date', '<=', $monthlyEndDate->format('Y-m-d H:i:s'))
                ->sum('amount');

            $cumulativeBalance += $monthlyEarnings - $monthlyExpenses;

            $monthlyBalances[] = [
                'month' => $currentMonth,
                'earnings' => $monthlyEarnings,
                'expenses' => $monthlyExpenses,
                'balance' => $cumulativeBalance,
            ];
        }

        return $monthlyBalances;
    }
}