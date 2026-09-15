<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\SearchController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    // Recherche
    Route::get('/recherche', [SearchController::class, 'index'])->name('search.index');

    // Catégories & produits
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::get('/categories/{category}', [CategoryController::class, 'show'])->name('categories.show');
    Route::get('/produits/{product}', [ProductController::class, 'show'])->name('products.show');

    // Panier
    Route::get('/panier', [CartController::class, 'index'])->name('cart.index');
    Route::post('/produits/{product}/panier', [CartController::class, 'store'])->name('cart.store');
    Route::put('/panier/{cartItem}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/panier/{cartItem}', [CartController::class, 'destroy'])->name('cart.destroy');

    // Commandes
    Route::get('/commandes', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/commandes/nouvelle', [OrderController::class, 'create'])->name('orders.create');
    Route::post('/commandes', [OrderController::class, 'store'])->name('orders.store');
    Route::get('/commandes/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::patch('/commandes/{order}/annuler', [OrderController::class, 'cancel'])->name('orders.cancel');

    // Avis
    Route::post('/commandes/{order}/produits/{product}/avis', [ReviewController::class, 'store'])->name('reviews.store');
    Route::post('/produits/{product}/avis', [ReviewController::class, 'storeForProduct'])->name('reviews.store-product');

    // Signalements
    Route::get('/signalements', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/signalements/nouveau', [ReportController::class, 'create'])->name('reports.create');
    Route::post('/signalements', [ReportController::class, 'store'])->name('reports.store');

    // Messagerie avec la boutique
    Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
    Route::post('/messages', [MessageController::class, 'store'])->name('messages.store');

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
});
