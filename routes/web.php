<?php

use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\Auth\SocialiteController;
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