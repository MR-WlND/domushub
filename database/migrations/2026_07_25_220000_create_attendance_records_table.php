<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_records', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                  ->constrained('users')
                  ->cascadeOnDelete();

            $table->date('work_date')->comment('Ngày làm việc');

            $table->datetime('check_in_at')->nullable()->comment('Giờ check-in');
            $table->datetime('check_out_at')->nullable()->comment('Giờ check-out');

            $table->enum('status', ['working', 'present', 'late', 'absent', 'half_day'])
                  ->default('working')
                  ->comment('Trạng thái: working=đang làm, present=đúng giờ, late=trễ, absent=vắng, half_day=nửa ngày');

            $table->enum('shift', ['full_day', 'morning', 'afternoon'])
                  ->default('full_day')
                  ->comment('Ca làm việc');

            $table->string('work_location')->nullable()->comment('Vị trí làm việc');

            $table->decimal('working_hours', 5, 2)->nullable()->comment('Số giờ làm việc (tính khi checkout)');
            $table->unsignedSmallInteger('late_minutes')->nullable()->comment('Số phút đến muộn');

            $table->text('note')->nullable()->comment('Ghi chú của admin');

            $table->foreignId('recorded_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete()
                  ->comment('Admin/Manager đã nhập hoặc sửa bản ghi');

            $table->timestamps();

            // Mỗi nhân viên chỉ có 1 bản ghi/ngày
            $table->unique(['user_id', 'work_date'], 'attendance_user_date_unique');

            $table->index('work_date');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_records');
    }
};
