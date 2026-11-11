<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\LiveController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::get('/mon-compte', [AccountController::class, 'show'])->name('account.show');
    Route::put('/mon-compte/coordonnees', [AccountController::class, 'updateProfile'])
        ->name('account.profile');
    Route::put('/mon-compte/adresse-livraison', [AccountController::class, 'updateDeliveryAddress'])
        ->name('account.address');
    Route::put('/mon-compte/mot-de-passe', [AccountController::class, 'updatePassword'])
        ->middleware('throttle:6,1')
        ->name('account.password');
});

// Les anciennes pages "Paramètres / Équipes" (modèle Laravel, pas au design Mosnoky)
// renvoient maintenant vers la page "Mon compte". Ce fichier doit être chargé
// AVANT settings.php dans routes/web.php pour que ces redirections soient prioritaires.
Route::redirect('/settings', '/mon-compte');
Route::get('/settings/{any}', fn () => redirect()->route('account.show'))->where('any', '.*');
Route::get('/teams/{any}', fn () => redirect()->route('account.show'))->where('any', '.*');

// Mise à jour en direct des compteurs et des notifications
Route::middleware('auth')->prefix('live')->group(function () {
    Route::get('/counts', [LiveController::class, 'counts'])->name('live.counts');
    Route::get('/notifications/{id}/open', [LiveController::class, 'openNotification'])->whereNumber('id')->name('live.notifications.open');
    Route::post('/notifications/read', [LiveController::class, 'readAllNotifications'])->name('live.notifications.read');
});
