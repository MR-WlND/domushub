<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('danh_muc_khau_tru', function (Blueprint $table) {
            $table->id();
            $table->string('ten_khau_tru');
            $table->enum('loai', ['tu_dong', 'thu_cong'])->default('thu_cong');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('danh_muc_khau_tru');
    }
};
