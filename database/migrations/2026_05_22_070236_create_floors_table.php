<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('floors', function (Blueprint $table) {
            $table->id();
            // Khóa ngoại nối với bảng blocks
            $table->foreignId('block_id')->constrained('blocks')->onDelete('cascade');
            $table->integer('floor_number');
            $table->timestamps();

            // Khóa Unique kết hợp (block_id, floor_number)
            $table->unique(['block_id', 'floor_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('floors');
    }
};