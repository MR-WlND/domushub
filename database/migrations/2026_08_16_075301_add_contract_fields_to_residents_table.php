<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('residents', function (Blueprint $table) {
            $table->string('contract_file')->nullable();
            $table->decimal('deposit_amount', 15, 2)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('residents', function (Blueprint $table) {
            $table->dropColumn(['contract_file', 'deposit_amount']);
        });
    }
};
