<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\HomeController;
use App\Http\Controllers\Admin\InvoiceController;
use App\Http\Controllers\Admin\ResidentManageController;
use App\Http\Controllers\Admin\ServicePriceController;
use App\Http\Controllers\Admin\UtilityMeterController;
use App\Http\Controllers\Admin\FacilityController as AdminFacilityController;
use App\Http\Controllers\Resident\ProfileController;
use App\Http\Controllers\Resident\InvoiceController as ResidentInvoiceController;
use App\Http\Controllers\Resident\TicketController as ResidentTicketController;
use App\Http\Controllers\Resident\FacilityController as ResidentFacilityController;

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
    Route::get('/admin/statistics/finance', [HomeController::class, 'statisticsFinance'])->name('admin.statistics.finance');
    Route::get('/admin/statistics/finance/export', [HomeController::class, 'exportFinanceExcel'])->name('admin.statistics.finance.export');
    Route::get('/admin/statistics/operations', [HomeController::class, 'statisticsOperations'])->name('admin.statistics.operations');
    Route::get('/admin/statistics/operations/export', [HomeController::class, 'exportOperationsExcel'])->name('admin.statistics.operations.export');
    Route::get('/admin/statistics/residents', [HomeController::class, 'statisticsResidents'])->name('admin.statistics.residents');
    Route::get('/admin/statistics/residents/export', [HomeController::class, 'exportResidentsExcel'])->name('admin.statistics.residents.export');

    // Activity Logs – 7 tabs
    Route::get('/admin/activity-logs/entry-exit',   [\App\Http\Controllers\Admin\ActivityLogController::class, 'entryExit'])->name('admin.activity-logs.entry-exit');
    Route::get('/admin/activity-logs/parking',      [\App\Http\Controllers\Admin\ActivityLogController::class, 'parking'])->name('admin.activity-logs.parking');
    Route::get('/admin/activity-logs/facility',     [\App\Http\Controllers\Admin\ActivityLogController::class, 'facilityBooking'])->name('admin.activity-logs.facility');
    Route::get('/admin/activity-logs/system',       [\App\Http\Controllers\Admin\ActivityLogController::class, 'system'])->name('admin.activity-logs.system');
    Route::get('/admin/activity-logs/finance',      [\App\Http\Controllers\Admin\ActivityLogController::class, 'finance'])->name('admin.activity-logs.finance');
    Route::get('/admin/activity-logs/hardware',     [\App\Http\Controllers\Admin\ActivityLogController::class, 'hardware'])->name('admin.activity-logs.hardware');
    Route::get('/admin/activity-logs/communication',[\App\Http\Controllers\Admin\ActivityLogController::class, 'communication'])->name('admin.activity-logs.communication');
    Route::get('/admin/activity-logs/utility',      [\App\Http\Controllers\Admin\ActivityLogController::class, 'utility'])->name('admin.activity-logs.utility');
    Route::get('/admin/activity-logs/export',       [\App\Http\Controllers\Admin\ActivityLogController::class, 'export'])->name('admin.activity-logs.export');
    // Redirect /admin/activity-logs → first tab
    Route::redirect('/admin/activity-logs', '/admin/activity-logs/entry-exit')->name('admin.activity-logs.index');

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
    Route::delete('/admin/utility-readings/{id}/image', [UtilityMeterController::class, 'removeImage'])->name('admin.utility-readings.remove-image');

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
    Route::post('/admin/invoices/details/{detail}/mark-paid', [InvoiceController::class, 'markDetailAsPaid'])->name('admin.invoices.details.mark-paid');
    Route::post('/admin/payments/{payment}/refund', [InvoiceController::class, 'refundPayment'])->name('admin.payments.refund');
    Route::get('/admin/payments/{payment}/receipt', [InvoiceController::class, 'printReceipt'])->name('admin.payments.receipt');

    // Danh sách cư dân
    Route::get('/admin/residents', [ResidentManageController::class, 'index'])->name('admin.residents.index');

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

    // Quản lý tiện ích chung cư (Facilities)
    Route::get('/admin/amenities', [AdminFacilityController::class, 'index'])->name('admin.amenities.index');
    Route::get('/admin/amenities/create', [AdminFacilityController::class, 'create'])->name('admin.amenities.create');
    Route::post('/admin/amenities', [AdminFacilityController::class, 'store'])->name('admin.amenities.store');
    Route::get('/admin/amenities/statistics', [AdminFacilityController::class, 'statistics'])->name('admin.amenities.statistics');
    Route::get('/admin/amenities/bookings', [AdminFacilityController::class, 'bookings'])->name('admin.amenities.bookings');
    Route::get('/admin/amenities/{facility}', [AdminFacilityController::class, 'show'])->name('admin.amenities.show');
    Route::get('/admin/amenities/{facility}/edit', [AdminFacilityController::class, 'edit'])->name('admin.amenities.edit');
    Route::put('/admin/amenities/{facility}', [AdminFacilityController::class, 'update'])->name('admin.amenities.update');
    Route::delete('/admin/amenities/{facility}', [AdminFacilityController::class, 'destroy'])->name('admin.amenities.destroy');
    Route::post('/admin/amenities/{facility}/images', [AdminFacilityController::class, 'storeImage'])->name('admin.amenities.images.store');
    Route::delete('/admin/amenities/{facility}/images/{index}', [AdminFacilityController::class, 'destroyImage'])->name('admin.amenities.images.destroy');
    Route::patch('/admin/amenities/{facility}/status', [AdminFacilityController::class, 'updateStatus'])->name('admin.amenities.status');
    Route::post('/admin/facility-bookings/{booking}/approve', [AdminFacilityController::class, 'approveBooking'])->name('admin.amenities.bookings.approve');
    Route::post('/admin/facility-bookings/{booking}/reject', [AdminFacilityController::class, 'rejectBooking'])->name('admin.amenities.bookings.reject');
    Route::post('/admin/facility-bookings/{booking}/cancel', [AdminFacilityController::class, 'cancelBooking'])->name('admin.amenities.bookings.cancel');
    Route::patch('/admin/facility-bookings/{booking}/status', [AdminFacilityController::class, 'updateBookingStatus'])->name('admin.amenities.bookings.status');

    Route::post('/admin/announcements/{id}/toggle-pin', [\App\Http\Controllers\Admin\AnnouncementController::class, 'togglePin'])->name('admin.announcements.toggle-pin');
    Route::resource('/admin/announcements', \App\Http\Controllers\Admin\AnnouncementController::class)->names('admin.announcements');
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

    // Trang cá nhân quản trị viên
    Route::get('/admin/profile', [\App\Http\Controllers\Admin\ProfileController::class, 'index'])->name('admin.profile.index');
    Route::put('/admin/profile', [\App\Http\Controllers\Admin\ProfileController::class, 'update'])->name('admin.profile.update');
    Route::put('/admin/profile/change-password', [\App\Http\Controllers\Admin\ProfileController::class, 'changePassword'])->name('admin.profile.change-password');
});

// DASHBOARD SECURITY ROUTES
Route::middleware(['security'])->group(function () {
    Route::get('/security/dashboard', function () {
        $vehiclesInside = \App\Models\VehicleLog::where('status', 'inside')->count();
        $visitorsInside = \App\Models\Visitor::where('status', 'checked_in')->count();
        $todayCheckins = \App\Models\VehicleLog::whereDate('check_in_at', today())->count();
        $todayCheckouts = \App\Models\VehicleLog::whereDate('check_out_at', today())->count();
        $todayVisitors = \App\Models\Visitor::whereDate('check_in_at', today())->count();

        $recentLogs = \App\Models\VehicleLog::with('vehicle')
            ->latest()
            ->take(8)
            ->get();

        return view('security.dashboard.index', compact(
            'vehiclesInside', 'visitorsInside', 'todayCheckins', 'todayCheckouts', 'todayVisitors', 'recentLogs'
        ));
    })->name('security.dashboard');

    // Quét QR xe vào
    Route::get('/security/vehicle-checkin', [\App\Http\Controllers\Security\VehicleCheckinController::class, 'index'])->name('security.vehicle-checkin.index');
    Route::post('/security/vehicle-checkin/scan', [\App\Http\Controllers\Security\VehicleCheckinController::class, 'scan'])->name('security.vehicle-checkin.scan');
    Route::post('/security/vehicle-checkin/confirm', [\App\Http\Controllers\Security\VehicleCheckinController::class, 'checkin'])->name('security.vehicle-checkin.confirm');

    // Quét QR xe ra
    Route::get('/security/vehicle-checkout', [\App\Http\Controllers\Security\VehicleCheckoutController::class, 'index'])->name('security.vehicle-checkout.index');
    Route::post('/security/vehicle-checkout/scan', [\App\Http\Controllers\Security\VehicleCheckoutController::class, 'scan'])->name('security.vehicle-checkout.scan');
    Route::post('/security/vehicle-checkout/confirm', [\App\Http\Controllers\Security\VehicleCheckoutController::class, 'checkout'])->name('security.vehicle-checkout.confirm');

    // Quét QR khách
    Route::get('/security/visitor-check', [\App\Http\Controllers\Security\VisitorCheckinController::class, 'index'])->name('security.visitor-check.index');
    Route::post('/security/visitor-check/scan', [\App\Http\Controllers\Security\VisitorCheckinController::class, 'scan'])->name('security.visitor-check.scan');
    Route::post('/security/visitor-check/checkin', [\App\Http\Controllers\Security\VisitorCheckinController::class, 'checkin'])->name('security.visitor-check.checkin');
    Route::post('/security/visitor-check/checkout', [\App\Http\Controllers\Security\VisitorCheckinController::class, 'checkout'])->name('security.visitor-check.checkout');
});

// DASHBOARD RESIDENT ROUTES
Route::middleware(['resident'])->group(function () {
    Route::get('/resident/dashboard', function () {
        $user = auth()->user();
        $apartment = $user->apartment;
        
        $recentAnnouncements = \App\Models\Announcement::with('user')
            ->published()
            ->ordered()
            ->take(5)
            ->get();

        $totalUnpaidAmount = 0;
        $dueDate = null;
        if ($apartment) {
            $unpaidBills = \App\Models\Invoice::where('apartment_id', $apartment->id)
                ->whereIn('status', ['unpaid', 'partial', 'overdue'])
                ->get();
            foreach ($unpaidBills as $bill) {
                $totalUnpaidAmount += ($bill->total_amount - $bill->paid_amount);
                if (!$dueDate || $bill->due_date->lt($dueDate)) {
                    $dueDate = $bill->due_date;
                }
            }
        }

        return view('resident.home.index', compact('recentAnnouncements', 'totalUnpaidAmount', 'dueDate', 'apartment'));
    })->name('resident.dashboard');

    Route::get('/resident/contact', function () {
        return view('resident.contact.index');
    })->name('resident.contact');

    // Profile
    Route::get('/resident/profile', [ProfileController::class, 'index'])->name('resident.profile.index');
    Route::put('/resident/profile', [ProfileController::class, 'update'])->name('resident.profile.update');
    Route::put('/resident/profile/change-password', [ProfileController::class, 'changePassword'])->name('resident.profile.change-password');

    // Hoá đơn cư dân
    Route::get('/resident/invoices', [ResidentInvoiceController::class, 'index'])->name('resident.invoices.index');
    Route::get('/resident/invoices/history', [ResidentInvoiceController::class, 'history'])->name('resident.invoices.history');
    Route::get('/resident/invoices/vnpay-return', [ResidentInvoiceController::class, 'vnpayReturn'])->name('resident.invoices.vnpay-return');
    Route::get('/resident/invoices/{id}', [ResidentInvoiceController::class, 'show'])->name('resident.invoices.show');
    Route::post('/resident/invoices/pay', [ResidentInvoiceController::class, 'pay'])->name('resident.invoices.pay');
    Route::get('/resident/payments/{payment}/receipt', [ResidentInvoiceController::class, 'printReceipt'])->name('resident.payments.receipt');

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

    Route::get('/resident/vehicles/{vehicle}/qr', [App\Http\Controllers\Resident\VehicleController::class, 'showQr'])
        ->name('resident.vehicles.qr');

    // QUẢN LÝ KHÁCH PHÍA CƯ DÂN
    Route::get('/resident/visitors', [\App\Http\Controllers\Resident\VisitorController::class, 'index'])->name('resident.visitors.index');
    Route::get('/resident/visitors/create', [\App\Http\Controllers\Resident\VisitorController::class, 'create'])->name('resident.visitors.create');
    Route::post('/resident/visitors', [\App\Http\Controllers\Resident\VisitorController::class, 'store'])->name('resident.visitors.store');
    Route::get('/resident/visitors/{id}', [\App\Http\Controllers\Resident\VisitorController::class, 'show'])->name('resident.visitors.show');
    Route::delete('/resident/visitors/{id}', [\App\Http\Controllers\Resident\VisitorController::class, 'destroy'])->name('resident.visitors.destroy');

    // PHẢN ÁNH SỰ CỐ PHÍA CƯ DÂN
    Route::get('/resident/tickets', [ResidentTicketController::class, 'index'])->name('resident.tickets.index');
    Route::get('/resident/tickets/create', [ResidentTicketController::class, 'create'])->name('resident.tickets.create');
    Route::post('/resident/tickets', [ResidentTicketController::class, 'store'])->name('resident.tickets.store');
    Route::get('/resident/tickets/{id}', [ResidentTicketController::class, 'show'])->name('resident.tickets.show');
    Route::post('/resident/tickets/{id}/cancel', [ResidentTicketController::class, 'cancel'])->name('resident.tickets.cancel');
    Route::post('/resident/tickets/{id}/feedback', [ResidentTicketController::class, 'feedback'])->name('resident.tickets.feedback');

    // BẢNG TIN & BÌNH LUẬN PHÍA CƯ DÂN
    Route::get('/resident/posts', [\App\Http\Controllers\Resident\PostController::class, 'index'])->name('resident.posts.index');
    Route::post('/resident/posts', [\App\Http\Controllers\Resident\PostController::class, 'store'])->name('resident.posts.store');
    Route::get('/resident/posts/{id}', [\App\Http\Controllers\Resident\PostController::class, 'show'])->name('resident.posts.show');
    Route::put('/resident/posts/{id}', [\App\Http\Controllers\Resident\PostController::class, 'update'])->name('resident.posts.update');
    Route::delete('/resident/posts/{id}', [\App\Http\Controllers\Resident\PostController::class, 'destroy'])->name('resident.posts.destroy');
    Route::post('/resident/posts/{id}/comments', [\App\Http\Controllers\Resident\PostController::class, 'storeComment'])->name('resident.posts.comments.store');
    Route::put('/resident/comments/{id}', [\App\Http\Controllers\Resident\PostController::class, 'updateComment'])->name('resident.comments.update');
    Route::delete('/resident/comments/{id}', [\App\Http\Controllers\Resident\PostController::class, 'destroyComment'])->name('resident.comments.destroy');
    Route::post('/resident/posts/{id}/report', [\App\Http\Controllers\Resident\PostController::class, 'report'])->name('resident.posts.report');
    Route::post('/resident/comments/{id}/report', [\App\Http\Controllers\Resident\PostController::class, 'reportComment'])->name('resident.comments.report');
    Route::post('/resident/like', [\App\Http\Controllers\Resident\PostController::class, 'toggleLike'])->name('resident.posts.like');
    Route::get('/resident/posts/{id}/comments', [\App\Http\Controllers\Resident\PostController::class, 'loadComments'])->name('resident.posts.comments.load');
    Route::get('/resident/search-members', [\App\Http\Controllers\Resident\PostController::class, 'searchMembersForMention'])->name('resident.posts.search-members');
    Route::post('/resident/posts/{id}/share-to-user', [\App\Http\Controllers\Resident\PostController::class, 'shareToUser'])->name('resident.posts.share-to-user');
    Route::post('/resident/comments/{id}/pin', [\App\Http\Controllers\Resident\PostController::class, 'togglePinComment'])->name('resident.comments.pin');
    Route::get('/resident/reactions/{likeable_type}/{likeable_id}', [\App\Http\Controllers\Resident\PostController::class, 'getReactions'])->name('resident.reactions');

    // BẢNG TIN CHUNG CƯ PHÍA CƯ DÂN (CHỈ GIỮ LẠI TRANG CHI TIẾT)
    Route::get('/resident/announcements/{id}', [\App\Http\Controllers\Resident\AnnouncementController::class, 'show'])->name('resident.announcements.show');

    // THÔNG BÁO CƯ DÂN
    Route::get('/resident/notifications', [\App\Http\Controllers\Resident\NotificationController::class, 'index'])->name('resident.notifications.index');
    Route::post('/resident/notifications/mark-read/{id?}', [\App\Http\Controllers\Resident\NotificationController::class, 'markRead'])->name('resident.notifications.mark-read');

    // TIỆN ÍCH CHUNG CƯ PHÍA CƯ DÂN
    Route::get('/resident/facilities', [ResidentFacilityController::class, 'index'])->name('resident.facilities.index');
    Route::get('/resident/facilities/{facility}', [ResidentFacilityController::class, 'show'])->name('resident.facilities.show');
    Route::get('/resident/facilities/{facility}/book', [ResidentFacilityController::class, 'book'])->name('resident.facilities.book');
    Route::post('/resident/facilities/{facility}/book', [ResidentFacilityController::class, 'storeBooking'])->name('resident.facilities.book.store');

    // LỊCH ĐẶT TIỆN ÍCH PHÍA CƯ DÂN
    Route::get('/resident/facility-bookings', [ResidentFacilityController::class, 'bookingHistory'])->name('resident.facility-bookings.index');
    Route::post('/resident/facility-bookings/{booking}/cancel', [ResidentFacilityController::class, 'cancelBooking'])->name('resident.facility-bookings.cancel');
    Route::get('/resident/facility-bookings/{booking}/qr', [ResidentFacilityController::class, 'showQr'])->name('resident.facility-bookings.qr');
    // Route thanh toán trực tiếp đã được chuyển sang hệ thống hóa đơn (resident.invoices)

    // AJAX: Khung giờ còn trống (dùng trong form đặt lịch)
    Route::post('/resident/api/available-slots', [\App\Http\Controllers\FacilityBookingController::class, 'getAvailableSlots'])->name('resident.api.available-slots');
});

Route::middleware(['admin'])->name('admin.')->group(function () {
    // Quản lý bài đăng cư dân (Admin Portal)
    Route::get('/admin/posts', [\App\Http\Controllers\Admin\PostController::class, 'index'])->name('posts.index');
    Route::post('/admin/posts/{id}/restore', [\App\Http\Controllers\Admin\PostController::class, 'restore'])->name('posts.restore');
    Route::post('/admin/posts/{id}/toggle-status', [\App\Http\Controllers\Admin\PostController::class, 'toggleStatus'])->name('posts.toggle-status');
    Route::post('/admin/posts/{id}/dismiss-reports', [\App\Http\Controllers\Admin\PostController::class, 'dismissReports'])->name('posts.dismiss-reports');
    Route::get('/admin/posts/{id}/json', [\App\Http\Controllers\Admin\PostController::class, 'getPostJson'])->name('posts.json');
    Route::delete('/admin/posts/{id}', [\App\Http\Controllers\Admin\PostController::class, 'destroy'])->name('posts.destroy');
    Route::post('/admin/comments/{id}/dismiss-reports', [\App\Http\Controllers\Admin\PostController::class, 'dismissCommentReports'])->name('comments.dismiss-reports');
    Route::delete('/admin/comments/{id}', [\App\Http\Controllers\Admin\PostController::class, 'destroyComment'])->name('comments.destroy');
    Route::post('/admin/comments/{id}/restore', [\App\Http\Controllers\Admin\PostController::class, 'restoreComment'])->name('comments.restore');
    Route::post('/admin/users/{id}/ban-posting', [\App\Http\Controllers\Admin\PostController::class, 'banPosting'])->name('users.ban-posting');
    Route::post('/admin/users/{id}/ban-commenting', [\App\Http\Controllers\Admin\PostController::class, 'banCommenting'])->name('users.ban-commenting');
});

// Fallback route to serve uploaded public storage files
Route::get('/storage/{any}', function ($any) {
    $path = storage_path('app/public/' . $any);
    if (file_exists($path)) {
        return response()->file($path);
    }
    abort(404);
})->where('any', '.*');
