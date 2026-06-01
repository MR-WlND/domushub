<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

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
    Route::get('admin', [HomeController::class, 'index'])->name('home');

    // Quản lý hạ tầng - Buildings/Blocks
    Route::get('/admin/buildings', [\App\Http\Controllers\BlockController::class, 'index'])->name('admin.buildings.index');
    Route::get('/admin/buildings/create', [\App\Http\Controllers\BlockController::class, 'create'])->name('admin.buildings.create');
    Route::post('/admin/buildings', [\App\Http\Controllers\BlockController::class, 'store'])->name('admin.buildings.store');
    Route::get('/admin/buildings/{block}', [\App\Http\Controllers\BlockController::class, 'show'])->name('admin.buildings.show');
    Route::get('/admin/buildings/{block}/edit', [\App\Http\Controllers\BlockController::class, 'edit'])->name('admin.buildings.edit');
    Route::put('/admin/buildings/{block}', [\App\Http\Controllers\BlockController::class, 'update'])->name('admin.buildings.update');
    Route::delete('/admin/buildings/{block}', [\App\Http\Controllers\BlockController::class, 'destroy'])->name('admin.buildings.destroy');

    // Block/Building routes (used in views)
    Route::get('/admin/blocks', [\App\Http\Controllers\BlockController::class, 'index'])->name('admin.blocks.index');
    Route::get('/admin/blocks/create', [\App\Http\Controllers\BlockController::class, 'create'])->name('admin.blocks.create');
    Route::post('/admin/blocks', [\App\Http\Controllers\BlockController::class, 'store'])->name('admin.blocks.store');
    Route::get('/admin/blocks/{block}/edit', [\App\Http\Controllers\BlockController::class, 'edit'])->name('admin.blocks.edit');
    Route::put('/admin/blocks/{block}', [\App\Http\Controllers\BlockController::class, 'update'])->name('admin.blocks.update');
    Route::delete('/admin/blocks/{block}', [\App\Http\Controllers\BlockController::class, 'destroy'])->name('admin.blocks.destroy');

    // Floors (Tầng)
    Route::get('/admin/floors', [\App\Http\Controllers\FloorController::class, 'index'])->name('admin.floors.index');
    Route::get('/admin/floors/create', [\App\Http\Controllers\FloorController::class, 'create'])->name('admin.floors.create');
    Route::post('/admin/floors', [\App\Http\Controllers\FloorController::class, 'store'])->name('admin.floors.store');
    Route::get('/admin/floors/{floor}', [\App\Http\Controllers\FloorController::class, 'show'])->name('admin.floors.show');
    Route::get('/admin/floors/{floor}/edit', [\App\Http\Controllers\FloorController::class, 'edit'])->name('admin.floors.edit');
    Route::put('/admin/floors/{floor}', [\App\Http\Controllers\FloorController::class, 'update'])->name('admin.floors.update');
    Route::delete('/admin/floors/{floor}', [\App\Http\Controllers\FloorController::class, 'destroy'])->name('admin.floors.destroy');

    // Apartments (Căn hộ/Phòng)
    Route::get('/admin/apartments', [\App\Http\Controllers\ApartmentController::class, 'index'])->name('admin.apartments.index');
    Route::get('/admin/apartments/create', [\App\Http\Controllers\ApartmentController::class, 'create'])->name('admin.apartments.create');
    Route::post('/admin/apartments', [\App\Http\Controllers\ApartmentController::class, 'store'])->name('admin.apartments.store');
    Route::get('/admin/apartments/{apartment}', [\App\Http\Controllers\ApartmentController::class, 'show'])->name('admin.apartments.show');
    Route::get('/admin/apartments/{apartment}/edit', [\App\Http\Controllers\ApartmentController::class, 'edit'])->name('admin.apartments.edit');
    Route::put('/admin/apartments/{apartment}', [\App\Http\Controllers\ApartmentController::class, 'update'])->name('admin.apartments.update');
    Route::delete('/admin/apartments/{apartment}', [\App\Http\Controllers\ApartmentController::class, 'destroy'])->name('admin.apartments.destroy');

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

Route::middleware(['admin'])->name('admin.')->group(function () {
    // Route xem danh sách và lọc
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::get('/users/{id}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');

    // Route xử lý cập nhật trạng thái/vai trò
    Route::put('/users/{id}/update-status', [UserController::class, 'updateStatus'])->name('users.updateStatus');
    Route::put('/users/{id}/reset-password', [UserController::class, 'resetPassword'])->name('users.resetPassword');
});
