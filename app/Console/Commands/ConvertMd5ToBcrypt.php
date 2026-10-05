<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class ConvertMd5ToBcrypt extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:convert-md5 {--mapping= : File chứa mapping MD5 -> plain text (JSON format)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Convert passwords từ MD5 sang Bcrypt (nếu có mapping)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('🔄 Convert MD5 passwords sang Bcrypt...');
        $this->info('');

        // Load mapping nếu có
        $mapping = [];
        if ($this->option('mapping')) {
            $mappingFile = $this->option('mapping');
            if (file_exists($mappingFile)) {
                $mapping = json_decode(file_get_contents($mappingFile), true) ?? [];
                $this->info("✓ Đã load " . count($mapping) . " mappings từ file");
            } else {
                $this->error("✗ Không tìm thấy file: {$mappingFile}");
                return Command::FAILURE;
            }
        }

        $users = User::all();
        $converted = 0;
        $notFound = 0;

        foreach ($users as $user) {
            $password = $user->getOriginal('password');
            
            // Kiểm tra xem có phải MD5 không
            if (!preg_match('/^[a-f0-9]{32}$/i', $password)) {
                continue; // Bỏ qua nếu không phải MD5
            }

            // Tìm plain text tương ứng trong mapping
            $md5Lower = strtolower($password);
            $plainText = $mapping[$md5Lower] ?? null;

            if ($plainText) {
                // Convert sang Bcrypt
                $bcryptHash = Hash::make($plainText);
                DB::table('users')
                    ->where('id', $user->id)
                    ->update(['password' => $bcryptHash]);
                
                $converted++;
                $this->info("✓ {$user->email}: Đã convert (MD5 → Bcrypt)");
            } else {
                $notFound++;
                $this->warn("⚠ {$user->email}: Không tìm thấy mapping cho MD5: {$password}");
            }
        }

        $this->info('');
        $this->info("✅ Đã convert: {$converted}");
        $this->info("⚠ Không tìm thấy mapping: {$notFound}");

        if ($notFound > 0) {
            $this->info('');
            $this->info('💡 Tạo file mapping (JSON):');
            $this->info('   {');
            $this->info('     "md5_hash_1": "plain_text_1",');
            $this->info('     "md5_hash_2": "plain_text_2"');
            $this->info('   }');
            $this->info('');
            $this->info('   Sau đó chạy: php artisan users:convert-md5 --mapping=mapping.json');
        }

        return Command::SUCCESS;
    }
}

