<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vehicle_logs', function (Blueprint $table) {
            $table->string('guest_plate', 20)->nullable()->after('qr_code');
            $table->enum('guest_vehicle_type', ['car', 'motorbike', 'electric_bike'])->nullable()->after('guest_plate');
            $table->string('guest_name', 100)->nullable()->after('guest_vehicle_type');
            $table->string('guest_phone', 20)->nullable()->after('guest_name');
            $table->text('guest_note')->nullable()->after('guest_phone');
            $table->boolean('is_guest')->default(false)->after('guest_note');
        });
    }

    public function down(): void
    {
        Schema::table('vehicle_logs', function (Blueprint $table) {
            $table->dropColumn(['guest_plate', 'guest_vehicle_type', 'guest_name', 'guest_phone', 'guest_note', 'is_guest']);
        });
    }
};
