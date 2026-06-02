<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('apartment_id')->constrained('apartments')->restrictOnDelete();
            $table->string('license_plate', 20)->unique();
            $table->enum('vehicle_type', ['motorbike', 'car', 'bicycle', 'other']);
            $table->string('brand', 50)->nullable();
            $table->string('qr_code', 255)->unique();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
