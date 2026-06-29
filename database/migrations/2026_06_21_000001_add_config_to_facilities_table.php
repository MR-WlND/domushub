<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('facilities', function (Blueprint $table) {
            if (!Schema::hasColumn('facilities', 'open_time')) {
                $table->time('open_time')->nullable()->after('description');
            }
            if (!Schema::hasColumn('facilities', 'close_time')) {
                $table->time('close_time')->nullable()->after('open_time');
            }
            if (!Schema::hasColumn('facilities', 'slot_duration')) {
                $table->unsignedSmallInteger('slot_duration')->default(60)->after('close_time');
            }
            if (!Schema::hasColumn('facilities', 'price_per_slot')) {
                $table->decimal('price_per_slot', 10, 0)->default(0)->after('slot_duration');
            }
            if (!Schema::hasColumn('facilities', 'rules')) {
                $table->text('rules')->nullable()->after('price_per_slot');
            }
        });
    }

    public function down(): void
    {
        Schema::table('facilities', function (Blueprint $table) {
            $table->dropColumn(['open_time', 'close_time', 'slot_duration', 'price_per_slot', 'rules']);
        });
    }
};
