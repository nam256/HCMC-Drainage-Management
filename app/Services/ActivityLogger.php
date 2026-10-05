<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class ActivityLogger
{
    /**
     * Ghi log cho hành động tạo mới
     */
    public static function logCreate(Model $model, $user = null): void
    {
        $user = $user ?? Auth::user();
        
        ActivityLog::create([
            'user_id' => $user?->id,
            'action' => 'create',
            'model_type' => get_class($model),
            'model_id' => $model->getKey(),
            'description' => self::generateDescription('create', $model),
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }

    /**
     * Ghi log cho hành động cập nhật
     */
    public static function logUpdate(Model $model, array $changes = [], $user = null): void
    {
        $user = $user ?? Auth::user();
        
        ActivityLog::create([
            'user_id' => $user?->id,
            'action' => 'update',
            'model_type' => get_class($model),
            'model_id' => $model->getKey(),
            'description' => self::generateDescription('update', $model),
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'changes' => !empty($changes) ? $changes : null,
        ]);
    }

    /**
     * Ghi log cho hành động xóa
     */
    public static function logDelete(Model $model, $user = null): void
    {
        $user = $user ?? Auth::user();
        
        // Lưu thông tin trước khi xóa
        $modelData = $model->getAttributes();
        
        ActivityLog::create([
            'user_id' => $user?->id,
            'action' => 'delete',
            'model_type' => get_class($model),
            'model_id' => $model->getKey(),
            'description' => self::generateDescription('delete', $model),
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'changes' => ['deleted_data' => $modelData],
        ]);
    }

    /**
     * Ghi log cho hành động xem
     */
    public static function logView(Model $model, $user = null): void
    {
        $user = $user ?? Auth::user();
        
        ActivityLog::create([
            'user_id' => $user?->id,
            'action' => 'view',
            'model_type' => get_class($model),
            'model_id' => $model->getKey(),
            'description' => self::generateDescription('view', $model),
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }

    /**
     * Ghi log cho hành động đăng nhập
     */
    public static function logLogin($user): void
    {
        $userType = $user->isAdmin() ? 'Admin' : 'User';
        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'login',
            'description' => "{$userType} {$user->name} ({$user->email}) đã đăng nhập",
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }

    /**
     * Ghi log cho hành động đăng xuất
     */
    public static function logLogout($user): void
    {
        $userType = $user->isAdmin() ? 'Admin' : 'User';
        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'logout',
            'description' => "{$userType} {$user->name} ({$user->email}) đã đăng xuất",
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }

    /**
     * Tạo mô tả tự động dựa trên model và action
     */
    private static function generateDescription(string $action, Model $model): string
    {
        $modelName = class_basename($model);
        $modelId = $model->getKey();
        
        // Lấy user hiện tại để xác định là Admin hay User
        $user = Auth::user();
        $userType = $user && $user->isAdmin() ? 'Admin' : 'User';
        $userName = $user ? $user->name : 'Hệ thống';
        
        switch ($action) {
            case 'create':
                if ($modelName === 'Cong') {
                    $name = $model->ten_cong_dap ?? $model->id ?? $modelId;
                    return "{$userType} {$userName} đã tạo cống mới: {$name} (ID: {$modelId})";
                }
                return "{$userType} {$userName} đã tạo {$modelName} mới (ID: {$modelId})";
                
            case 'update':
                if ($modelName === 'Cong') {
                    $name = $model->ten_cong_dap ?? $model->id ?? $modelId;
                    return "{$userType} {$userName} đã cập nhật cống: {$name} (ID: {$modelId})";
                }
                return "{$userType} {$userName} đã cập nhật {$modelName} (ID: {$modelId})";
                
            case 'delete':
                if ($modelName === 'Cong') {
                    $name = $model->ten_cong_dap ?? $model->id ?? $modelId;
                    return "{$userType} {$userName} đã xóa cống: {$name} (ID: {$modelId})";
                }
                return "{$userType} {$userName} đã xóa {$modelName} (ID: {$modelId})";
                
            case 'view':
                if ($modelName === 'Cong') {
                    $name = $model->ten_cong_dap ?? $model->id ?? $modelId;
                    return "{$userType} {$userName} đã xem chi tiết cống: {$name} (ID: {$modelId})";
                }
                return "{$userType} {$userName} đã xem chi tiết {$modelName} (ID: {$modelId})";
                
            default:
                return "{$userType} {$userName} thực hiện hành động {$action} trên {$modelName} (ID: {$modelId})";
        }
    }
}

