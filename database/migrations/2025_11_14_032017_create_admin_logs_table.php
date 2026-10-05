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
        Schema::create('admin_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_id')->nullable()->constrained('admins')->onDelete('set null')->comment('ID admin thực hiện');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null')->comment('ID user liên quan (nếu có)');
            $table->string('action', 50)->comment('Hành động: create, update, delete, view, login, logout, etc.');
            $table->string('model_type')->nullable()->comment('Loại model: Cong, User, etc.');
            $table->unsignedBigInteger('model_id')->nullable()->comment('ID của model liên quan');
            $table->string('description')->nullable()->comment('Mô tả ngắn');
            $table->text('details')->nullable()->comment('Chi tiết (JSON)');
            $table->json('old_values')->nullable()->comment('Giá trị cũ (khi update/delete)');
            $table->json('new_values')->nullable()->comment('Giá trị mới (khi create/update)');
            $table->string('ip_address', 45)->nullable()->comment('IP address');
            $table->text('user_agent')->nullable()->comment('User agent');
            $table->string('url')->nullable()->comment('URL thực hiện');
            $table->string('method', 10)->nullable()->comment('HTTP method');
            $table->timestamps();
            
            // Indexes
            $table->index('admin_id');
            $table->index('user_id');
            $table->index('action');
            $table->index(['model_type', 'model_id']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_logs');
    }
};
