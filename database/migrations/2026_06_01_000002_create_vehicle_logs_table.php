<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicle_logs', function (Blueprint $table) {
            $table->id();
            $table->string('qr_code_scanned', 255);
            $table->foreignId('vehicle_id')->nullable()->constrained('vehicles')->nullOnDelete();
            $table->foreignId('check_in_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('check_out_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('check_in_at')->nullable();
            $table->timestamp('check_out_at')->nullable();
            $table->enum('status', ['inside', 'outside'])->default('inside');
            $table->timestamp('created_at')->useCurrent();

            $table->index(['qr_code_scanned', 'status'], 'idx_logs_qr_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicle_logs');
    }
};
