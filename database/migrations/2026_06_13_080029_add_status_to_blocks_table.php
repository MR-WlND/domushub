<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('blocks', function (Blueprint $table) {
            $table->enum('status', ['active', 'inactive'])->default('active')->after('description');
        });

        // Cập nhật tất cả bản ghi cũ thành active
        DB::table('blocks')->update(['status' => 'active']);
    }

    public function down(): void
    {
        Schema::table('blocks', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
