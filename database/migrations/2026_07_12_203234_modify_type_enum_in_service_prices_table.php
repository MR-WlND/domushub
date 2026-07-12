<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Cập nhật ENUM để thêm 'motorbike' và 'car'
        DB::statement("ALTER TABLE service_prices MODIFY COLUMN type ENUM('electricity','water','parking','management_fee','internet','service','other','motorbike','car') NOT NULL");
        
        // 2. Chuyển đổi dữ liệu cũ (nếu có)
        // Nếu tên chứa 'ô tô' -> car, còn lại mặc định là motorbike
        DB::table('service_prices')->where('type', 'parking')->where('name', 'LIKE', '%ô tô%')->update(['type' => 'car']);
        DB::table('service_prices')->where('type', 'parking')->where('name', 'NOT LIKE', '%ô tô%')->update(['type' => 'motorbike']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('service_prices')->whereIn('type', ['motorbike', 'car'])->update(['type' => 'parking']);
        DB::statement("ALTER TABLE service_prices MODIFY COLUMN type ENUM('electricity','water','parking','management_fee','internet','service','other') NOT NULL");
    }
};
