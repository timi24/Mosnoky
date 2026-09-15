<?php

use App\Enums\TeamRole;
use App\Models\User;

test('authenticated users can visit the client dashboard', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('client.dashboard'))
        ->assertOk()
        ->assertSee('Client dashboard');
});

test('team administrators can visit the admin dashboard', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('admin.dashboard'))
        ->assertOk()
        ->assertSee('Administration');
});

test('team members cannot visit the admin dashboard', function () {
    $owner = User::factory()->create();
    $member = User::factory()->create();

    $owner->currentTeam->members()->attach($member, ['role' => TeamRole::Member->value]);
    $member->switchTeam($owner->currentTeam);

    $this->actingAs($member)
        ->get(route('admin.dashboard'))
        ->assertForbidden();
});
