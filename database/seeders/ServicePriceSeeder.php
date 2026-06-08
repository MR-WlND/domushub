<?php

namespace Database\Seeders;

use App\Models\ServicePrice;
use Illuminate\Database\Seeder;

class ServicePriceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Định nghĩa danh mục phí với type làm key để quản lý độc lập
        $servicePrices = [
            'electricity'    => ['name' => 'Giá điện', 'unit_price' => 3000, 'description' => 'Biểu giá điện theo kWh'],
            'water'          => ['name' => 'Giá nước', 'unit_price' => 15000, 'description' => 'Biểu giá nước theo m³'],
            'management_fee' => ['name' => 'Phí quản lý', 'unit_price' => 200000, 'description' => 'Phí quản lý hàng tháng'],
            'parking'        => ['name' => 'Phí gửi xe', 'unit_price' => 180000, 'description' => 'Phí gửi xe cơ bản'],
            'internet'       => ['name' => 'Phí Internet', 'unit_price' => 130000, 'description' => 'Phí Internet hàng tháng'],
            'service'        => ['name' => 'Phí dịch vụ khác', 'unit_price' => 0, 'description' => 'Các phí dịch vụ phát sinh'],
        ];

        foreach ($servicePrices as $type => $data) {
            // Sử dụng updateOrCreate với type là khóa duy nhất để đảm bảo tính nhất quán
            ServicePrice::updateOrCreate(
                ['type' => $type],
                [
                    'name'        => $data['name'],
                    'unit_price'  => $data['unit_price'],
                    'description' => $data['description'],
                    'status'      => 'active',
                ]
            );
        }
    }
}
