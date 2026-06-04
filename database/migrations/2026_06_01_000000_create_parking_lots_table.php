<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('parking_lots')) {
            Schema::create('parking_lots', function (Blueprint $table) {
                $table->id();
                $table->string('lot_number', 50)->unique();
                $table->enum('lot_type', ['motorbike', 'car']);
                $table->enum('status', ['available', 'occupied'])->default('available');
                $table->foreignId('apartment_id')->nullable()->constrained('apartments')->nullOnDelete();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('parking_lots');
    }
};
