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
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'banned_posting_until')) {
                $table->dateTime('banned_posting_until')->nullable()->after('status');
            }
            if (!Schema::hasColumn('users', 'banned_commenting_until')) {
                $table->dateTime('banned_commenting_until')->nullable()->after('banned_posting_until');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['banned_posting_until', 'banned_commenting_until']);
        });
    }
};
