<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('apartment_id')->constrained('apartments')->restrictOnDelete();
            $table->foreignId('sender_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('handler_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title', 200);
            $table->text('description');
            $table->string('image', 255)->nullable();
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->enum('status', ['pending', 'assigned', 'in_progress', 'completed', 'cancelled'])->default('pending');
            $table->unsignedTinyInteger('rating')->nullable();
            $table->text('feedback_comment')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index('status', 'idx_tickets_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
