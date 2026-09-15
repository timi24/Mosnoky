<x-layouts::auth :title="__('Créer un compte')">
    <div class="mosnoky-register">
        <aside class="mosnoky-register__intro">
            <div class="mosnoky-register__eyebrow">{{ __('Bienvenue chez Mosnoky') }}</div>
            <img src="{{ asset('images/mosnoky-logo.jpg') }}" alt="Mosnoky" class="mosnoky-register__logo">
            <h1>{{ __('Votre sélection commence ici.') }}</h1>
            <p>{{ __('Créez votre compte pour retrouver vos commandes, suivre vos achats et profiter d’une expérience pensée pour vous.') }}</p>
            <div class="mosnoky-register__proofs">
                <div><strong>01</strong><span>{{ __('Une boutique claire et soignée') }}</span></div>
                <div><strong>02</strong><span>{{ __('Des produits choisis avec attention') }}</span></div>
                <div><strong>03</strong><span>{{ __('Un espace client simple à utiliser') }}</span></div>
            </div>
        </aside>

        <div class="mosnoky-register__form flex flex-col gap-6">
            <div class="mosnoky-register__mobile-logo">
                <img src="{{ asset('images/mosnoky-logo.jpg') }}" alt="Mosnoky">
            </div>
            <x-auth-header :title="__('Créer un compte')" :description="__('Renseignez vos informations pour créer votre compte.')" />

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        @if ($teamInvitation)
            <x-team-invitation-alert :invitation="$teamInvitation" :action="__('Inscrivez-vous')" />
        @endif

        <form method="POST" action="{{ route('register.store') }}" class="flex flex-col gap-6">
            @csrf
            <!-- Name -->
            <flux:input
                name="name"
                :label="__('Nom complet')"
                :value="old('name')"
                type="text"
                required
                autofocus
                autocomplete="name"
                :placeholder="__('Votre nom complet')"
            />

            <!-- Email Address -->
            <flux:input
                name="email"
                :label="__('Adresse e-mail')"
                :value="old('email')"
                type="email"
                required
                autocomplete="email"
                placeholder="email@example.com"
            />

            <!-- Password -->
            <flux:input
                name="password"
                :label="__('Mot de passe')"
                type="password"
                required
                autocomplete="new-password"
                :placeholder="__('Mot de passe')"
                passwordrules="{{ \Illuminate\Validation\Rules\Password::defaults()->toPasswordRulesString() }}"
                viewable
            />

            <!-- Confirm Password -->
            <flux:input
                name="password_confirmation"
                :label="__('Confirmer le mot de passe')"
                type="password"
                required
                autocomplete="new-password"
                :placeholder="__('Confirmer le mot de passe')"
                passwordrules="{{ \Illuminate\Validation\Rules\Password::defaults()->toPasswordRulesString() }}"
                viewable
            />

            <div class="flex items-center justify-end">
                <flux:button type="submit" variant="primary" class="w-full" data-test="register-user-button">
                    {{ __('Créer mon compte') }}
                </flux:button>
            </div>
        </form>

        <!-- Social Login Divider -->
        <div class="flex items-center gap-4">
            <div class="flex-1 border-t border-zinc-300 dark:border-zinc-600"></div>
            <span class="text-sm text-zinc-500 dark:text-zinc-400">{{ __('Ou continuer avec') }}</span>
            <div class="flex-1 border-t border-zinc-300 dark:border-zinc-600"></div>
        </div>

        <!-- Social Login Buttons -->
        <div class="flex gap-4">
            <a href="{{ route('socialite.redirect', 'google') }}" class="flex-1">
                <flux:button variant="subtle" class="w-full flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                        <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                        <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                        <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                    </svg>
                    <span>{{ __('Google') }}</span>
                </flux:button>
            </a>
            <a href="{{ route('socialite.redirect', 'facebook') }}" class="flex-1">
                <flux:button variant="subtle" class="w-full flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                    </svg>
                    <span>{{ __('Facebook') }}</span>
                </flux:button>
            </a>
        </div>

        <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-600 dark:text-zinc-400">
            <span>{{ __('Vous avez déjà un compte ?') }}</span>
            <flux:link
                :href="$teamInvitation ? route('login', ['invitation' => $teamInvitation['code']]) : route('login')"
                data-test="team-invitation-login-link"
                wire:navigate
            >
                {{ __('Se connecter') }}
            </flux:link>
        </div>
    </div>

    <style>
        .mosnoky-register { --mosnoky-red: #b52d39; --mosnoky-ink: #3e3e3e; --mosnoky-paper: #fffdfb; display: grid; grid-template-columns: minmax(250px, .85fr) minmax(340px, 1fr); gap: 56px; width: min(920px, 100%); margin-inline: auto; padding: 38px; border: 1px solid #eadfda; border-radius: 26px; background: var(--mosnoky-paper); box-shadow: 0 28px 80px rgba(62, 62, 62, .13); }
        .mosnoky-register__intro { display: flex; flex-direction: column; gap: 20px; padding: 12px 34px 12px 4px; border-right: 1px solid #eadfda; }
        .mosnoky-register__eyebrow { color: var(--mosnoky-red); font-size: .7rem; font-weight: 800; letter-spacing: .15em; text-transform: uppercase; }
        .mosnoky-register__logo { width: min(100%, 250px); height: auto; margin: 5px 0 10px; }
        .mosnoky-register__intro h1 { margin: 0; color: var(--mosnoky-ink); font-family: Georgia, serif; font-size: clamp(2.2rem, 4vw, 3.4rem); font-weight: 400; line-height: .98; letter-spacing: 0; }
        .mosnoky-register__intro p { margin: 0; color: #74716f; font-size: .95rem; line-height: 1.65; }
        .mosnoky-register__proofs { display: flex; flex-direction: column; gap: 14px; margin-top: auto; padding-top: 20px; }
        .mosnoky-register__proofs div { display: flex; align-items: center; gap: 12px; color: var(--mosnoky-ink); font-size: .82rem; }
        .mosnoky-register__proofs strong { color: var(--mosnoky-red); font-size: .7rem; letter-spacing: .08em; }
        .mosnoky-register__form { min-width: 0; }
        .mosnoky-register__form :where(input:focus) { border-color: var(--mosnoky-red); box-shadow: 0 0 0 3px rgba(181, 45, 57, .12); }
        .mosnoky-register__form :where(button[type="submit"]) { background: var(--mosnoky-red); }
        .mosnoky-register__form :where(button[type="submit"]:hover) { background: #96232e; }
        .mosnoky-register__form :where(a) { color: var(--mosnoky-red); }
        .mosnoky-register__mobile-logo { display: none; }
        @media (max-width: 760px) { .mosnoky-register { grid-template-columns: 1fr; gap: 28px; padding: 24px 20px; border-radius: 18px; } .mosnoky-register__intro { display: none; } .mosnoky-register__mobile-logo { display: block; text-align: center; } .mosnoky-register__mobile-logo img { width: 190px; height: auto; } }
    </style>
</x-layouts::auth>
