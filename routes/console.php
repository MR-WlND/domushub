<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Hàng ngày lúc 00:05: Quét hóa đơn phí gửi xe quá hạn → chuyển xe sang pending_renewal
Schedule::command('vehicles:check-renewal')->dailyAt('00:05');

// Ngày 1 mỗi tháng lúc 06:00: Tạo hóa đơn phí gửi xe tháng mới
Schedule::command('parking:calculate-fee')->monthlyOn(1, '06:00');

// Kiểm tra tạm trú/tạm vắng hết hạn mỗi đêm lúc 01:00
Schedule::command('temporary-registration:check-expiry')->dailyAt('01:00');

// Kiểm tra khách thuê hết hạn hợp đồng mỗi đêm lúc 02:00
Schedule::command('residents:check-leases')->dailyAt('02:00');

// Kiểm tra lịch đặt tiện ích quá hạn (mỗi 15 phút)
Schedule::command('app:check-facility-booking-expiry')->everyFifteenMinutes();

