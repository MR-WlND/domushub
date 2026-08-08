<?php

namespace Tests\Feature\Auth;

use App\Models\ApartmentInvite;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Helpers\TestDataHelper;
use Tests\TestCase;

class ResidentRegisterPhoneTest extends TestCase
{
    use RefreshDatabase, TestDataHelper;

    private function createValidInvite(): ApartmentInvite
    {
        $apartment = $this->makeApartment();
        return ApartmentInvite::create([
            'apartment_id'          => $apartment->id,
            'block_id'              => $apartment->floor->block_id,
            'intended_relationship' => 'tenant',
            'invite_code'           => 'RES-TEST1234',
            'max_uses'              => 5,
            'uses_count'            => 0,
            'status'                => 'active',
            'created_by'            => $this->makeAdmin()->id,
        ]);
    }

    /**
     * Đăng ký với số điện thoại Việt Nam 10 số hợp lệ (ví dụ: 0901234567, 0381234567) không bị lỗi phone.
     */
    public function test_resident_registration_accepts_valid_vietnamese_phone(): void
    {
        $invite = $this->createValidInvite();

        $response = $this->post(route('resident.register.submit'), [
            'name'                  => 'Nguyễn Văn A',
            'phone'                 => '0901234567',
            'email'                 => 'nguyenvana@test.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
            'invite_code'           => $invite->invite_code,
        ]);

        $response->assertSessionHasNoErrors(['phone']);
    }

    /**
     * Đăng ký với số điện thoại không hợp lệ (không bắt đầu bằng 03, 05, 07, 08, 09 hoặc không đủ 10 số) sẽ bị lỗi validation.
     */
    public function test_resident_registration_rejects_invalid_phone_numbers(): void
    {
        $invite = $this->createValidInvite();

        $invalidPhones = [
            '1234567890',   // Không bắt đầu bằng 0
            '0123456789',   // Đầu 01 không còn hợp lệ tại Việt Nam
            '090123456',    // 9 chữ số (thiếu)
            '09012345678',  // 11 chữ số (thừa)
            '+84901234567', // Có mã quốc gia +84
            'abc09012345',  // Chứa chữ
        ];

        foreach ($invalidPhones as $phone) {
            $response = $this->post(route('resident.register.submit'), [
                'name'                  => 'Test User',
                'phone'                 => $phone,
                'email'                 => 'test_' . rand(100, 999) . '@example.com',
                'password'              => 'password123',
                'password_confirmation' => 'password123',
                'invite_code'           => $invite->invite_code,
            ]);

            $response->assertSessionHasErrors(['phone']);
        }
    }
}
