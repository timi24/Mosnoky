<?php

use App\Models\Administrator;
use App\Models\AppNotification;
use App\Models\User;

test('administrator can open their account page with the administrator role', function () {
    $administrator = User::factory()->create(['role' => 'ADMINISTRATEUR']);

    $this->actingAs($administrator)
        ->get(route('account.show'))
        ->assertOk()
        ->assertSee('Administrateur');
});

test('client navigation includes the account page', function () {
    $client = User::factory()->create(['role' => 'CLIENT']);

    $this->actingAs($client)
        ->get(route('client.dashboard'))
        ->assertOk()
        ->assertSee('Mon compte')
        ->assertSee(route('account.show'), false);
});

test('live counts determine administrator status from the user role', function () {
    $administrator = User::factory()->create(['role' => 'ADMINISTRATEUR']);
    $clientWithAdministratorRecord = User::factory()->create(['role' => 'CLIENT']);
    Administrator::create(['user_id' => $clientWithAdministratorRecord->id]);

    $this->actingAs($administrator)
        ->getJson(route('live.counts'))
        ->assertOk()
        ->assertJsonPath('is_admin', true);

    $this->actingAs($clientWithAdministratorRecord)
        ->getJson(route('live.counts'))
        ->assertOk()
        ->assertJsonPath('is_admin', false);
});

test('notification links use the user role to select the administrator destination', function () {
    $administrator = User::factory()->create(['role' => 'ADMINISTRATEUR']);
    $notification = AppNotification::create([
        'user_id' => $administrator->id,
        'title' => 'Paiement en attente',
        'content' => 'Un paiement est à traiter.',
    ]);

    $this->actingAs($administrator)
        ->get(route('live.notifications.open', $notification))
        ->assertRedirect(route('admin.payments.index'));
});

test('administrator notification cards link to their destination', function () {
    $administrator = User::factory()->create(['role' => 'ADMINISTRATEUR']);
    $notification = AppNotification::create([
        'user_id' => $administrator->id,
        'title' => 'Paiement en attente',
        'content' => 'Un paiement est à traiter.',
    ]);

    $this->actingAs($administrator)
        ->get(route('admin.notifications.index'))
        ->assertOk()
        ->assertSee(route('live.notifications.open', $notification), false)
        ->assertSee('Voir ›');
});

test('client notification dates and pagination are shown in French', function () {
    $client = User::factory()->create(['role' => 'CLIENT']);

    foreach (range(1, 16) as $index) {
        AppNotification::create([
            'user_id' => $client->id,
            'title' => 'Notification '.$index,
            'content' => 'Détail de la notification.',
            'sent_at' => now()->subMinutes(5),
        ]);
    }

    $this->actingAs($client)
        ->get(route('notifications.index'))
        ->assertOk()
        ->assertSee('Affichage de 1 à 15 sur 16 notifications')
        ->assertSee('Page 1 sur 2')
        ->assertSee('Précédente')
        ->assertSee('Suivante')
        ->assertSee('il y a');
});
