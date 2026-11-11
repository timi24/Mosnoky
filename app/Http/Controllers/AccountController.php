<?php

namespace App\Http\Controllers;

use App\Models\Address;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class AccountController extends Controller
{
    public function show(): View
    {
        $user = Auth::user();

        // Vérification du rôle dans la table users
        $isAdmin = strtoupper(trim($user->role ?? '')) === 'ADMINISTRATEUR';

        return view('account.show', [
            'user' => $user,
            'deliveryAddress' => Address::query()
                ->where('user_id', $user->id)
                ->where('address_type', 'LIVRAISON')
                ->where('is_default', true)
                ->first(),
            'isAdmin' => $isAdmin,
            'layout' => $isAdmin
                ? 'layouts.admin'
                : 'layouts.client',
        ]);
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validateWithBag('profile', [
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'regex:/^\\+?[0-9 ]{8,15}$/', Rule::unique('users', 'phone')->ignore($user->id)],
        ], [
            'name.required' => 'Indiquez votre nom complet.',
            'phone.regex' => 'Le numéro de téléphone est invalide.',
            'phone.unique' => 'Ce numéro de téléphone est déjà utilisé.',
        ]);

        $nameParts = preg_split('/\\s+/', trim($validated['name']), 2) ?: [];

        $user->forceFill([
            'name' => trim($validated['name']),
            'first_name' => $nameParts[0] ?? null,
            'last_name' => $nameParts[1] ?? null,
            'phone' => isset($validated['phone']) ? trim($validated['phone']) : null,
        ])->save();

        return redirect()->route('account.show')->with('account_status', 'Vos coordonnées ont été mises à jour.');
    }

    public function updateDeliveryAddress(Request $request): RedirectResponse
    {
        abort_if(strtoupper(trim($request->user()->role ?? '')) === 'ADMINISTRATEUR', 403);

        $validated = $request->validateWithBag('address', [
            'street' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:100'],
            'country' => ['required', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:20'],
        ], [
            'street.required' => 'Indiquez votre adresse de livraison.',
            'city.required' => 'Indiquez votre ville.',
            'country.required' => 'Indiquez votre pays.',
        ]);

        DB::transaction(function () use ($request, $validated) {
            Address::query()
                ->where('user_id', $request->user()->id)
                ->where('address_type', 'LIVRAISON')
                ->update(['is_default' => false]);

            $address = Address::query()->firstOrNew([
                'user_id' => $request->user()->id,
                'address_type' => 'LIVRAISON',
                'is_default' => false,
            ]);

            $address->fill($validated);
            $address->is_default = true;
            $address->save();
        });

        return redirect()->route('account.show')->with('account_status', 'Votre adresse de livraison a été enregistrée.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validateWithBag('password', [
            'current_password' => ['required', 'current_password'],
            'password' => [
                'required',
                'confirmed',
                'different:current_password',
                Password::defaults(),
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
