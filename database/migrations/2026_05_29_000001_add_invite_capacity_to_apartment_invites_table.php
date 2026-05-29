<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('apartment_invites', function (Blueprint $table) {
            $table->unsignedInteger('max_residents')->default(1)->after('status');
            $table->unsignedInteger('used_count')->default(0)->after('max_residents');
        });
    }

    public function down(): void
    {
        Schema::table('apartment_invites', function (Blueprint $table) {
            $table->dropColumn(['max_residents', 'used_count']);
        });
    }
};
