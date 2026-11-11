<?php

use App\Http\Controllers\AccountController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::get('/mon-compte', [AccountController::class, 'show'])->name('account.show');
    Route::put('/mon-compte/mot-de-passe', [AccountController::class, 'updatePassword'])
        ->middleware('throttle:6,1')
        ->name('account.password');
});
