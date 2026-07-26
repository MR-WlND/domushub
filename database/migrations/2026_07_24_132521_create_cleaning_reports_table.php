<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cleaning_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reported_by')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->string('location');
            $table->enum('priority', ['low', 'medium', 'high'])->default('medium');
            $table->text('description')->nullable();
            $table->json('images')->nullable();
            $table->enum('status', ['pending', 'processing', 'resolved'])->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cleaning_reports');
    }
};
