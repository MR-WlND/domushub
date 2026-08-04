<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE service_prices MODIFY COLUMN type ENUM('water', 'management_fee', 'internet', 'service', 'other', 'parking_fee', 'compensation', 'penalty', 'card_reissue') NOT NULL DEFAULT 'other'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert any new types to 'other' before altering enum back
        DB::table('service_prices')
            ->whereIn('type', ['compensation', 'penalty', 'card_reissue'])
            ->update(['type' => 'other']);

        DB::statement("ALTER TABLE service_prices MODIFY COLUMN type ENUM('water', 'management_fee', 'internet', 'service', 'other', 'parking_fee') NOT NULL DEFAULT 'other'");
    }
};
