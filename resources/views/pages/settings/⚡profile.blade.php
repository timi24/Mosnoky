<?php

use App\Concerns\ProfileValidationRules;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Mon profil')] class extends Component {
    use ProfileValidationRules;

    public string $name = '';
    public string $email = '';

    public function mount(): void
    {
        $this->name = Auth::user()->name;
        $this->email = Auth::user()->email;
    }

    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $validated = $this->validate($this->profileRules($user->id));

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        Flux::toast(variant: 'success', text: __('Profil mis à jour.'));
    }

    public function resendVerificationNotification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false));

            return;
        }

        $user->sendEmailVerificationNotification();

        Session::flash('status', 'verification-link-sent');
    }

    #[Computed]
    public function hasUnverifiedEmail(): bool
    {
        return Auth::user() instanceof MustVerifyEmail && ! Auth::user()->hasVerifiedEmail();
    }

    #[Computed]
    public function showDeleteUser(): bool
    {
        return ! Auth::user() instanceof MustVerifyEmail
            || (Auth::user() instanceof MustVerifyEmail && Auth::user()->hasVerifiedEmail());
    }
}; ?>

<section class="w-full max-w-xl mx-auto py-10 px-4">
    <flux:heading size="xl" class="mb-1">{{ __('Mon profil') }}</flux:heading>
    <flux:text class="mb-8">{{ __('Modifiez votre nom et votre adresse email.') }}</flux:text>

    <form wire:submit="updateProfileInformation" class="space-y-6">
        <flux:input wire:model="name" :label="__('Nom')" type="text" required autofocus autocomplete="name" />

        <div>
            <flux:input wire:model="email" :label="__('Email')" type="email" required autocomplete="email" />

            @if ($this->hasUnverifiedEmail)
                <div>
                    <flux:text class="mt-4">
                        {{ __("Votre adresse email n'est pas vérifiée.") }}

                        <flux:link class="text-sm cursor-pointer" wire:click.prevent="resendVerificationNotification">
                            {{ __('Cliquez ici pour renvoyer le lien de vérification.') }}
                        </flux:link>
                    </flux:text>

                    @if (session('status') === 'verification-link-sent')
                        <flux:text class="mt-2 font-medium !text-green-600">
                            {{ __('Un nouveau lien de vérification a été envoyé.') }}
                        </flux:text>
                    @endif
                </div>
            @endif
        </div>

        <flux:button variant="primary" type="submit" class="w-full" data-test="update-profile-button">
            {{ __('Enregistrer') }}
        </flux:button>
    </form>

    @if ($this->showDeleteUser)
        <div class="mt-10 pt-8 border-t">
            <livewire:pages::settings.delete-user-form />
        </div>
    @endif
</section>