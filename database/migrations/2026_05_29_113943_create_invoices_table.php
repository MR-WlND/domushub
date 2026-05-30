<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('apartment_id')->constrained('apartments')->onDelete('cascade');
            $table->foreignId('created_by')->constrained('users')->onDelete('restrict');
            $table->string('invoice_code', 30)->unique(); // VD: INV-2025-0001
            $table->string('title', 150);
            $table->enum('type', ['electricity', 'water', 'management_fee', 'parking', 'other']);
            $table->decimal('amount', 15, 2);
            $table->date('billing_month'); // Tháng tính phí (2025-05-01)
            $table->date('due_date');      // Hạn thanh toán
            $table->enum('status', ['unpaid', 'paid', 'overdue'])->default('unpaid');
            $table->timestamp('paid_at')->nullable();
            $table->string('payment_method', 50)->nullable(); // cash, transfer, ...
            $table->text('note')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
