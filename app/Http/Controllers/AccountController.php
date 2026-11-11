
<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class AccountController extends Controller
{
    /** Page Mon compte */
    public function show(): View
    {
        $user = Auth::user();

        // Vérification du rôle directement dans la table users
        $isAdmin = strtoupper(trim($user->role ?? '')) === 'ADMINISTRATEUR';

        return view('account.show', [
            'user' => $user,
            'isAdmin' => $isAdmin,
            'layout' => $isAdmin
                ? 'layouts.admin'
                : 'layouts.client',
        ]);
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validateWithBag('password', [
            'current_password' => ['required', 'current_password'],
            'password' => [
                'required',
                'confirmed',
                'different:current_password',
                Password::defaults()
            ],
        ], [
            'current_password.required' => 'Saisissez votre mot de passe actuel.',
            'current_password.current_password' => 'Le mot de passe actuel est incorrect.',
            'password.required' => 'Saisissez le nouveau mot de passe.',
            'password.confirmed' => 'La confirmation ne correspond pas au nouveau mot de passe.',
            'password.different' => 'Le nouveau mot de passe doit être différent de l\'ancien.',
            'password.min' => 'Le mot de passe doit contenir au moins :min caractères.',
            'password.mixed' => 'Utilisez au moins une majuscule et une minuscule.',
            'password.letters' => 'Utilisez au moins une lettre.',
            'password.numbers' => 'Utilisez au moins un chiffre.',
            'password.symbols' => 'Utilisez au moins un symbole (! @ # $ …).',
            'password.uncompromised' => 'Ce mot de passe est apparu dans une fuite de données : choisissez-en un autre.',
        ]);

        $request->user()->forceFill([
            'password' => Hash::make($validated['password']),
        ])->save();

        return redirect()->route('account.show')
            ->with('account_status', 'Votre mot de passe a bien été modifié.');
    }
}
