<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cleaning_reports', function (Blueprint $table) {
            $table->foreignId('assigned_to')->nullable()->after('status')->constrained('users')->onDelete('set null');
            $table->text('admin_note')->nullable()->after('assigned_to');
        });
    }

    public function down(): void
    {
        Schema::table('cleaning_reports', function (Blueprint $table) {
            $table->dropForeign(['assigned_to']);
            $table->dropColumn(['assigned_to', 'admin_note']);
        });
    }
};
