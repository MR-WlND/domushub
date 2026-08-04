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
        Schema::table('blocks', function (Blueprint $table) {
            if (!Schema::hasColumn('blocks', 'apartments_per_floor')) {
                $table->unsignedInteger('apartments_per_floor')->nullable()->after('total_basements')->comment('Số căn hộ mặc định mỗi tầng');
            }
            if (!Schema::hasColumn('blocks', 'amenities')) {
                $table->json('amenities')->nullable()->after('apartments_per_floor')->comment('Danh sách tiện ích tòa nhà');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('blocks', function (Blueprint $table) {
            $table->dropColumn(['apartments_per_floor', 'amenities']);
        });
    }
};
