<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action', 50);
            $table->string('target_type', 100)->nullable();
            $table->unsignedBigInteger('target_id')->nullable();
            $table->text('description')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['target_type', 'target_id'], 'idx_activity_logs_target');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
