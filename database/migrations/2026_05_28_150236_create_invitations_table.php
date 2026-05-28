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
        Schema::create('invitations', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // Mã mời (Ví dụ: INV-2026-X89Z)

            // Vai trò sẽ áp dụng cho người nhập mã này
            $table->enum('role', ['manager', 'staff', 'technician', 'security', 'resident'])->default('resident');

            // Các quyền đặc cách đi kèm (dạng JSON giống như bạn đã làm ở bảng User)
            $table->json('permissions')->nullable();

            $table->integer('max_uses')->default(1); // Số lần sử dụng tối đa (1 lần hoặc nhiều lần)
            $table->integer('uses_count')->default(0); // Số lần đã sử dụng thực tế

            $table->timestamp('expires_at')->nullable(); // Thời hạn hết hạn của mã
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade'); // Admin nào tạo mã này
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invitations');
    }
};
