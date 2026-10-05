<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Cong;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CongApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_can_view_cong_api(): void
    {
        $response = $this->getJson('/api/cong');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'type',
            'features' => []
        ]);
    }

    public function test_api_returns_empty_when_no_data(): void
    {
        $response = $this->getJson('/api/cong');

        $response->assertStatus(200);
        $response->assertJson([
            'type' => 'FeatureCollection',
            'features' => []
        ]);
    }

    public function test_authenticated_user_can_create_cong(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/cong', [
            'coordinates' => [[106.7000, 10.7756], [106.7020, 10.7760]],
            'quan_huyen' => 'Quận 1',
            'ten_tuyen' => 'Test Tuyến',
            'duong_kinh_mm' => 500,
            'vat_lieu' => 'Bê tông',
            'chieudai_m' => 100.5,
            'tinh_trang' => 'Tốt',
        ]);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'status',
            'id',
        ]);
    }

    public function test_authenticated_user_can_upload_file_with_cong(): void
    {
        Storage::fake('public');
        
        $user = User::factory()->create(['role' => 'user']);
        $file = UploadedFile::fake()->image('cong-image.jpg');

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/cong', [
            'coordinates' => [[106.7000, 10.7756], [106.7020, 10.7760]],
            'quan_huyen' => 'Quận 1',
            'ten_tuyen' => 'Test Tuyến',
            'file' => $file,
        ]);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'status',
            'id',
            'file_url',
        ]);
        
        // Kiểm tra file đã được lưu
        $this->assertNotNull($response->json('file_url'));
    }

    public function test_admin_can_update_cong(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        
        // Tạo cống test (sử dụng DB trực tiếp vì không có seeder trong test)
        $congId = \DB::table('cong')->insertGetId([
            'quan_huyen' => 'Quận 1',
            'ten_tuyen' => 'Tuyến Cũ',
            'geom' => \DB::raw("ST_GeomFromText('LINESTRING(106.7 10.77, 106.71 10.78)', 4326)"),
        ]);

        $response = $this->actingAs($admin, 'sanctum')->putJson("/api/cong/{$congId}", [
            'ten_tuyen' => 'Tuyến Mới',
            'quan_huyen' => 'Quận 2',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['status' => 'success']);
    }

    public function test_user_cannot_update_cong(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        
        $congId = \DB::table('cong')->insertGetId([
            'quan_huyen' => 'Quận 1',
            'ten_tuyen' => 'Test',
            'geom' => \DB::raw("ST_GeomFromText('LINESTRING(106.7 10.77, 106.71 10.78)', 4326)"),
        ]);

        $response = $this->actingAs($user, 'sanctum')->putJson("/api/cong/{$congId}", [
            'ten_tuyen' => 'Updated',
        ]);

        $response->assertStatus(403); // Forbidden
    }

    public function test_admin_can_delete_cong(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        
        $congId = \DB::table('cong')->insertGetId([
            'quan_huyen' => 'Quận 1',
            'ten_tuyen' => 'Test Delete',
            'geom' => \DB::raw("ST_GeomFromText('LINESTRING(106.7 10.77, 106.71 10.78)', 4326)"),
        ]);

        $response = $this->actingAs($admin, 'sanctum')->deleteJson("/api/cong/{$congId}");

        $response->assertStatus(200);
        $response->assertJson(['status' => 'success']);
        
        // Kiểm tra đã xóa
        $this->assertDatabaseMissing('cong', ['ogc_fid' => $congId]);
    }

    public function test_user_cannot_delete_cong(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        
        $congId = \DB::table('cong')->insertGetId([
            'quan_huyen' => 'Quận 1',
            'ten_tuyen' => 'Test',
            'geom' => \DB::raw("ST_GeomFromText('LINESTRING(106.7 10.77, 106.71 10.78)', 4326)"),
        ]);

        $response = $this->actingAs($user, 'sanctum')->deleteJson("/api/cong/{$congId}");

        $response->assertStatus(403); // Forbidden
    }

    public function test_create_cong_requires_coordinates(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/cong', [
            'quan_huyen' => 'Quận 1',
        ]);

        $response->assertStatus(422); // Validation error
        $response->assertJsonValidationErrors(['coordinates']);
    }
}

