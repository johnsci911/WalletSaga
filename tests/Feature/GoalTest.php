<?php

use App\Models\Goal;
use App\Models\User;

test('goal belongs to user', function () {
    $user = User::factory()->create();
    $goal = Goal::factory()->create(['user_id' => $user->id]);

    expect($goal->user)->toBeInstanceOf(User::class)
        ->and($goal->user->id)->toBe($user->id);
});

test('goal calculates progress percentage correctly', function () {
    $goal = Goal::factory()->create([
        'target_amount' => 1000,
        'current_amount' => 250,
    ]);

    expect($goal->progress_percentage)->toBe(25.0);
});

test('progress percentage caps at 100', function () {
    $goal = Goal::factory()->create([
        'target_amount' => 1000,
        'current_amount' => 1500,
    ]);

    expect($goal->progress_percentage)->toEqual(100);
});

test('goal calculates remaining amount correctly', function () {
    $goal = Goal::factory()->create([
        'target_amount' => 1000,
        'current_amount' => 350,
    ]);

    expect($goal->remaining_amount)->toBe(650.0);
});

test('remaining amount is zero when goal is exceeded', function () {
    $goal = Goal::factory()->create([
        'target_amount' => 1000,
        'current_amount' => 1200,
    ]);

    expect($goal->remaining_amount)->toEqual(0);
});

test('goal calculates days remaining correctly', function () {
    $goal = Goal::factory()->create([
        'deadline' => now()->addDays(10),
    ]);

    expect($goal->days_remaining)->toBeGreaterThanOrEqual(9)->toBeLessThanOrEqual(10);
});

test('goal detects overdue status', function () {
    $overdueGoal = Goal::factory()->create([
        'deadline' => now()->subDays(5),
        'is_completed' => false,
    ]);

    $futureGoal = Goal::factory()->create([
        'deadline' => now()->addDays(5),
        'is_completed' => false,
    ]);

    expect($overdueGoal->is_overdue)->toBeTrue()
        ->and($futureGoal->is_overdue)->toBeFalse();
});

test('completed goals are not marked as overdue', function () {
    $goal = Goal::factory()->create([
        'deadline' => now()->subDays(5),
        'is_completed' => true,
    ]);

    expect($goal->is_overdue)->toBeFalse();
});

test('goal can be marked as completed', function () {
    $goal = Goal::factory()->create([
        'target_amount' => 1000,
        'current_amount' => 500,
        'is_completed' => false,
    ]);

    $goal->markAsCompleted();

    expect($goal->is_completed)->toBeTrue()
        ->and($goal->completed_at)->not->toBeNull()
        ->and((float) $goal->current_amount)->toEqual(1000.0);
});

test('active scope filters out completed goals', function () {
    Goal::factory()->create(['is_completed' => true]);
    Goal::factory()->create(['is_completed' => false]);
    Goal::factory()->create(['is_completed' => false]);

    $activeGoals = Goal::active()->get();

    expect($activeGoals)->toHaveCount(2);
});

test('completed scope filters out active goals', function () {
    Goal::factory()->create(['is_completed' => true]);
    Goal::factory()->create(['is_completed' => true]);
    Goal::factory()->create(['is_completed' => false]);

    $completedGoals = Goal::completed()->get();

    expect($completedGoals)->toHaveCount(2);
});

test('projected completion date calculates correctly', function () {
    $goal = Goal::factory()->create([
        'target_amount' => 1000,
        'current_amount' => 400,
    ]);

    $monthlyContribution = 200;
    $projectedDate = $goal->getProjectedCompletionDate($monthlyContribution);

    // Remaining: 600, at 200/month = 3 months
    expect($projectedDate)->not->toBeNull()
        ->and(abs($projectedDate->diffInMonths(now())))->toBeGreaterThanOrEqual(2.9)
        ->and(abs($projectedDate->diffInMonths(now())))->toBeLessThanOrEqual(3.1);
});

test('projected completion date returns null for zero contribution', function () {
    $goal = Goal::factory()->create([
        'target_amount' => 1000,
        'current_amount' => 400,
    ]);

    expect($goal->getProjectedCompletionDate(0))->toBeNull();
});

test('projected completion date returns null when goal is already complete', function () {
    $goal = Goal::factory()->create([
        'target_amount' => 1000,
        'current_amount' => 1000,
    ]);

    expect($goal->getProjectedCompletionDate(100))->toBeNull();
});
