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
        // 1. Delete all electricity utility logs and meters
        DB::table('utility_meter_logs')->where('type', 'electricity')->delete();
        DB::table('utility_meters')->where('type', 'electricity')->delete();

        // 2. Change electricity invoices to other to avoid data loss
        DB::table('service_prices')->where('type', 'electricity')->update(['type' => 'other']);

        // 3. Update Enum types to exclude 'electricity'
        DB::statement("ALTER TABLE service_prices MODIFY COLUMN type ENUM('water', 'management_fee', 'internet', 'service', 'other', 'motorbike', 'car', 'bicycle', 'electric_bike') NOT NULL");

        // For utility_meters and utility_meter_logs: previous was ('electricity', 'water')
        DB::statement("ALTER TABLE utility_meters MODIFY COLUMN type ENUM('water') NOT NULL");
        DB::statement("ALTER TABLE utility_meter_logs MODIFY COLUMN type ENUM('water') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE service_prices MODIFY COLUMN type ENUM('electricity', 'water', 'management_fee', 'internet', 'service', 'other', 'motorbike', 'car', 'bicycle', 'electric_bike') NOT NULL");
        DB::statement("ALTER TABLE utility_meters MODIFY COLUMN type ENUM('electricity', 'water') NOT NULL");
        DB::statement("ALTER TABLE utility_meter_logs MODIFY COLUMN type ENUM('electricity', 'water') NOT NULL");
    }
};
