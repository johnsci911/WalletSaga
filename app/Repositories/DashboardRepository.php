<?php

namespace App\Repositories;

use App\Models\Earning;
use App\Models\Expense;
use App\Models\Loan;
use App\Models\EarningCategory;
use App\Models\ExpenseCategory;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\LengthAwarePaginator;

class DashboardRepository
{
    public function currentUser()
    {
        return Auth::user();
    }

    public function getAllEntries($search)
    {
        $search = '%' . strtolower($search) . '%';

        $earnings = $this->getEarnings($search);
        $expenses = $this->getExpenses($search);
        $loans    = $this->getLoans();

        return $earnings->concat($expenses)->concat($loans)->sortByDesc('date')->map(function ($entry) {
            $entry->id          = $entry->id;
            $entry->date        = \Carbon\Carbon::parse($entry->date)->format('Y-m-d H:i');
            $entry->type        = $entry instanceof Earning ? 'Earning' : 'Expense';
            $entry->category    = $entry->category_name ?? 'Loan';
            $entry->description = $entry->description ?? '';

            return $entry;
        });
    }

    public function getTotalEarnings()
    {
        return number_format(Earning::where('user_id', Auth::id())->sum('amount'), 2);
    }

    public function getTotalExpenses()
    {
        return number_format(Expense::where('user_id', Auth::id())->sum('amount'), 2);
    }

    public function getPaginatedEntries($entries, $page, $perPage = 5)
    {
        return new LengthAwarePaginator(
            $entries->forPage($page, $perPage),
            $entries->count(),
            $perPage,
            $page,
            ['path' => url()->current()]
        );
    }

    public function getEarningCategories()
    {
        return EarningCategory::where('name', '!=', 'Other')
            ->get()
            ->merge(EarningCategory::where('name', 'Other'))
            ->toArray();
    }

    public function getExpenseCategories()
    {
        return ExpenseCategory::where('name', '!=', 'Other')
            ->get()
            ->merge(ExpenseCategory::where('name', 'Other')->get())
            ->toArray();
    }

    public function createEarning($data)
    {
        return Earning::create([
            'user_id'               => Auth::id(),
            'date'                  => Carbon::parse($data['date'])->format('Y-m-d'),
            'amount'                => $data['amount'],
            'earning_categories_id' => $data['category'] != '' ? $data['category'] : 1,
            'description'           => $data['description'],
        ]);
    }

    public function updateEarning($id, $data)
    {
        $earning = Earning::findOrFail($id);

        $earning->update([
            'date'                  => Carbon::parse($data['date'])->format('Y-m-d H:i:s'),
            'amount'                => $data['amount'],
            'earning_categories_id' => $data['category'],
            'description'           => $data['description'],
        ]);

        return $earning;
    }

    public function createExpense($data)
    {
        return Expense::create([
            'user_id'               => Auth::id(),
            'date'                  => Carbon::parse($data['date'])->format('Y-m-d H:i:s'),
            'amount'                => $data['amount'],
            'expense_categories_id' => $data['category'] != '' ? $data['category'] : 1,
            'description'           => $data['description'],
        ]);
    }

    public function updateExpense($id, $data)
    {
        $expense = Expense::findOrFail($id);

        $expense->update([
            'date'                  => Carbon::parse($data['date'])->format('Y-m-d'),
            'amount'                => $data['amount'],
            'expense_categories_id' => $data['category'],
            'description'           => $data['description'],
        ]);

        return $expense;
    }

    public function deleteEntry($id, $type)
    {
        if ($type == 'Earning') {
            return Earning::where('id', $id)->delete();
        } else {
            return Expense::where('id', $id)->delete();
        }
    }

    public function getEarningById($id)
    {
        return Earning::where('id', $id)->first();
    }

    public function getExpenseById($id)
    {
        return Expense::where('id', $id)->first();
    }

    private function getEarnings($search)
    {
        return Earning::where('user_id', Auth::id())
            ->join('earning_categories', 'earnings.earning_categories_id', '=', 'earning_categories.id')
            ->select('earnings.id', 'earnings.date', 'earnings.amount', 'earnings.description', 'earning_categories.name as category_name')
            ->where(function ($query) use ($search) {
                $query->whereRaw('LOWER(earnings.description) LIKE ?', $search)
                    ->orWhereRaw('CAST(earnings.amount AS TEXT) LIKE ?', $search)
                    ->orWhereRaw('LOWER(earning_categories.name) LIKE ?', $search)
                    ->orWhereRaw('? LIKE ?', ['earning', $search]);
            })
            ->get();
    }

    private function getExpenses($search)
    {
        return Expense::where('user_id', Auth::id())
            ->join('expense_categories', 'expenses.expense_categories_id', '=', 'expense_categories.id')
            ->select('expenses.id', 'expenses.date', 'expenses.amount', 'expenses.description', 'expense_categories.name as category_name')
            ->where(function ($query) use ($search) {
                $query->whereRaw('LOWER(expenses.description) LIKE ?', $search)
                    ->orWhereRaw('CAST(expenses.amount AS TEXT) LIKE ?', $search)
                    ->orWhereRaw('LOWER(expense_categories.name) LIKE ?', $search)
                    ->orWhereRaw('? LIKE ?', ['expense', $search]);
            })
            ->get();
    }

    private function getLoans()
    {
        return Loan::where('user_id', Auth::id())->select('id', 'date', 'amount', 'user_id')->get();
    }
}
