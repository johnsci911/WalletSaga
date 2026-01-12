<?php

namespace App\Livewire;

use App\Models\Goal;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Goals extends Component
{
    use WithPagination;

    protected $queryString = ['page'];
    public $showGoalModal = false;
    public $editingGoalId = null;

    public $goalForm = [
        'name' => '',
        'description' => '',
        'target_amount' => '',
        'current_amount' => 0,
        'deadline' => '',
        'category' => '',
    ];

    public function render()
    {
        $goals = Auth::user()->goals()
            ->orderBy('is_completed', 'asc')
            ->orderBy('deadline', 'asc')
            ->paginate(6)
            ->through(function ($goal) {
                return [
                    'id' => $goal->id,
                    'name' => $goal->name,
                    'description' => $goal->description,
                    'target_amount' => number_format($goal->target_amount, 2),
                    'current_amount' => number_format($goal->current_amount, 2),
                    'remaining_amount' => number_format($goal->remaining_amount, 2),
                    'progress_percentage' => $goal->progress_percentage,
                    'deadline' => $goal->deadline ? $goal->deadline->format('M d, Y') : null,
                    'days_remaining' => $goal->days_remaining,
                    'is_overdue' => $goal->is_overdue,
                    'is_completed' => $goal->is_completed,
                    'category' => $goal->category,
                    'completed_at' => $goal->completed_at ? $goal->completed_at->format('M d, Y') : null,
                ];
            });

        return view('livewire.goals', ['goals' => $goals]);
    }

    /**
     * Get unique categories for the current user plus common defaults
     */
    public function getCategoriesProperty()
    {
        $existingCategories = Auth::user()->goals()
            ->whereNotNull('category')
            ->distinct()
            ->pluck('category')
            ->toArray();

        $defaultCategories = [
            'Travel',
            'Education',
            'Technology',
            'Emergency Fund',
            'Savings',
            'Health',
            'Insurance',
            'Gifts',
            'Vehicle',
            'Home',
            'Investment'
        ];

        return collect(array_merge($existingCategories, $defaultCategories))
            ->unique()
            ->sort()
            ->values();
    }

    public function addGoal()
    {
        $this->resetForm();
        $this->editingGoalId = null;
        $this->showGoalModal = true;
    }

    public function editGoal($id)
    {
        $goal = Goal::findOrFail($id);

        $this->editingGoalId = $id;
        $this->goalForm = [
            'name' => $goal->name,
            'description' => $goal->description,
            'target_amount' => $goal->target_amount,
            'current_amount' => $goal->current_amount,
            'deadline' => $goal->deadline ? $goal->deadline->format('Y-m-d') : '',
            'category' => $goal->category,
        ];

        $this->showGoalModal = true;
    }

    public function submitGoal()
    {
        $this->validate([
            'goalForm.name' => 'required|string|max:255',
            'goalForm.target_amount' => 'required|numeric|min:0.01',
            'goalForm.current_amount' => 'nullable|numeric|min:0',
            'goalForm.deadline' => 'nullable|date|after:today',
            'goalForm.category' => 'nullable|string|max:255',
        ]);

        $data = [
            'user_id' => Auth::id(),
            'name' => $this->goalForm['name'],
            'description' => $this->goalForm['description'],
            'target_amount' => $this->goalForm['target_amount'],
            'current_amount' => $this->goalForm['current_amount'] ?? 0,
            'deadline' => $this->goalForm['deadline'] ?: null,
            'category' => $this->goalForm['category'],
        ];

        if ($this->editingGoalId) {
            Goal::findOrFail($this->editingGoalId)->update($data);
        } else {
            Goal::create($data);
        }

        $this->showGoalModal = false;
        $this->resetForm();
    }

    public function deleteGoal($id)
    {
        Goal::findOrFail($id)->delete();
    }

    public function updateProgress($id, $amount)
    {
        $goal = Goal::findOrFail($id);
        $goal->update(['current_amount' => $amount]);

        // Auto-complete if target reached
        if ($goal->current_amount >= $goal->target_amount && !$goal->is_completed) {
            $goal->markAsCompleted();
        }
    }

    public function toggleComplete($id)
    {
        $goal = Goal::findOrFail($id);

        if ($goal->is_completed) {
            $goal->update([
                'is_completed' => false,
                'completed_at' => null,
            ]);
        } else {
            $goal->markAsCompleted();
        }
    }

    private function resetForm()
    {
        $this->goalForm = [
            'name' => '',
            'description' => '',
            'target_amount' => '',
            'current_amount' => 0,
            'deadline' => '',
            'category' => '',
        ];
    }
}
