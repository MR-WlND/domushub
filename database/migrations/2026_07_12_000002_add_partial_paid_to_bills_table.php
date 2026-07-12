<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Fix any invalid enum values (like empty string created by strict mode violation)
        DB::statement("UPDATE bills SET status = 'unpaid' WHERE status = '' OR status IS NULL OR status NOT IN ('unpaid', 'paid', 'overdue', 'cancelled')");
        // Add partial_paid to enum using raw statement since change() on enum has limitations in some doctrine versions
        DB::statement("ALTER TABLE bills MODIFY COLUMN status ENUM('unpaid', 'partial_paid', 'paid', 'overdue', 'cancelled') DEFAULT 'unpaid'");
    }

    public function down(): void
    {
        // Revert back, but if there are 'partial_paid' rows they might get truncated or cause error.
        // We'll update them to 'unpaid' first to be safe
        DB::statement("UPDATE bills SET status = 'unpaid' WHERE status = 'partial_paid'");
        DB::statement("ALTER TABLE bills MODIFY COLUMN status ENUM('unpaid', 'paid', 'overdue', 'cancelled') DEFAULT 'unpaid'");
    }
};
