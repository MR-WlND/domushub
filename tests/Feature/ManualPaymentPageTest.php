<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Apartment;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ManualPaymentPageTest extends TestCase
{
    /**
     * Test trang Thu tiền thủ công có thể truy cập khi có apartment_id.
     */
    public function test_manual_payment_page_accessible_for_admin(): void
    {
        $admin = User::where('role', 'admin')->first();
        if (!$admin) {
            $this->markTestSkipped('Không có user admin trong DB.');
        }
        
        $apartment = Apartment::first();
        if (!$apartment) {
            $this->markTestSkipped('Không có apartment trong DB.');
        }

        $response = $this->actingAs($admin)->get(portal_route('manual-payment.index', ['apartment_id' => $apartment->id]));
        $response->assertStatus(200);
        $response->assertSee('Thu tiền');
    }

    /**
     * Test trang redirect khi không có apartment_id.
     */
    public function test_manual_payment_redirects_without_apartment_id(): void
    {
        $admin = User::where('role', 'admin')->first();
        if (!$admin) {
            $this->markTestSkipped('Không có user admin trong DB.');
        }

        $response = $this->actingAs($admin)->get(portal_route('manual-payment.index'));
        $response->assertStatus(302);
    }

    /**
     * Test trang không truy cập được khi chưa đăng nhập.
     */
    public function test_manual_payment_page_requires_auth(): void
    {
        $response = $this->get(route('admin.manual-payment.index'));
        $response->assertRedirect();
    }
}
