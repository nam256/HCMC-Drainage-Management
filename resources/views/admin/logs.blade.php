<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-bold text-2xl text-gray-900 leading-tight">
                    {{ __('Lịch sử hoạt động') }}
                </h2>
                <p class="text-sm text-gray-600 mt-1">Xem tất cả hoạt động của người dùng trong hệ thống</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('cong.manage') }}" 
                   class="inline-flex items-center px-4 py-2 bg-blue-600 text-blue-50 rounded-lg hover:bg-blue-700 transition shadow-md">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                    </svg>
                    Quản lý cống
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Filters -->
            <div class="bg-white overflow-hidden shadow-lg rounded-xl mb-6 border border-gray-200">
                <div class="p-4">
                    <form method="GET" action="{{ route('admin.logs') }}" class="flex flex-col lg:flex-row items-end gap-3">
                        <!-- Search -->
                        <div class="flex-1 w-full">
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">🔍 Tìm kiếm</label>
                            <input type="text" 
                                   name="search" 
                                   value="{{ $search }}"
                                   placeholder="Tìm kiếm theo mô tả..." 
                                   class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md shadow-sm focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        
                        <!-- User Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Người dùng</label>
                            <select name="user_id" class="w-full lg:w-auto min-w-[150px] px-3 py-2 text-sm border border-gray-300 rounded-md shadow-sm focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">Tất cả</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ $selectedUserId == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <!-- Action Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Hành động</label>
                            <select name="action" class="w-full lg:w-auto min-w-[120px] px-3 py-2 text-sm border border-gray-300 rounded-md shadow-sm focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">Tất cả</option>
                                @foreach($actions as $action)
                                    <option value="{{ $action }}" {{ $selectedAction == $action ? 'selected' : '' }}>
                                        {{ ucfirst($action) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <!-- Date From -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Từ ngày</label>
                            <input type="date" 
                                   name="date_from" 
                                   value="{{ $dateFrom }}"
                                   class="w-full lg:w-auto min-w-[150px] px-3 py-2 text-sm border border-gray-300 rounded-md shadow-sm focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        
                        <!-- Date To -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Đến ngày</label>
                            <input type="date" 
                                   name="date_to" 
                                   value="{{ $dateTo }}"
                                   class="w-full lg:w-auto min-w-[150px] px-3 py-2 text-sm border border-gray-300 rounded-md shadow-sm focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
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
                        
                        @if($search || $selectedUserId || $selectedAction || $dateFrom || $dateTo)
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5" style="visibility: hidden;">&nbsp;</label>
                                <a href="{{ route('admin.logs') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 shadow-sm font-medium text-sm transition" style="height: 38px; display: inline-flex; align-items: center;">
                                    Xóa
                                </a>
                            </div>
                        @endif
                    </form>
                </div>
            </div>

            <!-- Logs Table -->
            <div class="bg-white overflow-hidden shadow-lg rounded-xl border border-gray-200">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gradient-to-r from-gray-800 to-gray-900">
                            <tr>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-blue-50 uppercase tracking-wider">
                                    Thời gian
                                </th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-blue-50 uppercase tracking-wider">
                                    Người dùng
                                </th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-blue-50 uppercase tracking-wider">
                                    Hành động
                                </th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-blue-50 uppercase tracking-wider">
                                    Mô tả
                                </th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-blue-50 uppercase tracking-wider">
                                    IP Address
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($logs as $log)
                                <tr class="hover:bg-indigo-50 transition-colors duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        {{ $log->created_at->format('d/m/Y H:i:s') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        @if($log->user)
                                            <span class="font-semibold text-gray-900">{{ $log->user->name }}</span>
                                            <div class="text-xs text-gray-500">{{ $log->user->email }}</div>
                                        @else
                                            <span class="text-gray-400">N/A</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $actionColors = [
                                                'create' => 'bg-green-100 text-green-800',
                                                'update' => 'bg-blue-100 text-blue-800',
                                                'delete' => 'bg-red-100 text-red-800',
                                                'view' => 'bg-gray-100 text-gray-800',
                                                'login' => 'bg-purple-100 text-purple-800',
                                                'logout' => 'bg-orange-100 text-orange-800',
                                            ];
                                            $color = $actionColors[$log->action] ?? 'bg-gray-100 text-gray-800';
                                        @endphp
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold {{ $color }}">
                                            {{ ucfirst($log->action) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-700">
                                        {{ $log->description }}
                                        @if($log->model_type)
                                            <div class="text-xs text-gray-500 mt-1">
                                                {{ class_basename($log->model_type) }} #{{ $log->model_id }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        {{ $log->ip_address ?? '-' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        <p class="mt-2 text-sm">Không tìm thấy log nào</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($logs->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $logs->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>

