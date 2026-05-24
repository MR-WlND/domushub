<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

// Root route - redirect to resident login
Route::get('/', function () {
    return view('auth.resident.login');
});

// Admin Login Routes
Route::get('/admin/login', [AuthController::class, 'showAdminLogin'])->name('admin.login');
Route::post('/admin/login', [AuthController::class, 'loginAdmin'])->name('admin.login.submit');

// Resident Login Routes
Route::get('/resident/login', [AuthController::class, 'showResidentLogin'])->name('resident.login');
Route::post('/resident/login', [AuthController::class, 'loginResident'])->name('resident.login.submit');

// Security Login Routes
Route::get('/security/login', [AuthController::class, 'showSecurityLogin'])->name('security.login');
Route::post('/security/login', [AuthController::class, 'loginSecurity'])->name('security.login.submit');

// Logout (accessible from all roles)
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Dashboard Routes
Route::middleware(['admin'])->group(function () {
    Route::get('/admin/dashboard', fn () => 'Admin dashboard')->name('admin.dashboard');
});

Route::middleware(['security'])->group(function () {
    Route::get('/security/dashboard', fn () => 'Security dashboard')->name('security.dashboard');
});

Route::middleware(['resident'])->group(function () {
    Route::get('/resident/dashboard', fn () => 'Resident dashboard')->name('resident.dashboard');
});

Route::get('/admin', function () {
    return view('layouts.admin.master');
});

Route::get('/resident', function () {
    return view('layouts.resident.master');
});

Route::get('/security', function () {
    return view('layouts.security.master');
});