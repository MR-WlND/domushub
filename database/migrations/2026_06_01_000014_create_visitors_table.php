<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visitors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('apartment_id')->constrained('apartments')->restrictOnDelete();
            $table->string('guest_name', 100);
            $table->string('guest_phone', 20)->nullable();
            $table->string('qr_token', 255)->unique();
            $table->timestamp('expired_at');
            $table->timestamp('check_in_at')->nullable();
            $table->timestamp('check_out_at')->nullable();
            $table->foreignId('check_in_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('check_out_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visitors');
    }
};
