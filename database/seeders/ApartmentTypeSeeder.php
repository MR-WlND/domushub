<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ApartmentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        // 1. Chèn danh sách loại căn hộ mẫu
        $types = [
            [
                'name' => 'Studio Plus',
                'description' => 'Căn hộ Studio phù hợp cho người độc thân hoặc cặp đôi',
                'base_service_fee' => 12000.00,
                'bedroom_count' => 0,
                'bathroom_count' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => '1BR Standard',
                'description' => 'Căn hộ tiêu chuẩn 1 phòng ngủ',
                'base_service_fee' => 14000.00,
                'bedroom_count' => 1,
                'bathroom_count' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => '2BR Classic',
                'description' => 'Căn hộ tiêu chuẩn 2 phòng ngủ',
                'base_service_fee' => 16000.00,
                'bedroom_count' => 2,
                'bathroom_count' => 2,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => '3BR Deluxe',
                'description' => 'Căn hộ cao cấp 3 phòng ngủ',
                'base_service_fee' => 18000.00,
                'bedroom_count' => 3,
                'bathroom_count' => 2,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Duplex Premium',
                'description' => 'Căn hộ thông tầng cao cấp',
                'base_service_fee' => 22000.00,
                'bedroom_count' => 3,
                'bathroom_count' => 3,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Penthouse Royal',
                'description' => 'Căn hộ siêu sang tầng áp mái',
                'base_service_fee' => 28000.00,
                'bedroom_count' => 4,
                'bathroom_count' => 4,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        foreach ($types as $type) {
            DB::table('apartment_types')->updateOrInsert(
                ['name' => $type['name']],
                $type
            );
        }

        // 2. Gán tự động loại căn hộ cho các căn hộ có sẵn dựa vào diện tích
        $studioId = DB::table('apartment_types')->where('name', 'Studio Plus')->value('id');
        $oneBRId = DB::table('apartment_types')->where('name', '1BR Standard')->value('id');
        $twoBRId = DB::table('apartment_types')->where('name', '2BR Classic')->value('id');
        $threeBRId = DB::table('apartment_types')->where('name', '3BR Deluxe')->value('id');

        DB::table('apartments')->where('area', '<', 50)->update(['apartment_type_id' => $studioId]);
        DB::table('apartments')->whereBetween('area', [50, 69.99])->update(['apartment_type_id' => $oneBRId]);
        DB::table('apartments')->whereBetween('area', [70, 99.99])->update(['apartment_type_id' => $twoBRId]);
        DB::table('apartments')->where('area', '>=', 100)->update(['apartment_type_id' => $threeBRId]);
    }
}
