<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('parking_lots', 'zone')) {
            Schema::table('parking_lots', function (Blueprint $table) {
                $table->string('zone', 50)->nullable()->after('lot_number')->index();
            });
        }
    }

    public function down(): void
    {
        Schema::table('parking_lots', function (Blueprint $table) {
            $table->dropColumn('zone');
        });
    }
};
