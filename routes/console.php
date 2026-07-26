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

// Hàng ngày lúc 23:55: Tự động đánh vắng mặt cho nhân viên không chấm công
// Áp dụng cho tất cả nhân viên nội bộ (admin/manager/staff/technician/security/cleaning)
Schedule::command('attendance:auto-absent')->dailyAt('23:55');

