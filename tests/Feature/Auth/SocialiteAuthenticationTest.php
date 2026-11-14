<?php

use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;

test('social login rejects an email the provider has not verified', function () {
    Socialite::fake('google', SocialiteUser::fake([
        'email' => 'client@example.com',
        'email_verified' => false,
    ]));

    $this->get(route('socialite.callback', 'google'))
        ->assertRedirect(route('login'))
        ->assertSessionHas('error');

    $this->assertGuest();
});
