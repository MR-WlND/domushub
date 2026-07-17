<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ticket_costs', function (Blueprint $table) {
            $table->enum('cost_type', ['repair', 'compensation'])->default('repair')->after('ticket_id');
            $table->unsignedBigInteger('responsible_user_id')->nullable()->after('note');
            $table->foreign('responsible_user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('ticket_costs', function (Blueprint $table) {
            $table->dropForeign(['responsible_user_id']);
            $table->dropColumn(['cost_type', 'responsible_user_id']);
        });
    }
};
