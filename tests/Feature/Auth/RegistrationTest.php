<?php

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Event;

test('registration screen can be rendered', function () {
    $response = $this->get(route('register'));

    $response->assertOk();
});

test('user role cannot be changed through mass assignment', function () {
    $user = User::factory()->create();
    $user->refresh();

    $user->fill(['role' => 'ADMINISTRATEUR']);

    expect($user->role)->toBe('CLIENT');
});

test('new users can register', function () {
    Event::fake([Registered::class]);

    $response = $this->post(route('register.store'), [
        'name' => 'John Doe',
        'email' => 'test@example.com',
        'password' => 'Password123!',
        'password_confirmation' => 'Password123!',
    ]);

    $user = User::where('email', 'test@example.com')->first();

    $response->assertSessionHasNoErrors()
        ->assertRedirect(route('verification.notice', absolute: false));

    $this->assertAuthenticated();
});
