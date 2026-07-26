<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Thêm cột phân loại OT và cảnh báo vượt trần vào bảng bang_luong.
 *
 * Lý do: Nâng cấp module chấm công tự động — phân biệt OT theo luật lao động VN:
 *   - ngày thường (×1.5), cuối tuần (×2.0), ngày lễ (×3.0)
 *
 * Thêm ca đêm vào enum shift của attendance_records.
 */
return new class extends Migration
{
    public function up(): void
    {
        // Thêm cột OT phân loại vào bang_luong
        Schema::table('bang_luong', function (Blueprint $table) {
            $table->decimal('so_gio_ot_thuong', 6, 2)->default(0)
                ->after('so_gio_ot')
                ->comment('Giờ OT ngày thường (hệ số ×1.5)');

            $table->decimal('so_gio_ot_cuoi_tuan', 6, 2)->default(0)
                ->after('so_gio_ot_thuong')
                ->comment('Giờ OT cuối tuần T7/CN (hệ số ×2.0)');

            $table->decimal('so_gio_ot_ngay_le', 6, 2)->default(0)
                ->after('so_gio_ot_cuoi_tuan')
                ->comment('Giờ OT ngày lễ quốc gia (hệ số ×3.0)');

            $table->boolean('canh_bao_ot')->default(false)
                ->after('so_gio_ot_ngay_le')
                ->comment('Cảnh báo vượt trần OT 40h/tháng theo Luật Lao động');
        });

        // Thêm ca đêm vào attendance_records.shift (nếu chưa có)
        // Dùng raw ALTER vì Laravel không hỗ trợ sửa enum trực tiếp
        DB::statement("
            ALTER TABLE attendance_records
            MODIFY COLUMN shift ENUM('full_day','morning','afternoon','night','office')
            DEFAULT 'full_day'
            COMMENT 'Ca làm việc: full_day=hành chính, morning=sáng, afternoon=chiều, night=đêm, office=VP'
        ");
    }

    public function down(): void
    {
        Schema::table('bang_luong', function (Blueprint $table) {
            $table->dropColumn(['so_gio_ot_thuong', 'so_gio_ot_cuoi_tuan', 'so_gio_ot_ngay_le', 'canh_bao_ot']);
        });

        // Revert enum (bỏ 'night' và 'office')
        DB::statement("
            ALTER TABLE attendance_records
            MODIFY COLUMN shift ENUM('full_day','morning','afternoon')
            DEFAULT 'full_day'
        ");
    }
};
