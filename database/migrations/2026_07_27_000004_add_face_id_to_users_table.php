<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration bổ sung thông tin khuôn mặt Face ID cho bảng users:
 *   - face_data: Chuỗi JSON chứa mẫu khuôn mặt (Face Descriptors / Feature Vectors / Base64 Reference)
 *   - face_registered_at: Thời điểm hoàn tất đăng ký Face ID
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->longText('face_data')->nullable()->after('avatar')->comment('Dữ liệu mô tả khuôn mặt Face ID (Descriptors)');
            $table->timestamp('face_registered_at')->nullable()->after('face_data')->comment('Thời điểm đăng ký Face ID');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['face_data', 'face_registered_at']);
        });
    }
};
