<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE service_prices MODIFY COLUMN type ENUM(
            'electricity', 'water', 'parking', 'management_fee',
            'internet', 'service', 'other',
            'motorbike', 'car', 'bicycle', 'electric_bike'
        ) NOT NULL");
    }

    public function down(): void
    {
        // Chuyển electric_bike về motorbike trước khi xóa enum value
        DB::table('service_prices')->where('type', 'electric_bike')->update(['type' => 'motorbike']);

        DB::statement("ALTER TABLE service_prices MODIFY COLUMN type ENUM(
            'electricity', 'water', 'parking', 'management_fee',
            'internet', 'service', 'other',
            'motorbike', 'car', 'bicycle'
        ) NOT NULL");
    }
};
