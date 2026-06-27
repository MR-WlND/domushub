<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('facilities', function (Blueprint $table) {
            // Khung giờ hoạt động
            $table->time('open_time')->nullable()->after('description');   // VD: 06:00
            $table->time('close_time')->nullable()->after('open_time');    // VD: 22:00

            // Thời lượng mỗi slot đặt (phút), VD: 60 = 1 tiếng/lần
            $table->unsignedSmallInteger('slot_duration')->default(60)->after('close_time');

            // Giá mỗi slot (0 = miễn phí)
            $table->decimal('price_per_slot', 10, 0)->default(0)->after('slot_duration');

            // Ghi chú cấu hình (quy định, lưu ý khi đặt)
            $table->text('rules')->nullable()->after('price_per_slot');
        });
    }

    public function down(): void
    {
        Schema::table('facilities', function (Blueprint $table) {
            $table->dropColumn(['open_time', 'close_time', 'slot_duration', 'price_per_slot', 'rules']);
        });
    }
};
