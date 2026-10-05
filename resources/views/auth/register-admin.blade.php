<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tạo tài khoản mới') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-4">
                        @if(auth()->check() && auth()->user()->isAdmin())
                            <div class="bg-blue-50 border border-blue-200 rounded-md p-4">
                                <p class="text-sm text-blue-800">
                                    <strong>Lưu ý:</strong> Bạn đang tạo tài khoản mới với quyền Admin. 
                                    Tài khoản sẽ được lưu vào database và có thể đăng nhập ngay.
                                </p>
                            </div>
                        @else
                            <div class="bg-yellow-50 border border-yellow-200 rounded-md p-4">
                                <p class="text-sm text-yellow-800">
                                    <strong>⚠️ Tạo tài khoản Admin đầu tiên:</strong> 
                                    Nếu đây là tài khoản admin đầu tiên trong hệ thống, bạn sẽ được tự động đăng nhập sau khi tạo.
                                    Tài khoản sẽ được lưu vào database và có thể sử dụng ngay.
                                </p>
                            </div>
                        @endif
                    </div>

                    <form method="POST" action="{{ route('register.admin') }}">
                        @csrf

                        <!-- Name -->
                        <div>
                            <x-input-label for="name" :value="__('Họ và tên')" />
                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <!-- Email Address -->
                        <div class="mt-4">
                            <x-input-label for="email" :value="__('Email')" />
                            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <!-- Password -->
                        <div class="mt-4">
                            <x-input-label for="password" :value="__('Mật khẩu')" />
                            <x-text-input id="password" class="block mt-1 w-full"
                                            type="password"
                                            name="password"
                                            required autocomplete="new-password" />
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>

                        <!-- Confirm Password -->
                        <div class="mt-4">
                            <x-input-label for="password_confirmation" :value="__('Xác nhận mật khẩu')" />
                            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                                            type="password"
                                            name="password_confirmation" required autocomplete="new-password" />
                            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                        </div>

                        <!-- Role -->
                        <div class="mt-4">
                            <x-input-label for="role" :value="__('Vai trò')" />
                            <select id="role" name="role" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="user" {{ old('role') == 'user' ? 'selected' : '' }}>Người dùng (User)</option>
                                <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Quản lý (Admin)</option>
                            </select>
                            <x-input-error :messages="$errors->get('role')" class="mt-2" />
                            <p class="mt-1 text-sm text-gray-500">
                                Chọn vai trò cho tài khoản mới
                            </p>
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('cong.manage') }}">
                                {{ __('Hủy') }}
                            </a>

                            <x-primary-button class="ms-4">
                                {{ __('Tạo tài khoản') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

