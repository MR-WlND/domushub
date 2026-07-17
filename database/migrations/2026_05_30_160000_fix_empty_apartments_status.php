<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Tự động quét và cập nhật các căn hộ đang ở trạng thái 'occupied' nhưng có 0 cư dân về 'vacant'
        $affected = DB::table('apartments')
            ->where('status', 'occupied')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('residents')
                    ->whereColumn('residents.apartment_id', 'apartments.id')
                    ->whereNull('residents.deleted_at');
            })
            ->update([
                'status' => 'vacant',
                'updated_at' => now(),
            ]);

        // Ghi lại log nếu chạy trong môi trường console
        if (app()->runningInConsole()) {
            echo "\n>>> Đã tự động cập nhật {$affected} căn hộ không có cư dân từ 'Đang ở' về 'Trống' thành công!\n";
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Không cần hoàn tác dữ liệu dọn dẹp
    }
};
