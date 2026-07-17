<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('apartment_invites', function (Blueprint $table) {
            $table->string('note', 200)->nullable()->after('intended_relationship');
        });
    }

    public function down(): void
    {
        Schema::table('apartment_invites', function (Blueprint $table) {
            $table->dropColumn('note');
        });
    }
};
