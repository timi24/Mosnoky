<?php

namespace App\Providers;

use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);

        $this->configureDefaults();
        if ($this->app->environment('production')) {
    \Illuminate\Support\Facades\URL::forceScheme('https');
}
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        // Règles de mot de passe strictes, appliquées dans TOUS les environnements
        Password::defaults(fn (): Password => Password::min(10)
            ->mixedCase()      // au moins une majuscule et une minuscule
            ->letters()        // au moins une lettre
            ->numbers()        // au moins un chiffre
            ->symbols()        // au moins un symbole (!@#$...)
            ->uncompromised()  // refuse les mots de passe déjà fuités publiquement
        );
    }
}
