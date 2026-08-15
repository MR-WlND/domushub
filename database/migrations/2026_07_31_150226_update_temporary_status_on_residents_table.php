<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE residents MODIFY COLUMN temporary_status VARCHAR(30) NOT NULL DEFAULT 'permanent'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE residents MODIFY COLUMN temporary_status ENUM('permanent', 'temporary') NOT NULL DEFAULT 'permanent'");
    }
};
