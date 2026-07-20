<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE service_prices MODIFY COLUMN type ENUM('electricity', 'water', 'parking', 'management_fee', 'internet', 'service', 'other', 'motorbike', 'car', 'bicycle') NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE service_prices MODIFY COLUMN type ENUM('electricity', 'water', 'parking', 'management_fee', 'internet', 'service', 'other') NOT NULL");
    }
};
