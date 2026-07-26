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

    public function down(): void
    {
        Schema::table('floors', function (Blueprint $table) {
            $cols = [];
            if (Schema::hasColumn('floors', 'expected_apartments')) $cols[] = 'expected_apartments';
            if (Schema::hasColumn('floors', 'floor_type')) $cols[] = 'floor_type';
            if (!empty($cols)) $table->dropColumn($cols);
        });
    }
};
