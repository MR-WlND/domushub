<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cleaning_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assigned_to')->constrained('users')->onDelete('cascade');
            $table->foreignId('assigned_by')->nullable()->constrained('users')->onDelete('set null');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('area');
            $table->string('area_group')->nullable();
            $table->time('start_time');
            $table->time('end_time');
            $table->enum('priority', ['low', 'medium', 'high'])->default('medium');
            $table->enum('status', ['pending', 'progress', 'done'])->default('pending');
            $table->json('checklist')->nullable();
            $table->text('manager_note')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->date('task_date');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cleaning_tasks');
    }
};
