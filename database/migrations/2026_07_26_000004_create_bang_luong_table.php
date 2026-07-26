<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bang_luong', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->unsignedTinyInteger('thang');
            $table->unsignedSmallInteger('nam');
            $table->decimal('luong_co_ban', 12, 2);
            $table->decimal('so_ngay_cong_chuan', 4, 1)->default(26);
            $table->decimal('so_ngay_cong_thuc_te', 4, 1)->default(0);
            $table->decimal('so_gio_ot', 6, 2)->default(0);
            $table->decimal('tien_luong_theo_cong', 12, 2)->default(0);
            $table->decimal('tien_ot', 12, 2)->default(0);
            $table->decimal('tong_phu_cap', 12, 2)->default(0);
            $table->decimal('tong_thuong', 12, 2)->default(0);
            $table->decimal('tong_khau_tru', 12, 2)->default(0);
            $table->decimal('thuc_linh', 12, 2)->default(0);
            $table->enum('trang_thai_duyet', ['nhap', 'cho_duyet', 'da_duyet'])->default('nhap');
            $table->foreignId('duyet_boi')->nullable()->constrained('users');
            $table->timestamp('ngay_duyet')->nullable();
            $table->foreignId('recorded_by')->nullable()->constrained('users');
            $table->timestamps();

            $table->unique(['user_id', 'thang', 'nam']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bang_luong');
    }
};
