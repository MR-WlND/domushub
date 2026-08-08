<?php

namespace Tests\Feature\Auth;

use App\Models\ApartmentInvite;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Helpers\TestDataHelper;
use Tests\TestCase;

class ResidentRegisterGmailTest extends TestCase
{
    use RefreshDatabase, TestDataHelper;

    private function createValidInvite(): ApartmentInvite
    {
        $apartment = $this->makeApartment();
        return ApartmentInvite::create([
            'apartment_id'          => $apartment->id,
            'block_id'              => $apartment->floor->block_id,
            'intended_relationship' => 'tenant',
            'invite_code'           => 'RES-GMAIL123',
            'max_uses'              => 5,
            'uses_count'            => 0,
            'status'                => 'active',
            'created_by'            => $this->makeAdmin()->id,
        ]);
    }

    /**
     * Đăng ký với địa chỉ Gmail hợp lệ (có đuôi @gmail.com) thành công qua bước validate email.
     */
    public function test_resident_registration_accepts_valid_gmail(): void
    {
        $invite = $this->createValidInvite();

        $response = $this->post(route('resident.register.submit'), [
            'name'                  => 'Nguyễn Văn B',
            'phone'                 => '0908765432',
            'email'                 => 'nguyenvanb.test@gmail.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
            'invite_code'           => $invite->invite_code,
        ]);

        $response->assertSessionHasNoErrors(['email']);
    }

    /**
     * Đăng ký với email không phải đuôi @gmail.com (như yahoo.com, outlook.com, ...) sẽ bị từ chối.
     */
    public function test_resident_registration_rejects_non_gmail_addresses(): void
    {
        $invite = $this->createValidInvite();

        $nonGmailAddresses = [
            'testuser@yahoo.com',
            'testuser@outlook.com',
            'testuser@hotmail.com',
            'testuser@company.vn',
            'testuser@gmail.com.vn',
        ];

        foreach ($nonGmailAddresses as $email) {
            $response = $this->post(route('resident.register.submit'), [
                'name'                  => 'Test User',
                'phone'                 => '09' . rand(10000000, 99999999),
                'email'                 => $email,
                'password'              => 'password123',
                'password_confirmation' => 'password123',
                'invite_code'           => $invite->invite_code,
            ]);

            $response->assertSessionHasErrors(['email']);
        }
    }
}
