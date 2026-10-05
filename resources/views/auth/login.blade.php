<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />
    
    @if(request('role') === 'admin')
        <div class="mb-4 bg-yellow-50 border border-yellow-200 rounded-md p-4">
            <p class="text-sm text-yellow-800">
                <strong>🔐 Đăng nhập với quyền Quản Trị:</strong> Vui lòng nhập email và mật khẩu của tài khoản Admin.
            </p>
        </div>
    @elseif(request('role') === 'user')
        <div class="mb-4 bg-blue-50 border border-blue-200 rounded-md p-4">
            <p class="text-sm text-blue-800">
                <strong>👤 Đăng nhập với quyền Người Dùng:</strong> Vui lòng nhập email và mật khẩu của bạn.
            </p>
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-between mt-4">
            <div>
                @php
                    $hasAdmin = \App\Models\User::where('role', 'admin')->exists();
                @endphp
                @if (!$hasAdmin)
                    <a class="underline text-sm text-indigo-600 hover:text-indigo-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('register.admin') }}">
                        {{ __('Tạo tài khoản Admin đầu tiên') }}
                    </a>
                @endif
            </div>
            
            <div class="flex items-center gap-3">
                @if (Route::has('password.request'))
                    <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('password.request') }}">
                        {{ __('Forgot your password?') }}
                    </a>
                @endif

                <x-primary-button>
                    {{ __('Log in') }}
                </x-primary-button>
            </div>
        </div>
    </form>
</x-guest-layout>
