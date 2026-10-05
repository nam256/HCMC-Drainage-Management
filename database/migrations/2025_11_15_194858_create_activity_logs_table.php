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
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('action', 50); // create, update, delete, view, login, logout
            $table->string('model_type')->nullable(); // App\Models\Cong, App\Models\User, etc.
            $table->unsignedBigInteger('model_id')->nullable(); // ID của model
            $table->text('description')->nullable(); // Mô tả chi tiết
            $table->string('ip_address', 45)->nullable(); // IPv4 hoặc IPv6
            $table->text('user_agent')->nullable(); // Browser info
            $table->json('changes')->nullable(); // Lưu thay đổi (old values, new values)
            $table->timestamp('created_at')->useCurrent();
            
            // Indexes
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
        Schema::dropIfExists('activity_logs');
    }
};
