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
        // Phí dịch vụ chung (type đơn thuần)
        $general = [
            'water'          => ['name' => 'Giá nước',          'unit_price' => 15000,  'description' => 'Biểu giá nước theo m³'],
            'management_fee' => ['name' => 'Phí quản lý',       'unit_price' => 15000,  'description' => 'Phí quản lý hàng tháng (trên m2)'],
            'internet'       => ['name' => 'Phí Internet',      'unit_price' => 130000, 'description' => 'Phí Internet hàng tháng'],
            'service'        => ['name' => 'Phí dịch vụ khác', 'unit_price' => 0,       'description' => 'Các phí dịch vụ phát sinh'],
            'other'          => ['name' => 'Phí khác',          'unit_price' => 0,       'description' => 'Các khoản phí khác'],
        ];

        foreach ($general as $type => $data) {
            ServicePrice::updateOrCreate(
                ['type' => $type, 'vehicle_type' => null],
                [
                    'name'        => $data['name'],
                    'unit_price'  => $data['unit_price'],
                    'description' => $data['description'],
                    'status'      => 'active',
                ]
            );
        }

        // Phí gửi xe (type = parking_fee, phân biệt qua vehicle_type)
        $parkingFees = [
            'motorbike'    => ['name' => 'Phí gửi xe máy',  'unit_price' => 100000,  'description' => 'Phí gửi xe máy (xăng)'],
            'electric_bike'=> ['name' => 'Phí gửi xe điện', 'unit_price' => 100000,  'description' => 'Phí gửi xe điện / xe đạp điện'],
            'car'          => ['name' => 'Phí gửi ô tô',    'unit_price' => 1200000, 'description' => 'Phí gửi xe ô tô'],
            'bicycle'      => ['name' => 'Phí gửi xe đạp',  'unit_price' => 50000,   'description' => 'Phí gửi xe đạp'],
        ];

        foreach ($parkingFees as $vehicleType => $data) {
            ServicePrice::updateOrCreate(
                ['type' => 'parking_fee', 'vehicle_type' => $vehicleType],
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
