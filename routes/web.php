<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\HomeController;
use App\Http\Controllers\Admin\InvoiceController;
use App\Http\Controllers\Admin\ResidentManageController;
use App\Http\Controllers\Admin\ServicePriceController;
use App\Http\Controllers\Admin\UtilityMeterController;
use App\Http\Controllers\Resident\ProfileController;
use App\Http\Controllers\Resident\InvoiceController as ResidentInvoiceController;

use App\Http\Controllers\Admin\InvitationController as AdminInvitationController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserController;

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

// =========================================================================
// DASHBOARD ADMIN ROUTES
// =========================================================================
Route::middleware(['admin'])->group(function () {
    Route::get('admin', [HomeController::class, 'index'])->name('admin.dashboard');

    // Block/Building routes (used in views)
    Route::get('/admin/blocks', [\App\Http\Controllers\Admin\BlockController::class, 'index'])->name('admin.blocks.index');
    Route::get('/admin/blocks/create', [\App\Http\Controllers\Admin\BlockController::class, 'create'])->name('admin.blocks.create');
    Route::post('/admin/blocks', [\App\Http\Controllers\Admin\BlockController::class, 'store'])->name('admin.blocks.store');
    Route::get('/admin/blocks/{block}', [\App\Http\Controllers\Admin\BlockController::class, 'show'])->name('admin.blocks.show');
    Route::get('/admin/blocks/{block}/edit', [\App\Http\Controllers\Admin\BlockController::class, 'edit'])->name('admin.blocks.edit');
    Route::put('/admin/blocks/{block}', [\App\Http\Controllers\Admin\BlockController::class, 'update'])->name('admin.blocks.update');
    Route::delete('/admin/blocks/{block}', [\App\Http\Controllers\Admin\BlockController::class, 'destroy'])->name('admin.blocks.destroy');

    // Floors (Tầng)
    Route::get('/admin/floors/create', [\App\Http\Controllers\Admin\FloorController::class, 'create'])->name('admin.floors.create');
    Route::post('/admin/floors', [\App\Http\Controllers\Admin\FloorController::class, 'store'])->name('admin.floors.store');
    Route::get('/admin/floors/{floor}', [\App\Http\Controllers\Admin\FloorController::class, 'show'])->name('admin.floors.show');
    Route::get('/admin/floors/{floor}/edit', [\App\Http\Controllers\Admin\FloorController::class, 'edit'])->name('admin.floors.edit');
    Route::put('/admin/floors/{floor}', [\App\Http\Controllers\Admin\FloorController::class, 'update'])->name('admin.floors.update');
    Route::delete('/admin/floors/{floor}', [\App\Http\Controllers\Admin\FloorController::class, 'destroy'])->name('admin.floors.destroy');

    // Apartments (Căn hộ/Phòng)
    Route::get('/admin/apartments', [\App\Http\Controllers\Admin\ApartmentController::class, 'index'])->name('admin.apartments.index');
    Route::get('/admin/apartments/create', [\App\Http\Controllers\Admin\ApartmentController::class, 'create'])->name('admin.apartments.create');
    Route::post('/admin/apartments', [\App\Http\Controllers\Admin\ApartmentController::class, 'store'])->name('admin.apartments.store');
    Route::get('/admin/apartments/{apartment}', [\App\Http\Controllers\Admin\ApartmentController::class, 'show'])->name('admin.apartments.show');
    Route::get('/admin/apartments/{apartment}/edit', [\App\Http\Controllers\Admin\ApartmentController::class, 'edit'])->name('admin.apartments.edit');
    Route::put('/admin/apartments/{apartment}', [\App\Http\Controllers\Admin\ApartmentController::class, 'update'])->name('admin.apartments.update');
    Route::delete('/admin/apartments/{apartment}', [\App\Http\Controllers\Admin\ApartmentController::class, 'destroy'])->name('admin.apartments.destroy');

    // Xoá mềm cư dân khỏi phòng
    Route::delete('/admin/residents/{id}', [ResidentManageController::class, 'destroy'])->name('admin.residents.destroy');

    // Điện nước & hoá đơn
    Route::get('/admin/utility-readings', [UtilityMeterController::class, 'index'])->name('admin.utility-readings.index');
    Route::get('/admin/utility-readings/create', [UtilityMeterController::class, 'create'])->name('admin.utility-readings.create');
    Route::post('/admin/utility-readings', [UtilityMeterController::class, 'store'])->name('admin.utility-readings.store');

    Route::get('/admin/service-prices', [ServicePriceController::class, 'index'])->name('admin.service-prices.index');
    Route::post('/admin/service-prices', [ServicePriceController::class, 'store'])->name('admin.service-prices.store');
    Route::put('/admin/service-prices/{id}', [ServicePriceController::class, 'update'])->name('admin.service-prices.update');
    Route::delete('/admin/service-prices/{id}', [ServicePriceController::class, 'destroy'])->name('admin.service-prices.destroy');

    Route::get('/admin/invoices', [InvoiceController::class, 'index'])->name('admin.invoices.index');
    Route::post('/admin/invoices/generate', [InvoiceController::class, 'generate'])->name('admin.invoices.generate');


    // Dịch vụ cư dân
    Route::get('/admin/residents', function () {
        return view('admin.dashboard.index');
    })->name('admin.residents.index');

    // QUẢN LÝ PHƯƠNG TIỆN PHÍA ADMIN
    Route::get('/admin/vehicles', [App\Http\Controllers\Admin\VehicleController::class, 'index'])->name('admin.vehicles.index');
    Route::post('/admin/vehicles/{vehicle}/assign-lot',  [App\Http\Controllers\Admin\VehicleController::class, 'assignLot'])->name('admin.vehicles.assignLot');
    Route::post('/admin/vehicles/{vehicle}/release-lot', [App\Http\Controllers\Admin\VehicleController::class, 'releaseLot'])->name('admin.vehicles.releaseLot');
    Route::post('/admin/vehicles/{vehicle}/approve',     [App\Http\Controllers\Admin\VehicleController::class, 'approve'])->name('admin.vehicles.approve');
    Route::post('/admin/vehicles/{vehicle}/lock',        [App\Http\Controllers\Admin\VehicleController::class, 'lock'])->name('admin.vehicles.lock');
    Route::post('/admin/vehicles/{vehicle}/unlock',      [App\Http\Controllers\Admin\VehicleController::class, 'unlock'])->name('admin.vehicles.unlock');

    // QUẢN LÝ LỐT ĐỖ XE
    Route::get('/admin/parking-lots', [App\Http\Controllers\Admin\ParkingLotController::class, 'index'])->name('admin.parking-lots.index');
    Route::post('/admin/parking-lots', [App\Http\Controllers\Admin\ParkingLotController::class, 'store'])->name('admin.parking-lots.store');
    Route::put('/admin/parking-lots/{parkingLot}', [App\Http\Controllers\Admin\ParkingLotController::class, 'update'])->name('admin.parking-lots.update');
    Route::delete('/admin/parking-lots/{parkingLot}', [App\Http\Controllers\Admin\ParkingLotController::class, 'destroy'])->name('admin.parking-lots.destroy');

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


// DASHBOARD SECURITY ROUTES

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


//  DASHBOARD RESIDENT ROUTES

Route::middleware(['resident'])->group(function () {

    Route::get('/resident/dashboard', function () {
        return view('resident.home.index');
    })->name('resident.dashboard');

    // Profile
    Route::get('/resident/profile', [ProfileController::class, 'index'])->name('resident.profile.index');
    Route::put('/resident/profile', [ProfileController::class, 'update'])->name('resident.profile.update');

    // Hoá đơn cư dân
    Route::get('/resident/invoices', [ResidentInvoiceController::class, 'index'])->name('resident.invoices.index');
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
});
