<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('facilities', function (Blueprint $table) {
            if (!Schema::hasColumn('facilities', 'booking_type')) {
                // 'slot' = đặt theo tiếng (giờ), 'person' = đặt theo người
                $table->enum('booking_type', ['slot', 'person'])->default('slot')->after('price_per_slot');
            }
            if (!Schema::hasColumn('facilities', 'price_per_person')) {
                // Giá mỗi người (dùng khi booking_type = 'person')
                $table->decimal('price_per_person', 10, 0)->default(0)->after('booking_type');
            }
        });
    }

    public function down(): void
    {
        Schema::table('facilities', function (Blueprint $table) {
            $table->dropColumn(['booking_type', 'price_per_person']);
        });
    }
};
