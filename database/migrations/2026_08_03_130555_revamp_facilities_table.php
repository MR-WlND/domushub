<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('facilities', function (Blueprint $table) {
            $table->string('facility_type')->nullable()->after('name');
            $table->string('location')->nullable()->after('facility_type');
            $table->string('fee_type')->default('free')->after('status');
            $table->decimal('price', 15, 0)->default(0)->after('fee_type');
            $table->integer('min_advance_booking_hours')->default(0)->after('price');
            $table->integer('max_advance_booking_days')->default(7)->after('min_advance_booking_hours');
        });
        
        // Cập nhật booking_type thành kiểu string để linh hoạt hơn
        // MySQL không hỗ trợ tốt việc sửa ENUM, tốt nhất là đổi tên cột cũ hoặc sử dụng modify 
        // Nhưng Doctrine DBAL (dùng trong change()) có thể lỗi với enum, nên dùng DB::statement an toàn hơn
        DB::statement("ALTER TABLE facilities MODIFY COLUMN booking_type VARCHAR(255) DEFAULT 'none'");

        // Migrate existing price data to new price column
        DB::statement("UPDATE facilities SET price = price_per_slot, fee_type = 'per_hour' WHERE price_per_slot > 0");
        DB::statement("UPDATE facilities SET price = price_per_person, fee_type = 'per_person' WHERE price_per_person > 0 AND price_per_slot = 0");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('facilities', function (Blueprint $table) {
            $table->dropColumn([
                'facility_type',
                'location',
                'fee_type',
                'price',
                'min_advance_booking_hours',
                'max_advance_booking_days'
            ]);
        });
        DB::statement("ALTER TABLE facilities MODIFY COLUMN booking_type ENUM('slot', 'person') DEFAULT 'slot'");
    }
};
