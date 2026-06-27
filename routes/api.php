<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FacilityBookingController;
use App\Http\Controllers\Admin\FacilityBookingController as AdminFacilityBookingController;

// Resident Facility Booking API (session-based)
Route::middleware(['web', 'resident'])->prefix('facility-bookings')->name('api.facility-bookings.')->group(function () {
    Route::post('/available-slots', [FacilityBookingController::class, 'getAvailableSlots'])->name('available-slots');
    Route::post('/', [FacilityBookingController::class, 'bookSlot'])->name('book');
    Route::get('/', [FacilityBookingController::class, 'getHistory'])->name('history');
    Route::patch('/{id}/cancel', [FacilityBookingController::class, 'cancelBooking'])->name('cancel');
    Route::post('/{id}/pay', [FacilityBookingController::class, 'payBooking'])->name('pay');
    Route::post('/check-in', [FacilityBookingController::class, 'checkIn'])->name('check-in');
});

// Admin Facility Booking API (session-based)
Route::middleware(['web', 'admin'])->prefix('admin/facility-bookings')->name('api.admin.facility-bookings.')->group(function () {
    Route::get('/', [AdminFacilityBookingController::class, 'index'])->name('index');
    Route::patch('/{id}/approve', [AdminFacilityBookingController::class, 'approve'])->name('approve');
    Route::patch('/{id}/cancel', [AdminFacilityBookingController::class, 'cancel'])->name('cancel');
    Route::patch('/{id}/status', [AdminFacilityBookingController::class, 'updateStatus'])->name('status');
});