<?php

namespace App\Livewire;

use App\Models\Expense;
use App\Models\EarningCategory;
use App\Models\ExpenseCategory;
use App\Models\Earning;
use App\Models\Loan;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Dashboard extends Component
{
    use WithPagination;

    protected $queryString = ['page'];

    public $search = '';
    public $entries = [];
    public $currentPageBalance = 0;
    public $totalBalance = 0;
    public $totalEarnings = 0;
    public $totalExpenses = 0;
    public $earningCategories = [];
    public $expenseCategories = [];
    public $page;
    public $editingEntryId = null;
    public $editingEntryType = null;
    public $earningForm = [
        'date'        => '',
        'amount'      => '',
        'category'    => 1,
        'description' => '',
    ];
    public $expenseForm = [
        'date'        => '',
        'amount'      => '',
        'category'    => 1,
        'description' => '',
    ];

    protected $listeners = ['submitEarning', 'submitExpense'];

    public function render()
    {
        $entries = $this->getPaginatedEntries();

        return view('livewire.dashboard', [
            'entries' => $entries,
            'currentPageBalance' => $this->calculateTotalBalance($entries),
            'totalBalance' => $this->calculateTotalBalance($this->getAllEntries()),
            'totalEarnings' => $this->getTotalEarnings(),
            'totalExpenses' => $this->getTotalExpenses(),
            'search' => $this->search,
        ]);
    }

    public function updatedSearch()
    {
        $this->page = 1;
        $this->entries = $this->getPaginatedEntries()->toArray();
        $this->currentPageBalance = $this->calculateTotalBalance($this->getPaginatedEntries());
        $this->totalBalance = $this->calculateTotalBalance($this->getAllEntries());
        $this->totalEarnings = $this->getTotalEarnings();
        $this->totalExpenses = $this->getTotalExpenses();
    }

    public function editEntry($id, $type)
    {
        $this->resetForms();

        $this->editingEntryId = $id;
        $this->editingEntryType = $type;

        if ($type === 'Earning') {
            $earning = Earning::findOrFail($id);
            $this->earningForm = [
                'date' => $this->formatDateForInput($earning->date),
                'amount' => $earning->amount,
                'category' => $earning->earning_categories_id,
                'description' => $earning->description,
            ];
        } else {
            $expense = Expense::findOrFail($id);
            $this->expenseForm = [
                'date' => $this->formatDateForInput($expense->date),
                'amount' => $expense->amount,
                'category' => $expense->expense_categories_id,
                'description' => $expense->description,
            ];
        }
    }

    public function cancelEdit()
    {
        $this->editingEntryId = null;
        $this->editingEntryType = null;
        $this->resetForms();
    }

    private function resetForms()
    {
        $this->earningForm = [
            'date' => '',
            'amount' => '',
            'category' => '',
            'description' => '',
        ];

        $this->expenseForm = [
            'date' => '',
            'amount' => '',
            'category' => '',
            'description' => '',
        ];
    }

    public function mount()
    {
        $this->page = request()->query('page', 1);

        $this->entries            = $this->getPaginatedEntries()->toArray();
        $this->earningCategories  = EarningCategory::all()->toArray();
        $this->expenseCategories  = ExpenseCategory::all()->toArray();
        $this->currentPageBalance = $this->calculateTotalBalance($this->getPaginatedEntries());
        $this->totalBalance       = $this->calculateTotalBalance($this->getAllEntries());
        $this->totalEarnings      = $this->getTotalEarnings();
        $this->totalExpenses      = $this->getTotalExpenses();
    }

    public function getAllEntries()
    {
        $search = '%' . $this->search . '%';
        $searchLower = strtolower($this->search);

        $earnings = Earning::where('user_id', Auth::id())
            ->join('earning_categories', 'earnings.earning_categories_id', '=', 'earning_categories.id')
            ->select('earnings.id', 'earnings.date', 'earnings.amount', 'earnings.description', 'earning_categories.name as category_name')
            ->where(function ($query) use ($search, $searchLower) {
                $query->where('earnings.description', 'like', $search)
                    ->orWhere('earnings.amount', 'like', $search)
                    ->orWhere('earning_categories.name', 'like', $search)
                    ->orWhereRaw('LOWER(?) LIKE ?', ['earning', $searchLower]);
            })
            ->get();

        $expenses = Expense::where('user_id', Auth::id())
            ->join('expense_categories', 'expenses.expense_categories_id', '=', 'expense_categories.id')
            ->select('expenses.id', 'expenses.date', 'expenses.amount', 'expenses.description', 'expense_categories.name as category_name')
            ->where(function ($query) use ($search, $searchLower) {
                $query->where('expenses.description', 'like', $search)
                    ->orWhere('expenses.amount', 'like', $search)
                    ->orWhere('expense_categories.name', 'like', $search)
                    ->orWhereRaw('LOWER(?) LIKE ?', ['expense', $searchLower]);
            })
            ->get();

        $loans = Loan::where('user_id', Auth::id())->select('id', 'date', 'amount', 'user_id')->get();

        $entries = $earnings->concat($expenses)->concat($loans)->sortByDesc('date')->map(function ($entry) {
            $entry->id          = $entry->id;
            $entry->date        = \Carbon\Carbon::parse($entry->date)->format('Y-m-d H:i');
            $entry->type        = $entry instanceof Earning ? 'Earning' : 'Expense';
            $entry->category    = $entry->category_name ?? 'Loan';
            $entry->description = $entry->description ?? '';

            return $entry;
        });

        return $entries;
    }

    public function getTotalEarnings()
    {
        return number_format(Earning::where('user_id', Auth::id())->sum('amount'), 2);
    }

    public function getTotalExpenses()
    {
        return number_format(Expense::where('user_id', Auth::id())->sum('amount'), 2);
    }

    public function getPaginatedEntries()
    {
        $entries = $this->getAllEntries();

        $perPage = 10;
        $page = (int) $this->page ?? 1;

        $paginator = new LengthAwarePaginator(
            $entries->forPage($page, $perPage),
            $entries->count(),
            $perPage,
            $page,
            ['path' => url()->current()]
        );

        return $paginator;
    }

    public function gotoPage($page)
    {
        $this->page = $page;

        $entries = $this->getPaginatedEntries();

        if ($entries instanceof LengthAwarePaginator) {
            $this->entries = $entries->toArray();
        } else {
            $this->entries = [];
        }

        $this->currentPageBalance = $this->calculateTotalBalance($entries);
    }

    public function calculateTotalBalance($entries)
    {
        $allEntries = $entries->map(function ($entry) {
            $entry->amount = $entry->amount;
            $entry->type   = $entry instanceof Earning ? 'Earning' : 'Expense';

            return $entry;
        })->toArray();

        $total = array_sum(array_map(function ($entry) {
            return $entry['type'] == 'Earning' ? $entry['amount'] : -$entry['amount'];
        }, $allEntries));

        return number_format($total, 2);
    }

    public function submitEarning()
    {
        $currentPage = $this->page;

        $data = $this->earningForm;

        if ($this->editingEntryId && $this->editingEntryType === 'Earning') {
            $earning = Earning::findOrFail($this->editingEntryId);

            $earning->update([
                'date'                  => $data['date'],
                'amount'                => $data['amount'],
                'earning_categories_id' => $data['category'],
                'description'           => $data['description'],
            ]);

            $this->editingEntryId = null;
            $this->editingEntryType = null;

            $this->page = $currentPage;
        } else {
            Earning::create([
                'date'                  => $data['date'],
                'amount'                => $data['amount'],
                'earning_categories_id' => $data['category'],
                'description'           => $data['description'],
                'user_id'               => Auth::id(),
            ]);

            $currentPage = 1;
        }

        $this->entries            = $this->getAllEntries()->toArray();
        $this->currentPageBalance = $this->calculateTotalBalance($this->getPaginatedEntries());
        $this->totalBalance       = $this->calculateTotalBalance($this->getAllEntries());
        $this->totalEarnings      = $this->getTotalEarnings();
        $this->gotoPage($currentPage);

        $this->reset('earningForm');
    }

    public function submitExpense()
    {
        $currentPage = $this->page;

        $data = $this->expenseForm;

        if ($this->editingEntryId && $this->editingEntryType === 'Expense') {
            $expense = Expense::findOrFail($this->editingEntryId);
            $expense->update([
                'date'                  => $data['date'],
                'amount'                => $data['amount'],
                'expense_categories_id' => $data['category'],
                'description'           => $data['description'],
            ]);
            $this->editingEntryId = null;
            $this->editingEntryType = null;
        } else {
            Expense::create([
                'date'                  => $data['date'],
                'amount'                => $data['amount'],
                'expense_categories_id' => $data['category'],
                'description'           => $data['description'],
                'user_id'               => Auth::id(),
            ]);

            $currentPage = 1;
        }

        $this->entries            = $this->getAllEntries()->toArray();
        $this->currentPageBalance = $this->calculateTotalBalance($this->getPaginatedEntries());
        $this->totalBalance       = $this->calculateTotalBalance($this->getAllEntries());
        $this->totalExpenses      = $this->getTotalExpenses();
        $this->gotoPage($currentPage);

        $this->reset('expenseForm');
    }

    private function formatDateForDisplay($date)
    {
        return $date ? Carbon::parse($date)->format('m/d/Y, h:i:s A') : null;
    }

    private function formatDateForInput($date)
    {
        return $date ? Carbon::parse($date)->format('Y-m-d\TH:i') : null;
    }

    public function deleteEntry($id, $type)
    {
        if ($type == 'Earning') {
            Earning::where('id', $id)->delete();
        } else {
            Expense::where('id', $id)->delete();
        }

        $this->entries            = $this->getPaginatedEntries()->toArray();
        $this->currentPageBalance = $this->calculateTotalBalance($this->getPaginatedEntries());
        $this->totalBalance       = $this->calculateTotalBalance($this->getAllEntries());
        $this->totalEarnings      = $this->getTotalEarnings();
        $this->totalExpenses      = $this->getTotalExpenses();
    }
}
