<?php

use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PaymentHistoryController;
use App\Http\Controllers\ReceiptController;
use Illuminate\Support\Facades\Route;

// Le webhook doit rester accessible sans authentification (appelé par les serveurs CinetPay)
Route::post('/paiement/cinetpay/notify', [PaymentController::class, 'notify'])->name('payments.cinetpay.notify');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::post('/commandes/{order}/paiement/cinetpay', [PaymentController::class, 'initiate'])->name('payments.cinetpay.initiate');
    Route::get('/paiement/cinetpay/retour/{order}', [PaymentController::class, 'returnPage'])->name('payments.cinetpay.return');

    Route::get('/paiements', [PaymentHistoryController::class, 'index'])->name('payments.index');
    Route::get('/commandes/{order}/recu', [ReceiptController::class, 'download'])->name('receipts.download');
});
