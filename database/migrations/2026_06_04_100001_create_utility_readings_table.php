<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('utility_readings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('apartment_id')->constrained('apartments')->onDelete('cascade');
            $table->enum('service_type', ['electricity', 'water']);
            $table->integer('billing_month');  // 1-12
            $table->integer('billing_year');
            $table->decimal('previous_reading', 12, 2)->default(0);
            $table->decimal('current_reading', 12, 2)->default(0);
            $table->decimal('consumption', 12, 2)->default(0);
            $table->decimal('unit_price', 12, 2)->default(0);
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->enum('status', ['draft', 'finalized'])->default('draft');
            $table->foreignId('recorded_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('finalized_at')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();

            // Mỗi căn hộ chỉ có 1 bản ghi mỗi loại dịch vụ mỗi tháng
            $table->unique(['apartment_id', 'service_type', 'billing_month', 'billing_year'], 'utility_readings_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('utility_readings');
    }
};
