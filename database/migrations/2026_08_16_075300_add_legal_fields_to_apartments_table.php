<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('apartments', function (Blueprint $table) {
            $table->date('handover_date')->nullable();
            $table->enum('legal_status', ['pending', 'processing', 'issued'])->default('pending');
        });
    }

    public function down(): void
    {
        Schema::table('apartments', function (Blueprint $table) {
            $table->dropColumn(['handover_date', 'legal_status']);
        });
    }
};
