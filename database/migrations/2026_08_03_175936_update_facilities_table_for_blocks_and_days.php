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
        Schema::table('facilities', function (Blueprint $table) {
            $table->unsignedBigInteger('block_id')->nullable()->after('facility_type');
            $table->unsignedBigInteger('floor_id')->nullable()->after('block_id');
            $table->json('operating_days')->nullable()->after('close_time');
            
            if (Schema::hasColumn('facilities', 'location')) {
                $table->dropColumn('location');
            }
            
            $table->foreign('block_id')->references('id')->on('blocks')->nullOnDelete();
            $table->foreign('floor_id')->references('id')->on('floors')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('facilities', function (Blueprint $table) {
            $table->dropForeign(['block_id']);
            $table->dropForeign(['floor_id']);
            
            $table->dropColumn(['block_id', 'floor_id', 'operating_days']);
            $table->string('location')->nullable()->after('facility_type');
        });
    }
};
