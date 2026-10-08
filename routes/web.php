<?php

use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\Auth\SocialiteController;
use App\Http\Controllers\Auth\VerifyEmailCodeController;
use App\Http\Controllers\ClientDashboardController;
use App\Http\Controllers\StorefrontController;
use App\Http\Middleware\EnsureUserIsAdmin;
use Illuminate\Support\Facades\Route;

Route::get('/', [StorefrontController::class, 'index'])->name('home');

Route::get('/produits/{product}/acheter', [StorefrontController::class, 'purchase'])
    ->middleware('auth')
    ->name('products.purchase');

Route::get('/auth/{provider}', [SocialiteController::class, 'redirect'])
    ->where('provider', 'google|facebook')
    ->name('socialite.redirect');

// Route de retour : c'est ici que Google/Facebook renvoient l'utilisateur
// une fois la connexion validée. Sans cette route, on tombe sur une 404.
Route::get('/auth/{provider}/callback', [SocialiteController::class, 'callback'])
    ->where('provider', 'google|facebook')
    ->name('socialite.callback');

// Page de vérification du compte par code à 6 chiffres reçu par email.
Route::post('/email/verify/code', [VerifyEmailCodeController::class, 'store'])
    ->middleware('auth')
    ->name('verification.verify.code');

require __DIR__.'/admin.php';
require __DIR__.'/client.php';
Route::get('/client/dashboard', ClientDashboardController::class)
    ->middleware(['auth', 'verified'])
    ->name('client.dashboard');

// Route générique utilisée par le menu de navigation, redirige selon le rôle
Route::get('/dashboard', function () {
    $user = request()->user();

    return $user?->role === 'ADMINISTRATEUR'
        ? redirect()->route('admin.dashboard')
        : redirect()->route('client.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

require __DIR__.'/settings.php';

require __DIR__.'/payments.php';
