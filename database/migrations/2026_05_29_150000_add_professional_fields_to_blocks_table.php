<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('blocks', function (Blueprint $table) {
            $table->string('code', 100)->nullable()->unique()->after('name');
            $table->enum('status', ['active', 'inactive', 'maintenance'])->default('active')->after('code');
            $table->unsignedInteger('number_of_floors')->nullable()->after('status');
            $table->unsignedInteger('total_apartments')->nullable()->after('number_of_floors');
            $table->string('manager_name', 100)->nullable()->after('total_apartments');
            $table->string('manager_contact', 100)->nullable()->after('manager_name');
        });
    }

    public function down(): void
    {
        Schema::table('blocks', function (Blueprint $table) {
            $table->dropUnique(['code']);
            $table->dropColumn([
                'code',
                'status',
                'number_of_floors',
                'total_apartments',
                'manager_name',
                'manager_contact',
            ]);
        });
    }
};
