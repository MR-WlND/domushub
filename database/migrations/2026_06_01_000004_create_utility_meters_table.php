<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('utility_meters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('apartment_id')->constrained('apartments')->restrictOnDelete();
            $table->enum('type', ['electricity', 'water']);
            $table->unsignedTinyInteger('record_month');
            $table->unsignedSmallInteger('record_year');
            $table->integer('old_value')->default(0);
            $table->integer('ocr_value')->nullable();
            $table->integer('new_value')->default(0);
            $table->integer('usage_amount')->nullable();
            $table->string('image_proof', 255)->nullable();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['apartment_id', 'type', 'record_month', 'record_year'], 'utility_meters_period_unique');
            $table->index('type', 'idx_meter_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('utility_meters');
    }
};
