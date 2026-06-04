<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_prices', function (Blueprint $table) {
            $table->id();
            $table->enum('service_type', ['electricity', 'water']);
            $table->decimal('price_per_unit', 12, 2); // VNĐ/kWh hoặc VNĐ/m³
            $table->date('effective_from');
            $table->date('effective_to')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_prices');
    }
};
