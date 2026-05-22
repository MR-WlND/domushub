<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('email', 150)->unique();
            $table->string('phone', 20)->unique();
            $table->string('password', 255);
            $table->string('avatar', 255)->nullable();
            
            // Định nghĩa Enum Role và Status theo cấu trúc của ông
            $table->enum('role', ['admin', 'manager', 'staff', 'technician', 'security', 'resident'])->default('resident');
            $table->enum('status', ['pending', 'active', 'banned'])->default('pending');
            
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->softDeletes(); // deleted_at
            $table->timestamps(); // created_at và updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};