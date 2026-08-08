<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE vehicles MODIFY COLUMN status ENUM('pending','awaiting_payment','active','pending_renewal','locked','inactive') NOT NULL DEFAULT 'pending'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE vehicles MODIFY COLUMN status ENUM('pending','active','pending_renewal','locked','inactive') NOT NULL DEFAULT 'pending'");
    }
};
