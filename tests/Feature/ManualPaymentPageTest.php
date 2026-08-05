<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Apartment;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ManualPaymentPageTest extends TestCase
{
    /**
     * Test trang Thu tiền thủ công có thể truy cập với admin.
     */
    public function test_manual_payment_page_accessible_for_admin(): void
    {
        $admin = User::where('role', 'admin')->first();
        if (!$admin) {
            $this->markTestSkipped('Không có user admin trong DB.');
        }

        $response = $this->actingAs($admin)->get(portal_route('manual-payment.index'));
        $response->assertStatus(200);
        $response->assertSee('Thu tiền thủ công');
    }

    /**
     * Test AJAX endpoint search trả về 200 khi có query.
     */
    public function test_search_endpoint_returns_json(): void
    {
        $admin = User::where('role', 'admin')->first();
        if (!$admin) {
            $this->markTestSkipped('Không có user admin trong DB.');
        }

        $response = $this->actingAs($admin)
            ->getJson(portal_route('manual-payment.search', ['q' => 'A1']));

        $response->assertStatus(200)
                 ->assertJsonStructure(['success']);
    }

    /**
     * Test AJAX search khi không có từ khóa trả về danh sách căn hộ nợ mặc định.
     */
    public function test_search_without_query_returns_default_list(): void
    {
        $admin = User::where('role', 'admin')->first();
        if (!$admin) {
            $this->markTestSkipped('Không có user admin trong DB.');
        }

        $response = $this->actingAs($admin)
            ->getJson(portal_route('manual-payment.search', ['q' => '']));

        $response->assertStatus(200)
                 ->assertJson(['success' => true, 'is_default_list' => true]);
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
