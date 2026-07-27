<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Mở rộng ENUM role trong bảng users để thêm 'receptionist'.
     */
    public function up(): void
    {
        // Thay đổi kiểu cột role – MySQL cần ALTER COLUMN MODIFY
        DB::statement("ALTER TABLE `users` MODIFY COLUMN `role` ENUM(
            'admin',
            'manager',
            'staff',
            'technician',
            'security',
            'cleaning',
            'receptionist',
            'resident'
        ) NOT NULL DEFAULT 'resident'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE `users` MODIFY COLUMN `role` ENUM(
            'admin',
            'manager',
            'staff',
            'technician',
            'security',
            'cleaning',
            'resident'
        ) NOT NULL DEFAULT 'resident'");
    }
};
