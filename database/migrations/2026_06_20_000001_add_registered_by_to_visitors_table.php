<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('visitors', function (Blueprint $table) {
            // Cư dân đã tạo QR mời khách
            $table->foreignId('registered_by')
                ->nullable()
                ->after('apartment_id')
                ->constrained('users')
                ->nullOnDelete();

            // Trạng thái của lượt viếng thăm
            $table->enum('status', ['pending', 'checked_in', 'checked_out', 'expired', 'cancelled'])
                ->default('pending')
                ->after('check_out_by');

            // Ghi chú thêm
            $table->text('note')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('visitors', function (Blueprint $table) {
            $table->dropForeign(['registered_by']);
            $table->dropColumn(['registered_by', 'status', 'note']);
        });
    }
};
