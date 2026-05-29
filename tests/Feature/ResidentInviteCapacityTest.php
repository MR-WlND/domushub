<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ResidentInviteCapacityTest extends TestCase
{
    use RefreshDatabase;

    public function test_resident_registration_uses_invite_capacity_and_creates_resident_record(): void
    {
        DB::table('blocks')->insert([
            ['name' => 'Block A', 'description' => 'Test block', 'created_at' => now(), 'updated_at' => now()],
        ]);

        DB::table('floors')->insert([
            ['block_id' => 1, 'floor_number' => 1, 'created_at' => now(), 'updated_at' => now()],
        ]);

        DB::table('apartments')->insert([
            ['floor_id' => 1, 'apartment_number' => 'A101', 'area' => 75.5, 'status' => 'vacant', 'created_at' => now(), 'updated_at' => now()],
        ]);

        DB::table('users')->insert([
            [
                'name' => 'Admin Owner',
                'email' => 'admin@example.com',
                'phone' => '0900000001',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        DB::table('apartment_invites')->insert([
            [
                'apartment_id' => 1,
                'created_by' => 1,
                'invite_code' => 'INVITE-TEST-001',
                'intended_relationship' => 'tenant',
                'status' => 'active',
                'max_residents' => 2,
                'used_count' => 0,
                'expired_at' => now()->addDay(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $response = $this->post(route('resident.register.submit'), [
            'name' => 'Cư dân mới',
            'phone' => '0900000010',
            'email' => 'newresident@example.com',
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
            'invite_code' => 'INVITE-TEST-001',
        ]);

        $response->assertRedirect(route('resident.login'));

        $this->assertDatabaseHas('users', ['email' => 'newresident@example.com', 'role' => 'resident']);
        $this->assertDatabaseHas('residents', [
            'apartment_id' => 1,
            'invite_id' => 1,
            'relationship' => 'tenant',
        ]);
        $this->assertDatabaseHas('apartment_invites', [
            'id' => 1,
            'used_count' => 1,
            'status' => 'active',
        ]);
    }
}
