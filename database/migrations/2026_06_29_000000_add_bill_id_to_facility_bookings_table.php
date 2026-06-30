<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('facility_bookings', function (Blueprint $table) {
            if (!Schema::hasColumn('facility_bookings', 'bill_id')) {
                $table->unsignedBigInteger('bill_id')->nullable()->after('payment_method');
                $table->foreign('bill_id')->references('id')->on('bills')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('facility_bookings', function (Blueprint $table) {
            if (Schema::hasColumn('facility_bookings', 'bill_id')) {
                $table->dropForeign(['bill_id']);
                $table->dropColumn('bill_id');
            }
        });
    }
};
