<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('apartment_members', function (Blueprint $table) {
            $table->date('date_of_birth')->nullable()->after('name');
            $table->string('phone', 50)->nullable()->after('relationship');
            $table->string('email', 150)->nullable()->after('phone');
        });
    }

    public function down(): void
    {
        Schema::table('apartment_members', function (Blueprint $table) {
            $table->dropColumn(['date_of_birth', 'phone', 'email']);
        });
    }
};
