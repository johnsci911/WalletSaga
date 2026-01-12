<?php

use App\Livewire\Goals;
use App\Models\Goal;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

test('goals component renders successfully', function () {
    Livewire::test(Goals::class)
        ->assertStatus(200);
});

test('component displays empty state when no goals exist', function () {
    Livewire::test(Goals::class)
        ->assertSee('No Goals Yet')
        ->assertSee('Create your first financial goal');
});

test('component displays goals list', function () {
    Goal::factory()->create([
        'user_id' => $this->user->id,
        'name' => 'Vacation Fund',
        'target_amount' => 5000,
    ]);

    Livewire::test(Goals::class)
        ->assertSee('Vacation Fund')
        ->assertSee('5,000.00');
});

test('can open add goal modal', function () {
    Livewire::test(Goals::class)
        ->call('addGoal')
        ->assertSet('showGoalModal', true)
        ->assertSet('editingGoalId', null);
});

test('can create a new goal', function () {
    Livewire::test(Goals::class)
        ->set('goalForm', [
            'name' => 'Emergency Fund',
            'description' => 'Save for emergencies',
            'target_amount' => 10000,
            'current_amount' => 0,
            'deadline' => now()->addMonths(6)->format('Y-m-d'),
            'category' => 'Emergency',
        ])
        ->call('submitGoal')
        ->assertSet('showGoalModal', false);

    expect(Goal::where('name', 'Emergency Fund')->exists())->toBeTrue();
});

test('validates required fields when creating goal', function () {
    Livewire::test(Goals::class)
        ->set('goalForm.name', '')
        ->set('goalForm.target_amount', '')
        ->call('submitGoal')
        ->assertHasErrors(['goalForm.name', 'goalForm.target_amount']);
});

test('validates target amount is positive', function () {
    Livewire::test(Goals::class)
        ->set('goalForm.name', 'Test Goal')
        ->set('goalForm.target_amount', -100)
        ->call('submitGoal')
        ->assertHasErrors(['goalForm.target_amount']);
});

test('validates deadline is in the future', function () {
    Livewire::test(Goals::class)
        ->set('goalForm.name', 'Test Goal')
        ->set('goalForm.target_amount', 1000)
        ->set('goalForm.deadline', now()->subDays(1)->format('Y-m-d'))
        ->call('submitGoal')
        ->assertHasErrors(['goalForm.deadline']);
});

test('can edit existing goal', function () {
    $goal = Goal::factory()->create([
        'user_id' => $this->user->id,
        'name' => 'Original Name',
        'target_amount' => 1000,
    ]);

    Livewire::test(Goals::class)
        ->call('editGoal', $goal->id)
        ->assertSet('editingGoalId', $goal->id)
        ->assertSet('showGoalModal', true)
        ->assertSet('goalForm.name', 'Original Name');
});

test('can update existing goal', function () {
    $goal = Goal::factory()->create([
        'user_id' => $this->user->id,
        'name' => 'Original Name',
        'target_amount' => 1000,
    ]);

    Livewire::test(Goals::class)
        ->call('editGoal', $goal->id)
        ->set('goalForm.name', 'Updated Name')
        ->set('goalForm.target_amount', 2000)
        ->call('submitGoal');

    $goal->refresh();

    expect($goal->name)->toBe('Updated Name')
        ->and((float) $goal->target_amount)->toEqual(2000.0);
});

test('can delete goal', function () {
    $goal = Goal::factory()->create([
        'user_id' => $this->user->id,
    ]);

    Livewire::test(Goals::class)
        ->call('deleteGoal', $goal->id);

    expect(Goal::find($goal->id))->toBeNull();
});

test('can toggle goal completion', function () {
    $goal = Goal::factory()->create([
        'user_id' => $this->user->id,
        'is_completed' => false,
    ]);

    Livewire::test(Goals::class)
        ->call('toggleComplete', $goal->id);

    $goal->refresh();

    expect($goal->is_completed)->toBeTrue()
        ->and($goal->completed_at)->not->toBeNull();
});

test('can reopen completed goal', function () {
    $goal = Goal::factory()->create([
        'user_id' => $this->user->id,
        'is_completed' => true,
        'completed_at' => now(),
    ]);

    Livewire::test(Goals::class)
        ->call('toggleComplete', $goal->id);

    $goal->refresh();

    expect($goal->is_completed)->toBeFalse()
        ->and($goal->completed_at)->toBeNull();
});

test('can update goal progress', function () {
    $goal = Goal::factory()->create([
        'user_id' => $this->user->id,
        'target_amount' => 1000,
        'current_amount' => 0,
    ]);

    Livewire::test(Goals::class)
        ->call('updateProgress', $goal->id, 500);

    $goal->refresh();

    expect((float) $goal->current_amount)->toEqual(500.0);
});

test('goal auto-completes when target is reached', function () {
    $goal = Goal::factory()->create([
        'user_id' => $this->user->id,
        'target_amount' => 1000,
        'current_amount' => 900,
        'is_completed' => false,
    ]);

    Livewire::test(Goals::class)
        ->call('updateProgress', $goal->id, 1000);

    $goal->refresh();

    expect($goal->is_completed)->toBeTrue();
});

test('only shows user own goals', function () {
    $otherUser = User::factory()->create();

    Goal::factory()->create([
        'user_id' => $this->user->id,
        'name' => 'My Goal',
    ]);

    Goal::factory()->create([
        'user_id' => $otherUser->id,
        'name' => 'Other User Goal',
    ]);

    Livewire::test(Goals::class)
        ->assertSee('My Goal')
        ->assertDontSee('Other User Goal');
});

test('goals are ordered by completion status then deadline', function () {
    Goal::factory()->create([
        'user_id' => $this->user->id,
        'name' => 'Completed Goal',
        'is_completed' => true,
        'deadline' => now()->addDays(5),
    ]);

    Goal::factory()->create([
        'user_id' => $this->user->id,
        'name' => 'Active Soon',
        'is_completed' => false,
        'deadline' => now()->addDays(2),
    ]);

    Goal::factory()->create([
        'user_id' => $this->user->id,
        'name' => 'Active Later',
        'is_completed' => false,
        'deadline' => now()->addDays(10),
    ]);

    $component = Livewire::test(Goals::class);
    $goals = $component->get('goals');

    expect($goals[0]['name'])->toBe('Active Soon')
        ->and($goals[1]['name'])->toBe('Active Later')
        ->and($goals[2]['name'])->toBe('Completed Goal');
});
