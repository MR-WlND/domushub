<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('blocks', 'status')) {
            Schema::table('blocks', function (Blueprint $table) {
                $table->enum('status', [
                    'active',
                    'inactive',
                    'maintenance'
                ])->default('active');
            });
        }
    }

    public function down(): void
    {
        Schema::table('blocks', function (Blueprint $table) {

            $table->dropColumn('status');

        });
    }
};