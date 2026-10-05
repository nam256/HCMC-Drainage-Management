<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\CongController;
use App\Http\Controllers\ActivityLogController;
use Illuminate\Support\Facades\Route;

// Trang chủ - công khai
Route::get('/', function () {
    return view('welcome');
});

// Routes yêu cầu đăng nhập
Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard - User và Admin đều có thể xem
    Route::get('/dashboard', function () {
        $user = auth()->user();
        $stats = [];
        
        // Admin thấy thống kê chi tiết
        if ($user->isAdmin()) {
            $stats = [
                'total_cong' => \App\Models\Cong::count(),
                'total_users' => \App\Models\User::count(),
            ];
        }
        
        return view('dashboard', [
            'user' => $user,
            'stats' => $stats,
        ]);
    })->name('dashboard');

    // Bản đồ - User và Admin đều có thể xem
    Route::get('/map', [MapController::class, 'index'])->name('map.index');
    Route::get('/cong', [MapController::class, 'index'])->name('cong.index'); // Alias

    // Quản lý (chỉ Admin)
    Route::middleware('admin')->group(function () {
        Route::get('/cong/manage', [MapController::class, 'manage'])->name('cong.manage');
        Route::get('/admin/logs', [ActivityLogController::class, 'index'])->name('admin.logs');
        Route::get('/admin/logs/{id}', [ActivityLogController::class, 'show'])->name('admin.logs.show');
    });

    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/password', [ProfileController::class, 'updatePassword'])->name('password.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Authentication routes
require __DIR__.'/auth.php';

// API Routes
Route::middleware('api')->prefix('api')->group(function () {
    // API công khai: Mọi người đều có thể xem
    Route::get('/cong', [CongController::class, 'index']);
    
    // API chỉ Admin: Chỉ admin mới được tạo mới, sửa và xóa
    // Dùng auth:web vì frontend dùng CSRF token (web session), không phải Sanctum Bearer token
    Route::middleware(['auth:web', 'admin'])->group(function () {
        Route::post('/cong', [CongController::class, 'store']);
        // Hỗ trợ cả PUT và POST (với _method=PUT) để xử lý FormData
        Route::match(['put', 'post'], '/cong/{id}', [CongController::class, 'update']);
        Route::delete('/cong/{id}', [CongController::class, 'destroy']);
    });
});
