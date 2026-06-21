<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('facility_bookings', function (Blueprint $table) {
            // Số người
            if (!Schema::hasColumn('facility_bookings', 'number_of_people')) {
                $table->unsignedInteger('number_of_people')->default(1)->after('end_time');
            }

            // QR Code
            if (!Schema::hasColumn('facility_bookings', 'qr_code')) {
                $table->string('qr_code')->nullable()->unique()->after('status');
            }

            // Thời gian check-in
            if (!Schema::hasColumn('facility_bookings', 'checked_in_at')) {
                $table->datetime('checked_in_at')->nullable()->after('qr_code');
            }

            // Trạng thái thanh toán
            if (!Schema::hasColumn('facility_bookings', 'payment_status')) {
                $table->enum('payment_status', ['unpaid', 'paid'])->default('unpaid')->after('checked_in_at');
            }

            // Phương thức thanh toán
            if (!Schema::hasColumn('facility_bookings', 'payment_method')) {
                $table->string('payment_method')->nullable()->after('payment_status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('facility_bookings', function (Blueprint $table) {
            $cols = [];
            if (Schema::hasColumn('facility_bookings', 'payment_method')) $cols[] = 'payment_method';
            if (Schema::hasColumn('facility_bookings', 'payment_status')) $cols[] = 'payment_status';
            if (Schema::hasColumn('facility_bookings', 'checked_in_at')) $cols[] = 'checked_in_at';
            if (Schema::hasColumn('facility_bookings', 'qr_code')) $cols[] = 'qr_code';
            if (Schema::hasColumn('facility_bookings', 'number_of_people')) $cols[] = 'number_of_people';

            if (!empty($cols)) {
                $table->dropColumn($cols);
            }
        });
    }
};
