<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('visitors', function (Blueprint $table) {
            // Đánh dấu khách đăng ký tại cổng (bảo vệ tạo thay vì cư dân tạo QR)
            if (!Schema::hasColumn('visitors', 'walk_in')) {
                $table->boolean('walk_in')->default(false)->after('note');
            }

            // Tên cư dân muốn gặp (nhập tự do khi bảo vệ đăng ký)
            if (!Schema::hasColumn('visitors', 'resident_to_meet')) {
                $table->string('resident_to_meet', 100)->nullable()->after('walk_in');
            }

            // ID cư dân xác nhận (nếu chọn từ danh sách)
            if (!Schema::hasColumn('visitors', 'confirmed_by_resident')) {
                $table->foreignId('confirmed_by_resident')
                    ->nullable()
                    ->after('resident_to_meet')
                    ->constrained('users')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('visitors', function (Blueprint $table) {
            $table->dropForeign(['confirmed_by_resident']);
            $table->dropColumn(['walk_in', 'resident_to_meet', 'confirmed_by_resident']);
        });
    }
};
