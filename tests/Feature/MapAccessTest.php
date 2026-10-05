<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MapAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_access_map(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user)->get('/map');

        $response->assertStatus(200);
        $response->assertViewIs('map');
    }

    public function test_authenticated_admin_can_access_map(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get('/map');

        $response->assertStatus(200);
        $response->assertViewIs('map');
    }

    public function test_guest_cannot_access_map(): void
    {
        $response = $this->get('/map');

        $response->assertRedirect(route('login'));
    }

    public function test_cong_route_aliases_to_map(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user)->get('/cong');

        $response->assertStatus(200);
        $response->assertViewIs('map');
    }
}

