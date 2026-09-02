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
        Schema::table('utility_meters', function (Blueprint $table) {
            $table->boolean('is_complained')->default(false)->after('status');
            $table->text('complaint_reason')->nullable()->after('is_complained');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('utility_meters', function (Blueprint $table) {
            $table->dropColumn(['is_complained', 'complaint_reason']);
        });
    }
};
