<x-layouts::auth :title="__('Connexion')">
    <div class="mosnoky-login">
        <div class="mosnoky-login__brand">
            <a href="{{ route('home') }}" aria-label="{{ __('Retour à la boutique') }}">
                <img src="{{ asset('images/mosnoky-logo.jpg') }}" alt="Mosnoky" class="mosnoky-login__logo">
            </a>
            <p>{{ __('Des produits choisis pour accompagner votre quotidien.') }}</p>
        </div>

        <div class="mosnoky-login__form flex flex-col gap-6">
            <x-auth-header :title="__('Se connecter')" :description="__('Retrouvez votre espace client et vos commandes.')" />

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        @if ($teamInvitation)
            <x-team-invitation-alert :invitation="$teamInvitation" :action="__('Se connecter')" />
        @endif

        <x-passkey-verify />

        <form method="POST" action="{{ route('login.store') }}" class="flex flex-col gap-6">
            @csrf

            <!-- Email Address -->
            <flux:input
                name="email"
                :label="__('Adresse e-mail')"
                :value="old('email')"
                type="email"
                required
                autofocus
                autocomplete="email"
                placeholder="nom@exemple.com"
            />

            <!-- Password -->
            <div class="relative">
                <flux:input
                    name="password"
                    :label="__('Mot de passe')"
                    type="password"
                    required
                    autocomplete="current-password"
                    :placeholder="__('Mot de passe')"
                    viewable
                />

                @if (Route::has('password.request'))
                    <flux:link class="absolute top-0 text-sm end-0" :href="route('password.request')" wire:navigate>
                        {{ __('Mot de passe oublié ?') }}
                    </flux:link>
                @endif
            </div>

            <!-- Remember Me -->
            <flux:checkbox name="remember" :label="__('Se souvenir')" :checked="old('remember')" />

            <div class="flex items-center justify-end">
                <flux:button variant="primary" type="submit" class="w-full" data-test="login-button">
                    {{ __('Se connecter') }}
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

        <div class="space-x-1 text-sm text-center rtl:space-x-reverse text-zinc-600 dark:text-zinc-400">
            <span>{{ __('Vous n\'avez pas encore de compte ?') }}</span>
            <flux:link
                :href="$teamInvitation ? route('register', ['invitation' => $teamInvitation['code']]) : route('register')"
                data-test="register-link"
                wire:navigate
            >
                {{ __('Créer un compte') }}
            </flux:link>
        </div>
    </div>

    <style>
        .mosnoky-login { --mosnoky-red: #b52d39; --mosnoky-ink: #3e3e3e; --mosnoky-paper: #fffdfb; display: grid; grid-template-columns: minmax(220px, .8fr) minmax(300px, 1fr); gap: 48px; align-items: center; width: min(820px, 100%); margin-inline: auto; padding: 34px; border: 1px solid #eadfda; border-radius: 24px; background: var(--mosnoky-paper); box-shadow: 0 24px 70px rgba(62, 62, 62, .12); }
        .mosnoky-login__brand { display: flex; flex-direction: column; gap: 18px; align-items: center; padding: 12px 20px 12px 0; border-right: 1px solid #eadfda; text-align: center; }
        .mosnoky-login__brand a { display: block; width: 100%; }
        .mosnoky-login__logo { display: block; width: min(100%, 260px); height: auto; margin: 0 auto; }
        .mosnoky-login__brand p { max-width: 220px; margin: 0; color: #74716f; font-size: .9rem; line-height: 1.6; }
        .mosnoky-login__form { min-width: 0; }
        .mosnoky-login__form :where(input:focus) { border-color: var(--mosnoky-red); box-shadow: 0 0 0 3px rgba(181, 45, 57, .12); }
        .mosnoky-login__form :where(button[type="submit"]) { background: var(--mosnoky-red); }
        .mosnoky-login__form :where(button[type="submit"]:hover) { background: #96232e; }
        .mosnoky-login__form :where(a) { color: var(--mosnoky-red); }
        @media (max-width: 760px) { .mosnoky-login { grid-template-columns: 1fr; gap: 28px; padding: 24px 20px; border-radius: 18px; } .mosnoky-login__brand { padding: 0 0 24px; border-right: 0; border-bottom: 1px solid #eadfda; } .mosnoky-login__logo { width: 210px; } }
    </style>
</x-layouts::auth>