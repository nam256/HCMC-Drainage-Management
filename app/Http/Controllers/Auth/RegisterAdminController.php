<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Admin;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisterAdminController extends Controller
{
    /**
     * Create a new controller instance.
     * Cho phép tạo admin đầu tiên nếu chưa có admin nào
     * Nếu đã có admin thì yêu cầu đăng nhập admin
     */
    public function __construct()
    {
        // Không áp dụng middleware ở đây, sẽ kiểm tra trong method
    }

    /**
     * Display the admin registration view.
     */
    public function create(): View
    {
        // Kiểm tra nếu đã có admin thì yêu cầu đăng nhập
        $hasAdmin = User::where('role', 'admin')->exists();
        
        if ($hasAdmin && !auth()->check()) {
            return redirect()->route('login')
                ->with('error', 'Vui lòng đăng nhập với tài khoản admin để tạo tài khoản mới.');
        }
        
        if ($hasAdmin && auth()->check() && !auth()->user()->isAdmin()) {
            abort(403, 'Chỉ admin mới có thể tạo tài khoản mới.');
        }
        
        return view('auth.register-admin');
    }

    /**
     * Handle an incoming admin registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // Kiểm tra nếu đã có admin thì yêu cầu đăng nhập
        $hasAdmin = User::where('role', 'admin')->exists();
        
        if ($hasAdmin && !auth()->check()) {
            return redirect()->route('login')
                ->with('error', 'Vui lòng đăng nhập với tài khoản admin để tạo tài khoản mới.');
        }
        
        if ($hasAdmin && auth()->check() && !auth()->user()->isAdmin()) {
            abort(403, 'Chỉ admin mới có thể tạo tài khoản mới.');
        }
        
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'in:admin,user'], // Chỉ admin hoặc user
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'email_verified_at' => now(), // Tự động verify email
        ]);

        // Nếu là admin, tạo record trong bảng admins
        if ($request->role === 'admin') {
            Admin::createFromUser($user, [
                'full_name' => $request->name,
                'status' => 'active',
            ]);
        }

        event(new Registered($user));

        // Nếu chưa đăng nhập và tạo admin đầu tiên, tự động đăng nhập
        if (!Auth::check() && $request->role === 'admin' && !User::where('role', 'admin')->where('id', '!=', $user->id)->exists()) {
            Auth::login($user);
            return redirect()->route('dashboard')
                ->with('success', "Đã tạo tài khoản admin đầu tiên và tự động đăng nhập!");
        }

        // Nếu đã đăng nhập (admin tạo tài khoản khác)
        if (Auth::check()) {
            return redirect()->route('cong.manage')
                ->with('success', "Đã tạo tài khoản {$request->role} mới: {$request->email}");
        }

        // Nếu tạo user thường (chưa đăng nhập)
        return redirect()->route('login')
            ->with('success', "Đã tạo tài khoản thành công! Vui lòng đăng nhập.");
    }
}

