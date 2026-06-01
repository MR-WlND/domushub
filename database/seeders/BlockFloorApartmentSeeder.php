<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BlockFloorApartmentSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $blocks = [
            [
                'name' => 'Tòa A',
                'code' => 'A',
                'status' => 'active',
                'number_of_floors' => 5,
                'total_apartments' => 25,
                'manager_name' => 'Nguyễn Văn An',
                'manager_contact' => '0900000101',
                'description' => 'Tòa A nằm ở phía Đông khu chung cư.',
            ],
            [
                'name' => 'Tòa B',
                'code' => 'B',
                'status' => 'active',
                'number_of_floors' => 5,
                'total_apartments' => 25,
                'manager_name' => 'Lê Thị Bình',
                'manager_contact' => '0900000102',
                'description' => 'Tòa B có view công viên và trường học.',
            ],
            [
                'name' => 'Tòa C',
                'code' => 'C',
                'status' => 'maintenance',
                'number_of_floors' => 5,
                'total_apartments' => 25,
                'manager_name' => 'Trần Văn Cường',
                'manager_contact' => '0900000103',
                'description' => 'Tòa C đang trong giai đoạn bảo trì nhẹ.',
            ],
        ];

        foreach ($blocks as $blockData) {
            $blockData['created_at'] = $now;
            $blockData['updated_at'] = $now;

            $existingBlock = DB::table('blocks')
                ->where('name', $blockData['name'])
                ->orWhere('code', $blockData['code'])
                ->first();

            if ($existingBlock) {
                $blockId = $existingBlock->id;
            } else {
                $blockId = DB::table('blocks')->insertGetId($blockData);
            }

            for ($floorNumber = 1; $floorNumber <= 5; $floorNumber++) {
                $existingFloor = DB::table('floors')
                    ->where('block_id', $blockId)
                    ->where('floor_number', $floorNumber)
                    ->first();

                if ($existingFloor) {
                    $floorId = $existingFloor->id;
                } else {
                    $floorId = DB::table('floors')->insertGetId([
                        'block_id' => $blockId,
                        'floor_number' => $floorNumber,
                        'name' => 'Tầng ' . $floorNumber,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }

                for ($apartmentIndex = 1; $apartmentIndex <= 5; $apartmentIndex++) {
                    $apartmentNumber = ($floorNumber * 100) + $apartmentIndex;
                    $existingApartment = DB::table('apartments')
                        ->where('floor_id', $floorId)
                        ->where('apartment_number', (string) $apartmentNumber)
                        ->first();

                    if ($existingApartment) {
                        continue;
                    }

                    $statusOptions = ['vacant', 'occupied', 'maintenance'];
                    $status = $statusOptions[($apartmentIndex - 1) % count($statusOptions)];

                    DB::table('apartments')->insert([
                        'floor_id' => $floorId,
                        'apartment_number' => (string) $apartmentNumber,
                        'area' => 45 + ($apartmentIndex * 2),
                        'status' => $status,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }
            }
        }
    }
}
