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
        $startDate = now()->startOfYear();
        $endDate = now()->endOfYear();

        $earnings = $this->getEarningsInDateRange($startDate, $endDate);
        $expenses = $this->getExpensesInDateRange($startDate, $endDate);

        $lastEarningDate = $earnings->max('date');
        $lastExpenseDate = $expenses->max('date');

        $lastDataDate = max(
            $lastEarningDate ? strtotime($lastEarningDate) : 0,
            $lastExpenseDate ? strtotime($lastExpenseDate) : 0
        );

        if ($lastDataDate) {
            $endDate = min(Carbon::createFromTimestamp($lastDataDate), now()->endOfYear());
        } else {
            $endDate = now();
        }

        $monthlyBalances = [];
        $cumulativeBalance = 0;

        $initialEarnings = Earning::where('user_id', Auth::id())
            ->where('date', '<', $startDate)
            ->sum('amount');
        $initialExpenses = Expense::where('user_id', Auth::id())
            ->where('date', '<', $startDate)
            ->sum('amount');
        $cumulativeBalance = $initialEarnings - $initialExpenses;

        for ($month = $startDate->copy(); $month <= $endDate; $month->addMonth()) {
            $currentMonth = $month->format('M Y');

            $monthlyStartDate = $month->copy()->startOfMonth();
            $monthlyEndDate = $month->copy()->endOfMonth();

            $monthlyEarnings = $earnings
                ->filter(fn ($e) => Carbon::parse($e->date)->between($monthlyStartDate, $monthlyEndDate, true))
                ->sum('amount');

            $monthlyExpenses = $expenses
                ->filter(fn ($e) => Carbon::parse($e->date)->between($monthlyStartDate, $monthlyEndDate, true))
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
