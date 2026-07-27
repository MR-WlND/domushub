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
            'management_fee' => ['name' => 'Phí quản lý', 'unit_price' => 15000, 'description' => 'Phí quản lý hàng tháng (trên m2)'],
            'motorbike'      => ['type' => 'parking_fee', 'vehicle_type' => 'motorbike', 'name' => 'Phí gửi xe máy', 'unit_price' => 100000, 'description' => 'Phí gửi xe máy (xăng)',],
            'electric_bike'  => ['type' => 'parking_fee', 'vehicle_type' => 'electric_bike', 'name' => 'Phí gửi xe điện', 'unit_price' => 100000, 'description' => 'Phí gửi xe điện / xe đạp điện',],
            'car'            => ['type' => 'parking_fee', 'vehicle_type' => 'car', 'name' => 'Phí gửi ô tô', 'unit_price' => 1200000, 'description' => 'Phí gửi xe ô tô',],
            'bicycle'        => ['type' => 'parking_fee', 'vehicle_type' => 'bicycle', 'name' => 'Phí gửi xe đạp', 'unit_price' => 50000, 'description' => 'Phí gửi xe đạp',],
            'internet'       => ['name' => 'Phí Internet', 'unit_price' => 130000, 'description' => 'Phí Internet hàng tháng'],
            'service'        => ['name' => 'Phí dịch vụ khác', 'unit_price' => 0, 'description' => 'Các phí dịch vụ phát sinh'],
        ];

        foreach ($servicePrices as $type => $data) {
            ServicePrice::updateOrCreate(
                [
                    'type' => $data['type'] ?? $type,
                    'vehicle_type' => $data['vehicle_type'] ?? null,
                ],
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
