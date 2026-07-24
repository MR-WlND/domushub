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
        Schema::table('service_prices', function (Blueprint $table) {
            $table->string('vehicle_type')->nullable()->after('type')->comment('Áp dụng cho loại xe: car, motorbike, bicycle, electric_bike');
        });

        // 1. Thêm parking_fee vào enum
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE service_prices MODIFY COLUMN type ENUM('water', 'management_fee', 'internet', 'service', 'other', 'motorbike', 'car', 'bicycle', 'electric_bike', 'parking_fee') NOT NULL DEFAULT 'other'");

        // 2. Chuyển đổi dữ liệu cũ: type cũ (motorbike...) thành parking_fee và gán vehicle_type = type cũ
        \Illuminate\Support\Facades\DB::table('service_prices')
            ->whereIn('type', ['motorbike', 'car', 'bicycle', 'electric_bike'])
            ->update([
                'vehicle_type' => \Illuminate\Support\Facades\DB::raw('type'),
                'type' => 'parking_fee'
            ]);

        // 3. Rút gọn enum type (xóa các loại xe cũ khỏi enum)
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE service_prices MODIFY COLUMN type ENUM('water', 'management_fee', 'internet', 'service', 'other', 'parking_fee') NOT NULL DEFAULT 'other'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1. Trả lại enum cũ cộng thêm parking_fee để có thể chứa cả hai loại
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE service_prices MODIFY COLUMN type ENUM('water', 'management_fee', 'internet', 'service', 'other', 'motorbike', 'car', 'bicycle', 'electric_bike', 'parking_fee') NOT NULL DEFAULT 'other'");

        // 2. Phục hồi dữ liệu
        \Illuminate\Support\Facades\DB::table('service_prices')
            ->where('type', 'parking_fee')
            ->whereNotNull('vehicle_type')
            ->update([
                'type' => \Illuminate\Support\Facades\DB::raw('vehicle_type')
            ]);

        // 3. Khôi phục enum ban đầu
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE service_prices MODIFY COLUMN type ENUM('water', 'management_fee', 'internet', 'service', 'other', 'motorbike', 'car', 'bicycle', 'electric_bike') NOT NULL DEFAULT 'other'");

        Schema::table('service_prices', function (Blueprint $table) {
            $table->dropColumn('vehicle_type');
        });
    }
};
