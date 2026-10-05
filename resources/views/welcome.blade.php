<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>WebGIS - Hệ Thống Quản Lý Cống Thoát Nước</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <a href="/" class="flex items-center space-x-2">
                        <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                        </svg>
                        <span class="text-xl font-semibold text-gray-900">WebGIS</span>
                    </a>
                </div>
                
                <div class="flex items-center space-x-4">
                    @guest
                        <a href="{{ route('login') }}" class="text-gray-700 hover:text-indigo-600 px-4 py-2 rounded-md text-sm font-medium transition">
                            Đăng nhập
                        </a>
                        <a href="{{ route('register') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md text-sm font-medium transition">
                            Đăng ký
                        </a>
                    @else
                        <a href="{{ route('dashboard') }}" class="text-gray-700 hover:text-indigo-600 px-4 py-2 rounded-md text-sm font-medium transition">
                            Dashboard
                        </a>
                        <a href="{{ route('map.index') }}" class="text-gray-700 hover:text-indigo-600 px-4 py-2 rounded-md text-sm font-medium transition">
                            Bản đồ
                        </a>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="text-gray-700 hover:text-red-600 px-4 py-2 rounded-md text-sm font-medium transition">
                                Đăng xuất
                            </button>
                        </form>
                    @endguest
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="bg-indigo-600 text-white py-16 md:py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h1 class="text-4xl md:text-5xl font-bold mb-4">
                    Hệ Thống WebGIS
                </h1>
                <p class="text-xl md:text-2xl mb-2 text-indigo-100">
                    Quản Lý Cống Thoát Nước
                </p>
                <p class="text-lg text-indigo-100 mb-12 max-w-2xl mx-auto mt-4">
                    Quản lý, hiển thị và phân tích dữ liệu cống thoát nước trên bản đồ số
                </p>
                
                @guest
                    <div class="max-w-4xl mx-auto">
                        <h2 class="text-2xl font-semibold mb-8">
                            Bạn muốn đăng nhập với vai trò nào?
                        </h2>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                            <!-- Admin Login Card -->
                            <div class="bg-white text-gray-900 rounded-lg p-8 shadow-lg hover:shadow-xl transition cursor-pointer" onclick="window.location.href='{{ route('login') }}?role=admin'">
                                <div class="text-center">
                                    <div class="w-16 h-16 bg-yellow-500 rounded-lg flex items-center justify-center mx-auto mb-4">
                                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                        </svg>
                                    </div>
                                    <h3 class="text-xl font-bold mb-2">Người Quản Trị</h3>
                                    <p class="text-gray-600 mb-4 text-sm">
                                        Đăng nhập với quyền quản lý hệ thống
                                    </p>
                                    <button class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-md font-medium transition">
                                        Đăng nhập Admin
                                    </button>
                                </div>
                            </div>
                            
                            <!-- User Login Card -->
                            <div class="bg-white text-gray-900 rounded-lg p-8 shadow-lg hover:shadow-xl transition cursor-pointer" onclick="window.location.href='{{ route('login') }}?role=user'">
                                <div class="text-center">
                                    <div class="w-16 h-16 bg-blue-500 rounded-lg flex items-center justify-center mx-auto mb-4">
                                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                    </div>
                                    <h3 class="text-xl font-bold mb-2">Người Dùng</h3>
                                    <p class="text-gray-600 mb-4 text-sm">
                                        Đăng nhập để xem và sử dụng hệ thống
                                    </p>
                                    <button class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-md font-medium transition">
                                        Đăng nhập User
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="text-center">
                            <p class="text-indigo-100 mb-2">Chưa có tài khoản?</p>
                            <a href="{{ route('register') }}" class="text-white underline hover:text-yellow-200">
                                Đăng ký tài khoản mới
                            </a>
                        </div>
                    </div>
                @else
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <a href="{{ route('dashboard') }}" class="bg-white text-indigo-600 px-8 py-3 rounded-lg text-lg font-semibold hover:bg-gray-100 transition shadow-lg">
                            Vào Dashboard
                        </a>
                        <a href="{{ route('map.index') }}" class="bg-indigo-700 text-white px-8 py-3 rounded-lg text-lg font-semibold hover:bg-indigo-800 transition border-2 border-white">
                            Xem Bản đồ
                        </a>
                    </div>
                @endguest
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-3">
                    Tính Năng Nổi Bật
                </h2>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                    Hệ thống được thiết kế để quản lý dữ liệu cống một cách hiệu quả
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Feature 1 -->
                <div class="bg-white border border-gray-200 rounded-lg p-6 hover:shadow-md transition">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Bản Đồ Tương Tác</h3>
                    <p class="text-gray-600 text-sm leading-relaxed">
                        Xem và tương tác với dữ liệu cống trên bản đồ số. Zoom, pan và click để xem thông tin chi tiết.
                    </p>
                </div>

                <!-- Feature 2 -->
                <div class="bg-white border border-gray-200 rounded-lg p-6 hover:shadow-md transition">
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Quản Lý Dữ Liệu</h3>
                    <p class="text-gray-600 text-sm leading-relaxed">
                        Thêm, sửa, xóa thông tin cống dễ dàng. Upload file đính kèm cho mỗi tuyến cống.
                    </p>
                </div>

                <!-- Feature 3 -->
                <div class="bg-white border border-gray-200 rounded-lg p-6 hover:shadow-md transition">
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Phân Quyền An Toàn</h3>
                    <p class="text-gray-600 text-sm leading-relaxed">
                        Hệ thống phân quyền rõ ràng: Người dùng xem và thêm mới, Quản lý có đầy đủ quyền.
                    </p>
                </div>

                <!-- Feature 4 -->
                <div class="bg-white border border-gray-200 rounded-lg p-6 hover:shadow-md transition">
                    <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Upload File</h3>
                    <p class="text-gray-600 text-sm leading-relaxed">
                        Đính kèm ảnh, PDF cho mỗi tuyến cống. Lưu trữ an toàn và truy cập dễ dàng.
                    </p>
                </div>

                <!-- Feature 5 -->
                <div class="bg-white border border-gray-200 rounded-lg p-6 hover:shadow-md transition">
                    <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Dữ Liệu Chính Xác</h3>
                    <p class="text-gray-600 text-sm leading-relaxed">
                        Sử dụng PostGIS để lưu trữ và truy vấn dữ liệu không gian chính xác. Hỗ trợ định dạng GeoJSON.
                    </p>
                </div>

                <!-- Feature 6 -->
                <div class="bg-white border border-gray-200 rounded-lg p-6 hover:shadow-md transition">
                    <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Hiệu Suất Cao</h3>
                    <p class="text-gray-600 text-sm leading-relaxed">
                        Tối ưu hiệu suất với Laravel và Vite. Load nhanh, trải nghiệm mượt mà trên mọi thiết bị.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section class="py-16 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-3">
                    Cách Sử Dụng
                </h2>
                <p class="text-lg text-gray-600">
                    Chỉ 3 bước đơn giản để bắt đầu
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="text-center">
                    <div class="w-16 h-16 bg-indigo-600 text-white rounded-full flex items-center justify-center text-2xl font-bold mx-auto mb-4">
                        1
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Đăng Ký Tài Khoản</h3>
                    <p class="text-gray-600 text-sm">
                        Tạo tài khoản miễn phí trong vài giây. Chỉ cần email và mật khẩu.
                    </p>
                </div>

                <div class="text-center">
                    <div class="w-16 h-16 bg-indigo-600 text-white rounded-full flex items-center justify-center text-2xl font-bold mx-auto mb-4">
                        2
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Đăng Nhập</h3>
                    <p class="text-gray-600 text-sm">
                        Đăng nhập với email và mật khẩu. Hệ thống sẽ kiểm tra và cho phép truy cập.
                    </p>
                </div>

                <div class="text-center">
                    <div class="w-16 h-16 bg-indigo-600 text-white rounded-full flex items-center justify-center text-2xl font-bold mx-auto mb-4">
                        3
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Sử Dụng Hệ Thống</h3>
                    <p class="text-gray-600 text-sm">
                        Xem bản đồ, thêm dữ liệu mới hoặc quản lý (nếu là admin). Đơn giản và dễ dàng!
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8">
                <div>
                    <h3 class="text-lg font-semibold mb-3">WebGIS</h3>
                    <p class="text-gray-400 text-sm">
                        Hệ thống quản lý và hiển thị dữ liệu cống thoát nước trên bản đồ số.
                    </p>
                </div>
                
                <div>
                    <h3 class="text-lg font-semibold mb-3">Liên Kết Nhanh</h3>
                    <ul class="space-y-2 text-gray-400 text-sm">
                        @guest
                            <li><a href="{{ route('login') }}" class="hover:text-white transition">Đăng nhập</a></li>
                            <li><a href="{{ route('register') }}" class="hover:text-white transition">Đăng ký</a></li>
                        @else
                            <li><a href="{{ route('dashboard') }}" class="hover:text-white transition">Dashboard</a></li>
                            <li><a href="{{ route('map.index') }}" class="hover:text-white transition">Bản đồ</a></li>
                            <li><a href="{{ route('profile.edit') }}" class="hover:text-white transition">Cài đặt</a></li>
                        @endguest
                    </ul>
                </div>
                
                <div>
                    <h3 class="text-lg font-semibold mb-3">Công Nghệ</h3>
                    <ul class="space-y-2 text-gray-400 text-sm">
                        <li>Laravel Framework</li>
                        <li>Leaflet.js</li>
                        <li>PostgreSQL + PostGIS</li>
                        <li>Tailwind CSS</li>
                    </ul>
                </div>
            </div>
            
            <div class="border-t border-gray-800 pt-6 text-center">
                <p class="text-gray-400 text-sm">&copy; {{ date('Y') }} WebGIS. Tất cả quyền được bảo lưu.</p>
            </div>
        </div>
    </footer>
</body>
</html>
