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
        Schema::table('apartment_types', function (Blueprint $table) {
            $table->integer('living_room_count')->default(1)->after('bedroom_count')->nullable();
            $table->string('balcony_direction')->nullable()->after('bathroom_count');
            $table->string('furniture_status')->nullable()->after('balcony_direction');
            $table->json('furniture_list')->nullable()->after('furniture_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('apartment_types', function (Blueprint $table) {
            $table->dropColumn([
                'living_room_count',
                'balcony_direction',
                'furniture_status',
                'furniture_list'
            ]);
        });
    }
};
