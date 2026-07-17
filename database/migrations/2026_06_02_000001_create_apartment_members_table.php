<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('apartment_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('apartment_id')->constrained('apartments')->onDelete('cascade');
            $table->foreignId('invite_id')->nullable()->constrained('apartment_invites')->onDelete('cascade');
            $table->string('name', 150);
            $table->string('birth_year', 4)->nullable();
            $table->string('relationship', 50)->nullable();
            $table->enum('status', ['pending', 'verified'])->default('verified');
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('apartment_members');
    }
};
