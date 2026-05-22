<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('residents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('restrict');
            $table->foreignId('apartment_id')->constrained('apartments')->onDelete('restrict');
            $table->foreignId('invite_id')->nullable()->constrained('apartment_invites')->onDelete('set null');
            
            $table->enum('relationship', ['owner', 'tenant', 'family_member']);
            $table->enum('temporary_status', ['permanent', 'temporary'])->default('permanent');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->softDeletes();
            $table->timestamps();

            // Khóa Unique kết hợp (user_id, apartment_id)
            $table->unique(['user_id', 'apartment_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('residents');
    }
};