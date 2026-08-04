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
        $block = Block::create(['name' => 'Tòa A']);
        $floor = Floor::create([
            'block_id'     => $block->id,
            'name'         => 'Tầng 1',
            'floor_number' => 1,
        ]);

        return Apartment::create(array_merge([
            'floor_id'         => $floor->id,
            'apartment_number' => 'A101',
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
            'phone'        => '0912345678',
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
            'phone'  => '0987654321',
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
            'phone'  => '0987654399',
            'status' => 'active',
        ], $overrides));
    }
}
