<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialiteController
{
    public function redirect(string $provider): RedirectResponse
    {
        return Socialite::driver($provider)->redirect();
    }

    public function callback(Request $request, string $provider): RedirectResponse
    {
        try {
            $socialUser = Socialite::driver($provider)->user();
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', __('La connexion avec :provider a échoué. Réessayez.', ['provider' => ucfirst($provider)]));
        }

        $email = strtolower(trim((string) $socialUser->getEmail()));
        $emailIsVerified = filter_var($socialUser->user['email_verified'] ?? false, FILTER_VALIDATE_BOOL);

        if ($email === '' || ! $emailIsVerified) {
            return redirect()->route('login')->with('error', __('Le fournisseur ne confirme pas cette adresse e-mail. Vérifiez-la ou utilisez votre mot de passe.'));
        }

        $user = User::where('email', $email)->first();

        if (! $user) {
            // Create new user from social provider data
            $user = User::create([
                'name' => $socialUser->getName(),
                'email' => $email,
                'password' => bcrypt(Str::random(32)), // Random password for social users
                'email_verified_at' => now(), // Social logins are pre-verified
            ]);

            event(new Registered($user));
        }

        // Log the user in
        Auth::login($user, remember: true);
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard', $user->currentTeam));
    }
}
