<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('visitors', function (Blueprint $table) {
            $table->string('vehicle_plate', 20)->nullable()->after('note');
            $table->string('vehicle_type', 20)->nullable()->after('vehicle_plate');
        });
    }

    public function down(): void
    {
        Schema::table('visitors', function (Blueprint $table) {
            $table->dropColumn(['vehicle_plate', 'vehicle_type']);
        });
    }
};
