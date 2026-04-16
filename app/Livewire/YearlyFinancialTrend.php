<?php

namespace App\Livewire;

use App\Models\Earning;
use App\Models\Expense;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class YearlyFinancialTrend extends Component
{
    public $weeklyBalances;

    public function render()
    {
        return view('livewire.yearly-financial-trend');
    }

    public function mount()
    {
        $this->weeklyBalances = $this->getWeeklyBalances();
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

    public function getWeeklyBalances()
    {
        $startDate = now()->startOfYear()->startOfWeek(Carbon::MONDAY);
        $endDate = now()->endOfYear();

        $earnings = $this->getEarningsInDateRange(now()->startOfYear(), $endDate);
        $expenses = $this->getExpensesInDateRange(now()->startOfYear(), $endDate);

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

        $weeklyBalances = [];
        $cumulativeBalance = 0;

        $initialEarnings = Earning::where('user_id', Auth::id())
            ->where('date', '<', now()->startOfYear())
            ->sum('amount');
        $initialExpenses = Expense::where('user_id', Auth::id())
            ->where('date', '<', now()->startOfYear())
            ->sum('amount');
        $cumulativeBalance = $initialEarnings - $initialExpenses;

        for ($week = $startDate->copy(); $week <= $endDate; $week->addWeek()) {
            $weekStart = $week->copy()->startOfDay();
            $weekEnd = $week->copy()->addDays(6)->endOfDay();

            $weekLabel = $weekStart->format('M d');

            $weeklyEarnings = $earnings
                ->filter(fn ($e) => Carbon::parse($e->date)->between($weekStart, $weekEnd))
                ->sum('amount');

            $weeklyExpenses = $expenses
                ->filter(fn ($e) => Carbon::parse($e->date)->between($weekStart, $weekEnd))
                ->sum('amount');

            $cumulativeBalance += $weeklyEarnings - $weeklyExpenses;

            $weeklyBalances[] = [
                'week' => $weekLabel,
                'weekStart' => $weekStart->format('Y-m-d'),
                'earnings' => $weeklyEarnings,
                'expenses' => $weeklyExpenses,
                'balance' => $cumulativeBalance,
            ];
        }

        return $weeklyBalances;
    }
}
