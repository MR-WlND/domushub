<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('floors', function (Blueprint $table) {

            if (!Schema::hasColumn('floors', 'status')) {
                $table->enum('status', [
                    'active',
                    'maintenance',
                    'inactive'
                ])->default('active');
            }

            if (!Schema::hasColumn('floors', 'description')) {
                $table->text('description')->nullable();
            }

        });
    }

    public function down(): void
    {
        Schema::table('floors', function (Blueprint $table) {

            if (Schema::hasColumn('floors', 'status')) {
                $table->dropColumn('status');
            }

            if (Schema::hasColumn('floors', 'description')) {
                $table->dropColumn('description');
            }

        });
    }
};