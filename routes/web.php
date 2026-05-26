<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

// Root route - redirect to resident login
Route::get('/', function () {
    return redirect()->route('resident.login');
});

// Admin Login Routes
Route::get('/admin/login', [AuthController::class, 'showAdminLogin'])->name('admin.login');
Route::post('/admin/login', [AuthController::class, 'loginAdmin'])->name('admin.login.submit');

// Resident Routes
Route::get('/resident/login', [AuthController::class, 'showResidentLogin'])->name('resident.login');
Route::post('/resident/login', [AuthController::class, 'loginResident'])->name('resident.login.submit');
Route::get('/resident/register', [AuthController::class, 'showResidentRegister'])->name('resident.register');
Route::post('/resident/register', [AuthController::class, 'registerResident'])->name('resident.register.submit');
Route::get('/resident/forgot-password', [AuthController::class, 'showForgotPassword'])->name('resident.forgot-password');
Route::post('/resident/forgot-password', [AuthController::class, 'sendResetCode'])->name('resident.forgot-password.submit');
Route::get('/resident/reset-password', [AuthController::class, 'showResetPassword'])->name('resident.reset-password');
Route::post('/resident/reset-password', [AuthController::class, 'resetPassword'])->name('resident.reset-password.submit');

// Security Login Routes
Route::get('/security/login', [AuthController::class, 'showSecurityLogin'])->name('security.login');
Route::post('/security/login', [AuthController::class, 'loginSecurity'])->name('security.login.submit');

// Logout (accessible from all roles)
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Dashboard Routes
Route::middleware(['admin'])->group(function () {
    Route::get('/admin/dashboard', function () {
        return view('admin.dashboard.index');
    })->name('admin.dashboard');

    // Quản lý hạ tầng
    Route::get('/admin/buildings', function () {
        return view('admin.dashboard.index');
    })->name('admin.buildings.index');

    Route::get('/admin/apartments', function () {
        return view('admin.dashboard.index');
    })->name('admin.apartments.index');

    Route::get('/admin/invitations', function () {
        return view('admin.dashboard.index');
    })->name('admin.invitations.index');

    // Điện nước & hoá đơn
    Route::get('/admin/utility-readings', function () {
        return view('admin.dashboard.index');
    })->name('admin.utility-readings.index');

    Route::get('/admin/service-prices', function () {
        return view('admin.dashboard.index');
    })->name('admin.service-prices.index');

    Route::get('/admin/invoices', function () {
        return view('admin.dashboard.index');
    })->name('admin.invoices.index');

    // Dịch vụ cư dân
    Route::get('/admin/residents', function () {
        return view('admin.dashboard.index');
    })->name('admin.residents.index');

    Route::get('/admin/vehicles', function () {
        return view('admin.dashboard.index');
    })->name('admin.vehicles.index');

    Route::get('/admin/incidents', function () {
        return view('admin.dashboard.index');
    })->name('admin.incidents.index');

    Route::get('/admin/amenities', function () {
        return view('admin.dashboard.index');
    })->name('admin.amenities.index');

    // Tương tác & bảng tin
    Route::get('/admin/announcements', function () {
        return view('admin.dashboard.index');
    })->name('admin.announcements.index');

    // Cấu hình hệ thống
    Route::get('/admin/roles', function () {
        return view('admin.dashboard.index');
    })->name('admin.roles.index');

    Route::get('/admin/activity-logs', function () {
        return view('admin.dashboard.index');
    })->name('admin.activity-logs.index');
});

Route::middleware(['security'])->group(function () {
    Route::get('/security/dashboard', function () {
        return view('security.dashboard.index');
    })->name('security.dashboard');

    Route::get('/security/vehicle-checkin', function () {
        return view('security.vehicle-checkin.index');
    })->name('security.vehicle-checkin.index');

    Route::get('/security/vehicle-checkout', function () {
        return view('security.vehicle-checkout.index');
    })->name('security.vehicle-checkout.index');

    Route::get('/security/visitor-check', function () {
        return view('security.visitor-check.index');
    })->name('security.visitor-check.index');
});


Route::middleware(['resident'])->group(function () {
    Route::get('/resident/dashboard', function () {
        return view('resident.home.index');
    })->name('resident.dashboard');
});
