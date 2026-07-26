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
        Schema::table('bills', function (Blueprint $table) {
            $table->decimal('previous_debt', 12, 2)->default(0)->after('paid_amount')->comment('Nợ các kỳ trước tại thời điểm phát hành');
            $table->decimal('current_amount', 12, 2)->default(0)->after('previous_debt')->comment('Tổng tiền của hóa đơn tháng này');
            $table->decimal('total_due_at_issue', 12, 2)->default(0)->after('current_amount')->comment('Tổng phải thanh toán khi phát hành');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bills', function (Blueprint $table) {
            $table->dropColumn(['previous_debt', 'current_amount', 'total_due_at_issue']);
        });
    }
};
