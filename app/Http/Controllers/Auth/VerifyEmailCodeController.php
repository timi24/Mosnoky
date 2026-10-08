<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Auth\Events\Verified;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class VerifyEmailCodeController
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'size:6'],
        ]);

        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return redirect()->intended(route('dashboard'));
        }

        $codeMatches = $user->verification_code !== null
            && hash_equals($user->verification_code, $validated['code']);

        $notExpired = $user->verification_code_expires_at !== null
            && now()->lessThan($user->verification_code_expires_at);

        if (! $codeMatches || ! $notExpired) {
            return back()->withErrors([
                'code' => 'Ce code est incorrect ou a expiré. Demandez-en un nouveau ci-dessous.',
            ]);
        }

        $user->forceFill([
            'email_verified_at' => now(),
            'verification_code' => null,
            'verification_code_expires_at' => null,
        ])->save();

        event(new Verified($user));

        return redirect()->intended(route('dashboard'))
            ->with('status', 'Votre adresse email a été vérifiée avec succès.');
    }
}
