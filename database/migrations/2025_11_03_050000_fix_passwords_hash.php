<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Hash lại tất cả passwords đang là plain text
     */
    public function up(): void
    {
        $users = DB::table('users')->get();
        
        foreach ($users as $user) {
            $password = $user->password;
            
            // Kiểm tra xem password đã được hash bằng bcrypt chưa
            // Bcrypt hash có format: $2y$... hoặc $2a$... hoặc $2b$... và có độ dài 60 ký tự
            $isBcryptHash = preg_match('/^\$2[ayb]\$\d{2}\$[.\/A-Za-z0-9]{53}$/', $password);
            
            if (!$isBcryptHash) {
                // Password là plain text - hash nó lại
                // Lưu ý: Nếu password quá ngắn hoặc không hợp lệ, set password mặc định
                if (strlen($password) < 3) {
                    // Nếu password quá ngắn, set password mặc định là "password"
                    $hashedPassword = Hash::make('password');
                } else {
                    // Hash password hiện tại
                    $hashedPassword = Hash::make($password);
                }
                
                DB::table('users')
                    ->where('id', $user->id)
                    ->update(['password' => $hashedPassword]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Không thể reverse việc hash password
        // Migration này là một chiều
    }
};

