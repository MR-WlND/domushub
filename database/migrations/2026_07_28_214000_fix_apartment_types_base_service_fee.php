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
        // 1. Nhân 1000 với các giá trị quá nhỏ (dưới 1000) ví dụ: 10 -> 10000
        DB::table('apartment_types')
            ->where('base_service_fee', '<', 1000)
            ->update([
                'base_service_fee' => DB::raw('base_service_fee * 1000')
            ]);

        // 2. Sửa các giá trị quá lớn do nhân nhầm nhiều lần (ví dụ: 1.111.000.000 -> 1.111)
        $types = DB::table('apartment_types')->get();
        foreach ($types as $type) {
            $fee = (float) $type->base_service_fee;
            if ($fee >= 1000000) {
                while ($fee >= 1000000) {
                    $fee = $fee / 1000;
                }
                DB::table('apartment_types')
                    ->where('id', $type->id)
                    ->update(['base_service_fee' => $fee]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Divide by 1000 to revert the change
        DB::table('apartment_types')
            ->where('base_service_fee', '>=', 1000)
            ->update([
                'base_service_fee' => DB::raw('base_service_fee / 1000')
            ]);
    }
};
