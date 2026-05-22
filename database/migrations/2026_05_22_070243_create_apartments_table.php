<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('apartments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('floor_id')->constrained('floors')->onDelete('cascade');
            $table->string('apartment_number', 20);
            $table->decimal('area', 10, 2);
            $table->enum('status', ['vacant', 'occupied', 'maintenance'])->default('vacant');
            $table->softDeletes();
            $table->timestamps();

            // Khóa Unique kết hợp (floor_id, apartment_number)
            $table->unique(['floor_id', 'apartment_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('apartments');
    }
};