<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class SystemLogger
{
    /**
     * Ghi log thao tác hệ thống sử dụng Spatie Activitylog.
     *
     * @param string $action Thao tác thực hiện (VD: 'Đăng nhập', 'Cập nhật nhân viên')
     * @param string $target Đối tượng bị tác động (VD: 'Hệ thống', 'Nhân viên: Nguyễn Văn A')
     * @param array $properties Thông tin thêm (tuỳ chọn)
     * @return void
     */
    public static function log(string $action, string $target, array $properties = []): void
    {
        $causer = Auth::user();

        // Tự động thêm IP, User Agent và Đối tượng vào properties
        $mergedProperties = array_merge([
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'target'     => $target,
        ], $properties);

        $activity = activity('system_security')
            ->withProperties($mergedProperties);

        if ($causer) {
            $activity->causedBy($causer);
        }

        $activity->log($action);
    }
}
