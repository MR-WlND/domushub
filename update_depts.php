<?php

use App\Models\Department;

// 1. Rename BQL to MGT
$mgt = Department::where('code', 'BQL')->first();
if ($mgt) {
    $mgt->update([
        'code' => 'MGT',
        'is_shift' => false,
        'description' => 'Ban Quản lý chung cư, xử lý khiếu nại, dịch vụ cư dân, và điều hành tổng thể.'
    ]);
}

// 2. Set is_shift and descriptions for others
$desc = [
    'SEC' => 'Quản lý an ninh, tuần tra, kiểm soát ra vào.',
    'TECH' => 'Bảo trì hệ thống điện, nước, kỹ thuật tòa nhà.',
    'REC' => 'Tiếp đón cư dân, khách, tiếp nhận thông tin.',
    'CLEAN' => 'Dọn dẹp vệ sinh khu vực công cộng, cảnh quan.'
];

foreach (['SEC', 'TECH', 'REC', 'CLEAN'] as $code) {
    $dept = Department::where('code', $code)->first();
    if ($dept) {
        $dept->update([
            'is_shift' => true,
            'description' => $desc[$code] ?? null
        ]);
    }
}

echo "Updated departments successfully.";
