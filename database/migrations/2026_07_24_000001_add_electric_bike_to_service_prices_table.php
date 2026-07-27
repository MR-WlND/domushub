<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Chuyển parking về other trước khi xóa enum value
        DB::table('service_prices')->where('type', 'parking')->update(['type' => 'other']);

        DB::statement("ALTER TABLE service_prices MODIFY COLUMN type ENUM(
            'electricity', 'water', 'management_fee',
            'internet', 'service', 'other',
            'motorbike', 'car', 'bicycle', 'electric_bike'
        ) NOT NULL");
    }

    public function down(): void
    {
        // Khôi phục parking vào ENUM trước
        DB::statement("ALTER TABLE service_prices MODIFY COLUMN type ENUM(
            'electricity', 'water', 'parking', 'management_fee',
            'internet', 'service', 'other',
            'motorbike', 'car', 'bicycle', 'electric_bike'
        ) NOT NULL");
        
        // Chuyển electric_bike về motorbike trước khi xóa enum value
        DB::table('service_prices')->where('type', 'electric_bike')->update(['type' => 'motorbike']);

        // Xóa electric_bike
        DB::statement("ALTER TABLE service_prices MODIFY COLUMN type ENUM(
            'electricity', 'water', 'parking', 'management_fee',
            'internet', 'service', 'other',
            'motorbike', 'car', 'bicycle'
        ) NOT NULL");
    }
};
