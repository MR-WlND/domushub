<?php

namespace Database\Seeders;

use App\Models\Facility;
use Illuminate\Database\Seeder;

class FacilitySeeder extends Seeder
{
    public function run(): void
    {
        $facilities = [
            [
                'name'        => 'Hồ bơi',
                'capacity'    => 30,
                'description' => 'Hồ bơi ngoài trời tầng 5, mở cửa 6:00 - 20:00',
                'status'      => 'available',
            ],
            [
                'name'        => 'Phòng Gym',
                'capacity'    => 20,
                'description' => 'Phòng tập thể dục đầy đủ thiết bị, mở cửa 5:30 - 22:00',
                'status'      => 'available',
            ],
            [
                'name'        => 'Sân BBQ',
                'capacity'    => 50,
                'description' => 'Khu vực nướng BBQ ngoài trời tầng thượng',
                'status'      => 'available',
            ],
            [
                'name'        => 'Phòng sinh hoạt cộng đồng',
                'capacity'    => 100,
                'description' => 'Phòng hội họp, tổ chức sự kiện cư dân',
                'status'      => 'available',
            ],
            [
                'name'        => 'Sân tennis',
                'capacity'    => 4,
                'description' => 'Sân tennis tiêu chuẩn, mở cửa 6:00 - 21:00',
                'status'      => 'maintenance',
            ],
            [
                'name'        => 'Phòng đọc sách',
                'capacity'    => 15,
                'description' => 'Thư viện mini dành cho cư dân và trẻ em',
                'status'      => 'available',
            ],
        ];

        foreach ($facilities as $data) {
            Facility::updateOrCreate(
                ['name' => $data['name']],
                $data
            );
        }
    }
}
