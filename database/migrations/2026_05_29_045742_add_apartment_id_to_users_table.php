<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('users', 'apartment_id')) {

            Schema::table('users', function (Blueprint $table) {

                $table->foreignId('apartment_id')
                    ->nullable()
                    ->after('avatar')
                    ->constrained('apartments')
                    ->nullOnDelete();

            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'apartment_id')) {

            Schema::table('users', function (Blueprint $table) {

                $table->dropForeign(['apartment_id']);
                $table->dropColumn('apartment_id');

            });
        }
    }
};
