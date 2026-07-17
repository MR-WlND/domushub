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
        // Bỏ qua nếu cột đã tồn tại (do migration khác tạo trước)
        if (Schema::hasColumn('users', 'apartment_id')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('apartment_id')->nullable()->after('status')->constrained('apartments')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Chỉ xóa nếu cột thực sự tồn tại
        if (!Schema::hasColumn('users', 'apartment_id')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['apartment_id']);
            $table->dropColumn('apartment_id');
        });
    }
};
