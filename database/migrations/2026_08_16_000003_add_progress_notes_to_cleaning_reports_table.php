<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cleaning_reports', function (Blueprint $table) {
            $table->json('progress_notes')->nullable()->after('admin_note');
        });
    }

    public function down(): void
    {
        Schema::table('cleaning_reports', function (Blueprint $table) {
            $table->dropColumn('progress_notes');
        });
    }
};
