<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('utility_meters', function (Blueprint $table) {
            // Lưu mảng đường dẫn ảnh dưới dạng JSON (tối đa 5 ảnh)
            $table->text('images')->nullable()->after('image_proof')
                ->comment('JSON array of image paths – up to 5 photos per reading');
        });
    }

    public function down(): void
    {
        Schema::table('utility_meters', function (Blueprint $table) {
            $table->dropColumn('images');
        });
    }
};
