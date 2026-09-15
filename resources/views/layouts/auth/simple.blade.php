<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-[#f7f5ef] antialiased">
        <div class="auth-shell bg-background flex min-h-svh flex-col items-center justify-center gap-6 p-6 md:p-10">
            <div class="flex w-full max-w-5xl flex-col gap-2">
                <a href="{{ route('home') }}" class="auth-shell__brand flex flex-col items-center gap-2 font-medium" wire:navigate>
                    <img src="{{ asset('images/mosnoky-logo.jpg') }}" alt="Mosnoky" class="h-auto w-44">
                </a>
                <div class="flex flex-col gap-6">
                    {{ $slot }}
                </div>
            </div>
        </div>

        <style>
            .auth-shell {
                background-color: #f7f5ef;
                background-image: radial-gradient(circle at 15% 15%, rgba(181, 45, 57, .08), transparent 24rem), radial-gradient(circle at 85% 85%, rgba(62, 62, 62, .07), transparent 28rem);
            }

            .auth-shell:has(.mosnoky-login, .mosnoky-register) .auth-shell__brand {
                display: none;
            }

            @media (max-width: 760px) {
                .auth-shell:has(.mosnoky-login, .mosnoky-register) {
                    justify-content: flex-start;
                }
            }
        </style>

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
