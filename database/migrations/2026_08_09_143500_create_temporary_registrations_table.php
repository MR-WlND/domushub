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
        Schema::create('temporary_registrations', function (Blueprint $table) {
            $table->id();
            
            // 1. Khóa ngoại
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); 
            $table->foreignId('apartment_id')->constrained('apartments')->onDelete('cascade');
            $table->foreignId('approved_by')->nullable()->constrained('users');

            // 2. Thông tin phân loại & thời gian
            $table->enum('type', ['residence', 'absence']); // residence: Tạm trú, absence: Tạm vắng
            $table->date('start_date');
            $table->date('end_date')->nullable();

            // 3. Thông tin bổ sung
            $table->text('reason')->nullable();
            $table->string('attachment_path')->nullable();
            $table->text('rejection_reason')->nullable();

            // 4. Trạng thái xử lý
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('temporary_registrations');
    }
};
