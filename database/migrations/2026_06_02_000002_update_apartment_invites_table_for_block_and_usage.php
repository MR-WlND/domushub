<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('apartment_invites', function (Blueprint $table) {
            $table->foreignId('block_id')->nullable()->constrained('blocks')->onDelete('cascade');
            $table->integer('max_uses')->default(1)->after('status');
            $table->integer('uses_count')->default(0)->after('max_uses');
        });

        if (Schema::hasColumn('apartment_invites', 'apartment_id')) {
            Schema::table('apartment_invites', function (Blueprint $table) {
                $table->unsignedBigInteger('apartment_id')->nullable()->change();
            });
        }
    }

    public function down(): void
    {
        Schema::table('apartment_invites', function (Blueprint $table) {
            $table->dropForeign(['block_id']);
            $table->dropColumn('block_id');
            $table->dropColumn('max_uses');
            $table->dropColumn('uses_count');
        });

        if (Schema::hasColumn('apartment_invites', 'apartment_id')) {
            Schema::table('apartment_invites', function (Blueprint $table) {
                $table->unsignedBigInteger('apartment_id')->nullable(false)->change();
            });
        }
    }
};
