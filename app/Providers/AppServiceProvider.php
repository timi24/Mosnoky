<?php

namespace App\Providers;

use App\Models\Administrator;
use App\Models\AppNotification;
use App\Models\Message;
use App\Models\Payment;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
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

        $this->shareUnreadCounters();
    }

    /**
     * Compteurs "non lus" pour les menus (client ET admin), calculés sur
     * toutes les pages : l'icône apparaît dès qu'un message arrive, quelle
     * que soit la page où se trouve la personne.
     */
    protected function shareUnreadCounters(): void
    {
        View::composer('layouts.*', function ($view) {
            if (! Auth::check()) {
                return;
            }

            $userId = Auth::id();
            $adminIds = Administrator::pluck('user_id');

            if ($adminIds->contains($userId)) {
                // Admin : messages envoyés par des clients et pas encore lus
                $view->with('adminUnreadMessageCount',
                    Message::whereNotIn('sender_id', $adminIds)->whereNull('read_at')->count()
                );
                // Paiements Orange/Moov déclarés par des clients et à vérifier
                $view->with('adminPendingPaymentCount',
                    Payment::where('status', 'EN_ATTENTE')->whereNotNull('submitted_at')->count()
                );
            } else {
                // Client : messages de l'équipe pas encore lus + notifications non lues
                $view->with('unreadMessageCount',
                    Message::whereIn('sender_id', $adminIds)
                        ->where('receiver_id', $userId)
                        ->whereNull('read_at')
                        ->count()
                );
                $view->with('unreadNotificationCount',
                    AppNotification::where('user_id', $userId)->where('is_read', false)->count()
                );
            }
        });
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
