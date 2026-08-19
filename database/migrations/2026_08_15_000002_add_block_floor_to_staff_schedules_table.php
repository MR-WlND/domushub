<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('staff_schedules', function (Blueprint $table) {
            $table->foreignId('block_id')->nullable()->after('work_date')->constrained('blocks')->onDelete('set null');
            $table->foreignId('floor_id')->nullable()->after('block_id')->constrained('floors')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('staff_schedules', function (Blueprint $table) {
            $table->dropForeign(['floor_id']);
            $table->dropForeign(['block_id']);
            $table->dropColumn(['floor_id', 'block_id']);
        });
    }
};
