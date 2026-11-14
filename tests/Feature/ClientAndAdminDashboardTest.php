<?php

use App\Enums\TeamRole;
use App\Models\User;

test('authenticated users can visit the client dashboard', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('client.dashboard'))
        ->assertOk()
        ->assertSee('Espace client privilégié');
});

test('team administrators can visit the admin dashboard', function () {
    $user = User::factory()->create(['role' => 'ADMINISTRATEUR']);

    $this->actingAs($user)
        ->get(route('admin.dashboard'))
        ->assertOk()
        ->assertSee('Administration')
        ->assertSee('À traiter')
        ->assertSee('Produits en stock bas')
        ->assertSee('Avis à modérer')
        ->assertSee('Accès rapides')
        ->assertSee('id="pay-alarm-bar"', false)
        ->assertSee('id="pay-alarm-pill"', false)
        ->assertSee('data-admin-payment-count', false);

    $this->actingAs($user)
        ->get(route('admin.products.create'))
        ->assertOk()
        ->assertSee('data-preview-target="create-product-image-preview"', false)
        ->assertSee('Un aperçu vous aidera à vérifier votre sélection', false);
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
