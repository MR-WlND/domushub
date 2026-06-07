<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->text('note')->nullable()->after('paid_at');
            $table->unsignedBigInteger('recorded_by')->nullable()->after('note');
            $table->timestamp('refunded_at')->nullable()->after('recorded_by');
            $table->text('refund_note')->nullable()->after('refunded_at');
            $table->unsignedBigInteger('refunded_by')->nullable()->after('refund_note');

            $table->foreign('recorded_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('refunded_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['recorded_by']);
            $table->dropForeign(['refunded_by']);
            $table->dropColumn(['note', 'recorded_by', 'refunded_at', 'refund_note', 'refunded_by']);
        });
    }
};
