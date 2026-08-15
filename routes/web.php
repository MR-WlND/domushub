<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\HomeController;
use App\Http\Controllers\Admin\UtilityMeterController;
use App\Http\Controllers\Admin\ServicePriceController;
use App\Http\Controllers\Admin\InvoiceController;
use App\Http\Controllers\Admin\ResidentManageController;
use App\Http\Controllers\Admin\FacilityController as AdminFacilityController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\InvitationController;
use App\Http\Controllers\Resident\InvoiceController as ResidentInvoiceController;
use App\Http\Controllers\Resident\ProfileController;
use App\Http\Controllers\Resident\TicketController as ResidentTicketController;
use App\Http\Controllers\Resident\FacilityController as ResidentFacilityController;
use Illuminate\Support\Facades\Route;

// Root route - redirect to resident login
Route::get('/', function () {
    return redirect()->route('resident.login');
});



// Shortcut routes
Route::get('/resident', function () {
    if (! Auth::check()) return redirect()->route('resident.login');
    return redirect()->route('resident.dashboard');
});

Route::get('/security', function () {
    if (! Auth::check()) return redirect()->route('security.login');
    return redirect()->route('security.dashboard');
});

Route::get('/manager', function () {
    if (! Auth::check()) return redirect()->route('manager.login');
    return redirect()->route('manager.dashboard');
});

Route::get('/staff', function () {
    if (! Auth::check()) return redirect()->route('staff.login');
    return redirect()->route('staff.utility-readings.index');
});

Route::get('/technician', function () {
    if (! Auth::check()) return redirect()->route('technician.login');
    return redirect()->route('technician.tickets.my-tasks');
});

Route::get('/cleaning', function () {
    if (! Auth::check()) return redirect()->route('cleaning.login');
    return redirect()->route('cleaning.dashboard');
});

// Admin Login Routes
Route::get('/admin/login', [AuthController::class, 'showAdminLogin'])->name('admin.login');
Route::post('/admin/login', [AuthController::class, 'loginAdmin'])->name('admin.login.submit');

// Manager Login Routes
Route::get('/manager/login', [AuthController::class, 'showManagerLogin'])->name('manager.login');
Route::post('/manager/login', [AuthController::class, 'loginManager'])->name('manager.login.submit');

// Staff Login Routes
Route::get('/staff/login', [AuthController::class, 'showStaffLogin'])->name('staff.login');
Route::post('/staff/login', [AuthController::class, 'loginStaff'])->name('staff.login.submit');

// Technician Login Routes
Route::get('/technician/login', [AuthController::class, 'showTechnicianLogin'])->name('technician.login');
Route::post('/technician/login', [AuthController::class, 'loginTechnician'])->name('technician.login.submit');

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

// Cleaning Login Routes
Route::get('/cleaning/login', [AuthController::class, 'showCleaningLogin'])->name('cleaning.login');
Route::post('/cleaning/login', [AuthController::class, 'loginCleaning'])->name('cleaning.login.submit');

// Receptionist Login Routes
Route::get('/receptionist/login', [AuthController::class, 'showReceptionistLogin'])->name('receptionist.login');
Route::post('/receptionist/login', [AuthController::class, 'loginReceptionist'])->name('receptionist.login.submit');

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
$portalRoutes = function () {

    Route::get('/', [HomeController::class, 'index'])->name('dashboard');
    Route::get('/statistics', [HomeController::class, 'statistics'])->name('statistics');
    Route::get('/statistics/finance', [HomeController::class, 'statisticsFinance'])->name('statistics.finance');
    Route::get('/statistics/finance/export', [HomeController::class, 'exportFinanceExcel'])->name('statistics.finance.export');
    Route::get('/statistics/operations', [HomeController::class, 'statisticsOperations'])->name('statistics.operations');
    Route::get('/statistics/operations/export', [HomeController::class, 'exportOperationsExcel'])->name('statistics.operations.export');
    Route::get('/statistics/residents', [HomeController::class, 'statisticsResidents'])->name('statistics.residents');
    Route::get('/statistics/residents/export', [HomeController::class, 'exportResidentsExcel'])->name('statistics.residents.export');

    // System & Security Logs
    Route::get('/system-logs', [\App\Http\Controllers\Admin\ActivityLogController::class, 'index'])->name('system-logs.index');
    Route::get('/system-logs/{id}', [\App\Http\Controllers\Admin\ActivityLogController::class, 'show'])->name('system-logs.show');

    // Notification History
    Route::get('/notification-logs', [\App\Http\Controllers\Admin\ActivityLogController::class, 'notificationLogs'])->name('notification-logs.index');

    // Finance History
    Route::get('/finance-logs', [\App\Http\Controllers\Admin\ActivityLogController::class, 'financeLogs'])->name('finance-logs.index');

    // Utility Meter History (Lịch sử ghi số điện nước)
    Route::get('/utility-logs', [\App\Http\Controllers\Admin\UtilityLogController::class, 'index'])->name('utility-logs.index');

    // Block/Building routes (used in views)
    Route::get('/blocks', [\App\Http\Controllers\Admin\BlockController::class, 'index'])->name('blocks.index');
    Route::get('/blocks/create', [\App\Http\Controllers\Admin\BlockController::class, 'create'])->name('blocks.create');
    Route::post('/blocks', [\App\Http\Controllers\Admin\BlockController::class, 'store'])->name('blocks.store');
    Route::get('/blocks/{block}', [\App\Http\Controllers\Admin\BlockController::class, 'show'])->name('blocks.show');
    Route::get('/blocks/{block}/edit', [\App\Http\Controllers\Admin\BlockController::class, 'edit'])->name('blocks.edit');
    Route::put('/blocks/{block}', [\App\Http\Controllers\Admin\BlockController::class, 'update'])->name('blocks.update');
    Route::delete('/blocks/{block}', [\App\Http\Controllers\Admin\BlockController::class, 'destroy'])->name('blocks.destroy');

    // Floors (Tầng)
    Route::get('/floors/create', [\App\Http\Controllers\Admin\FloorController::class, 'create'])->name('floors.create');
    Route::post('/floors', [\App\Http\Controllers\Admin\FloorController::class, 'store'])->name('floors.store');
    Route::get('/floors/{floor}', [\App\Http\Controllers\Admin\FloorController::class, 'show'])->name('floors.show');
    Route::get('/floors/{floor}/edit', [\App\Http\Controllers\Admin\FloorController::class, 'edit'])->name('floors.edit');
    Route::put('/floors/{floor}', [\App\Http\Controllers\Admin\FloorController::class, 'update'])->name('floors.update');
    Route::delete('/floors/{floor}', [\App\Http\Controllers\Admin\FloorController::class, 'destroy'])->name('floors.destroy');

    // Apartments (Căn hộ/Phòng)
    Route::get('/apartments', [\App\Http\Controllers\Admin\ApartmentController::class, 'index'])->name('apartments.index');
    Route::get('/apartments/create', [\App\Http\Controllers\Admin\ApartmentController::class, 'create'])->name('apartments.create');
    Route::post('/apartments', [\App\Http\Controllers\Admin\ApartmentController::class, 'store'])->name('apartments.store');
    Route::get('/apartments/import-template', [\App\Http\Controllers\Admin\ApartmentController::class, 'downloadTemplate'])->name('apartments.import-template');
    Route::post('/apartments/import', [\App\Http\Controllers\Admin\ApartmentController::class, 'import'])->name('apartments.import');
    Route::get('/apartments/{apartment}', [\App\Http\Controllers\Admin\ApartmentController::class, 'show'])->name('apartments.show');
    Route::get('/apartments/{apartment}/edit', [\App\Http\Controllers\Admin\ApartmentController::class, 'edit'])->name('apartments.edit');
    Route::put('/apartments/{apartment}', [\App\Http\Controllers\Admin\ApartmentController::class, 'update'])->name('apartments.update');
    Route::delete('/apartments/{apartment}', [\App\Http\Controllers\Admin\ApartmentController::class, 'destroy'])->name('apartments.destroy');
    Route::post('/apartments/{apartment}/assign-owner', [\App\Http\Controllers\Admin\ApartmentController::class, 'assignOwner'])->name('apartments.assign-owner');

    // Apartment Types (Loại căn hộ)
    Route::get('/apartment-types', [\App\Http\Controllers\Admin\ApartmentTypeController::class, 'index'])->name('apartment-types.index');
    Route::get('/apartment-types/create', [\App\Http\Controllers\Admin\ApartmentTypeController::class, 'create'])->name('apartment-types.create');
    Route::post('/apartment-types', [\App\Http\Controllers\Admin\ApartmentTypeController::class, 'store'])->name('apartment-types.store');
    Route::get('/apartment-types/{apartmentType}', [\App\Http\Controllers\Admin\ApartmentTypeController::class, 'show'])->name('apartment-types.show');
    Route::get('/apartment-types/{apartmentType}/edit', [\App\Http\Controllers\Admin\ApartmentTypeController::class, 'edit'])->name('apartment-types.edit');
    Route::put('/apartment-types/{apartmentType}', [\App\Http\Controllers\Admin\ApartmentTypeController::class, 'update'])->name('apartment-types.update');
    Route::delete('/apartment-types/{apartmentType}', [\App\Http\Controllers\Admin\ApartmentTypeController::class, 'destroy'])->name('apartment-types.destroy');


    // Xoá mềm cư dân khỏi phòng
    Route::delete('/residents/{id}', [ResidentManageController::class, 'destroy'])->name('residents.destroy');
    Route::patch('/residents/{id}/status', [ResidentManageController::class, 'updateStatus'])->name('residents.update-status');

    // Thông báo (Notifications)
    Route::get('/notifications', [\App\Http\Controllers\Admin\NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/mark-read/{id?}', [\App\Http\Controllers\Admin\NotificationController::class, 'markRead'])->name('notifications.mark-read');

    // Điện nước & hoá đơn
    Route::get('/utility-readings', [UtilityMeterController::class, 'index'])->name('utility-readings.index');
    Route::get('/utility-readings/create', [UtilityMeterController::class, 'create'])->name('utility-readings.create');
    Route::post('/utility-readings', [UtilityMeterController::class, 'store'])->name('utility-readings.store');
    Route::get('/utility-readings/batch', [UtilityMeterController::class, 'batchCreate'])->name('utility-readings.batch');
    Route::post('/utility-readings/batch', [UtilityMeterController::class, 'batchStore'])->name('utility-readings.batch.store');
    Route::get('/utility-readings/get-old-value', [UtilityMeterController::class, 'getOldValue'])->name('utility-readings.get-old-value');
    Route::post('/utility-readings/ocr', [UtilityMeterController::class, 'ocr'])->name('utility-readings.ocr');

    Route::get('/utility-readings/{id}', [UtilityMeterController::class, 'show'])->name('utility-readings.show');
    Route::get('/utility-readings/{id}/edit', [UtilityMeterController::class, 'edit'])->name('utility-readings.edit');
    Route::put('/utility-readings/{id}', [UtilityMeterController::class, 'update'])->name('utility-readings.update');
    Route::delete('/utility-readings/{id}', [UtilityMeterController::class, 'destroy'])->name('utility-readings.destroy');
    Route::post('/utility-readings/{id}/approve', [UtilityMeterController::class, 'approve'])->name('utility-readings.approve');
    Route::post('/utility-readings/{id}/reject', [UtilityMeterController::class, 'reject'])->name('utility-readings.reject');
    Route::post('/utility-readings/batch-approve', [UtilityMeterController::class, 'batchApprove'])->name('utility-readings.batch-approve');
    Route::delete('/utility-readings/{id}/image', [UtilityMeterController::class, 'removeImage'])->name('utility-readings.remove-image');

    Route::get('/service-prices', [ServicePriceController::class, 'index'])->name('service-prices.index');
    Route::post('/service-prices', [ServicePriceController::class, 'store'])->name('service-prices.store');
    Route::put('/service-prices/{id}', [ServicePriceController::class, 'update'])->name('service-prices.update');
    Route::delete('/service-prices/{id}', [ServicePriceController::class, 'destroy'])->name('service-prices.destroy');

    Route::get('/thu-tien-thu-cong', [\App\Http\Controllers\Admin\ManualPaymentController::class, 'index'])->name('manual-payment.index');

    Route::post('/thu-tien-thu-cong/process', [\App\Http\Controllers\Admin\ManualPaymentController::class, 'process'])->name('manual-payment.process');
    Route::get('/invoices', [InvoiceController::class, 'index'])->name('invoices.index');
    Route::get('/invoices/stats', [InvoiceController::class, 'stats'])->name('invoices.stats');
    Route::get('/invoices/create', [InvoiceController::class, 'create'])->name('invoices.create');
    Route::post('/invoices', [InvoiceController::class, 'store'])->name('invoices.store');

    Route::get('/invoices/batch', [InvoiceController::class, 'batchCreate'])->name('invoices.batch');
    Route::post('/invoices/batch', [InvoiceController::class, 'batchStore'])->name('invoices.batch.store');

    // Route 'generate' đã bị xóa — chức năng xuất hàng loạt dùng /invoices/batch thay thế
    Route::get('/invoices/apartment/{apartment}', [InvoiceController::class, 'apartmentInvoices'])->name('invoices.apartment');
    Route::get('/invoices/{invoice}', [InvoiceController::class, 'show'])->name('invoices.show');
    Route::get('/invoices/{invoice}/edit', [InvoiceController::class, 'edit'])->name('invoices.edit');
    Route::put('/invoices/{invoice}', [InvoiceController::class, 'update'])->name('invoices.update');
    Route::match(['post', 'patch'], '/invoices/{invoice}/mark-paid', [InvoiceController::class, 'markAsPaid'])->name('invoices.mark-paid');
    Route::post('/invoices/{invoice}/cancel', [InvoiceController::class, 'cancelInvoice'])->name('invoices.cancel');
    Route::post('/invoices/batch-resend-notification', [InvoiceController::class, 'batchResendNotification'])->name('invoices.batch-resend-notification');
    Route::post('/invoices/{invoice}/resend-notification', [InvoiceController::class, 'resendNotification'])->name('invoices.resend-notification');
    Route::get('/invoices/{invoice}/print', [InvoiceController::class, 'printInvoice'])->name('invoices.print');
    Route::post('/payments/{payment}/refund', [InvoiceController::class, 'refundPayment'])->name('payments.refund');
    Route::get('/payments/{payment}/receipt', [InvoiceController::class, 'printReceipt'])->name('payments.receipt');

    // Danh sách cư dân
    Route::get('/residents', [ResidentManageController::class, 'index'])->name('residents.index');
    Route::get('/residents/{id}', [ResidentManageController::class, 'show'])->name('residents.show');
    // Quản lý phản ánh & điều phối kỹ thuật (admin / manager)
    Route::get('/tickets', [App\Http\Controllers\Admin\TicketController::class, 'index'])->name('tickets.index');
    Route::get('/tickets/report', [App\Http\Controllers\Admin\TicketController::class, 'report'])->name('tickets.report');
    Route::post('/tickets/{id}/review/approve', [App\Http\Controllers\Admin\TicketController::class, 'approveReview'])->name('tickets.review.approve');
    Route::post('/tickets/{id}/review/reject', [App\Http\Controllers\Admin\TicketController::class, 'rejectReview'])->name('tickets.review.reject');
    Route::get('/tickets/dispatch', [App\Http\Controllers\Admin\TicketController::class, 'dispatchIndex'])->name('tickets.dispatch');
    Route::get('/tickets/my-tasks', [App\Http\Controllers\Admin\TicketController::class, 'myTasks'])->name('tickets.my-tasks');
    Route::get('/tickets/{id}', [App\Http\Controllers\Admin\TicketController::class, 'show'])->name('tickets.show');
    Route::post('/tickets/{id}/assign', [App\Http\Controllers\Admin\TicketController::class, 'assign'])->name('tickets.assign');
    Route::post('/tickets/{id}/accept', [App\Http\Controllers\Admin\TicketController::class, 'acceptTask'])->name('tickets.accept');
    Route::post('/tickets/{id}/update-progress', [App\Http\Controllers\Admin\TicketController::class, 'updateProgress'])->name('tickets.update-progress');
    Route::post('/tickets/{id}/costs', [App\Http\Controllers\Admin\TicketController::class, 'addCost'])->name('tickets.add-cost');
    Route::delete('/tickets/{id}/costs/{costId}', [App\Http\Controllers\Admin\TicketController::class, 'deleteCost'])->name('tickets.delete-cost');
    Route::post('/tickets/{id}/assign-accused', [App\Http\Controllers\Admin\TicketController::class, 'assignAccused'])->name('tickets.assign-accused');

    // QUẢN LÝ PHƯƠNG TIỆN PHÍA ADMIN
    Route::get('/vehicles', [App\Http\Controllers\Admin\VehicleController::class, 'index'])->name('vehicles.index');
    Route::post('/vehicles',                       [App\Http\Controllers\Admin\VehicleController::class, 'store'])->name('vehicles.store');
    Route::post('/vehicles/{vehicle}/assign-lot',  [App\Http\Controllers\Admin\VehicleController::class, 'assignLot'])->name('vehicles.assignLot');
    Route::post('/vehicles/{vehicle}/release-lot', [App\Http\Controllers\Admin\VehicleController::class, 'releaseLot'])->name('vehicles.releaseLot');
    Route::post('/vehicles/{vehicle}/approve',     [App\Http\Controllers\Admin\VehicleController::class, 'approve'])->name('vehicles.approve');
    Route::post('/vehicles/{vehicle}/lock',        [App\Http\Controllers\Admin\VehicleController::class, 'lock'])->name('vehicles.lock');
    Route::post('/vehicles/{vehicle}/unlock',      [App\Http\Controllers\Admin\VehicleController::class, 'unlock'])->name('vehicles.unlock');
    Route::delete('/vehicles/{vehicle}',           [App\Http\Controllers\Admin\VehicleController::class, 'destroy'])->name('vehicles.destroy');
    
    // LỊCH SỬ RA VÀO (Admin)
    Route::get('/vehicle-logs', [App\Http\Controllers\Admin\VehicleLogController::class, 'index'])->name('vehicle-logs.index');
    Route::get('/visitor-logs', [App\Http\Controllers\Admin\VisitorLogController::class, 'index'])->name('visitor-logs.index');

    // QUẢN LÝ LỐT ĐỖ XE
    Route::get('/parking-lots', [App\Http\Controllers\Admin\ParkingLotController::class, 'index'])->name('parking-lots.index');
    Route::post('/parking-lots', [App\Http\Controllers\Admin\ParkingLotController::class, 'store'])->name('parking-lots.store');
    Route::put('/parking-lots/{parkingLot}', [App\Http\Controllers\Admin\ParkingLotController::class, 'update'])->name('parking-lots.update');
    Route::delete('/parking-lots/{parkingLot}', [App\Http\Controllers\Admin\ParkingLotController::class, 'destroy'])->name('parking-lots.destroy');

    // Quản lý tiện ích chung cư (Facilities)
    Route::get('/amenities', [AdminFacilityController::class, 'index'])->name('amenities.index');
    Route::get('/amenities/create', [AdminFacilityController::class, 'create'])->name('amenities.create');
    Route::get('/amenities/floors/{block}', [AdminFacilityController::class, 'getFloorsByBlock'])->name('amenities.floors');
    Route::post('/amenities', [AdminFacilityController::class, 'store'])->name('amenities.store');
    Route::get('/amenities/statistics', [AdminFacilityController::class, 'statistics'])->name('amenities.statistics');
    Route::get('/amenities/statistics/export', [AdminFacilityController::class, 'exportExcel'])->name('amenities.statistics.export');
    Route::get('/amenities/bookings', [AdminFacilityController::class, 'bookings'])->name('amenities.bookings');
    Route::get('/amenities/{facility}', [AdminFacilityController::class, 'show'])->name('amenities.show');
    Route::get('/amenities/{facility}/edit', [AdminFacilityController::class, 'edit'])->name('amenities.edit');
    Route::put('/amenities/{facility}', [AdminFacilityController::class, 'update'])->name('amenities.update');
    Route::delete('/amenities/{facility}', [AdminFacilityController::class, 'destroy'])->name('amenities.destroy');
    Route::post('/amenities/{facility}/images', [AdminFacilityController::class, 'storeImage'])->name('amenities.images.store');
    Route::delete('/amenities/{facility}/images/{index}', [AdminFacilityController::class, 'destroyImage'])->name('amenities.images.destroy');
    Route::patch('/amenities/{facility}/status', [AdminFacilityController::class, 'updateStatus'])->name('amenities.status');
    Route::post('/facility-bookings/{booking}/approve', [AdminFacilityController::class, 'approveBooking'])->name('amenities.bookings.approve');
    Route::post('/facility-bookings/{booking}/reject', [AdminFacilityController::class, 'rejectBooking'])->name('amenities.bookings.reject');
    Route::post('/facility-bookings/{booking}/cancel', [AdminFacilityController::class, 'cancelBooking'])->name('amenities.bookings.cancel');
    Route::patch('/facility-bookings/{booking}/status', [AdminFacilityController::class, 'updateBookingStatus'])->name('amenities.bookings.status');

    Route::post('/announcements/{id}/toggle-pin', [\App\Http\Controllers\Admin\AnnouncementController::class, 'togglePin'])->name('announcements.toggle-pin');
    Route::resource('/announcements', \App\Http\Controllers\Admin\AnnouncementController::class)->names('announcements');
    // Quản lý tài khoản người dùng
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::get('/users/{id}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');
    Route::put('/users/{id}/update-status', [UserController::class, 'updateStatus'])->name('users.updateStatus');
    Route::put('/users/{id}/reset-password', [UserController::class, 'resetPassword'])->name('users.resetPassword');
    Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');

    // Quản lý nhân sự
    Route::resource('departments', \App\Http\Controllers\Admin\DepartmentController::class)->except(['create', 'show', 'edit']);
    Route::resource('staffs', \App\Http\Controllers\Admin\StaffController::class);
    
    // Phân ca làm việc (Khung lịch)
    Route::get('/schedules', [\App\Http\Controllers\Admin\ScheduleController::class, 'index'])->name('schedules.index');
    
    // Cập nhật định mức ca
    Route::post('/schedules/{shift}/requirements', [\App\Http\Controllers\Admin\ScheduleController::class, 'updateRequirements'])->name('schedules.requirements');
    
    // API & Phân công (RESTful StaffSchedules)
    Route::get('/api/staffs', [\App\Http\Controllers\Admin\StaffScheduleController::class, 'getStaffs'])->name('api.staffs');
    Route::get('/api/floors', [\App\Http\Controllers\Admin\StaffScheduleController::class, 'getFloors'])->name('api.floors');
    Route::post('/staff-schedules', [\App\Http\Controllers\Admin\StaffScheduleController::class, 'store'])->name('staff-schedules.store');
    Route::delete('/staff-schedules/{staff_schedule}', [\App\Http\Controllers\Admin\StaffScheduleController::class, 'destroy'])->name('staff-schedules.destroy');
    Route::patch('/staff-schedules/{staff_schedule}/leader', [\App\Http\Controllers\Admin\StaffScheduleController::class, 'toggleLeader'])->name('staff-schedules.leader');
    Route::post('/staff-schedules/copy', [\App\Http\Controllers\Admin\StaffScheduleController::class, 'copy'])->name('staff-schedules.copy');
    Route::post('/staff-schedules/preview-copy', [\App\Http\Controllers\Admin\StaffScheduleController::class, 'previewCopy'])->name('staff-schedules.preview-copy');

    // Quản lý mã mời
    Route::get('/invitations', [InvitationController::class, 'index'])->name('invitations.index');
    Route::post('/invitations', [InvitationController::class, 'store'])->name('invitations.store');
    Route::delete('/invitations/{id}', [InvitationController::class, 'destroy'])->name('invitations.destroy');

    // Trang cá nhân quản trị viên
    Route::get('/profile', [\App\Http\Controllers\Admin\ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile', [\App\Http\Controllers\Admin\ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/change-password', [\App\Http\Controllers\Admin\ProfileController::class, 'changePassword'])->name('profile.change-password');



    // Quản lý bài đăng cư dân (Admin Portal)
    Route::get('/posts', [\App\Http\Controllers\Admin\PostController::class, 'index'])->name('posts.index');
    Route::post('/posts/{id}/restore', [\App\Http\Controllers\Admin\PostController::class, 'restore'])->name('posts.restore');
    Route::post('/posts/{id}/toggle-status', [\App\Http\Controllers\Admin\PostController::class, 'toggleStatus'])->name('posts.toggle-status');
    Route::post('/posts/{id}/dismiss-reports', [\App\Http\Controllers\Admin\PostController::class, 'dismissReports'])->name('posts.dismiss-reports');
    Route::get('/posts/{id}/json', [\App\Http\Controllers\Admin\PostController::class, 'getPostJson'])->name('posts.json');
    Route::delete('/posts/{id}', [\App\Http\Controllers\Admin\PostController::class, 'destroy'])->name('posts.destroy');
    Route::post('/comments/{id}/dismiss-reports', [\App\Http\Controllers\Admin\PostController::class, 'dismissCommentReports'])->name('comments.dismiss-reports');
    Route::delete('/comments/{id}', [\App\Http\Controllers\Admin\PostController::class, 'destroyComment'])->name('comments.destroy');
    Route::post('/comments/{id}/restore', [\App\Http\Controllers\Admin\PostController::class, 'restoreComment'])->name('comments.restore');
    Route::post('/users/{id}/ban-posting', [\App\Http\Controllers\Admin\PostController::class, 'banPosting'])->name('users.ban-posting');
    Route::post('/users/{id}/ban-commenting', [\App\Http\Controllers\Admin\PostController::class, 'banCommenting'])->name('users.ban-commenting');

    // Quản lý vệ sinh (Admin & Manager)
    Route::get('/cleaning-tasks', [\App\Http\Controllers\Admin\CleaningTaskController::class, 'index'])->name('cleaning-tasks.index');
    Route::get('/cleaning-tasks/create', [\App\Http\Controllers\Admin\CleaningTaskController::class, 'create'])->name('cleaning-tasks.create');
    Route::post('/cleaning-tasks', [\App\Http\Controllers\Admin\CleaningTaskController::class, 'store'])->name('cleaning-tasks.store');
    Route::delete('/cleaning-tasks/{id}', [\App\Http\Controllers\Admin\CleaningTaskController::class, 'destroy'])->name('cleaning-tasks.destroy');

    Route::get('/cleaning-reports', [\App\Http\Controllers\Admin\CleaningReportController::class, 'index'])->name('cleaning-reports.index');
    Route::patch('/cleaning-reports/{id}/status', [\App\Http\Controllers\Admin\CleaningReportController::class, 'updateStatus'])->name('cleaning-reports.update-status');

};

Route::middleware(['admin'])->prefix('admin')->name('admin.')->group($portalRoutes);
Route::middleware(['manager'])->prefix('manager')->name('manager.')->group($portalRoutes);
Route::middleware(['staff'])->prefix('staff')->name('staff.')->group($portalRoutes);
Route::middleware(['technician'])->prefix('technician')->name('technician.')->group($portalRoutes);


Route::middleware(['security'])->group(function () {
    Route::get('/security/dashboard', function () {
        $vehiclesInside = \App\Models\VehicleLog::where('status', 'inside')->count();
        $todayCheckins = \App\Models\VehicleLog::whereDate('check_in_at', today())->count();
        $todayCheckouts = \App\Models\VehicleLog::whereDate('check_out_at', today())->count();

        $recentLogs = \App\Models\VehicleLog::with('vehicle')
            ->latest()
            ->take(8)
            ->get();

        return view('security.dashboard.index', compact(
            'vehiclesInside', 'todayCheckins', 'todayCheckouts', 'recentLogs'
        ));
    })->name('security.dashboard');

    // Quét QR xe vào
    Route::get('/security/vehicle-checkin', [\App\Http\Controllers\Security\VehicleCheckinController::class, 'index'])->name('security.vehicle-checkin.index');
    Route::post('/security/vehicle-checkin/scan', [\App\Http\Controllers\Security\VehicleCheckinController::class, 'scan'])->name('security.vehicle-checkin.scan');
    Route::post('/security/vehicle-checkin/confirm', [\App\Http\Controllers\Security\VehicleCheckinController::class, 'checkin'])->name('security.vehicle-checkin.confirm');
    Route::post('/security/vehicle-checkin/guest', [\App\Http\Controllers\Security\VehicleCheckinController::class, 'guestCheckin'])->name('security.vehicle-checkin.guest');

    // Quét QR xe ra
    Route::get('/security/vehicle-checkout', [\App\Http\Controllers\Security\VehicleCheckoutController::class, 'index'])->name('security.vehicle-checkout.index');
    Route::post('/security/vehicle-checkout/scan', [\App\Http\Controllers\Security\VehicleCheckoutController::class, 'scan'])->name('security.vehicle-checkout.scan');
    Route::post('/security/vehicle-checkout/confirm', [\App\Http\Controllers\Security\VehicleCheckoutController::class, 'checkout'])->name('security.vehicle-checkout.confirm');

    // Xem lịch sử xe cho bảo vệ
    Route::get('/security/vehicle-logs', [\App\Http\Controllers\Admin\VehicleLogController::class, 'index'])->name('security.vehicle-logs.index');
});

// DASHBOARD CLEANING ROUTES
Route::middleware(['cleaning'])->group(function () {
    Route::get('/cleaning/dashboard', [\App\Http\Controllers\Cleaning\DashboardController::class, 'index'])->name('cleaning.dashboard');

    Route::get('/cleaning/profile', [\App\Http\Controllers\Cleaning\ProfileController::class, 'index'])->name('cleaning.profile');
    Route::put('/cleaning/profile', [\App\Http\Controllers\Cleaning\ProfileController::class, 'update'])->name('cleaning.profile.update');
    Route::put('/cleaning/profile/change-password', [\App\Http\Controllers\Cleaning\ProfileController::class, 'changePassword'])->name('cleaning.profile.change-password');

    Route::get('/cleaning/report', [\App\Http\Controllers\Cleaning\ReportController::class, 'index'])->name('cleaning.report');
    Route::post('/cleaning/report', [\App\Http\Controllers\Cleaning\ReportController::class, 'store'])->name('cleaning.report.store');
    Route::get('/cleaning/report/{id}', [\App\Http\Controllers\Cleaning\ReportController::class, 'show'])->name('cleaning.report.show');

    Route::get('/cleaning/tasks', [\App\Http\Controllers\Cleaning\TaskController::class, 'index'])->name('cleaning.tasks');
    Route::get('/cleaning/tasks/{id}', [\App\Http\Controllers\Cleaning\TaskController::class, 'show'])->name('cleaning.tasks.show');
    Route::patch('/cleaning/tasks/{id}/status', [\App\Http\Controllers\Cleaning\TaskController::class, 'updateStatus'])->name('cleaning.tasks.update-status');
    Route::patch('/cleaning/tasks/{id}/checklist', [\App\Http\Controllers\Cleaning\TaskController::class, 'updateChecklist'])->name('cleaning.tasks.update-checklist');
});

// DASHBOARD RESIDENT ROUTES
Route::middleware(['resident'])->group(function () {
    Route::get('/resident/dashboard', function (\Illuminate\Http\Request $request) {
        $user = auth()->user();
        $apartment = $user->apartment;
        $apartmentId = $user->apartment_id;

        // Số dư nợ
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

        // Thông báo từ ban quản lý
        $announcements = \App\Models\Announcement::where('status', 'published')
            ->orderByDesc('pinned')
            ->latest()
            ->limit(5)
            ->get();

        // Lấy thông báo khẩn cấp dạng Popup mới nhất
        $popupAnnouncement = \App\Models\Announcement::where('status', 'published')
            ->where('is_popup', true)
            ->latest()
            ->first();

        // Bài viết cư dân (full feed với filter + pagination)
        $postQuery = \App\Models\Post::with(['user', 'images', 'comments', 'likedByCurrentUser'])
            ->withCount(['likes', 'comments'])
            ->where('status', 'published')
            ->whereDoesntHave('reports', function($q) {
                $q->where('user_id', auth()->id());
            })
            ->whereDoesntHave('hides', function($q) {
                $q->where('user_id', auth()->id());
            });

        $postQuery->orderBy('created_at', 'desc');

        $posts = $postQuery->paginate(10)->withQueryString();

        return view('resident.home.index', compact(
            'user', 'apartment', 'totalUnpaidAmount', 'dueDate',
            'announcements', 'posts', 'popupAnnouncement'
        ));
    })->name('resident.dashboard');

    Route::get('/resident/contact', function () {
        return view('resident.contact.index');
    })->name('resident.contact');

    // Profile
    Route::get('/resident/profile', [ProfileController::class, 'index'])->name('resident.profile.index');
    Route::put('/resident/profile', [ProfileController::class, 'update'])->name('resident.profile.update');
    Route::put('/resident/profile/change-password', [ProfileController::class, 'changePassword'])->name('resident.profile.change-password');
    Route::post('/resident/profile/leave', [ProfileController::class, 'leaveApartment'])->name('resident.profile.leave');
    Route::post('/resident/profile/transfer-owner/send-otp', [ProfileController::class, 'sendTransferOtp'])->name('resident.profile.transfer-owner.send-otp');
    Route::post('/resident/profile/transfer-owner/verify', [ProfileController::class, 'verifyTransferOwner'])->name('resident.profile.transfer-owner.verify');

    // Hoá đơn cư dân
    Route::get('/resident/invoices', [ResidentInvoiceController::class, 'index'])->name('resident.invoices.index');
    Route::get('/resident/invoices/history', [ResidentInvoiceController::class, 'history'])->name('resident.invoices.history');
    Route::get('/resident/invoices/vnpay-return', [ResidentInvoiceController::class, 'vnpayReturn'])->name('resident.invoices.vnpay-return');
    Route::get('/resident/invoices/{id}', [ResidentInvoiceController::class, 'show'])->name('resident.invoices.show');
    Route::get('/resident/invoices/{id}/print', [ResidentInvoiceController::class, 'printInvoice'])->name('resident.invoices.print');
    Route::post('/resident/invoices/{invoice}/complaint-water', [ResidentInvoiceController::class, 'complaintWater'])->name('resident.invoices.complaint-water');
    Route::post('/resident/invoices/pay', [ResidentInvoiceController::class, 'pay'])->name('resident.invoices.pay');
    Route::post('/resident/invoices/pay-details', [ResidentInvoiceController::class, 'payDetails'])->name('resident.invoices.pay-details');
    Route::get('/resident/payments/{payment}/receipt', [ResidentInvoiceController::class, 'printReceipt'])->name('resident.payments.receipt');

    // Quản lý thành viên gia đình & nhân khẩu & mã mời
    Route::get('/resident/members', [\App\Http\Controllers\Resident\MemberController::class, 'index'])->name('resident.members.index');
    Route::post('/resident/members/declared', [\App\Http\Controllers\Resident\MemberController::class, 'storeDeclared'])->name('resident.members.declared.store');
    Route::delete('/resident/members/declared/{member}', [\App\Http\Controllers\Resident\MemberController::class, 'destroyDeclared'])->name('resident.members.declared.destroy');
    Route::post('/resident/members/invitations', [\App\Http\Controllers\Resident\MemberController::class, 'storeInvite'])->name('resident.members.invitations.store');
    Route::delete('/resident/members/invitations/{id}', [\App\Http\Controllers\Resident\MemberController::class, 'destroyInvite'])->name('resident.members.invitations.destroy');
    Route::delete('/resident/members/registered/{id}', [\App\Http\Controllers\Resident\MemberController::class, 'destroyRegistered'])->name('resident.members.registered.destroy');
    Route::post('/resident/members/approve/{id}', [\App\Http\Controllers\Resident\MemberController::class, 'approveResident'])->name('resident.members.approve');
    Route::post('/resident/members/reject/{id}', [\App\Http\Controllers\Resident\MemberController::class, 'rejectResident'])->name('resident.members.reject');

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

    Route::get('/resident/vehicles/{vehicle}/qr/download', [App\Http\Controllers\Resident\VehicleController::class, 'downloadQr'])
        ->name('resident.vehicles.qr.download');

    // QUẢN LÝ KHÁCH PHÍA CƯ DÂN
    Route::get('/resident/visitors', [\App\Http\Controllers\Resident\VisitorController::class, 'index'])->name('resident.visitors.index');
    Route::get('/resident/visitors/create', [\App\Http\Controllers\Resident\VisitorController::class, 'create'])->name('resident.visitors.create');
    Route::post('/resident/visitors', [\App\Http\Controllers\Resident\VisitorController::class, 'store'])->name('resident.visitors.store');
    Route::get('/resident/visitors/{id}', [\App\Http\Controllers\Resident\VisitorController::class, 'show'])->name('resident.visitors.show');
    Route::post('/resident/visitors/{id}/approve', [\App\Http\Controllers\Resident\VisitorController::class, 'approve'])->name('resident.visitors.approve');
    Route::post('/resident/visitors/{id}/reject', [\App\Http\Controllers\Resident\VisitorController::class, 'reject'])->name('resident.visitors.reject');
    Route::delete('/resident/visitors/{id}', [\App\Http\Controllers\Resident\VisitorController::class, 'destroy'])->name('resident.visitors.destroy');
    // PHẢN ÁNH SỰ CỐ PHÍA CƯ DÂN
    Route::get('/resident/tickets', [ResidentTicketController::class, 'index'])->name('resident.tickets.index');
    Route::get('/resident/tickets/create', [ResidentTicketController::class, 'create'])->name('resident.tickets.create');
    Route::post('/resident/tickets', [ResidentTicketController::class, 'store'])->name('resident.tickets.store');
    Route::get('/resident/tickets/{id}', [ResidentTicketController::class, 'show'])->name('resident.tickets.show');
    Route::post('/resident/tickets/{id}/cancel', [ResidentTicketController::class, 'cancel'])->name('resident.tickets.cancel');
    Route::post('/resident/tickets/{id}/feedback', [ResidentTicketController::class, 'feedback'])->name('resident.tickets.feedback');

    // GỢI Ý KHẮC PHỤC
    Route::get('/resident/tips/water-valve', function() { return view('resident.tips.water-valve'); })->name('resident.tips.water-valve');
    Route::get('/resident/tips/circuit-breaker', function() { return view('resident.tips.circuit-breaker'); })->name('resident.tips.circuit-breaker');

    // HƯỚNG DẪN SỬ DỤNG
    Route::get('/resident/guide', function() { return view('resident.guide.index'); })->name('resident.guide');

    // BẢNG TIN & BÌNH LUẬN PHÍA CƯ DÂN
    Route::get('/resident/posts', function (\Illuminate\Http\Request $request) {
        $user = auth()->user();
        $posts = \App\Models\Post::with(['user', 'images', 'comments', 'likedByCurrentUser'])
            ->withCount(['likes', 'comments'])
            ->where('status', 'published')
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('resident.posts.index', compact('posts', 'user'));
    })->name('resident.posts.index');
    Route::get('/resident/posts/create', function() { return view('resident.posts.create'); })->name('resident.posts.create');
    Route::post('/resident/posts', [\App\Http\Controllers\Resident\PostController::class, 'store'])->name('resident.posts.store');
    Route::get('/resident/posts/{id}', [\App\Http\Controllers\Resident\PostController::class, 'show'])->name('resident.posts.show');
    Route::get('/resident/posts/{id}/edit', [\App\Http\Controllers\Resident\PostController::class, 'edit'])->name('resident.posts.edit');
    Route::put('/resident/posts/{id}', [\App\Http\Controllers\Resident\PostController::class, 'update'])->name('resident.posts.update');
    Route::delete('/resident/posts/{id}', [\App\Http\Controllers\Resident\PostController::class, 'destroy'])->name('resident.posts.destroy');
    Route::post('/resident/posts/{id}/comments', [\App\Http\Controllers\Resident\PostController::class, 'storeComment'])->name('resident.posts.comments.store');
    Route::put('/resident/comments/{id}', [\App\Http\Controllers\Resident\PostController::class, 'updateComment'])->name('resident.comments.update');
    Route::delete('/resident/comments/{id}', [\App\Http\Controllers\Resident\PostController::class, 'destroyComment'])->name('resident.comments.destroy');
    Route::post('/resident/posts/{id}/report', [\App\Http\Controllers\Resident\PostController::class, 'report'])->name('resident.posts.report');
    Route::post('/resident/posts/{id}/hide', [\App\Http\Controllers\Resident\PostController::class, 'hide'])->name('resident.posts.hide');
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

    // CHATBOT AI CƯ DÂN
    Route::get('/resident/chatbot/history', [\App\Http\Controllers\Resident\ChatbotController::class, 'getHistory'])->name('resident.chatbot.history');
    Route::post('/resident/chatbot/message', [\App\Http\Controllers\Resident\ChatbotController::class, 'sendMessage'])
        ->middleware('throttle:10,1')
        ->name('resident.chatbot.message');
    Route::post('/resident/chatbot/clear', [\App\Http\Controllers\Resident\ChatbotController::class, 'clearHistory'])->name('resident.chatbot.clear');
    Route::post('/resident/tickets/{id}/respond-accusation', [ResidentTicketController::class, 'respondAccusation'])->name('resident.tickets.respond-accusation');
});

// =========================================================================
// DASHBOARD RECEPTIONIST ROUTES
// =========================================================================
Route::middleware(['receptionist'])->prefix('receptionist')->name('receptionist.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [\App\Http\Controllers\Receptionist\DashboardController::class, 'index'])->name('dashboard');

    // Bưu phẩm
    Route::get('/parcels',                          [\App\Http\Controllers\Receptionist\ParcelController::class, 'index'])->name('parcels.index');
    Route::get('/parcels/create',                   [\App\Http\Controllers\Receptionist\ParcelController::class, 'create'])->name('parcels.create');
    Route::post('/parcels',                         [\App\Http\Controllers\Receptionist\ParcelController::class, 'store'])->name('parcels.store');
    Route::get('/parcels/{id}',                     [\App\Http\Controllers\Receptionist\ParcelController::class, 'show'])->name('parcels.show');
    Route::post('/parcels/{id}/received',           [\App\Http\Controllers\Receptionist\ParcelController::class, 'markReceived'])->name('parcels.received');
    Route::post('/parcels/{id}/returned',           [\App\Http\Controllers\Receptionist\ParcelController::class, 'markReturned'])->name('parcels.returned');
    Route::post('/parcels/{id}/notify',             [\App\Http\Controllers\Receptionist\ParcelController::class, 'markNotified'])->name('parcels.notify');

    // Phản ánh
    Route::get('/tickets',                          [\App\Http\Controllers\Receptionist\TicketController::class, 'index'])->name('tickets.index');
    Route::get('/tickets/create',                   [\App\Http\Controllers\Receptionist\TicketController::class, 'create'])->name('tickets.create');
    Route::post('/tickets',                         [\App\Http\Controllers\Receptionist\TicketController::class, 'store'])->name('tickets.store');
    Route::get('/tickets/{id}',                     [\App\Http\Controllers\Receptionist\TicketController::class, 'show'])->name('tickets.show');

    // Đặt lịch tiện ích
    Route::get('/amenities',                        [\App\Http\Controllers\Receptionist\AmenityController::class, 'index'])->name('amenities.index');
    Route::get('/amenities/scan-qr',                [\App\Http\Controllers\Receptionist\AmenityController::class, 'scanQr'])->name('amenities.scan-qr');
    Route::post('/amenities/scan-qr/scan',          [\App\Http\Controllers\Receptionist\AmenityController::class, 'processQrScan'])->name('amenities.scan-qr.scan');
    Route::post('/amenities/scan-qr/checkin',       [\App\Http\Controllers\Receptionist\AmenityController::class, 'checkin'])->name('amenities.scan-qr.checkin');
    Route::post('/amenities/{id}/approve',          [\App\Http\Controllers\Receptionist\AmenityController::class, 'approveBooking'])->name('amenities.approve');
    Route::post('/amenities/{id}/reject',           [\App\Http\Controllers\Receptionist\AmenityController::class, 'rejectBooking'])->name('amenities.reject');

    // Quản lý khách
    Route::get('/walk-in',                          [\App\Http\Controllers\Receptionist\VisitorController::class, 'walkIn'])->name('walk-in.index');
    Route::get('/walk-in/residents',                [\App\Http\Controllers\Receptionist\VisitorController::class, 'getResidents'])->name('walk-in.residents');
    Route::post('/walk-in',                         [\App\Http\Controllers\Receptionist\VisitorController::class, 'store'])->name('walk-in.store');
    Route::post('/walk-in/checkout',                [\App\Http\Controllers\Receptionist\VisitorController::class, 'checkout'])->name('walk-in.checkout');
    Route::get('/visitor-log',                      [\App\Http\Controllers\Receptionist\VisitorController::class, 'log'])->name('visitor-log.index');

    // Trang cá nhân
    Route::get('/profile',                          [\App\Http\Controllers\Receptionist\ProfileController::class, 'index'])->name('profile');
    Route::put('/profile',                          [\App\Http\Controllers\Receptionist\ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/change-password',          [\App\Http\Controllers\Receptionist\ProfileController::class, 'changePassword'])->name('profile.change-password');
});

// Shortcut route
Route::get('/receptionist', function () {
    if (! Auth::check()) return redirect()->route('receptionist.login');
    return redirect()->route('receptionist.dashboard');
});

// Fallback route to serve uploaded public storage files
Route::get('/storage/{any}', function ($any) {
    $path = storage_path('app/public/' . $any);
    if (file_exists($path)) {
        return response()->file($path);
    }
    abort(404);
})->where('any', '.*');