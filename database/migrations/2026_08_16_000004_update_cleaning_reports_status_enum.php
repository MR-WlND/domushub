<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE cleaning_reports MODIFY COLUMN status ENUM('pending', 'processing', 'completed_pending', 'resolved') DEFAULT 'pending'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE cleaning_reports MODIFY COLUMN status ENUM('pending', 'processing', 'resolved') DEFAULT 'pending'");
    }
};
