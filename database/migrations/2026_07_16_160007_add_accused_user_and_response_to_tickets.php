<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->unsignedBigInteger('accused_user_id')->nullable()->after('reported_person');
            $table->enum('accused_response', ['confirmed', 'denied'])->nullable()->after('accused_user_id');
            $table->text('accused_response_comment')->nullable()->after('accused_response');
            $table->timestamp('accused_responded_at')->nullable()->after('accused_response_comment');

            $table->foreign('accused_user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropForeign(['accused_user_id']);
            $table->dropColumn(['accused_user_id', 'accused_response', 'accused_response_comment', 'accused_responded_at']);
        });
    }
};
