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
        Schema::table('utility_meters', function (Blueprint $table) {
            $table->enum('status', ['pending', 'approved'])->default('pending')->after('recorded_by');
        });

        // Cập nhật tất cả các bản ghi có sẵn thành approved để giữ tương thích ngược
        DB::table('utility_meters')->update(['status' => 'approved']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('utility_meters', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
