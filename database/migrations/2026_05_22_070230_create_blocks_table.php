<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blocks', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->unique();
            $table->string('code', 100)->nullable()->unique();
            $table->enum('status', ['active', 'inactive', 'maintenance'])->default('active');
            $table->string('manager_name', 100)->nullable();
            $table->string('manager_contact', 100)->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blocks');
    }
};