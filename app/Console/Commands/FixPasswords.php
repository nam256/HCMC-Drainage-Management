<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class FixPasswords extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:fix-passwords';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Hash lại tất cả passwords đang là plain text trong database';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Đang kiểm tra và hash lại passwords...');
        
        $users = User::all();
        $fixed = 0;
        $skipped = 0;

        foreach ($users as $user) {
            $password = $user->getOriginal('password'); // Lấy raw password từ DB
            
            // Kiểm tra xem password đã được hash bằng bcrypt chưa
            $isBcryptHash = preg_match('/^\$2[ayb]\$\d{2}\$[.\/A-Za-z0-9]{53}$/', $password);
            
            if (!$isBcryptHash) {
                // Lưu password cũ để thông báo
                $oldPassword = $password;
                
                // Hash password
                $hashedPassword = Hash::make($password);
                
                // Update trực tiếp trong DB (bỏ qua cast)
                DB::table('users')
                    ->where('id', $user->id)
                    ->update(['password' => $hashedPassword]);
                
                $fixed++;
                $this->line("✓ Đã hash password cho: {$user->email}");
            } else {
                $skipped++;
            }
        }

        $this->info('');
        $this->info("✓ Đã xử lý: {$fixed} passwords");
        $this->info("  Đã bỏ qua: {$skipped} passwords (đã được hash)");
        
        if ($fixed > 0) {
            $this->warn('');
            $this->warn('⚠ LƯU Ý: Các passwords đã được hash lại.');
            $this->warn('   Nếu bạn không thể đăng nhập, vui lòng sử dụng "Forgot Password" để reset.');
        }

        return Command::SUCCESS;
    }
}

