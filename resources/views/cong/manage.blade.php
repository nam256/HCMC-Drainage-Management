<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-bold text-2xl text-gray-900 leading-tight">
                    {{ __('Quản lý cống') }}
                </h2>
                <p class="text-sm text-gray-600 mt-1">Quản lý toàn bộ dữ liệu cống trong hệ thống</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('map.index') }}" 
                   class="inline-flex items-center px-4 py-2 bg-blue-600 text-blue-50 rounded-lg hover:bg-blue-700 transition shadow-md">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                    </svg>
                    Xem bản đồ
                </a>
                <a href="{{ route('admin.logs') }}" 
                   class="inline-flex items-center px-4 py-2 bg-purple-600 text-blue-50 rounded-lg hover:bg-purple-700 transition shadow-md">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Quản lý hoạt động người dùng
                </a>
                <span class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-semibold bg-gradient-to-r from-red-500 to-red-600 text-blue-50 shadow-md">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0A7 7 0 013 16z" clip-rule="evenodd"/>
                    </svg>
                    Admin
                </span>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <!-- Total Cống -->
                <div class="bg-gradient-to-br from-blue-500 to-blue-600 overflow-hidden shadow-lg rounded-xl transform hover:scale-105 transition duration-200">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <p class="text-blue-100 text-sm font-medium mb-1">Tổng số cống</p>
                                <p class="text-3xl font-bold text-blue-50">{{ number_format($stats['total'] ?? 0) }}</p>
                            </div>
                            <div class="bg-white bg-opacity-20 rounded-lg p-3">
                                <svg class="h-8 w-8 text-blue-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Cống có file -->
                <div class="bg-gradient-to-br from-green-500 to-green-600 overflow-hidden shadow-lg rounded-xl transform hover:scale-105 transition duration-200">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <p class="text-green-100 text-sm font-medium mb-1">Có file đính kèm</p>
                                <p class="text-3xl font-bold text-blue-50">{{ number_format($stats['with_file'] ?? 0) }}</p>
                            </div>
                            <div class="bg-white bg-opacity-20 rounded-lg p-3">
                                <svg class="h-8 w-8 text-blue-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total Users -->
                <div class="bg-gradient-to-br from-purple-500 to-purple-600 overflow-hidden shadow-lg rounded-xl transform hover:scale-105 transition duration-200">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <p class="text-purple-100 text-sm font-medium mb-1">Tổng người dùng</p>
                                <p class="text-3xl font-bold text-blue-50">{{ number_format($stats['total_users'] ?? 0) }}</p>
                            </div>
                            <div class="bg-white bg-opacity-20 rounded-lg p-3">
                                <svg class="h-8 w-8 text-blue-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="bg-gradient-to-br from-orange-500 to-orange-600 overflow-hidden shadow-lg rounded-xl transform hover:scale-105 transition duration-200">
                    <div class="p-6">
                        <div class="flex flex-col items-center justify-center h-full">
                            <p class="text-orange-100 text-sm font-medium mb-3">Thao tác nhanh</p>
                            <button onclick="showAddModal()" 
                                    class="inline-flex items-center px-5 py-2.5 bg-white text-orange-600 rounded-lg hover:bg-orange-50 transition shadow-lg font-bold text-sm">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                Thêm mới
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Search and Filters -->
            <div class="bg-white overflow-hidden shadow-lg rounded-xl mb-6 border border-gray-200">
                <div class="p-4">
                    <form method="GET" action="{{ route('cong.manage') }}" class="flex flex-col lg:flex-row items-end gap-3">
                        <!-- Search Input -->
                        <div class="flex-1 w-full">
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">🔍 Tìm kiếm</label>
                            <div class="relative">
                                <input type="text" 
                                       name="search" 
                                       value="{{ $search }}"
                                       placeholder="Tìm kiếm theo ID, tên cống, địa điểm, lý trình..." 
                                       class="w-full pl-9 pr-3 py-2 text-sm border border-gray-300 rounded-md shadow-sm focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                                <svg class="absolute left-2.5 top-2.5 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                        </div>
                        
                        <!-- Sort By -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Sắp xếp theo</label>
                            <select name="sort_by" class="w-full lg:w-auto min-w-[120px] px-3 py-2 text-sm border border-gray-300 rounded-md shadow-sm focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="ogc_fid" {{ $sortBy == 'ogc_fid' ? 'selected' : '' }}>ID</option>
                                <option value="id" {{ $sortBy == 'id' ? 'selected' : '' }}>Mã số</option>
                                <option value="ten_cong_dap" {{ $sortBy == 'ten_cong_dap' ? 'selected' : '' }}>Tên cống</option>
                                <option value="dia_diem" {{ $sortBy == 'dia_diem' ? 'selected' : '' }}>Địa điểm</option>
                                <option value="created_at" {{ $sortBy == 'created_at' ? 'selected' : '' }}>Ngày tạo</option>
                            </select>
                        </div>
                        
                        <!-- Sort Direction -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Thứ tự</label>
                            <select name="sort_dir" class="w-full lg:w-auto min-w-[120px] px-3 py-2 text-sm border border-gray-300 rounded-md shadow-sm focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="asc" {{ $sortDir == 'asc' ? 'selected' : '' }}>Tăng dần ↑</option>
                                <option value="desc" {{ $sortDir == 'desc' ? 'selected' : '' }}>Giảm dần ↓</option>
                            </select>
                        </div>
                        
                        <!-- Search Button -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5" style="visibility: hidden;">&nbsp;</label>
                            <button type="submit" class="px-5 py-2 bg-gray-500 text-red-600 rounded-md hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 shadow-sm font-medium text-sm transition flex items-center gap-2" style="background-color: #6B7280 !important; color: #DC2626 !important; height: 38px; margin-top: 0;">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: #DC2626 !important;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                                <span style="color: #DC2626 !important; font-weight: bold;">Tìm</span>
                            </button>
                        </div>
                        
                        @if($search)
                            <div>
                                <a href="{{ route('cong.manage') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 shadow-sm font-medium text-sm transition">
                                    Xóa
                                </a>
                            </div>
                        @endif
                    </form>
                </div>
            </div>

            <!-- Actions Bar -->
            <div class="bg-gradient-to-r from-gray-50 to-gray-100 overflow-hidden shadow-md rounded-xl mb-6 border border-gray-200">
                <div class="p-5 flex flex-col md:flex-row items-center justify-between gap-4">
                    <div class="flex flex-wrap items-center gap-3">
                        <a href="{{ route('map.index') }}" class="inline-flex items-center px-5 py-2.5 bg-blue-600 text-blue-50 rounded-lg hover:bg-blue-700 transition shadow-md font-semibold">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                            </svg>
                            Xem bản đồ
                        </a>
                        <a href="{{ route('admin.logs') }}" class="inline-flex items-center px-5 py-2.5 bg-purple-600 text-blue-50 rounded-lg hover:bg-purple-700 transition shadow-md font-semibold">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Quản lý hoạt động người dùng
                        </a>
                        <a href="{{ route('register.admin') }}" class="inline-flex items-center px-5 py-2.5 bg-green-600 text-blue-50 rounded-lg hover:bg-green-700 transition shadow-md font-semibold">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Tạo Admin
                        </a>
                    </div>
                    <div class="px-4 py-2 bg-white rounded-lg shadow-sm border border-gray-200">
                        <span class="text-sm font-semibold text-gray-700">
                            Hiển thị <span class="text-indigo-600">{{ $congList->firstItem() ?? 0 }}</span> - 
                            <span class="text-indigo-600">{{ $congList->lastItem() ?? 0 }}</span> / 
                            <span class="text-indigo-600">{{ $congList->total() }}</span> kết quả
                        </span>
                    </div>
                </div>
            </div>

            <!-- Data Table -->
            <div class="bg-white overflow-hidden shadow-lg rounded-xl border border-gray-200">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gradient-to-r from-gray-800 to-gray-900">
                            <tr>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-blue-50 uppercase tracking-wider">
                                    ID
                                </th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-blue-50 uppercase tracking-wider">
                                    Tên cống
                                </th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-blue-50 uppercase tracking-wider">
                                    Địa điểm
                                </th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-blue-50 uppercase tracking-wider">
                                    Tọa độ
                                </th>
                                <th scope="col" class="px-6 py-4 text-center text-xs font-bold text-blue-50 uppercase tracking-wider">
                                    File
                                </th>
                                <th scope="col" class="px-6 py-4 text-right text-xs font-bold text-blue-50 uppercase tracking-wider">
                                    Thao tác
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($congList as $cong)
                                <tr class="hover:bg-indigo-50 transition-colors duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-indigo-100 text-indigo-800">
                                            {{ $cong->id ?? '-' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-semibold text-gray-900">{{ $cong->ten_cong_dap ?? '-' }}</div>
                                        @if($cong->ly_trinh)
                                            <div class="text-xs text-gray-500 mt-1">📍 {{ $cong->ly_trinh }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        {{ $cong->dia_diem ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        @if($cong->toa_do_x && $cong->toa_do_y)
                                            <div class="font-mono text-gray-700">
                                                <div>X: <span class="text-indigo-600">{{ number_format($cong->toa_do_x, 6) }}</span></div>
                                                <div>Y: <span class="text-indigo-600">{{ number_format($cong->toa_do_y, 6) }}</span></div>
                                            </div>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        @if($cong->file_path)
                                            <a href="{{ asset('storage/' . $cong->file_path) }}" target="_blank" 
                                               class="inline-flex items-center justify-center w-10 h-10 bg-green-100 text-green-700 rounded-lg hover:bg-green-200 transition shadow-sm" 
                                               title="Tải file">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                </svg>
                                            </a>
                                        @else
                                            <span class="inline-flex items-center justify-center w-10 h-10 bg-gray-100 text-gray-400 rounded-lg">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                </svg>
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <div class="flex justify-end gap-2">
                                            <button onclick="editCong({{ $cong->ogc_fid }})" 
                                                    class="inline-flex items-center px-4 py-2 bg-blue-600 text-blue-50 rounded-lg hover:bg-blue-700 transition shadow-md font-semibold text-sm" 
                                                    title="Sửa">
                                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                                Sửa
                                            </button>
                                            <button onclick="deleteCong({{ $cong->ogc_fid }})" 
                                                    class="inline-flex items-center px-4 py-2 bg-red-600 text-blue-50 rounded-lg hover:bg-red-700 transition shadow-md font-semibold text-sm" 
                                                    title="Xóa">
                                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                                Xóa
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                                        </svg>
                                        <p class="mt-2 text-sm">Không tìm thấy dữ liệu cống nào</p>
                                        @if($search)
                                            <p class="text-xs text-gray-400 mt-1">Thử tìm kiếm với từ khóa khác</p>
                                        @endif
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($congList->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $congList->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Modal Form Thêm/Sửa -->
    <div id="congModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-75 overflow-y-auto h-full w-full z-50">
        <div class="relative top-10 mx-auto p-6 border-0 w-11/12 md:w-3/4 lg:w-2/3 xl:w-1/2 shadow-2xl rounded-2xl bg-white max-h-[95vh] overflow-y-auto">
            <div class="mt-2">
                <div class="flex justify-between items-center mb-6 pb-4 border-b-2 border-gray-200">
                    <h3 class="text-2xl font-bold text-gray-900" id="modalTitle">Thêm cống mới</h3>
                    <button onclick="closeModal()" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                
                <form id="congForm" onsubmit="submitCongForm(event)">
                    <input type="hidden" id="cong_id" name="cong_id">
                    
                    <div class="space-y-4">
                        <!-- Tọa độ - Đơn giản, không có bản đồ -->
                        <div class="bg-gradient-to-br from-blue-50 to-indigo-50 p-4 rounded-xl border-2 border-blue-200">
                            <p class="text-sm text-blue-800 mb-3 font-semibold">
                                📍 <strong>Tọa độ:</strong> Nhập tọa độ X (Kinh độ) và Y (Vĩ độ) hoặc lấy từ bản đồ chính
                            </p>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Tọa độ X (Kinh độ) *</label>
                                    <input type="number" step="any" id="edit_toa_do_x" name="toa_do_x" required
                                           placeholder="106.660172"
                                           class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Tọa độ Y (Vĩ độ) *</label>
                                    <input type="number" step="any" id="edit_toa_do_y" name="toa_do_y" required
                                           placeholder="10.762622"
                                           class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition">
                                </div>
                            </div>
                        </div>

                        <!-- Thông tin cơ bản -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Mã số</label>
                                <input type="text" id="edit_id" name="id" maxlength="254"
                                       class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Tên công đập *</label>
                                <input type="text" id="edit_ten_cong_dap" name="ten_cong_dap" maxlength="254" required
                                       class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition">
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Lý trình</label>
                                <input type="text" id="edit_ly_trinh" name="ly_trinh" maxlength="254"
                                       class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Địa điểm</label>
                                <input type="text" id="edit_dia_diem" name="dia_diem" maxlength="254"
                                       class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition">
                            </div>
                        </div>

                        <!-- Cấp công trình, Chiều dài, Đường kính -->
                        <div class="grid grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Cấp công trình</label>
                                <input type="text" id="edit_cap_cong_tri" name="cap_cong_tri" maxlength="254"
                                       placeholder="VD: I, II"
                                       class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Chiều dài (m)</label>
                                <input type="number" step="1" id="edit_chieu_dai" name="chieu_dai"
                                       class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition"
                                       placeholder="VD: 100">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Đường kính (cm)</label>
                                <input type="number" step="1" id="edit_duong_kinh" name="duong_kinh"
                                       class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition"
                                       placeholder="VD: 50">
                            </div>
                        </div>

                        <!-- Hình thức, Bề rộng, Chiều cao -->
                        <div class="grid grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Hình thức</label>
                                <input type="text" id="edit_hinh_thuc" name="hinh_thuc" maxlength="254"
                                       placeholder="VD: Cống hộp, Cống tròn"
                                       class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Bề rộng</label>
                                <input type="number" step="any" id="edit_be_rong" name="be_rong"
                                       class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition"
                                       placeholder="VD: 2.5">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Chiều cao</label>
                                <input type="number" step="any" id="edit_chieu_cao" name="chieu_cao"
                                       class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition"
                                       placeholder="VD: 3.0">
                            </div>
                        </div>

                        <!-- Mục tiêu, Gói thầu, Đơn vị quản lý -->
                        <div class="grid grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Mục tiêu</label>
                                <input type="text" id="edit_muc_tieu_nhi" name="muc_tieu_nhi" maxlength="254"
                                       class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Gói thầu</label>
                                <input type="text" id="edit_goi_thau" name="goi_thau" maxlength="254"
                                       class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Đơn vị quản lý</label>
                                <input type="text" id="edit_don_vi_quan_l" name="don_vi_quan_l" maxlength="254"
                                       class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition">
                            </div>
                        </div>

                        <!-- Upload file đính kèm -->
                        <div class="bg-gray-50 p-4 rounded-xl border-2 border-gray-200">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                📎 File đính kèm (ảnh hoặc PDF, tối đa 5MB)
                            </label>
                            <input type="file" id="edit_file" name="file" accept="image/*,.pdf"
                                   class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition bg-white">
                            <p class="text-xs text-gray-600 mt-2 font-medium">Chấp nhận: JPG, PNG, PDF. Để trống nếu không muốn thay đổi file hiện tại.</p>
                            <div id="edit_filePreview" class="mt-3"></div>
                            <div id="current_file" class="mt-3"></div>
                        </div>

                        <!-- Ghi chú -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Ghi chú</label>
                            <textarea id="edit_ghi_chu" name="ghi_chu" rows="3"
                                      class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition"></textarea>
                        </div>
                    </div>

                    <div class="mt-8 pt-6 border-t-2 border-gray-200 flex justify-end gap-3">
                        <button type="button" onclick="closeModal()" 
                                class="px-6 py-2.5 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 font-semibold shadow-md transition">
                            Hủy
                        </button>
                        <button type="submit" id="submitBtn"
                                class="px-6 py-2.5 bg-indigo-600 text-blue-50 rounded-lg hover:bg-indigo-700 font-semibold shadow-md transition">
                            Lưu
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @vite(['resources/js/app.js'])
    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

        // Tự động mở modal sửa nếu có query parameter edit
        // Tự động mở modal thêm mới nếu có query parameter add với tọa độ
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const editId = urlParams.get('edit');
            const add = urlParams.get('add');
            const x = urlParams.get('x');
            const y = urlParams.get('y');
            
            if (editId) {
                // Xóa query parameter khỏi URL
                window.history.replaceState({}, document.title, window.location.pathname);
                // Mở modal sửa
                editCong(editId);
            } else if (add === '1' && x && y) {
                // Tự động mở modal thêm mới với tọa độ đã setup
                console.log('📍 Nhận tọa độ từ bản đồ:', { x, y });
                
                // Xóa query parameter khỏi URL (clean URL) TRƯỚC
                window.history.replaceState({}, document.title, '/cong/manage');
                
                // Tìm các input field TRƯỚC khi mở modal
                const toaDoXInput = document.getElementById('edit_toa_do_x');
                const toaDoYInput = document.getElementById('edit_toa_do_y');
                
                if (toaDoXInput && toaDoYInput) {
                    // Điền tọa độ vào form TRƯỚC khi mở modal
                    toaDoXInput.value = parseFloat(x).toFixed(6);
                    toaDoYInput.value = parseFloat(y).toFixed(6);
                    console.log('✅ Đã điền tọa độ vào form:', { x: parseFloat(x).toFixed(6), y: parseFloat(y).toFixed(6) });
                    
                    // Mở modal "Thêm cống mới" SAU KHI đã điền tọa độ
                    showAddModal();
                    
                    // Đợi modal mở xong rồi mới set readonly và style
                    setTimeout(() => {
                        // Đặt readonly cho các trường tọa độ (người dùng không thể chỉnh sửa)
                        toaDoXInput.readOnly = true;
                        toaDoYInput.readOnly = true;
                        
                        // Đánh dấu là readonly để có thể khôi phục sau khi reset
                        toaDoXInput.setAttribute('data-readonly', 'true');
                        toaDoYInput.setAttribute('data-readonly', 'true');
                        
                        // Thêm style để hiển thị rõ là đã được điền tự động
                        toaDoXInput.style.backgroundColor = '#f0f9ff';
                        toaDoXInput.style.cursor = 'not-allowed';
                        toaDoYInput.style.backgroundColor = '#f0f9ff';
                        toaDoYInput.style.cursor = 'not-allowed';
                        
                        // Thêm thông báo vào label
                        const xLabel = toaDoXInput.previousElementSibling;
                        const yLabel = toaDoYInput.previousElementSibling;
                        if (xLabel && xLabel.tagName === 'LABEL' && !xLabel.innerHTML.includes('Tự động')) {
                            xLabel.innerHTML += ' <span class="text-xs text-blue-600">(Tự động từ bản đồ)</span>';
                        }
                        if (yLabel && yLabel.tagName === 'LABEL' && !yLabel.innerHTML.includes('Tự động')) {
                            yLabel.innerHTML += ' <span class="text-xs text-blue-600">(Tự động từ bản đồ)</span>';
                        }
                        
                        console.log('✅ Đã đặt readonly và style cho trường tọa độ');
                        
                        // Focus vào trường đầu tiên (mã số hoặc tên cống)
                        const idInput = document.getElementById('edit_id');
                        const tenCongInput = document.getElementById('edit_ten_cong_dap');
                        if (idInput) {
                            idInput.focus();
                        } else if (tenCongInput) {
                            tenCongInput.focus();
                        }
                    }, 100);
                } else {
                    console.error('❌ Không tìm thấy input fields để điền tọa độ');
                    // Vẫn mở modal nếu không tìm thấy input
                    showAddModal();
                }
            }
        });

        function showAddModal() {
            // Lưu tọa độ trước khi reset form (nếu có)
            const toaDoXInput = document.getElementById('edit_toa_do_x');
            const toaDoYInput = document.getElementById('edit_toa_do_y');
            const savedX = toaDoXInput ? toaDoXInput.value : null;
            const savedY = toaDoYInput ? toaDoYInput.value : null;
            
            document.getElementById('congModal').classList.remove('hidden');
            document.getElementById('modalTitle').textContent = 'Thêm cống mới';
            document.getElementById('congForm').reset();
            document.getElementById('cong_id').value = '';
            document.getElementById('edit_filePreview').innerHTML = '';
            
            // Khôi phục tọa độ sau khi reset (nếu có)
            if (savedX && toaDoXInput) {
                toaDoXInput.value = savedX;
                // Set lại readonly nếu đã được set trước đó
                if (toaDoXInput.hasAttribute('data-readonly')) {
                    toaDoXInput.readOnly = true;
                    toaDoXInput.style.backgroundColor = '#f0f9ff';
                    toaDoXInput.style.cursor = 'not-allowed';
                }
            }
            if (savedY && toaDoYInput) {
                toaDoYInput.value = savedY;
                // Set lại readonly nếu đã được set trước đó
                if (toaDoYInput.hasAttribute('data-readonly')) {
                    toaDoYInput.readOnly = true;
                    toaDoYInput.style.backgroundColor = '#f0f9ff';
                    toaDoYInput.style.cursor = 'not-allowed';
                }
            }
            document.getElementById('current_file').innerHTML = '';
            document.getElementById('submitBtn').textContent = 'Thêm cống';
        }

        function closeModal() {
            document.getElementById('congModal').classList.add('hidden');
        }

        function editCong(id) {
            console.log('Đang tải dữ liệu cống với ID:', id);
            // Load dữ liệu cống
            fetch(`/api/cong`)
                .then(r => r.json())
                .then(data => {
                    console.log('Dữ liệu API:', data);
                    // Tìm cống theo ogc_fid hoặc id
                    const cong = data.features.find(f => {
                        const fid = f.properties.ogc_fid;
                        const cid = f.properties.id;
                        return fid == id || fid === String(id) || cid == id || cid === String(id);
                    });
                    
                    if (!cong) {
                        console.error('Không tìm thấy cống với ID:', id);
                        alert('Không tìm thấy cống với ID: ' + id);
                        return;
                    }

                    console.log('Tìm thấy cống:', cong);
                    const props = cong.properties;
                    
                    // Điền form
                    document.getElementById('cong_id').value = id;
                    document.getElementById('edit_toa_do_x').value = props.toa_do_x || '';
                    document.getElementById('edit_toa_do_y').value = props.toa_do_y || '';
                    document.getElementById('edit_id').value = props.id || '';
                    document.getElementById('edit_ten_cong_dap').value = props.ten_cong_dap || '';
                    document.getElementById('edit_ly_trinh').value = props.ly_trinh || '';
                    document.getElementById('edit_dia_diem').value = props.dia_diem || '';
                    document.getElementById('edit_cap_cong_tri').value = props.cap_cong_tri || '';
                    document.getElementById('edit_chieu_dai').value = props.chieu_dai || '';
                    document.getElementById('edit_duong_kinh').value = props.duong_kinh || '';
                    document.getElementById('edit_hinh_thuc').value = props.hinh_thuc || '';
                    document.getElementById('edit_be_rong').value = props.be_rong || '';
                    document.getElementById('edit_chieu_cao').value = props.chieu_cao || '';
                    document.getElementById('edit_muc_tieu_nhi').value = props.muc_tieu_nhi || '';
                    document.getElementById('edit_goi_thau').value = props.goi_thau || '';
                    document.getElementById('edit_don_vi_quan_l').value = props.don_vi_quan_l || '';
                    document.getElementById('edit_ghi_chu').value = props.ghi_chu || '';
                    
                    // Hiển thị file hiện tại
                    const currentFileDiv = document.getElementById('current_file');
                    if (props.file_url) {
                        currentFileDiv.innerHTML = `
                            <div class="p-2 bg-blue-50 rounded border border-blue-200">
                                <p class="text-sm text-blue-700 mb-1"><strong>File hiện tại:</strong></p>
                                <a href="${props.file_url}" target="_blank" class="text-blue-600 hover:underline text-sm">
                                    📎 ${props.file_name || 'Xem file'}
                                </a>
                            </div>
                        `;
                    } else {
                        currentFileDiv.innerHTML = '<p class="text-sm text-gray-500">Chưa có file đính kèm</p>';
                    }
                    
                    document.getElementById('edit_filePreview').innerHTML = '';
                    document.getElementById('modalTitle').textContent = 'Sửa cống';
                    document.getElementById('submitBtn').textContent = 'Cập nhật';
                    document.getElementById('congModal').classList.remove('hidden');
                })
                .catch(err => {
                    console.error('Error:', err);
                    alert('Lỗi khi tải dữ liệu: ' + err.message);
                });
        }

        // Preview file khi chọn
        document.addEventListener('DOMContentLoaded', function() {
            const fileInput = document.getElementById('edit_file');
            if (fileInput) {
                fileInput.addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    const preview = document.getElementById('edit_filePreview');
                    if (file) {
                        if (file.type.startsWith('image/')) {
                            const reader = new FileReader();
                            reader.onload = function(e) {
                                preview.innerHTML = `
                                    <div class="mt-2">
                                        <img src="${e.target.result}" alt="Preview" class="max-w-xs rounded">
                                        <p class="text-sm text-gray-600 mt-1">${file.name} (${(file.size / 1024).toFixed(2)} KB)</p>
                                    </div>
                                `;
                            };
                            reader.readAsDataURL(file);
                        } else {
                            preview.innerHTML = `
                                <div class="mt-2 p-2 bg-gray-100 rounded">
                                    <p class="text-sm text-gray-700">📄 ${file.name}</p>
                                    <p class="text-xs text-gray-500">${(file.size / 1024).toFixed(2)} KB</p>
                                </div>
                            `;
                        }
                    } else {
                        preview.innerHTML = '';
                    }
                });
            }
        });

        // Submit form
        function submitCongForm(e) {
            e.preventDefault();
            
            const form = document.getElementById('congForm');
            const formData = new FormData(form);
            const congId = document.getElementById('cong_id').value;
            const isEdit = congId !== '';
            
            // Đảm bảo tọa độ được thêm vào FormData (ngay cả khi field readonly)
            const toaDoXInput = document.getElementById('edit_toa_do_x');
            const toaDoYInput = document.getElementById('edit_toa_do_y');
            let toaDoX = '';
            let toaDoY = '';
            
            if (toaDoXInput) {
                toaDoX = toaDoXInput.value || toaDoXInput.getAttribute('value') || '';
                // Xóa khoảng trắng và kiểm tra không rỗng
                toaDoX = toaDoX.trim();
            }
            if (toaDoYInput) {
                toaDoY = toaDoYInput.value || toaDoYInput.getAttribute('value') || '';
                // Xóa khoảng trắng và kiểm tra không rỗng
                toaDoY = toaDoY.trim();
            }
            
            // LUÔN thêm tọa độ vào FormData (kể cả khi rỗng, để backend validate)
            if (toaDoX) {
                formData.set('toa_do_x', toaDoX);
            } else {
                // Nếu không có giá trị, thử lấy từ URL parameter
                const urlParams = new URLSearchParams(window.location.search);
                const xFromUrl = urlParams.get('x');
                if (xFromUrl) {
                    formData.set('toa_do_x', xFromUrl);
                    toaDoX = xFromUrl;
                }
            }
            
            if (toaDoY) {
                formData.set('toa_do_y', toaDoY);
            } else {
                // Nếu không có giá trị, thử lấy từ URL parameter
                const urlParams = new URLSearchParams(window.location.search);
                const yFromUrl = urlParams.get('y');
                if (yFromUrl) {
                    formData.set('toa_do_y', yFromUrl);
                    toaDoY = yFromUrl;
                }
            }
            
            // Debug: Log tất cả dữ liệu form trước khi gửi
            console.log('📤 [MANAGE] Dữ liệu form trước khi gửi:');
            console.log('  - congId:', congId);
            console.log('  - isEdit:', isEdit);
            console.log('  - toa_do_x (từ input):', toaDoXInput ? toaDoXInput.value : 'null');
            console.log('  - toa_do_y (từ input):', toaDoYInput ? toaDoYInput.value : 'null');
            console.log('  - toa_do_x (sẽ gửi):', toaDoX);
            console.log('  - toa_do_y (sẽ gửi):', toaDoY);
            console.log('  - FormData entries:');
            for (let [key, value] of formData.entries()) {
                console.log(`    ${key}:`, value);
            }
            
            const submitBtn = document.getElementById('submitBtn');
            const originalText = submitBtn.textContent;
            submitBtn.disabled = true;
            submitBtn.textContent = 'Đang lưu...';

            // Với FormData, dùng POST với _method=PUT để Laravel parse đúng
            const url = isEdit ? `/api/cong/${congId}` : '/api/cong';
            const method = isEdit ? 'POST' : 'POST'; // Dùng POST cho cả edit và add
            if (isEdit) {
                formData.append('_method', 'PUT'); // Thêm _method=PUT cho Laravel
            }

            console.log('📤 [MANAGE] Gửi request:', { url, method, congId, hasMethodField: isEdit });

            fetch(url, {
                method: method,
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    // Không set Content-Type để browser tự set với boundary cho FormData
                },
                credentials: 'same-origin',
                body: formData
            })
            .then(r => {
                console.log('📥 [MANAGE] Response status:', r.status);
                if (!r.ok) {
                    return r.json().then(err => { 
                        console.error('❌ [MANAGE] Error response:', err);
                        throw err; 
                    });
                }
                return r.json();
            })
            .then(data => {
                console.log('📥 [MANAGE] Response data:', data);
                if (data.status === 'success') {
                    // Nếu là thêm mới (không phải sửa), lưu ID vào sessionStorage để hiển thị màu xanh trên bản đồ
                    if (!isEdit && (data.id || data.ogc_fid)) {
                        const newCongIds = JSON.parse(sessionStorage.getItem('newCongIds') || '[]');
                        const congId = data.id || data.ogc_fid;
                        // Chuyển đổi sang string để so sánh chính xác
                        const congIdStr = String(congId);
                        const newCongIdsStr = newCongIds.map(id => String(id));
                        
                        if (!newCongIdsStr.includes(congIdStr)) {
                            newCongIds.push(congId);
                            sessionStorage.setItem('newCongIds', JSON.stringify(newCongIds));
                            console.log('Đã đánh dấu cống mới với ID:', congId);
                        }
                    }
                    
                    if (isEdit) {
                        alert('Đã cập nhật thành công!');
                        closeModal();
                        // Reload ngay để hiển thị dữ liệu mới
                        window.location.reload();
                    } else {
                        // Lấy tọa độ từ form để chuyển về bản đồ
                        const x = formData.get('toa_do_x');
                        const y = formData.get('toa_do_y');
                        
                        console.log('📍 Tọa độ cống mới:', { x, y });
                        
                        // Đóng modal
                        closeModal();
                        
                        // Tự động chuyển về trang bản đồ với tọa độ để zoom đến vị trí mới
                        if (x && y) {
                            const mapUrl = `/map?zoom=${x},${y}`;
                            console.log('🔄 Chuyển về bản đồ với tọa độ:', mapUrl);
                            window.location.href = mapUrl;
                        } else {
                            // Nếu không có tọa độ, chỉ chuyển về bản đồ
                            window.location.href = '/map';
                        }
                    }
                } else {
                    alert('Lỗi: ' + (data.message || JSON.stringify(data)));
                }
            })
            .catch(err => {
                console.error('Error:', err);
                alert('Lỗi: ' + err.message);
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.textContent = originalText;
            });
        }

        function deleteCong(id) {
            if (!confirm('Bạn có chắc chắn muốn xóa cống này?')) {
                return;
            }

            fetch(`/api/cong/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                credentials: 'same-origin'
            })
            .then(r => r.json())
            .then(data => {
                if (data.status === 'success') {
                    alert('Đã xóa thành công!');
                    window.location.reload();
                } else {
                    alert('Lỗi: ' + (data.message || 'Không thể xóa'));
                }
            })
            .catch(err => {
                console.error('Error:', err);
                alert('Lỗi khi xóa: ' + err.message);
            });
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('congModal');
            if (event.target == modal) {
                closeModal();
            }
        }
    </script>
</x-app-layout>
