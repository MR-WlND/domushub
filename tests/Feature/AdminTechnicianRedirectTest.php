<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminTechnicianRedirectTest extends TestCase
{
    use RefreshDatabase;

    public function test_technician_login_redirects_to_my_tasks(): void
    {
        $technician = User::factory()->create([
            'email' => 'tech@example.com',
            'password' => Hash::make('secret123'),
            'role' => 'technician',
            'status' => 'active',
        ]);

        $response = $this->post(route('admin.login.submit'), [
            'email' => $technician->email,
            'password' => 'secret123',
        ]);

        $response->assertRedirect(route('admin.tickets.my-tasks'));
    }

    public function test_technician_is_redirected_away_from_accounting_pages(): void
    {
        $technician = User::factory()->create([
            'role' => 'technician',
            'status' => 'active',
        ]);

        $response = $this->actingAs($technician)->get(route('admin.utility-readings.index'));

        $response->assertRedirect(route('admin.tickets.my-tasks'));
    }
}
