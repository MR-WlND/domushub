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
            if (!Schema::hasColumn('visitors', 'registered_by')) {
                $table->foreignId('registered_by')
                    ->nullable()
                    ->after('apartment_id')
                    ->constrained('users')
                    ->nullOnDelete();
            }

            // Trạng thái của lượt viếng thăm
            if (!Schema::hasColumn('visitors', 'status')) {
                $table->enum('status', ['pending', 'checked_in', 'checked_out', 'expired', 'cancelled'])
                    ->default('pending')
                    ->after('check_out_by');
            }

            // Ghi chú thêm
            if (!Schema::hasColumn('visitors', 'note')) {
                $table->text('note')->nullable()->after('status');
            }
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
