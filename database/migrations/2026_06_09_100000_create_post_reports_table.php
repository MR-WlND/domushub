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
        // 1. Tạo bảng trung gian post_reports
        Schema::create('post_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained('posts')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('reason');
            $table->timestamps();

            // Ràng buộc Unique kết hợp giữa post_id và user_id
            $table->unique(['post_id', 'user_id']);
        });

        // 2. Drop cột reports_count dư thừa trên bảng posts nếu nó tồn tại
        if (Schema::hasColumn('posts', 'reports_count')) {
            Schema::table('posts', function (Blueprint $table) {
                $table->dropColumn('reports_count');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1. Drop bảng post_reports
        Schema::dropIfExists('post_reports');

        // 2. Khôi phục lại cột reports_count trên bảng posts nếu chưa có
        if (!Schema::hasColumn('posts', 'reports_count')) {
            Schema::table('posts', function (Blueprint $table) {
                $table->unsignedInteger('reports_count')->default(0)->after('ai_flagged');
            });
        }
    }
};
