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
            if (!Schema::hasColumn('floors', 'expected_apartments')) {
                $table->unsignedInteger('expected_apartments')->nullable()->after('description');
            }
            if (!Schema::hasColumn('floors', 'floor_type')) {
                $table->enum('floor_type', [
                    'resident',
                    'basement',
                    'commercial',
                    'service',
                ])->default('resident')->after('expected_apartments');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('floors', function (Blueprint $table) {
            $table->dropColumn(['expected_apartments', 'floor_type']);
        });
    }
};
