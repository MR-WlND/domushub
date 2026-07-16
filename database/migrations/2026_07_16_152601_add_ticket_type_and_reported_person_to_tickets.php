<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->enum('ticket_type', ['complaint', 'report'])->default('complaint')->after('apartment_id');
            $table->string('reported_person', 255)->nullable()->after('description');
        });
    }

    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropColumn(['ticket_type', 'reported_person']);
        });
    }
};
