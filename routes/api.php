<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\SepayWebhookController;
use App\Http\Controllers\SepayController;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/payments/gateways',      [PaymentController::class, 'availableGateways'])->name('api.payments.gateways');
    Route::post('/payments/generate-qr',  [PaymentController::class, 'generateQR'])->name('api.payments.generate-qr');
    Route::post('/payments/check-status', [PaymentController::class, 'checkStatus'])->name('api.payments.check-status');
    Route::get('/payments/history',       [PaymentController::class, 'history'])->name('api.payments.history');
    Route::post('/payments/refund',       [PaymentController::class, 'refund'])->name('api.payments.refund');
});

// Sepay webhook — không cần auth, Sepay tự gửi API key trong header
Route::post('/payments/webhook/sepay', [SepayWebhookController::class, 'handle'])->name('api.payments.webhook.sepay');
Route::post('/sepay/webhook', [SepayController::class, 'webhook']);