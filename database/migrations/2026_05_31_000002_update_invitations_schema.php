<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invitations', function (Blueprint $table) {
            $table->foreignId('building_id')->nullable()->constrained('blocks')->onDelete('set null');
            $table->foreignId('apartment_id')->nullable()->constrained('apartments')->onDelete('set null');
            $table->foreignId('apartment_member_id')->nullable()->constrained('apartment_members')->onDelete('set null');
            $table->enum('type', ['resident_master', 'member_invite'])->default('resident_master');
            $table->enum('status', ['active', 'used', 'expired', 'cancelled'])->default('active');
        });
    }

    public function down(): void
    {
        Schema::table('invitations', function (Blueprint $table) {
            $table->dropForeign(['building_id']);
            $table->dropForeign(['apartment_id']);
            $table->dropForeign(['apartment_member_id']);
            $table->dropColumn(['building_id', 'apartment_id', 'apartment_member_id', 'type', 'status']);
        });
    }
};
