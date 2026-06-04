<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ApartmentSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        // Block
        DB::table('blocks')->insert([
            ['name' => 'Block A', 'created_at' => $now, 'updated_at' => $now],
        ]);
        $blockId = DB::getPdo()->lastInsertId();

        // Floors
        DB::table('floors')->insert([
            ['block_id' => $blockId, 'floor_number' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['block_id' => $blockId, 'floor_number' => 2, 'created_at' => $now, 'updated_at' => $now],
        ]);
        $floor1 = DB::table('floors')->where('floor_number', 1)->value('id');
        $floor2 = DB::table('floors')->where('floor_number', 2)->value('id');

        // Apartments
        DB::table('apartments')->insert([
            ['floor_id' => $floor1, 'apartment_number' => '001', 'area' => 65.5, 'status' => 'occupied', 'created_at' => $now, 'updated_at' => $now],
            ['floor_id' => $floor1, 'apartment_number' => '101', 'area' => 65.5, 'status' => 'occupied', 'created_at' => $now, 'updated_at' => $now],
            ['floor_id' => $floor2, 'apartment_number' => '201', 'area' => 72.0, 'status' => 'occupied', 'created_at' => $now, 'updated_at' => $now],
        ]);
        $apt001 = DB::table('apartments')->where('apartment_number', '001')->value('id');
        $apt101 = DB::table('apartments')->where('apartment_number', '101')->value('id');
        $apt201 = DB::table('apartments')->where('apartment_number', '201')->value('id');

        // Link residents (user_id 4 = Cư Dân A, user_id 5 = Cư Dân B)
        $userA = DB::table('users')->where('email', 'resident.a@example.com')->value('id');
        $userB = DB::table('users')->where('email', 'resident.b@example.com')->value('id');

        DB::table('residents')->insert([
            [
                'user_id'          => $userA,
                'apartment_id'     => $apt001,
                'invite_id'        => null,
                'relationship'     => 'owner',
                'temporary_status' => 'permanent',
                'start_date'       => $now->toDateString(),
                'end_date'         => null,
                'created_at'       => $now,
                'updated_at'       => $now,
            ],
            [
                'user_id'          => $userB,
                'apartment_id'     => $apt201,
                'invite_id'        => null,
                'relationship'     => 'owner',
                'temporary_status' => 'permanent',
                'start_date'       => $now->toDateString(),
                'end_date'         => null,
                'created_at'       => $now,
                'updated_at'       => $now,
            ],
        ]);

        // Update users.apartment_id
        DB::table('users')->where('id', $userA)->update(['apartment_id' => $apt001]);
        DB::table('users')->where('id', $userB)->update(['apartment_id' => $apt201]);
    }
}
