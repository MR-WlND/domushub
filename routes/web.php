<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\SepayController;
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

// Manager Login Routes
Route::get('/manager/login', [AuthController::class, 'showManagerLogin'])->name('manager.login');
Route::post('/manager/login', [AuthController::class, 'loginManager'])->name('manager.login.submit');

// Technician Login Routes
Route::get('/technician/login', [AuthController::class, 'showTechnicianLogin'])->name('technician.login');
Route::post('/technician/login', [AuthController::class, 'loginTechnician'])->name('technician.login.submit');

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

    // Notifications
    Route::get('/admin/notifications/recent', [\App\Http\Controllers\Admin\NotificationController::class, 'recent'])->name('admin.notifications.recent');
    Route::post('/admin/notifications/mark-read', [\App\Http\Controllers\Admin\NotificationController::class, 'markRead'])->name('admin.notifications.mark-read');

    // Block/Building routes (used in views)
    Route::get('/admin/blocks', [\App\Http\Controllers\Admin\BlockController::class, 'index'])->name('admin.blocks.index');
    Route::get('/admin/blocks/create', [\App\Http\Controllers\Admin\BlockController::class, 'create'])->name('admin.blocks.create');
    Route::post('/admin/blocks', [\App\Http\Controllers\Admin\BlockController::class, 'store'])->name('admin.blocks.store');
    Route::get('/admin/blocks/{block}', [\App\Http\Controllers\Admin\BlockController::class, 'show'])->name('admin.blocks.show');
    Route::get('/admin/blocks/{block}/edit', [\App\Http\Controllers\Admin\BlockController::class, 'edit'])->name('admin.blocks.edit');
    Route::put('/admin/blocks/{block}', [\App\Http\Controllers\Admin\BlockController::class, 'update'])->name('admin.blocks.update');
    Route::delete('/admin/blocks/{block}', [\App\Http\Controllers\Admin\BlockController::class, 'destroy'])->name('admin.blocks.destroy');

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
    // Profile
    Route::get('/resident/profile', [ProfileController::class, 'index'])->name('resident.profile.index');
    Route::put('/resident/profile', [ProfileController::class, 'update'])->name('resident.profile.update');

    // Hoá đơn cư dân
    Route::get('/resident/invoices', [ResidentInvoiceController::class, 'index'])->name('resident.invoices.index');
    Route::get('/resident/invoices/history', [ResidentInvoiceController::class, 'history'])->name('resident.invoices.history');
    Route::get('/resident/invoices/{id}/payment-gateways', [ResidentInvoiceController::class, 'showPaymentGateways'])->name('resident.invoices.payment-gateways');
    Route::post('/resident/invoices/{id}/generate-qr', [ResidentInvoiceController::class, 'generateQR'])->name('resident.invoices.generate-qr');
    Route::post('/resident/invoices/{id}/check-payment', [ResidentInvoiceController::class, 'checkPayment'])->name('resident.invoices.check-payment');
    Route::post('/resident/invoices/{id}/pay', [ResidentInvoiceController::class, 'pay'])->name('resident.invoices.pay');

    // Quản lý thành viên gia đình & nhân khẩu & mã mời
    Route::get('/resident/members', [\App\Http\Controllers\Resident\MemberController::class, 'index'])->name('resident.members.index');
    Route::post('/resident/members/declared', [\App\Http\Controllers\Resident\MemberController::class, 'storeDeclared'])->name('resident.members.declared.store');
    Route::delete('/resident/members/declared/{member}', [\App\Http\Controllers\Resident\MemberController::class, 'destroyDeclared'])->name('resident.members.declared.destroy');
    Route::post('/resident/members/invitations', [\App\Http\Controllers\Resident\MemberController::class, 'storeInvite'])->name('resident.members.invitations.store');
    Route::delete('/resident/members/invitations/{id}', [\App\Http\Controllers\Resident\MemberController::class, 'destroyInvite'])->name('resident.members.invitations.destroy');
    Route::delete('/resident/members/registered/{id}', [\App\Http\Controllers\Resident\MemberController::class, 'destroyRegistered'])->name('resident.members.registered.destroy');

    // QUẢN LÝ PHƯƠNG TIỆN PHÍA CƯ DÂN
    Route::get('/resident/vehicles', [App\Http\Controllers\Resident\VehicleController::class, 'index'])
        ->name('resident.vehicles.index');
    Route::get('/resident/vehicles/create', [App\Http\Controllers\Resident\VehicleController::class, 'create'])
        ->name('resident.vehicles.create');
    Route::post('/resident/vehicles', [App\Http\Controllers\Resident\VehicleController::class, 'store'])
        ->name('resident.vehicles.store');
    Route::delete('/resident/vehicles/{vehicle}', [App\Http\Controllers\Resident\VehicleController::class, 'destroy'])
        ->name('resident.vehicles.destroy');
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
    Route::get('/admin/invitations', [AdminInvitationController::class, 'index'])->name('invitations.index');
    Route::post('/admin/invitations', [AdminInvitationController::class, 'store'])->name('invitations.store');
    Route::delete('/admin/invitations/{id}', [AdminInvitationController::class, 'destroy'])->name('invitations.destroy');

    //sepay
    Route::post('/sepay/webhook', [SepayController::class, 'webhook']);
});

// ─── RESIDENT: Phản ánh sự cố ────────────────────────────────────────────────
Route::middleware(['resident'])->group(function () {
    Route::get('/resident/incidents', [\App\Http\Controllers\Resident\IncidentController::class, 'index'])
        ->name('resident.incidents.index');
    Route::get('/resident/incidents/create', [\App\Http\Controllers\Resident\IncidentController::class, 'create'])
        ->name('resident.incidents.create');
    Route::post('/resident/incidents', [\App\Http\Controllers\Resident\IncidentController::class, 'store'])
        ->name('resident.incidents.store');
    Route::get('/resident/incidents/{id}', [\App\Http\Controllers\Resident\IncidentController::class, 'show'])
        ->name('resident.incidents.show');
});

// ─── MANAGER: Tiếp nhận & Điều phối ─────────────────────────────────────────
Route::middleware(['manager'])->prefix('manager')->name('manager.')->group(function () {
    Route::get('/dashboard', function () {
        return redirect()->route('manager.incidents.index');
    })->name('dashboard');

    Route::get('/incidents', [\App\Http\Controllers\Manager\IncidentController::class, 'index'])
        ->name('incidents.index');
    Route::get('/incidents/{id}', [\App\Http\Controllers\Manager\IncidentController::class, 'show'])
        ->name('incidents.show');
    Route::post('/incidents/{id}/assign', [\App\Http\Controllers\Manager\IncidentController::class, 'assign'])
        ->name('incidents.assign');
    Route::post('/incidents/{id}/confirm', [\App\Http\Controllers\Manager\IncidentController::class, 'confirm'])
        ->name('incidents.confirm');
    Route::post('/incidents/{id}/close', [\App\Http\Controllers\Manager\IncidentController::class, 'close'])
        ->name('incidents.close');
});

// ─── TECHNICIAN: Xử lý task ──────────────────────────────────────────────────
Route::middleware(['technician'])->prefix('technician')->name('technician.')->group(function () {
    Route::get('/dashboard', function () {
        return redirect()->route('technician.incidents.index');
    })->name('dashboard');

    Route::get('/incidents', [\App\Http\Controllers\Technician\IncidentController::class, 'index'])
        ->name('incidents.index');
    Route::get('/incidents/{id}', [\App\Http\Controllers\Technician\IncidentController::class, 'show'])
        ->name('incidents.show');
    Route::post('/incidents/{id}/update-status', [\App\Http\Controllers\Technician\IncidentController::class, 'updateStatus'])
        ->name('incidents.update-status');
});
