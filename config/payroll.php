<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Cấu hình Module Tính Lương Nhân Viên (Payroll)
    |--------------------------------------------------------------------------
    */

    'so_ngay_cong_chuan' => 26,

    'gio_chuan_ca' => [
        'full_day'  => 8,   // giờ, sau khi trừ nghỉ trưa
        'morning'   => 4,
        'afternoon' => 4,
    ],

    'he_so_ot' => [
        'ngay_thuong' => 1.5,
        'cuoi_tuan'   => 2.0,
        'ngay_le'     => 3.0,
    ],

    'don_gia_phut_di_muon' => 5000, // VNĐ/phút đến muộn

    'khau_tru_toi_da_theo_ngay' => 1.0, // không khấu trừ đi muộn quá 1 công/ngày
];
