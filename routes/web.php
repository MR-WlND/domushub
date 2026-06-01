<?php

use App\Http\Controllers\AdminApartmentMemberController;
use App\Http\Controllers\ApartmentMemberController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\InvitationController;

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
Route::get('/member/register', [AuthController::class, 'showMemberRegister'])->name('member.register');
Route::post('/member/register', [AuthController::class, 'registerMember'])->name('member.register.submit');
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

    // Quản lý hạ tầng
    Route::get('/admin/buildings', function () {
        return view('admin.dashboard.index');
    })->name('admin.buildings.index');

    Route::get('/admin/apartments', function () {
        return view('admin.dashboard.index');
    })->name('admin.apartments.index');


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

    Route::get('/resident/members', [ApartmentMemberController::class, 'index'])->name('resident.members.index');
    Route::post('/resident/members', [ApartmentMemberController::class, 'store'])->name('resident.members.store');
    Route::delete('/resident/members/{member}', [ApartmentMemberController::class, 'destroy'])->name('resident.members.destroy');
    Route::post('/resident/members/{member}/invite', [ApartmentMemberController::class, 'invite'])->name('resident.members.invite');
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

    // Quản lý mã mời (Invitations)
    Route::get('/admin/invitations', [InvitationController::class, 'index'])->name('invitations.index');
    Route::post('/admin/invitations', [InvitationController::class, 'store'])->name('invitations.store');
    Route::delete('/admin/invitations/{id}', [InvitationController::class, 'destroy'])->name('invitations.destroy');

    Route::get('/admin/apartment-members', [AdminApartmentMemberController::class, 'index'])->name('apartment-members.index');
    Route::put('/admin/apartment-members/{member}/verify', [AdminApartmentMemberController::class, 'verify'])->name('apartment-members.verify');
    Route::put('/admin/apartment-members/{member}/reject', [AdminApartmentMemberController::class, 'reject'])->name('apartment-members.reject');
});
