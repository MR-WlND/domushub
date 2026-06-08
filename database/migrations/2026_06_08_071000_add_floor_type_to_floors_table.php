<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('floors', 'floor_type')) {
            Schema::table('floors', function (Blueprint $table) {
                $table->dropColumn('floor_type');
            });
        }

        Schema::table('floors', function (Blueprint $table) {
            $table->string('floor_type', 30)->default('residential')->after('name');
        });
    }

    public function down(): void
    {
        Schema::table('floors', function (Blueprint $table) {
            $table->dropColumn('floor_type');
        });
    }
};
