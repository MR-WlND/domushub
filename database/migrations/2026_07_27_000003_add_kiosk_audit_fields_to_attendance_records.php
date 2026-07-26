<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration thêm các trường đối soát Kiosk Chấm công Thông minh:
 *   - snapshot_photo: Ảnh chụp từ webcam sảnh tại thời điểm chấm công
 *   - ip_address: Địa chỉ IP máy Kiosk/Mạng BQL
 *   - device_info: Thiết bị/User Agent thực hiện
 *   - camera_info: Camera/Webcam sử dụng
 *   - liveness_verified: Cờ xác thực liveness chống giả mạo
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendance_records', function (Blueprint $table) {
            $table->string('snapshot_photo', 255)->nullable()->after('note')->comment('Đường dẫn ảnh chụp từ camera Kiosk BQL');
            $table->string('ip_address', 45)->nullable()->after('snapshot_photo')->comment('Địa chỉ IP máy chấm công Kiosk');
            $table->string('device_info', 255)->nullable()->after('ip_address')->comment('Thiết bị/Trình duyệt Kiosk');
            $table->string('camera_info', 100)->nullable()->after('device_info')->comment('Tên camera/webcam');
            $table->boolean('liveness_verified')->default(true)->after('camera_info')->comment('Đã xác thực người thật (Liveness)');
        });
    }

    public function down(): void
    {
        Schema::table('attendance_records', function (Blueprint $table) {
            $table->dropColumn(['snapshot_photo', 'ip_address', 'device_info', 'camera_info', 'liveness_verified']);
        });
    }
};
