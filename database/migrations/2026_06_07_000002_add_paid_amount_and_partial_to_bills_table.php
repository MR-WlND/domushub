<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Thêm paid_amount để theo dõi đã thu được bao nhiêu
        Schema::table('bills', function (Blueprint $table) {
            $table->decimal('paid_amount', 15, 2)->default(0)->after('total_amount');
            $table->unsignedBigInteger('created_by')->nullable()->after('paid_amount');
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
        });

        // Thêm giá trị 'partial' vào enum status của bảng bills
        // MySQL cần ALTER COLUMN trực tiếp
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE bills MODIFY COLUMN status ENUM('unpaid','partial','paid','overdue','cancelled') DEFAULT 'unpaid'");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE bills MODIFY COLUMN status ENUM('unpaid','paid','overdue','cancelled') DEFAULT 'unpaid'");
        }

        Schema::table('bills', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropColumn(['paid_amount', 'created_by']);
        });
    }
};
