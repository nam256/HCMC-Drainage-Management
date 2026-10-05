<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserRoleTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_register_gets_default_role_user(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        
        $user = User::where('email', 'test@example.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('user', $user->role);
        $this->assertTrue($user->isUser());
        $this->assertFalse($user->isAdmin());
    }

    public function test_user_can_access_dashboard(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertViewIs('dashboard');
    }

    public function test_user_can_access_map(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user)->get('/map');

        $response->assertStatus(200);
        $response->assertViewIs('map');
    }

    public function test_user_cannot_access_admin_manage_page(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user)->get('/cong/manage');

        $response->assertStatus(403); // Forbidden
    }

    public function test_user_cannot_access_admin_registration(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user)->get('/register-admin');

        $response->assertStatus(403); // Forbidden
    }

    public function test_admin_can_access_all_pages(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->get('/dashboard')->assertStatus(200);
        $this->actingAs($admin)->get('/map')->assertStatus(200);
        $this->actingAs($admin)->get('/cong/manage')->assertStatus(200);
        $this->actingAs($admin)->get('/register-admin')->assertStatus(200);
    }

    public function test_admin_can_create_user_account(): void
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
        
        $user = User::where('email', 'newuser@example.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('user', $user->role);
    }

    public function test_admin_can_create_admin_account(): void
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
        
        $newAdmin = User::where('email', 'newadmin@example.com')->first();
        $this->assertNotNull($newAdmin);
        $this->assertEquals('admin', $newAdmin->role);
        $this->assertTrue($newAdmin->isAdmin());
    }

    public function test_login_validates_correct_password(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('correct-password'),
            'role' => 'user',
        ]);

        // Đúng mật khẩu
        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'correct-password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));

        // Đăng xuất
        $this->post('/logout');

        // Sai mật khẩu
        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('email');
    }
}

