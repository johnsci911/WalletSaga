<?php

use App\Models\EarningCategory;
use App\Models\User;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('earnings can be created', function () {
    $user = User::factory()->create();

    $category = EarningCategory::factory()->create();

    $this->actingAs($user);

    $earningData = [
        'date' => now()->format('Y-m-d H:i:s'),
        'amount' => 100.00,
        'earning_categories_id' => $category->id,
        'description' => 'Test Earning',
        'user_id' => $user->id,
    ];

    $response = $this->post('/livewire/update', $earningData);

    $response->assertStatus(200);
});
