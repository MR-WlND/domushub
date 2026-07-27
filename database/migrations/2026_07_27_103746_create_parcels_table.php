<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('parcels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('apartment_id')->constrained('apartments')->onDelete('cascade');
            $table->string('sender_name')->comment('Tên người gửi');
            $table->string('tracking_code')->nullable()->comment('Mã vận đơn');
            $table->string('carrier')->nullable()->comment('Đơn vị vận chuyển (GHN, GHTK, ...)');
            $table->text('description')->nullable()->comment('Mô tả bưu phẩm');
            $table->enum('status', ['pending', 'notified', 'received', 'returned'])
                ->default('pending')
                ->comment('pending=mới nhận, notified=đã báo cư dân, received=cư dân đã lấy, returned=hoàn trả');
            $table->timestamp('arrived_at')->useCurrent()->comment('Thời gian bưu phẩm đến');
            $table->timestamp('received_at')->nullable()->comment('Thời gian cư dân nhận');
            $table->timestamp('returned_at')->nullable()->comment('Thời gian hoàn trả');
            $table->text('note')->nullable()->comment('Ghi chú của lễ tân');
            $table->foreignId('created_by')->constrained('users')->comment('Lễ tân nhập bưu phẩm');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('parcels');
    }
};
