<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // Ghi log đăng nhập
        try {
            ActivityLogger::logLogin(Auth::user());
        } catch (\Exception $e) {
            \Log::warning('Không thể ghi log đăng nhập:', ['error' => $e->getMessage()]);
        }

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        // Lưu user trước khi logout để ghi log
        $user = Auth::user();

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        // Ghi log đăng xuất
        if ($user) {
            try {
                ActivityLogger::logLogout($user);
            } catch (\Exception $e) {
                \Log::warning('Không thể ghi log đăng xuất:', ['error' => $e->getMessage()]);
            }
        }

        return redirect('/');
    }
}
