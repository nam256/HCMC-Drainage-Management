<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('admins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->onDelete('cascade');
            $table->string('admin_code', 50)->unique()->nullable()->comment('Mã admin riêng');
            $table->string('full_name')->nullable()->comment('Họ và tên đầy đủ');
            $table->string('phone', 20)->nullable()->comment('Số điện thoại');
            $table->string('department')->nullable()->comment('Phòng ban');
            $table->string('position')->nullable()->comment('Chức vụ');
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active')->comment('Trạng thái');
            $table->text('notes')->nullable()->comment('Ghi chú');
            $table->timestamp('last_login_at')->nullable()->comment('Lần đăng nhập cuối');
            $table->string('last_login_ip', 45)->nullable()->comment('IP đăng nhập cuối');
            $table->timestamps();
            
            // Indexes
            $table->index('admin_code');
            $table->index('status');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admins');
    }
};
