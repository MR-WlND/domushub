<?php

use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\InvoiceStatController;
use Illuminate\Support\Facades\Route;

// Auth views (tạm thời chưa có controller)
Route::get('/', fn() => view('auth.login'))->name('login');

Route::get('/register', fn() => view('auth.register'))->name('register');

Route::get('/forgot-password', fn() => view('auth.forgot-password'))->name('password.request');

Route::post('/forgot-password', function () {
    // TODO: xử lý gửi email reset password
    return back()->with('status', 'Nếu email tồn tại, chúng tôi đã gửi liên kết đặt lại mật khẩu.');
})->name('password.email');

// ── Admin Routes ──────────────────────────────────────────────────────────────
Route::prefix('admin')->name('admin.')->group(function () {

    // Dashboard thống kê hóa đơn
    Route::get('/invoices/stats', [InvoiceStatController::class, 'index'])
         ->name('invoices.stats');

    // Đánh dấu thanh toán (trước resource để không bị override)
    Route::patch('/invoices/{invoice}/mark-paid', [InvoiceController::class, 'markAsPaid'])
         ->name('invoices.mark-paid');

    // CRUD hóa đơn
    Route::resource('invoices', InvoiceController::class)
         ->only(['index', 'create', 'store', 'show']);
});
