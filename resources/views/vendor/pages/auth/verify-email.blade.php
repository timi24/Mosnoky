<x-layouts::auth :title="__('Vérification de l\'email')">
    <div class="mt-4 flex flex-col gap-6">
        <flux:text class="text-center">
            {{ __('Nous avons envoyé un code à 6 chiffres à votre adresse email. Entrez-le ci-dessous pour activer votre compte.') }}
        </flux:text>

        @if (session('status') == 'verification-link-sent')
            <flux:text class="text-center font-medium !dark:text-green-400 !text-green-600">
                {{ __('Un nouveau code vient d\'être envoyé à votre adresse email.') }}
            </flux:text>
        @endif

        <form method="POST" action="{{ route('verification.verify.code') }}" class="flex flex-col gap-4">
            @csrf

            <flux:input
                name="code"
                type="text"
                inputmode="numeric"
                maxlength="6"
                autocomplete="one-time-code"
                autofocus
                placeholder="000000"
                class="text-center text-2xl tracking-[0.5em]"
            />
            @error('code')
                <flux:text class="text-center !text-red-600 !dark:text-red-400 text-sm">{{ $message }}</flux:text>
            @enderror

            <flux:button type="submit" variant="primary" class="w-full">
                {{ __('Vérifier mon compte') }}
            </flux:button>
        </form>

        <div class="flex flex-col items-center justify-between space-y-3">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <flux:button type="submit" variant="ghost" class="w-full">
                    {{ __('Renvoyer le code') }}
                </flux:button>
            </form>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <flux:button variant="ghost" type="submit" class="text-sm cursor-pointer" data-test="logout-button">
                    {{ __('Se déconnecter') }}
                </flux:button>
            </form>
        </div>
    </div>
</x-layouts::auth>
