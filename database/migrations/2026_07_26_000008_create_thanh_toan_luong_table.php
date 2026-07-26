<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('thanh_toan_luong', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bang_luong_id')->constrained('bang_luong');
            $table->enum('hinh_thuc', ['tien_mat', 'chuyen_khoan'])->default('chuyen_khoan');
            $table->timestamp('ngay_thanh_toan')->nullable();
            $table->enum('trang_thai', ['chua_thanh_toan', 'dang_xu_ly', 'da_thanh_toan'])->default('chua_thanh_toan');
            $table->foreignId('xu_ly_boi')->nullable()->constrained('users');
            $table->text('ghi_chu')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('thanh_toan_luong');
    }
};
