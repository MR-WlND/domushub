<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('danh_muc_phu_cap', function (Blueprint $table) {
            $table->id();
            $table->string('ten_phu_cap');
            $table->decimal('muc_mac_dinh', 12, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('danh_muc_phu_cap');
    }
};
