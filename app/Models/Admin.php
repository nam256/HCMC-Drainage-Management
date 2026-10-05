<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Admin extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'admin_code',
        'full_name',
        'phone',
        'department',
        'position',
        'status',
        'notes',
        'last_login_at',
        'last_login_ip',
    ];

    protected $casts = [
        'last_login_at' => 'datetime',
    ];

    /**
     * Quan hệ với User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Quan hệ với AdminLogs
     */
    public function logs(): HasMany
    {
        return $this->hasMany(AdminLog::class);
    }

    /**
     * Kiểm tra admin có đang active không
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Kiểm tra admin có bị suspended không
     */
    public function isSuspended(): bool
    {
        return $this->status === 'suspended';
    }

    /**
     * Cập nhật thông tin đăng nhập
     */
    public function updateLastLogin(string $ip): void
    {
        $this->update([
            'last_login_at' => now(),
            'last_login_ip' => $ip,
        ]);
    }

    /**
     * Scope: Chỉ lấy admin active
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Tạo admin từ user
     */
    public static function createFromUser(User $user, array $attributes = []): self
    {
        return self::create(array_merge([
            'user_id' => $user->id,
            'admin_code' => 'ADM' . str_pad($user->id, 6, '0', STR_PAD_LEFT),
            'full_name' => $user->name,
            'status' => 'active',
        ], $attributes));
    }
}
