<?php

namespace Tests\Helpers;

use App\Models\Apartment;
use App\Models\Block;
use App\Models\Floor;
use App\Models\User;

/**
 * Helper trait cho việc tạo dữ liệu test dùng chung.
 */
trait TestDataHelper
{
    /**
     * Tạo Block → Floor → Apartment với các field bắt buộc.
     */
    protected function makeApartment(array $overrides = []): Apartment
    {
        $block = Block::create(['name' => 'Tòa ' . strtoupper(\Illuminate\Support\Str::random(5))]);
        $floor = Floor::create([
            'block_id'     => $block->id,
            'name'         => 'Tầng 1',
            'floor_number' => rand(1, 30),
        ]);

        return Apartment::create(array_merge([
            'floor_id'         => $floor->id,
            'apartment_number' => 'APT-' . rand(100, 999),
            'area'             => 65.0,
            'status'           => 'vacant',
        ], $overrides));
    }

    /**
     * Tạo resident gắn với một apartment.
     */
    protected function makeResident(Apartment $apartment, array $overrides = []): User
    {
        return User::factory()->create(array_merge([
            'role'         => 'resident',
            'phone'        => '09' . rand(10000000, 99999999),
            'status'       => 'active',
            'apartment_id' => $apartment->id,
        ], $overrides));
    }

    /**
     * Tạo admin user.
     */
    protected function makeAdmin(array $overrides = []): User
    {
        return User::factory()->create(array_merge([
            'role'   => 'admin',
            'phone'  => '09' . rand(10000000, 99999999),
            'status' => 'active',
        ], $overrides));
    }

    /**
     * Tạo technician user.
     */
    protected function makeTechnician(array $overrides = []): User
    {
        return User::factory()->create(array_merge([
            'role'   => 'technician',
            'phone'  => '09' . rand(10000000, 99999999),
            'status' => 'active',
        ], $overrides));
    }
}
