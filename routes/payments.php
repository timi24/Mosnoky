<?php

use App\Http\Controllers\AdminPaymentController;
use App\Http\Controllers\ManualPaymentController;
use App\Http\Controllers\PaymentHistoryController;
use App\Http\Controllers\ReceiptController;
use App\Http\Middleware\EnsureUserIsAdmin;
use Illuminate\Support\Facades\Route;

// ---------------------------------------------------------------------------
// CinetPay est mis de côté pour l'instant (pas de compte marchand).
// Pour le réactiver plus tard : remettre les routes PaymentController
// (initiate, return, notify) à la place du paiement manuel ci-dessous.
// ---------------------------------------------------------------------------

Route::middleware(['auth', 'verified'])->group(function () {
    // Même nom de route qu'avant : le bouton "Payer" de la page commande
    // continue de fonctionner sans rien modifier, il mène maintenant à la
    // page de paiement Orange Money / Moov Money.
    Route::post('/commandes/{order}/paiement/cinetpay', [ManualPaymentController::class, 'start'])->name('payments.cinetpay.initiate');

    Route::get('/commandes/{order}/paiement', [ManualPaymentController::class, 'show'])->name('payments.manual.show');
    Route::post('/commandes/{order}/paiement/reference', [ManualPaymentController::class, 'submit'])
        ->middleware('throttle:10,1')
        ->name('payments.manual.submit');

    Route::get('/paiements', [PaymentHistoryController::class, 'index'])->name('payments.index');
    Route::get('/commandes/{order}/recu', [ReceiptController::class, 'download'])->name('receipts.download');
});

// Espace administrateur : vérifier et confirmer les paiements déclarés
Route::middleware(['auth', EnsureUserIsAdmin::class])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/paiements', [AdminPaymentController::class, 'index'])->name('payments.index');
    Route::get('/paiements/alerte', [AdminPaymentController::class, 'alert'])->name('payments.alert');
    Route::post('/paiements/{payment}/confirmer', [AdminPaymentController::class, 'confirm'])->name('payments.confirm');
    Route::post('/paiements/{payment}/rejeter', [AdminPaymentController::class, 'reject'])->name('payments.reject');
});
