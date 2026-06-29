<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;

class SystemLogger
{
    /**
     * Ghi log thao tác hệ thống sử dụng Spatie Activitylog.
     *
     * @param string $logName Tên nhóm log (ví dụ: system, communication)
     * @param string $description Mô tả chi tiết hành động
     * @param mixed|null $subject Đối tượng bị tác động (tuỳ chọn)
     * @param array $properties Thông tin thêm (tuỳ chọn)
     * @return void
     */
    public static function log(string $logName, string $description, $subject = null, array $properties = []): void
    {
        $causer = Auth::user();

        $activity = activity($logName)
            ->withProperties($properties);

        if ($causer) {
            $activity->causedBy($causer);
        }

        if ($subject) {
            $activity->performedOn($subject);
        }

        $activity->log($description);
    }
}
