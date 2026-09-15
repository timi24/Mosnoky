<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialiteController
{
    public function redirect($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    public function callback($provider)
    {
        try {
            $socialUser = Socialite::driver($provider)->user();
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', __('Unable to login via :provider', ['provider' => ucfirst($provider)]));
        }

        // Find or create user by provider and provider ID
        $user = User::where('email', $socialUser->getEmail())->first();

        if (! $user) {
            // Create new user from social provider data
            $user = User::create([
                'name' => $socialUser->getName(),
                'email' => $socialUser->getEmail(),
                'password' => bcrypt(Str::random(32)), // Random password for social users
                'email_verified_at' => now(), // Social logins are pre-verified
            ]);

            event(new Registered($user));
        }

        // Log the user in
        Auth::login($user, remember: true);

        return redirect()->intended(route('dashboard', $user->currentTeam));
    }
}
