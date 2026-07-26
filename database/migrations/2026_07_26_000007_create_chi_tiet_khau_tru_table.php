<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chi_tiet_khau_tru', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bang_luong_id')->constrained('bang_luong')->cascadeOnDelete();
            $table->foreignId('danh_muc_khau_tru_id')->constrained('danh_muc_khau_tru');
            $table->decimal('so_tien', 12, 2);
            $table->text('ly_do')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chi_tiet_khau_tru');
    }
};
