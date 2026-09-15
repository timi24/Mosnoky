<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\DeliveryController;
use App\Http\Controllers\Admin\MessageController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\ReviewController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Middleware\EnsureUserIsAdmin;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'verified', EnsureUserIsAdmin::class])
    ->group(function () {
        Route::get('/', AdminDashboardController::class)->name('dashboard');

        Route::resource('categories', CategoryController::class)->except('show');
        Route::resource('products', ProductController::class)->except('show');

        Route::delete('products/{product}/images/{image}', [ProductController::class, 'destroyImage'])
            ->name('products.images.destroy');
        Route::patch('products/{product}/images/{image}/primary', [ProductController::class, 'makeImagePrimary'])
            ->name('products.images.make-primary');
        Route::patch('products/{product}/images/{image}/color', [ProductController::class, 'updateImageColor'])
            ->name('products.images.update-color');

        Route::resource('orders', OrderController::class)->only(['index', 'show']);
        Route::patch('orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.update-status');

        Route::resource('clients', ClientController::class)->only(['index', 'show']);

        Route::get('deliveries', [DeliveryController::class, 'index'])->name('deliveries.index');
        Route::get('deliveries/{order}/edit', [DeliveryController::class, 'edit'])->name('deliveries.edit');
        Route::put('deliveries/{order}', [DeliveryController::class, 'update'])->name('deliveries.update');

        Route::get('reviews', [ReviewController::class, 'index'])->name('reviews.index');
        Route::patch('reviews/{review}/approve', [ReviewController::class, 'approve'])->name('reviews.approve');
        Route::patch('reviews/{review}/hide', [ReviewController::class, 'hide'])->name('reviews.hide');

        Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
        Route::patch('reports/{report}/status', [ReportController::class, 'updateStatus'])->name('reports.update-status');

        Route::get('messages', [MessageController::class, 'index'])->name('messages.index');
        Route::get('messages/{client}', [MessageController::class, 'show'])->name('messages.show');
        Route::post('messages/{client}', [MessageController::class, 'store'])->name('messages.store');

        Route::get('notifications', [NotificationController::class, 'index'])->name('notifications.index');
    });
