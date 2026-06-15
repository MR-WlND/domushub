<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bill_details', function (Blueprint $table) {
            $table->string('status')->default('unpaid')->after('amount');
        });
    }

    public function down(): void
    {
        Schema::table('bill_details', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
