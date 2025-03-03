<?php

namespace App\Livewire;

use App\Repositories\DashboardRepository;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

class Dashboard extends Component
{
    use WithPagination;

    protected $queryString = ['page'];

    public $user;
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
    public $shouldFocusDate = null;
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
    private $repository;

    public function boot(DashboardRepository $repository)
    {
        $this->repository = $repository;
    }

    public function render()
    {
        $entries = $this->getPaginatedEntries();

        return view('livewire.dashboard', [
            'entries'            => $entries,
            'currentPageBalance' => $this->calculateTotalBalance($entries),
            'totalBalance'       => $this->calculateTotalBalance($this->getAllEntries()),
            'totalEarnings'      => $this->totalEarnings,
            'totalExpenses'      => $this->totalExpenses,
            'search'             => $this->search,
        ]);
    }

    public function updatedSearch()
    {
        $this->page = 1;
        $this->refreshData();
    }

    public function addEntry($type)
    {
        // Reset form first
        $this->resetForms();

        $this->editingEntryId   = null;
        $this->editingEntryType = $type;
        $this->shouldFocusDate  = $type;

        $this->dispatch('scrollToBottom');
    }

    public function cancelAdd()
    {
        $this->resetForms();

        $this->editingEntryType = null;

        $this->dispatch('scrollToTop');
    }

    public function editEntry($id, $type)
    {
        $this->resetForms();

        $this->editingEntryId = $id;
        $this->editingEntryType = $type;

        if ($type === 'Earning') {
            $earning = $this->repository->getEarningById($id);
            $this->earningForm = [
                'date'        => $this->formatDateForInput($earning->date),
                'amount'      => $earning->amount,
                'category'    => $earning->earning_categories_id,
                'description' => $earning->description,
            ];

            $this->shouldFocusDate = 'earning';
        } else {
            $expense = $this->repository->getExpenseById($id);
            $this->expenseForm = [
                'date'        => $this->formatDateForInput($expense->date),
                'amount'      => $expense->amount,
                'category'    => $expense->expense_categories_id,
                'description' => $expense->description,
            ];

            $this->shouldFocusDate = 'expense';
        }

        $this->dispatch('scrollToBottom');
    }

    public function cancelEdit()
    {
        $this->editingEntryId = null;
        $this->editingEntryType = null;
        $this->resetForms();

        $this->dispatch('scrollToTop');
    }

    private function resetForms()
    {
        $this->shouldFocusDate = null;
        $this->editingEntryType = null;

        $this->earningForm = [
            'date'        => '',
            'amount'      => '',
            'category'    => '',
            'description' => '',
        ];

        $this->expenseForm = [
            'date'        => '',
            'amount'      => '',
            'category'    => '',
            'description' => '',
        ];
    }

    public function mount()
    {
        $this->user = $this->repository->currentUser();
        $this->page = request()->query('page', 1);

        $this->refreshData();
    }

    private function refreshData()
    {
        $this->entries            = $this->getPaginatedEntries()->toArray();
        $this->earningCategories  = $this->repository->getEarningCategories();
        $this->expenseCategories  = $this->repository->getExpenseCategories();
        $this->currentPageBalance = $this->calculateTotalBalance($this->getPaginatedEntries());
        $this->totalBalance       = $this->calculateTotalBalance($this->getAllEntries());
        $this->totalEarnings      = $this->repository->getTotalEarnings();
        $this->totalExpenses      = $this->repository->getTotalExpenses();
    }

    public function getAllEntries()
    {
        return $this->repository->getAllEntries($this->search);
    }

    public function getPaginatedEntries()
    {
        $entries = $this->getAllEntries();
        return $this->repository->getPaginatedEntries($entries, $this->page);
    }

    public function gotoPage($page)
    {
        $this->page = $page;
        $this->refreshData();
    }

    public function calculateTotalBalance($entries)
    {
        $total = $entries->sum(function ($entry) {
            return $entry->type == 'Earning' ? $entry->amount : -$entry->amount;
        });

        return number_format($total, 2);
    }

    public function submitEarning()
    {
        $currentPage = $this->page;
        $data = $this->earningForm;

        if ($this->editingEntryId && $this->editingEntryType === 'Earning') {
            $this->repository->updateEarning($this->editingEntryId, $data);
            $this->editingEntryId   = null;
            $this->editingEntryType = null;
        } else {
            $this->repository->createEarning($data);
            $currentPage = 1;
        }

        $this->refreshData();
        $this->gotoPage($currentPage);
        $this->reset('earningForm');

        $this->dispatch('scrollToTop');
    }

    public function submitExpense()
    {
        $currentPage = $this->page;
        $data = $this->expenseForm;

        if ($this->editingEntryId && $this->editingEntryType === 'Expense') {
            $this->repository->updateExpense($this->editingEntryId, $data);
            $this->editingEntryId = null;
            $this->editingEntryType = null;
        } else {
            $this->repository->createExpense($data);
            $currentPage = 1;
        }

        $this->refreshData();
        $this->gotoPage($currentPage);
        $this->reset('expenseForm');

        $this->dispatch('scrollToTop');
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
        $this->repository->deleteEntry($id, $type);
        $this->refreshData();
        $this->resetForms();
    }
}

