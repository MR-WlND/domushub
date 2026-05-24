<?php

use Illuminate\Support\Facades\Route;

// Auth views (tạm thời chưa có controller)
Route::get('/', fn() => view('auth.login'))->name('login');

Route::get('/register', fn() => view('auth.register'))->name('register');

Route::get('/forgot-password', fn() => view('auth.forgot-password'))->name('password.request');

Route::post('/forgot-password', function () {
    // TODO: xử lý gửi email reset password
    return back()->with('status', 'Nếu email tồn tại, chúng tôi đã gửi liên kết đặt lại mật khẩu.');
})->name('password.email');

