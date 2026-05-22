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
        // 1. Bảng users đã được cập nhật theo cấu trúc mới của bạn
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('email', 150)->unique();
            $table->string('phone', 20)->unique();
            $table->string('password', 255);
            $table->string('avatar', 255)->nullable();
            
            // Định nghĩa Enum Role và Status
            $table->enum('role', ['admin', 'manager', 'staff', 'technician', 'security', 'resident'])->default('resident');
            $table->enum('status', ['pending', 'active', 'banned'])->default('pending');
            
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->softDeletes(); // Thêm cột deleted_at
            $table->timestamps(); // Thêm created_at và updated_at
        });

        // 2. Bảng password_reset_tokens (Giữ nguyên mặc định của Laravel)
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        // 3. Bảng sessions (Giữ nguyên mặc định của Laravel)
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Đảm bảo xóa cả 3 bảng khi rollback
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};