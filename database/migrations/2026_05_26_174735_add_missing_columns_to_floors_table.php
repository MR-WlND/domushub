<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('floors', function (Blueprint $table) {

            $table->enum('status', [
                'active',
                'maintenance',
                'inactive'
            ])->default('active');

            $table->text('description')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('floors', function (Blueprint $table) {
            $table->string('status')->default('active');
            $table->text('description')->nullable();
        });
    }
};
