<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('apartment_id')->constrained('apartments')->restrictOnDelete();
            $table->string('title', 200);
            $table->unsignedTinyInteger('billing_month');
            $table->unsignedSmallInteger('billing_year');
            $table->date('due_date');
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->enum('status', ['unpaid', 'paid', 'overdue', 'cancelled'])->default('unpaid');
            $table->softDeletes();
            $table->timestamps();

            $table->index(['billing_year', 'billing_month'], 'idx_bills_month');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bills');
    }
};
