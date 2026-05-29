<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        
        if (!Schema::hasTable('vehicles')) {

            Schema::create('vehicles', function (Blueprint $table) {

                $table->id();

                $table->foreignId('apartment_id')
                    ->constrained('apartments')
                    ->onDelete('cascade');

                $table->string('license_plate', 20)->unique();

                $table->enum('vehicle_type', [
                    'xe điện',
                    'xe máy',
                    'ô tô'
                ]);

                $table->string('brand', 50)->nullable();

                $table->string('image')->nullable();

                $table->string('qr_code')->nullable();

                $table->enum('status', [
                    'pending',
                    'approved',
                    'rejected'
                ])->default('pending');

                $table->softDeletes();

                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
