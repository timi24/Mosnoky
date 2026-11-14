<?php

use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;

test('email verification screen can be rendered', function () {
    $user = User::factory()->unverified()->create();

    $response = $this->actingAs($user)->get(route('verification.notice'));

    $response->assertOk();
});

test('email can be verified with its hashed code', function () {
    $user = User::factory()->unverified()->create([
        'verification_code' => hash_hmac('sha256', '123456', config('app.key')),
        'verification_code_expires_at' => now()->addMinutes(15),
    ]);

    Event::fake([Verified::class]);

    $this->actingAs($user)
        ->post(route('verification.verify.code'), ['code' => '123456'])
        ->assertRedirect(route('dashboard', absolute: false));

    expect($user->fresh()->hasVerifiedEmail())->toBeTrue()
        ->and($user->fresh()->verification_code)->toBeNull();

    Event::assertDispatched(Verified::class);
});

test('email verification attempts are rate limited', function () {
    $user = User::factory()->unverified()->create();
    $this->actingAs($user);

    foreach (range(1, 5) as $attempt) {
        $this->post(route('verification.verify.code'), ['code' => '000000'])
            ->assertRedirect();
    }

    $this->post(route('verification.verify.code'), ['code' => '000000'])
        ->assertTooManyRequests();
});

test('email can be verified', function () {
    $user = User::factory()->unverified()->create();
    $team = $user->personalTeam();

    Event::fake();

    $verificationUrl = URL::temporarySignedRoute(
        'verification.verify',
        now()->addMinutes(60),
        ['id' => $user->id, 'hash' => sha1($user->email)],
    );

    $response = $this->actingAs($user)->get($verificationUrl);

    Event::assertDispatched(Verified::class);
    expect($user->fresh()->hasVerifiedEmail())->toBeTrue();

    $response->assertRedirect("/{$team->slug}/dashboard?verified=1");
});

test('email is not verified with invalid hash', function () {
    $user = User::factory()->unverified()->create();

    $verificationUrl = URL::temporarySignedRoute(
        'verification.verify',
        now()->addMinutes(60),
        ['id' => $user->id, 'hash' => sha1('wrong-email')],
    );

    $this->actingAs($user)->get($verificationUrl);

    expect($user->fresh()->hasVerifiedEmail())->toBeFalse();
});

test('already verified user visiting verification link is redirected without firing event again', function () {
    $user = User::factory()->create([
        'email_verified_at' => now(),
    ]);
    $team = $user->personalTeam();

    Event::fake();

    $verificationUrl = URL::temporarySignedRoute(
        'verification.verify',
        now()->addMinutes(60),
        ['id' => $user->id, 'hash' => sha1($user->email)],
    );

    $this->actingAs($user)->get($verificationUrl)
        ->assertRedirect("/{$team->slug}/dashboard?verified=1");

    expect($user->fresh()->hasVerifiedEmail())->toBeTrue();
    Event::assertNotDispatched(Verified::class);
});
