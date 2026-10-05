<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CreateOrUpdateUsersSeeder extends Seeder
{
    /**
     * Tạo hoặc cập nhật users với passwords đã hash
     */
    public function run(): void
    {
        $this->command->info('Đang tạo/cập nhật users...');

        // Danh sách users cần tạo/cập nhật
        $users = [
            [
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'password' => 'password', // Sẽ được hash tự động
                'role' => 'admin',
            ],
            [
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => 'password', // Sẽ được hash tự động
                'role' => 'user',
            ],
        ];

        foreach ($users as $userData) {
            $user = User::where('email', $userData['email'])->first();

            if ($user) {
                // User đã tồn tại - cập nhật password
                $user->password = Hash::make($userData['password']);
                $user->name = $userData['name'];
                $user->role = $userData['role'] ?? 'user';
                $user->save();

                $this->command->info("✓ Đã cập nhật: {$userData['email']} (password: {$userData['password']})");
            } else {
                // Tạo user mới
                User::create([
                    'name' => $userData['name'],
                    'email' => $userData['email'],
                    'password' => Hash::make($userData['password']), // Hash password
                    'role' => $userData['role'] ?? 'user',
                    'email_verified_at' => now(),
                ]);

                $this->command->info("✓ Đã tạo mới: {$userData['email']} (password: {$userData['password']})");
            }
        }

        $this->command->info('');
        $this->command->info('✅ Hoàn thành!');
        $this->command->info('');
        $this->command->info('📋 Thông tin đăng nhập:');
        $this->command->info('   Admin: admin@example.com / password');
        $this->command->info('   User:  test@example.com / password');
        $this->command->info('');
        $this->command->warn('⚠ Lưu ý: Sau khi đăng nhập, vui lòng đổi password!');
    }
}

