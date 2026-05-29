<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicle_logs', function (Blueprint $table) {
            $table->id();

            // Xe liên quan
            $table->foreignId('vehicle_id')
                ->nullable()
                ->constrained('vehicles')
                ->nullOnDelete();

            // Nhân viên bảo vệ check-in / check-out
            $table->foreignId('checked_in_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('checked_out_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // Thời gian vào / ra
            $table->timestamp('check_in_at')->nullable();
            $table->timestamp('check_out_at')->nullable();

            // QR tại thời điểm scan (giữ lịch sử snapshot)
            $table->string('qr_code')->nullable();

            // Trạng thái (tuỳ chọn, có thể bỏ nếu muốn suy ra từ time)
            $table->enum('status', ['inside', 'outside'])->default('inside');

            // Index tối ưu truy vấn
            $table->index('vehicle_id');
            $table->index(['status']);
            $table->index(['qr_code']);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicle_logs');
    }
};
