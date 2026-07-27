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
        Schema::table('blocks', function (Blueprint $table) {
            $table->unsignedInteger('total_floors')->nullable()->after('status')->comment('Số tầng nổi tối đa');
            $table->unsignedInteger('total_basements')->nullable()->after('total_floors')->comment('Số tầng hầm tối đa');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('blocks', function (Blueprint $table) {
            $table->dropColumn(['total_floors', 'total_basements']);
        });
    }
};
