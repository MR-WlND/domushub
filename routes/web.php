<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\HomeController;
use App\Http\Controllers\Admin\InvoiceController;
use App\Http\Controllers\Admin\ResidentManageController;
use App\Http\Controllers\Admin\ServicePriceController;
use App\Http\Controllers\Admin\UtilityMeterController;
use App\Http\Controllers\Resident\ProfileController;
use App\Http\Controllers\Resident\InvoiceController as ResidentInvoiceController;
use App\Http\Controllers\Resident\TicketController as ResidentTicketController;

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
// VNPay IPN – Webhook nhận thông báo server-to-server từ VNPay
// KHÔNG đặt trong middleware auth vì VNPay gọi trực tiếp không có session
// =========================================================================
Route::get('/vnpay/ipn', [\App\Http\Controllers\Resident\InvoiceController::class, 'vnpayIpn'])->name('vnpay.ipn');

// =========================================================================
// DASHBOARD ADMIN ROUTES
// =========================================================================
Route::middleware(['admin'])->group(function () {
    Route::get('admin', [HomeController::class, 'index'])->name('admin.dashboard');
    Route::get('/admin/statistics', [HomeController::class, 'statistics'])->name('admin.statistics');

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
    Route::get('/admin/apartments/import-template', [\App\Http\Controllers\Admin\ApartmentController::class, 'downloadTemplate'])->name('admin.apartments.import-template');
    Route::post('/admin/apartments/import', [\App\Http\Controllers\Admin\ApartmentController::class, 'import'])->name('admin.apartments.import');
    Route::get('/admin/apartments/{apartment}', [\App\Http\Controllers\Admin\ApartmentController::class, 'show'])->name('admin.apartments.show');
    Route::get('/admin/apartments/{apartment}/edit', [\App\Http\Controllers\Admin\ApartmentController::class, 'edit'])->name('admin.apartments.edit');
    Route::put('/admin/apartments/{apartment}', [\App\Http\Controllers\Admin\ApartmentController::class, 'update'])->name('admin.apartments.update');
    Route::delete('/admin/apartments/{apartment}', [\App\Http\Controllers\Admin\ApartmentController::class, 'destroy'])->name('admin.apartments.destroy');

    // Xoá mềm cư dân khỏi phòng
    Route::delete('/admin/residents/{id}', [ResidentManageController::class, 'destroy'])->name('admin.residents.destroy');

    // Thông báo (Notifications)
    Route::get('/admin/notifications', [\App\Http\Controllers\Admin\NotificationController::class, 'index'])->name('admin.notifications.index');
    Route::post('/admin/notifications/mark-read/{id?}', [\App\Http\Controllers\Admin\NotificationController::class, 'markRead'])->name('admin.notifications.mark-read');

    // Điện nước & hoá đơn
    Route::get('/admin/utility-readings', [UtilityMeterController::class, 'index'])->name('admin.utility-readings.index');
    Route::get('/admin/utility-readings/create', [UtilityMeterController::class, 'create'])->name('admin.utility-readings.create');
    Route::post('/admin/utility-readings', [UtilityMeterController::class, 'store'])->name('admin.utility-readings.store');
    Route::get('/admin/utility-readings/batch', [UtilityMeterController::class, 'batchCreate'])->name('admin.utility-readings.batch');
    Route::post('/admin/utility-readings/batch', [UtilityMeterController::class, 'batchStore'])->name('admin.utility-readings.batch.store');
    Route::get('/admin/utility-readings/get-old-value', [UtilityMeterController::class, 'getOldValue'])->name('admin.utility-readings.get-old-value');
    Route::get('/admin/utility-readings/import-template', [UtilityMeterController::class, 'downloadTemplate'])->name('admin.utility-readings.import-template');
    Route::post('/admin/utility-readings/import', [UtilityMeterController::class, 'import'])->name('admin.utility-readings.import');
    Route::get('/admin/utility-readings/{id}', [UtilityMeterController::class, 'show'])->name('admin.utility-readings.show');
    Route::get('/admin/utility-readings/{id}/edit', [UtilityMeterController::class, 'edit'])->name('admin.utility-readings.edit');
    Route::put('/admin/utility-readings/{id}', [UtilityMeterController::class, 'update'])->name('admin.utility-readings.update');
    Route::delete('/admin/utility-readings/{id}', [UtilityMeterController::class, 'destroy'])->name('admin.utility-readings.destroy');
    Route::post('/admin/utility-readings/{id}/approve', [UtilityMeterController::class, 'approve'])->name('admin.utility-readings.approve');
    Route::post('/admin/utility-readings/{id}/reject', [UtilityMeterController::class, 'reject'])->name('admin.utility-readings.reject');
    Route::post('/admin/utility-readings/batch-approve', [UtilityMeterController::class, 'batchApprove'])->name('admin.utility-readings.batch-approve');

    Route::get('/admin/service-prices', [ServicePriceController::class, 'index'])->name('admin.service-prices.index');
    Route::post('/admin/service-prices', [ServicePriceController::class, 'store'])->name('admin.service-prices.store');
    Route::put('/admin/service-prices/{id}', [ServicePriceController::class, 'update'])->name('admin.service-prices.update');
    Route::delete('/admin/service-prices/{id}', [ServicePriceController::class, 'destroy'])->name('admin.service-prices.destroy');

    Route::get('/admin/invoices', [InvoiceController::class, 'index'])->name('admin.invoices.index');
    Route::get('/admin/invoices/stats', [InvoiceController::class, 'stats'])->name('admin.invoices.stats');
    Route::get('/admin/invoices/create', [InvoiceController::class, 'create'])->name('admin.invoices.create');
    Route::post('/admin/invoices', [InvoiceController::class, 'store'])->name('admin.invoices.store');

    Route::get('/admin/invoices/batch', [InvoiceController::class, 'batchCreate'])->name('admin.invoices.batch');
    Route::post('/admin/invoices/batch', [InvoiceController::class, 'batchStore'])->name('admin.invoices.batch.store');

    Route::post('/admin/invoices/generate', [InvoiceController::class, 'generate'])->name('admin.invoices.generate');
    Route::get('/admin/invoices/{invoice}', [InvoiceController::class, 'show'])->name('admin.invoices.show');
    Route::match(['post', 'patch'], '/admin/invoices/{invoice}/mark-paid', [InvoiceController::class, 'markAsPaid'])->name('admin.invoices.mark-paid');
    Route::post('/admin/payments/{payment}/refund', [InvoiceController::class, 'refundPayment'])->name('admin.payments.refund');


    // Dịch vụ cư dân
    Route::get('/admin/residents', function () {
        return view('admin.dashboard.index');
    })->name('admin.residents.index');

    // Quản lý phản ánh & điều phối kỹ thuật (admin / manager)
    Route::get('/admin/tickets', [App\Http\Controllers\Admin\TicketController::class, 'index'])->name('admin.tickets.index');
    Route::get('/admin/tickets/report', [App\Http\Controllers\Admin\TicketController::class, 'report'])->name('admin.tickets.report');
    Route::post('/admin/tickets/{id}/review/approve', [App\Http\Controllers\Admin\TicketController::class, 'approveReview'])->name('admin.tickets.review.approve');
    Route::post('/admin/tickets/{id}/review/reject', [App\Http\Controllers\Admin\TicketController::class, 'rejectReview'])->name('admin.tickets.review.reject');
    Route::get('/admin/tickets/dispatch', [App\Http\Controllers\Admin\TicketController::class, 'dispatchIndex'])->name('admin.tickets.dispatch');
    Route::get('/admin/tickets/my-tasks', [App\Http\Controllers\Admin\TicketController::class, 'myTasks'])->name('admin.tickets.my-tasks');
    Route::get('/admin/tickets/{id}', [App\Http\Controllers\Admin\TicketController::class, 'show'])->name('admin.tickets.show');
    Route::post('/admin/tickets/{id}/assign', [App\Http\Controllers\Admin\TicketController::class, 'assign'])->name('admin.tickets.assign');
    Route::post('/admin/tickets/{id}/accept', [App\Http\Controllers\Admin\TicketController::class, 'acceptTask'])->name('admin.tickets.accept');
    Route::post('/admin/tickets/{id}/update-progress', [App\Http\Controllers\Admin\TicketController::class, 'updateProgress'])->name('admin.tickets.update-progress');

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

    Route::get('/resident/contact', function () {
        return view('resident.contact.index');
    })->name('resident.contact');

    // Profile
    Route::get('/resident/profile', [ProfileController::class, 'index'])->name('resident.profile.index');
    Route::put('/resident/profile', [ProfileController::class, 'update'])->name('resident.profile.update');

    // Hoá đơn cư dân
    Route::get('/resident/invoices', [ResidentInvoiceController::class, 'index'])->name('resident.invoices.index');
    Route::get('/resident/invoices/history', [ResidentInvoiceController::class, 'history'])->name('resident.invoices.history');
    Route::get('/resident/invoices/vnpay-return', [ResidentInvoiceController::class, 'vnpayReturn'])->name('resident.invoices.vnpay-return');
    Route::get('/resident/invoices/{id}', [ResidentInvoiceController::class, 'show'])->name('resident.invoices.show');
    Route::post('/resident/invoices/pay', [ResidentInvoiceController::class, 'pay'])->name('resident.invoices.pay');

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

    // PHẢN ÁNH SỰ CỐ PHÍA CƯ DÂN
    Route::get('/resident/tickets', [ResidentTicketController::class, 'index'])->name('resident.tickets.index');
    Route::get('/resident/tickets/create', [ResidentTicketController::class, 'create'])->name('resident.tickets.create');
    Route::post('/resident/tickets', [ResidentTicketController::class, 'store'])->name('resident.tickets.store');
    Route::get('/resident/tickets/{id}', [ResidentTicketController::class, 'show'])->name('resident.tickets.show');
    Route::post('/resident/tickets/{id}/cancel', [ResidentTicketController::class, 'cancel'])->name('resident.tickets.cancel');
    Route::post('/resident/tickets/{id}/feedback', [ResidentTicketController::class, 'feedback'])->name('resident.tickets.feedback');
    // Quản lý tài khoản người dùng
    Route::get('/admin/users', [UserController::class, 'index'])->name('admin.users.index');
    Route::get('/admin/users/create', [UserController::class, 'create'])->name('admin.users.create');
    Route::post('/admin/users', [UserController::class, 'store'])->name('admin.users.store');
    Route::get('/admin/users/{id}/edit', [UserController::class, 'edit'])->name('admin.users.edit');
    Route::put('/admin/users/{id}', [UserController::class, 'update'])->name('admin.users.update');
    Route::put('/admin/users/{id}/update-status', [UserController::class, 'updateStatus'])->name('admin.users.updateStatus');
    Route::put('/admin/users/{id}/reset-password', [UserController::class, 'resetPassword'])->name('admin.users.resetPassword');

    // Quản lý mã mời
    Route::get('/admin/invitations', [AdminInvitationController::class, 'index'])->name('admin.invitations.index');
    Route::post('/admin/invitations', [AdminInvitationController::class, 'store'])->name('admin.invitations.store');
    Route::delete('/admin/invitations/{id}', [AdminInvitationController::class, 'destroy'])->name('admin.invitations.destroy');
});

// Fallback route to serve uploaded public storage files (useful if symlink is missing or fails on local Windows development)
Route::get('/storage/{any}', function ($any) {
    $path = storage_path('app/public/' . $any);
    if (file_exists($path)) {
        return response()->file($path);
    }
    abort(404);
})->where('any', '.*');



