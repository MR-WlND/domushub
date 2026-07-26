<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Giờ làm việc quy định (ca hành chính mặc định)
    |--------------------------------------------------------------------------
    */
    'start_time'              => env('ATTENDANCE_START_TIME', '08:00'),
    'end_time'                => env('ATTENDANCE_END_TIME', '17:00'),
    'late_threshold_minutes'  => env('ATTENDANCE_LATE_THRESHOLD', 5),

    /*
    |--------------------------------------------------------------------------
    | Vị trí làm việc theo Block chung cư cao cấp
    |--------------------------------------------------------------------------
    */
    'work_locations' => [
        'Sảnh chính – Lễ tân',
        'Block A – Lễ tân',
        'Block A – Bảo vệ cổng',
        'Block B – Lễ tân',
        'Block B – Bảo vệ cổng',
        'Block C – Lễ tân',
        'Block C – Bảo vệ cổng',
        'Bãi xe B1',
        'Bãi xe B2',
        'Khu kỹ thuật / Máy móc',
        'Hồ bơi / Tiện ích',
        'Văn phòng BQL',
        'Toàn tòa nhà',
        'Khác',
    ],

    /*
    |--------------------------------------------------------------------------
    | Ca làm việc (kể cả ca đêm cho bảo vệ 24/7)
    |--------------------------------------------------------------------------
    */
    'shifts' => [
        'full_day'  => 'Ca hành chính (08:00–17:00)',
        'morning'   => 'Ca sáng (06:00–14:00)',
        'afternoon' => 'Ca chiều (14:00–22:00)',
        'night'     => 'Ca đêm (22:00–06:00)',
        'office'    => 'Văn phòng linh hoạt',
    ],

    /*
    |--------------------------------------------------------------------------
    | Cấu hình chi tiết từng ca (số giờ chuẩn, giờ bắt đầu, có ca đêm không)
    |--------------------------------------------------------------------------
    | is_night: true = được hưởng phụ cấp ca đêm (+30%)
    */
    'shift_config' => [
        'full_day'  => ['hours' => 8, 'start' => '08:00', 'end' => '17:00', 'is_night' => false],
        'morning'   => ['hours' => 8, 'start' => '06:00', 'end' => '14:00', 'is_night' => false],
        'afternoon' => ['hours' => 8, 'start' => '14:00', 'end' => '22:00', 'is_night' => false],
        'night'     => ['hours' => 8, 'start' => '22:00', 'end' => '06:00', 'is_night' => true],
        'office'    => ['hours' => 8, 'start' => '08:00', 'end' => '17:00', 'is_night' => false],
    ],

    /*
    |--------------------------------------------------------------------------
    | Ngày lễ Việt Nam (format: d-m, dùng để tính OT ngày lễ × 300%)
    | Cập nhật hàng năm hoặc cấu hình qua env
    |--------------------------------------------------------------------------
    */
    'vietnam_holidays' => [
        '01-01', // Tết Dương lịch
        '28-01', // 30 Tết (2026)
        '29-01', // Mùng 1 Tết (2026)
        '30-01', // Mùng 2 Tết (2026)
        '31-01', // Mùng 3 Tết (2026)
        '01-02', // Mùng 4 Tết (2026)
        '02-02', // Mùng 5 Tết (2026)
        '16-04', // Giỗ tổ Hùng Vương (2026)
        '30-04', // Ngày Giải phóng miền Nam
        '01-05', // Ngày Quốc tế Lao động
        '02-09', // Quốc khánh
        '03-09', // Bù nghỉ Quốc khánh (2026)
    ],

    /*
    |--------------------------------------------------------------------------
    | Trần OT tháng theo Bộ luật Lao động 2019
    |--------------------------------------------------------------------------
    */
    'ot_tran_thang' => 40, // giờ OT tối đa/tháng
    'ot_tran_nam'   => 200, // giờ OT tối đa/năm (cơ bản)
];
