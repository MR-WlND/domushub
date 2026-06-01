<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('blocks', function (Blueprint $table) {
            if (!Schema::hasColumn('blocks', 'code')) {
                $table->string('code', 100)->nullable()->unique()->after('name');
            }
            if (!Schema::hasColumn('blocks', 'status')) {
                $table->enum('status', ['active', 'inactive', 'maintenance'])->default('active')->after('code');
            }
            if (!Schema::hasColumn('blocks', 'number_of_floors')) {
                $table->unsignedInteger('number_of_floors')->nullable()->after('status');
            }
            if (!Schema::hasColumn('blocks', 'total_apartments')) {
                $table->unsignedInteger('total_apartments')->nullable()->after('number_of_floors');
            }
            if (!Schema::hasColumn('blocks', 'manager_name')) {
                $table->string('manager_name', 100)->nullable()->after('total_apartments');
            }
            if (!Schema::hasColumn('blocks', 'manager_contact')) {
                $table->string('manager_contact', 100)->nullable()->after('manager_name');
            }
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
