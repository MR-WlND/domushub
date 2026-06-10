<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('incidents', function (Blueprint $table) {
            $table->id();

            // Người gửi phản ánh (cư dân)
            $table->foreignId('resident_id')->constrained('users')->cascadeOnDelete();

            // Căn hộ liên quan
            $table->foreignId('apartment_id')->nullable()->constrained('apartments')->nullOnDelete();

            // Thông tin phản ánh
            $table->string('title', 200);
            $table->text('description');
            $table->enum('category', [
                'electrical',   // Điện
                'plumbing',     // Nước / ống nước
                'elevator',     // Thang máy
                'cleaning',     // Vệ sinh
                'security',     // An ninh
                'infrastructure', // Hạ tầng / kết cấu
                'other',        // Khác
            ])->default('other');
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');

            // Trạng thái xử lý
            $table->enum('status', [
                'pending',      // Chờ tiếp nhận
                'assigned',     // Đã phân công
                'in_progress',  // Đang xử lý
                'resolved',     // Đã xử lý (kỹ thuật cập nhật)
                'confirmed',    // Quản lý xác nhận hoàn thành
                'closed',       // Đã đóng / từ chối
            ])->default('pending');

            // Phân công kỹ thuật viên
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('assigned_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('assigned_at')->nullable();

            // Kỹ thuật viên cập nhật kết quả
            $table->text('technician_note')->nullable();
            $table->timestamp('resolved_at')->nullable();

            // Quản lý xác nhận
            $table->foreignId('confirmed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('confirmed_at')->nullable();

            // Ảnh đính kèm (JSON array of paths)
            $table->json('images')->nullable();

            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('incidents');
    }
};
