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
            $table->string('name', 100);
            $table->enum('type', ['electricity', 'water', 'parking', 'management_fee', 'internet', 'service', 'other']);
            $table->decimal('unit_price', 15, 2);
            $table->enum('status', ['pending', 'active', 'banned'])->default('active');
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_prices');
    }
};
