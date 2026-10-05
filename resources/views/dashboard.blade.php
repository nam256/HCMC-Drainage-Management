<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Dashboard') }}
            </h2>
            @if(auth()->user()->isAdmin())
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0A7 7 0 013 16z" clip-rule="evenodd"/>
                    </svg>
                    Admin
                </span>
            @else
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0A7 7 0 013 16z" clip-rule="evenodd"/>
                    </svg>
                    User
                </span>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Welcome Message -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-2">
                        Chào mừng, {{ auth()->user()->name }}!
                    </h3>
                    <p class="text-gray-600">
                        @if(auth()->user()->isAdmin())
                            Bạn đang đăng nhập với quyền <strong>Quản trị viên</strong>. Bạn có thể xem, thêm, sửa và xóa dữ liệu cống.
                        @else
                            Bạn đang đăng nhập với quyền <strong>Người dùng</strong>. Bạn có thể xem và thêm dữ liệu cống mới.
                        @endif
                    </p>
                </div>
            </div>

            <!-- Statistics Cards (Chỉ Admin) -->
            @if(auth()->user()->isAdmin() && isset($stats))
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <!-- Total Cống -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 bg-blue-500 rounded-md p-3">
                                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                                    </svg>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">Tổng số cống</dt>
                                        <dd class="text-lg font-semibold text-gray-900">{{ number_format($stats['total_cong'] ?? 0) }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Total Users -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                    </svg>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">Tổng số người dùng</dt>
                                        <dd class="text-lg font-semibold text-gray-900">{{ number_format($stats['total_users'] ?? 0) }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Quick Actions -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Thao tác nhanh</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Xem bản đồ -->
                        <a href="{{ route('map.index') }}" class="flex items-center p-4 bg-blue-50 hover:bg-blue-100 rounded-lg transition">
                            <svg class="w-8 h-8 text-blue-600 mr-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                            </svg>
                            <div>
                                <div class="font-semibold text-gray-900">Xem bản đồ</div>
                                <div class="text-sm text-gray-600">Xem dữ liệu cống trên bản đồ</div>
                            </div>
                        </a>

                        @if(auth()->user()->isAdmin())
                            <!-- Quản lý cống (Chỉ Admin) -->
                            <a href="{{ route('cong.manage') }}" class="flex items-center p-4 bg-green-50 hover:bg-green-100 rounded-lg transition">
                                <svg class="w-8 h-8 text-green-600 mr-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                                </svg>
                                <div>
                                    <div class="font-semibold text-gray-900">Quản lý cống</div>
                                    <div class="text-sm text-gray-600">Xem, sửa, xóa dữ liệu cống</div>
                                </div>
                            </a>
                        @endif

                        <!-- Profile -->
                        <a href="{{ route('profile.edit') }}" class="flex items-center p-4 bg-purple-50 hover:bg-purple-100 rounded-lg transition">
                            <svg class="w-8 h-8 text-purple-600 mr-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            <div>
                                <div class="font-semibold text-gray-900">Cài đặt tài khoản</div>
                                <div class="text-sm text-gray-600">Quản lý thông tin cá nhân</div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
