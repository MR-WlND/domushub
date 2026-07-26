<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Giờ làm việc quy định
    |--------------------------------------------------------------------------
    | start_time : Giờ bắt đầu ca làm việc (24h format)
    | end_time   : Giờ kết thúc ca làm việc (24h format)
    | late_threshold_minutes : Số phút trễ tối thiểu mới bị tính "đi muộn"
    */
    'start_time'              => env('ATTENDANCE_START_TIME', '08:00'),
    'end_time'                => env('ATTENDANCE_END_TIME', '17:00'),
    'late_threshold_minutes'  => env('ATTENDANCE_LATE_THRESHOLD', 5),

    /*
    |--------------------------------------------------------------------------
    | Vị trí làm việc mặc định
    |--------------------------------------------------------------------------
    */
    'work_locations' => [
        'Sảnh chính',
        'Tầng 1',
        'Tầng 2',
        'Bãi xe',
        'Khu A',
        'Khu B',
        'Khu C',
        'Văn phòng',
        'Khác',
    ],

    /*
    |--------------------------------------------------------------------------
    | Ca làm việc
    |--------------------------------------------------------------------------
    */
    'shifts' => [
        'full_day'  => 'Cả ngày',
        'morning'   => 'Ca sáng',
        'afternoon' => 'Ca chiều',
    ],
];
