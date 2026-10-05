<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminRegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_registration_page(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get('/register-admin');

        $response->assertStatus(200);
        $response->assertViewIs('auth.register-admin');
    }

    public function test_user_cannot_access_admin_registration_page(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user)->get('/register-admin');

        $response->assertStatus(403);
    }

    public function test_guest_cannot_access_admin_registration_page(): void
    {
        $response = $this->get('/register-admin');

        $response->assertRedirect(route('login'));
    }

    public function test_admin_can_create_user_via_registration(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->post('/register-admin', [
            'name' => 'New User',
            'email' => 'newuser@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'user',
        ]);

        $response->assertRedirect(route('cong.manage'));
        $response->assertSessionHas('success');
        
        $this->assertDatabaseHas('users', [
            'email' => 'newuser@example.com',
            'role' => 'user',
        ]);
    }

    public function test_admin_can_create_admin_via_registration(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->post('/register-admin', [
            'name' => 'New Admin',
            'email' => 'newadmin@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'admin',
        ]);

        $response->assertRedirect(route('cong.manage'));
        
        $this->assertDatabaseHas('users', [
            'email' => 'newadmin@example.com',
            'role' => 'admin',
        ]);
        
        $newAdmin = User::where('email', 'newadmin@example.com')->first();
        $this->assertTrue($newAdmin->isAdmin());
    }

    public function test_admin_registration_validates_required_fields(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->post('/register-admin', []);

        $response->assertSessionHasErrors(['name', 'email', 'password', 'role']);
    }

    public function test_admin_registration_validates_email_unique(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $existingUser = User::factory()->create(['email' => 'existing@example.com']);

        $response = $this->actingAs($admin)->post('/register-admin', [
            'name' => 'Test',
            'email' => 'existing@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'user',
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    public function test_admin_registration_validates_password_confirmation(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->post('/register-admin', [
            'name' => 'Test',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'different',
            'role' => 'user',
        ]);

        $response->assertSessionHasErrors(['password']);
    }
}

