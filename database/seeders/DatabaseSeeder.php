<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Tạo/cập nhật users với passwords đã hash
        $this->call([
            CreateOrUpdateUsersSeeder::class,
        ]);

        // Import dữ liệu cống mẫu
        $this->call([
            CongSeeder::class,
        ]);
    }
}
