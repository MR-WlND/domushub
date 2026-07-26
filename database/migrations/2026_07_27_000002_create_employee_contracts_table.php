<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tạo bảng hợp đồng lao động nhân sự (Employee Contracts)
 * Phù hợp mô hình quản lý chung cư cao cấp: nhân sự nội bộ, vendor thuê ngoài, thời vụ.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_contracts', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('ma_hop_dong', 50)->unique()->comment('Mã hợp đồng (HD-001...)');

            $table->enum('loai_hop_dong', [
                'thu_viec',               // Thử việc (2 tháng)
                'xac_dinh_thoi_han',       // Xác định thời hạn (1-3 năm)
                'khong_thoi_han',         // Không xác định thời hạn
                'vendor_thue_ngoai',      // Vendor/Thuê ngoài (Bảo vệ, Vệ sinh...)
                'thoi_vu',                // Thời vụ / Dự án
            ])->default('xac_dinh_thoi_han');

            $table->date('ngay_bat_dau')->comment('Ngày bắt đầu hiệu lực');
            $table->date('ngay_ket_thuc')->nullable()->comment('Ngày hết hạn (null nếu không thời hạn)');

            $table->decimal('luong_co_ban', 12, 2)->default(0)->comment('Lương cơ bản hợp đồng');

            $table->enum('trang_thai', [
                'hieu_luc',     // Đang hiệu lực
                'sap_het_han',  // Cảnh báo hết hạn (trong 30 ngày)
                'het_han',      // Đã hết hạn
                'thanh_ly',     // Đã thanh lý
            ])->default('hieu_luc');

            $table->text('ghi_chu')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();

            $table->index(['user_id', 'trang_thai']);
            $table->index('ngay_ket_thuc');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_contracts');
    }
};
