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
        Schema::table('temporary_registrations', function (Blueprint $table) {
            $table->string('guest_name')->nullable()->after('status');
            $table->string('guest_phone')->nullable()->after('guest_name');
            $table->string('guest_cccd')->nullable()->after('guest_phone');
            $table->string('guest_email')->nullable()->after('guest_cccd');
            $table->date('guest_dob')->nullable()->after('guest_email');
            $table->enum('guest_gender', ['male', 'female', 'other'])->nullable()->after('guest_dob');
            $table->string('guest_hometown')->nullable()->after('guest_gender');
            $table->string('relationship')->nullable()->after('guest_hometown');
            $table->json('attachments')->nullable()->after('attachment_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('temporary_registrations', function (Blueprint $table) {
            $table->dropColumn([
                'guest_email',
                'guest_dob',
                'guest_gender',
                'guest_hometown',
                'relationship',
                'attachments',
            ]);
        });
    }
};
