<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cleaning_report_assignees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cleaning_report_id')->constrained('cleaning_reports')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['cleaning_report_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cleaning_report_assignees');
    }
};
