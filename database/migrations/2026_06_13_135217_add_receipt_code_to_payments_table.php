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
        Schema::table('payments', function (Blueprint $table) {
            $table->string('receipt_code', 50)->nullable()->unique()->after('transaction_code');
            $table->string('vnp_txn_ref', 100)->nullable()->after('receipt_code'); // Mã giao dịch VNPay để đối soát
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['receipt_code', 'vnp_txn_ref']);
        });
    }
};
