<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Cập nhật enum status trong bảng vehicles:
     * Thêm 2 trạng thái mới: pending_renewal, locked
     */
    public function up(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        // MySQL yêu cầu dùng ALTER TABLE MODIFY COLUMN để thay đổi enum
        DB::statement("
            ALTER TABLE vehicles
            MODIFY COLUMN status ENUM(
                'pending',
                'active',
                'pending_renewal',
                'locked',
                'inactive'
            ) NOT NULL DEFAULT 'pending'
        ");
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        // Rollback về enum cũ (chỉ 3 trạng thái)
        // Cảnh báo: dữ liệu nào đang là 'pending_renewal' hoặc 'locked' sẽ gây lỗi
        DB::statement("
            ALTER TABLE vehicles
            MODIFY COLUMN status ENUM(
                'pending',
                'active',
                'inactive'
            ) NOT NULL DEFAULT 'pending'
        ");
    }
};
