<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Bản đồ cống') }}
            </h2>
            <div class="flex items-center space-x-4">
                @if($canEdit ?? false)
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0A7 7 0 013 16z" clip-rule="evenodd"/>
                        </svg>
                        Chế độ Admin - Có thể chỉnh sửa
                    </span>
                @else
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0A7 7 0 013 16z" clip-rule="evenodd"/>
                        </svg>
                        Chế độ xem
                    </span>
                @endif
            </div>
        </div>
    </x-slot>

    <style>
        @keyframes pulse {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: 0.7;
            }
        }
    </style>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Info Banner -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-blue-700">
                            @if($canEdit ?? false)
                                <strong>Quyền Admin:</strong> Bạn có thể thêm, sửa và xóa dữ liệu cống trên bản đồ. Click vào đường cống để xem thông tin chi tiết.
                            @else
                                <strong>Quyền xem:</strong> Bạn có thể xem dữ liệu cống trên bản đồ. Click vào đường cống để xem thông tin chi tiết.
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <!-- Thanh công cụ ngang phía trên bản đồ (Map Control Style) -->
            <div class="bg-white border-b border-gray-300 mb-0" style="height: 50px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                <div class="flex items-center h-full gap-1 px-2">
                    <!-- Nút Tìm kiếm & Bộ lọc -->
                    <button onclick="toggleFilterPanel()" id="search-filter-btn" 
                            class="h-9 px-4 bg-white hover:bg-gray-50 border border-gray-300 rounded text-sm font-medium text-gray-700 flex items-center gap-2 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                        </svg>
                        <span>Tìm kiếm & Bộ lọc</span>
                        <svg id="filter-arrow" class="w-3 h-3 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    
                    @if($canEdit ?? false)
                    <!-- Nút Thêm cống -->
                    <button onclick="showAddCongModal()" 
                            class="h-9 px-4 bg-white hover:bg-gray-50 border border-gray-300 rounded text-sm font-medium text-gray-700 flex items-center gap-2 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        <span>Thêm cống</span>
                    </button>
                    
                    <!-- Nút Bật vẽ điểm -->
                    <button id="toggleDrawMode" onclick="toggleDrawMode()" 
                            class="h-9 px-4 bg-white hover:bg-gray-50 border border-gray-300 rounded text-sm font-medium text-gray-700 flex items-center gap-2 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                        </svg>
                        <span id="drawModeText">Bật vẽ điểm</span>
                    </button>
                    @endif
                    
                    <!-- Nút Làm mới -->
                    <button onclick="reloadMapData()" 
                            class="h-9 px-4 bg-white hover:bg-gray-50 border border-gray-300 rounded text-sm font-medium text-gray-700 flex items-center gap-2 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        <span>Làm mới</span>
                    </button>
                </div>
            </div>
            
            <!-- Panel Tìm kiếm & Bộ lọc (trượt xuống) -->
            <div id="filter-panel" class="hidden bg-white border-b border-gray-300 mb-0" style="max-height: 180px; overflow-y: auto; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                <div class="p-3 grid grid-cols-1 md:grid-cols-4 gap-3">
                    <!-- Ô tìm kiếm -->
                    <div class="md:col-span-1">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Tìm kiếm</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-2 flex items-center pointer-events-none">
                                <svg class="h-3.5 w-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                            <input 
                                type="text" 
                                id="search-input" 
                                placeholder="Tên, mã số, địa điểm..."
                                class="block w-full pl-8 pr-2 py-1.5 text-sm border border-gray-300 rounded focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                            />
                            <button 
                                id="search-clear-btn" 
                                onclick="clearSearch()" 
                                class="absolute inset-y-0 right-0 pr-2 flex items-center hidden"
                                title="Xóa"
                            >
                                <svg class="h-3.5 w-3.5 text-gray-400 hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                            <div id="search-suggestions" class="hidden absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded shadow-lg max-h-48 overflow-y-auto top-full"></div>
                        </div>
                    </div>
                    
                    <!-- Dropdown Khu vực -->
                    <div class="md:col-span-1">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Khu vực</label>
                        <select id="filter-area" class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Tất cả khu vực</option>
                        </select>
                    </div>
                    
                    <!-- Dropdown Loại công trình -->
                    <div class="md:col-span-1">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Loại công trình</label>
                        <select id="filter-type" class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Tất cả loại</option>
                        </select>
                    </div>
                    
                    <!-- Dropdown Cấp công trình -->
                    <div class="md:col-span-1">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Cấp công trình</label>
                        <select id="filter-level" class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Tất cả cấp</option>
                        </select>
                    </div>
                </div>
                
                <!-- Loading indicator -->
                <div id="search-loading" class="hidden px-3 py-2 text-center bg-blue-50 border-t border-blue-100">
                    <div class="inline-flex items-center gap-2">
                        <div class="animate-spin rounded-full h-3 w-3 border-2 border-blue-600 border-t-transparent"></div>
                        <span class="text-xs text-blue-700">Đang tìm kiếm...</span>
                    </div>
                </div>
                
                <!-- Panel kết quả tìm kiếm -->
                <div id="search-results-panel" class="hidden border-t border-gray-200 max-h-48 overflow-y-auto">
                    <div class="px-3 py-2 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
                        <div id="search-results-count" class="text-xs font-semibold text-gray-700"></div>
                        <button onclick="clearSearch()" class="text-gray-400 hover:text-gray-600 transition-colors" title="Đóng">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                    <div id="search-results-list" class="flex-1 overflow-y-auto"></div>
                </div>
                
                <!-- Nút Xóa bộ lọc -->
                <div id="clear-filters-container" class="hidden px-3 py-2 border-t border-gray-200">
                    <button 
                        id="clear-filters-btn" 
                        onclick="clearFilters()" 
                        class="w-full bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-1.5 rounded text-xs font-medium flex items-center justify-center gap-2 transition-colors"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        Xóa bộ lọc
                    </button>
                </div>
            </div>
            
            @if(!($canEdit ?? false))
            <!-- Thông báo cho user thường -->
            <div class="bg-yellow-50 border border-yellow-200 rounded px-3 py-2 mb-4">
                <p class="text-sm text-yellow-800">
                    <strong>ℹ️ Chế độ xem:</strong> Bạn đang xem với quyền User. Chỉ Admin mới có thể thêm và vẽ điểm trên bản đồ.
                </p>
            </div>
            @endif

            <!-- Map Container -->
            <div class="bg-white shadow-sm sm:rounded-lg relative">
                <div id="map-stats" style="display: none;">
                    <strong>📊 Thống kê:</strong>
                    <div id="stats-content">Đang tải...</div>
                </div>
                <!-- Loading Indicator -->
                <div id="map-loading" class="absolute inset-0 bg-white bg-opacity-90 flex items-center justify-center z-[2000] transition-opacity duration-300">
                    <div class="text-center">
                        <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600 mb-4"></div>
                        <p class="text-gray-700 font-semibold">Đang tải dữ liệu bản đồ...</p>
                    </div>
                </div>
                
                <!-- Modal hướng dẫn tìm kiếm -->
                <div id="search-help-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-[3000] flex items-center justify-center">
                    <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
                        <div class="p-6">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-xl font-bold text-gray-800">📖 Hướng dẫn tìm kiếm cống</h3>
                                <button onclick="closeSearchHelpModal()" class="text-gray-500 hover:text-gray-700">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                            <div class="space-y-4 text-sm text-gray-700">
                                <div>
                                    <h4 class="font-semibold text-gray-800 mb-2">🔍 Tìm kiếm bằng text:</h4>
                                    <ul class="list-disc list-inside space-y-1 ml-4">
                                        <li>Gõ tên cống: <code class="bg-gray-100 px-2 py-1 rounded">Cống ÔĐ4b</code></li>
                                        <li>Gõ mã số: <code class="bg-gray-100 px-2 py-1 rounded">C1615</code> hoặc <code class="bg-gray-100 px-2 py-1 rounded">1615</code></li>
                                        <li>Gõ địa điểm: <code class="bg-gray-100 px-2 py-1 rounded">Hóc Môn</code> hoặc <code class="bg-gray-100 px-2 py-1 rounded">Q.12</code></li>
                                        <li>Hỗ trợ tìm kiếm không dấu: <code class="bg-gray-100 px-2 py-1 rounded">cong OD</code> → tìm thấy "Cống ÔĐ"</li>
                                    </ul>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-800 mb-2">📍 Tìm kiếm bằng bộ lọc:</h4>
                                    <ul class="list-disc list-inside space-y-1 ml-4">
                                        <li>Chọn khu vực từ dropdown → Tự động lọc tất cả cống ở khu vực đó</li>
                                        <li>Chọn loại công trình → Lọc theo loại (Cống điều tiết, Cống thoát nước, v.v.)</li>
                                        <li>Chọn cấp công trình → Lọc theo cấp (I, II, III, IV)</li>
                                        <li>Có thể kết hợp nhiều bộ lọc cùng lúc</li>
                                    </ul>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-800 mb-2">🗺️ Tìm kiếm theo tọa độ:</h4>
                                    <ul class="list-disc list-inside space-y-1 ml-4">
                                        <li>Gõ tọa độ: <code class="bg-gray-100 px-2 py-1 rounded">106.808739, 10.868036</code></li>
                                        <li>Hoặc: <code class="bg-gray-100 px-2 py-1 rounded">106.808739 10.868036</code></li>
                                        <li>Hệ thống tự động tìm cống gần nhất với tọa độ đó</li>
                                    </ul>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-800 mb-2">⌨️ Phím tắt:</h4>
                                    <ul class="list-disc list-inside space-y-1 ml-4">
                                        <li><kbd class="bg-gray-100 px-2 py-1 rounded">Ctrl+F</kbd> hoặc <kbd class="bg-gray-100 px-2 py-1 rounded">Cmd+F</kbd>: Focus vào ô tìm kiếm</li>
                                        <li><kbd class="bg-gray-100 px-2 py-1 rounded">Esc</kbd>: Xóa tìm kiếm, reset bản đồ</li>
                                        <li><kbd class="bg-gray-100 px-2 py-1 rounded">↑↓</kbd>: Điều hướng trong gợi ý</li>
                                    </ul>
                                </div>
                            </div>
                            <div class="mt-6 flex justify-end">
                                <button onclick="closeSearchHelpModal()" class="bg-blue-600 hover:bg-blue-700 px-6 py-2 rounded-lg font-bold" style="color: #DC2626 !important;">
                                    Đóng
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div id="map" style="width: 100%; height: 600px; min-height: 600px; position: relative; transition: margin-top 0.3s ease, height 0.3s ease;"></div>
            </div>
        </div>
        
        <!-- Chú thích màu - Footer -->
        <div class="mt-4 bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="max-w-7xl mx-auto">
                <div class="flex items-center gap-6 flex-wrap">
                    <div class="font-bold text-gray-700 text-sm">Chú thích:</div>
                    <div class="flex items-center gap-2">
                        <div class="legend-color" style="background-color: #0EA5E9; width: 16px; height: 16px; border-radius: 50%; border: 2px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.3);"></div>
                        <span class="text-sm text-gray-700">Cống test (ID 361)</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="legend-color" style="background-color: #F97316; width: 16px; height: 16px; border-radius: 50%; border: 2px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.3);"></div>
                        <span class="text-sm text-gray-700">Cống đặc biệt (ÔĐ4b)</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="legend-color" style="background-color: #3B82F6; width: 16px; height: 16px; border-radius: 50%; border: 2px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.3);"></div>
                        <span class="text-sm text-gray-700">Cống mới</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="legend-color" style="background-color: #FF0000; width: 16px; height: 16px; border-radius: 50%; border: 2px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.3);"></div>
                        <span class="text-sm text-gray-700">Cống cũ</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="legend-color" style="background-color: #F59E0B; width: 16px; height: 16px; border-radius: 50%; border: 2px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.3);"></div>
                        <span class="text-sm text-gray-700">Kết quả tìm kiếm</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Leaflet CSS - Local -->
    <link rel="stylesheet" href="{{ asset('css/leaflet.css') }}" />
    <!-- Leaflet.draw CSS - Local -->
    <link rel="stylesheet" href="{{ asset('css/leaflet.draw.css') }}" />
    
    <style>
        .cong-marker {
            background: transparent !important;
            border: none !important;
        }
        .cong-marker-hover {
            background: transparent !important;
            border: none !important;
        }
        
        /* Làm nút Draw a marker trong Leaflet.draw toolbar to hơn */
        .leaflet-draw-toolbar a {
            width: 44px !important;
            height: 44px !important;
            line-height: 44px !important;
            font-size: 20px !important;
        }
        .leaflet-draw-toolbar .leaflet-draw-draw-marker {
            background-size: 32px 32px !important;
            background-position: center !important;
        }
        .cong-popup .leaflet-popup-content-wrapper {
            border-radius: 8px;
        }
        .cong-popup .leaflet-popup-content {
            margin: 15px;
        }
        .cong-popup table {
            border-collapse: collapse;
        }
        .cong-popup table th {
            text-align: left;
            padding-right: 10px;
            padding-bottom: 5px;
            vertical-align: top;
        }
        .cong-popup table td {
            padding-bottom: 5px;
        }
        #map-stats {
            position: absolute;
            top: 10px;
            right: 10px;
            background: white;
            padding: 10px 15px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            z-index: 1000;
            font-size: 14px;
        }
        /* Chú thích đã được di chuyển xuống footer, không cần CSS cho map-legend nữa */
        @keyframes pulse {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: 0.7;
            }
        }
    </style>
    
    @vite(['resources/js/app.js'])

    <!-- Leaflet JS - Local -->
    <script src="{{ asset('js/leaflet.js') }}"></script>
    <!-- Leaflet.draw JS - Local -->
    <script src="{{ asset('js/leaflet.draw.js') }}"></script>
    <script>
        // CSRF Token cho API calls
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
        const canEdit = @json($canEdit ?? false);
        const userRole = @json($userRole ?? 'guest');

        // Initialize map
        var map = L.map('map').setView([10.762622, 106.660172], 13);

        // Cấu hình icon mặc định của Leaflet để dùng file local
        delete L.Icon.Default.prototype._getIconUrl;
        L.Icon.Default.mergeOptions({
            iconUrl: '{{ asset("images/marker-icon.png") }}',
            iconRetinaUrl: '{{ asset("images/marker-icon-2x.png") }}',
            shadowUrl: '{{ asset("images/marker-shadow.png") }}',
            iconSize: [25, 41],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34],
            shadowSize: [41, 41]
        });

        // Tile layer
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);
        
        // Di chuyển attribution control sang góc dưới bên trái (phía trên chú thích)
        map.attributionControl.setPosition('bottomleft');

        // Leaflet.draw: Chỉ cho phép Admin vẽ điểm trên bản đồ
        let drawControl;
        let drawHandler = null;
        let isDrawingMode = false;
        console.log('🔍 Kiểm tra canEdit:', canEdit, 'userRole:', userRole);
        
        if (canEdit) {
            // Đợi một chút để đảm bảo Leaflet.draw đã load hoàn toàn
            setTimeout(function() {
                // Kiểm tra Leaflet.draw đã load chưa
                if (typeof L === 'undefined' || typeof L.Draw === 'undefined') {
                    console.error('❌ Leaflet.draw chưa được load! Vui lòng kiểm tra CDN.');
                    console.error('L:', typeof L, 'L.Draw:', typeof L.Draw);
                } else {
                    console.log('✅ Leaflet.draw đã được load, sẵn sàng sử dụng');
                    // Không cần tạo drawControl nữa vì chỉ dùng L.Draw.Marker trực tiếp
                    // drawControl chỉ cần để đánh dấu là đã khởi tạo
                    drawControl = { initialized: true };
                    console.log('✅ Leaflet.draw đã được kích hoạt cho Admin (dùng L.Draw.Marker trực tiếp)');
                }
            }, 200); // Delay 200ms để đảm bảo Leaflet.draw đã load xong
        } else {
            console.log('ℹ️ Leaflet.draw không được kích hoạt - canEdit =', canEdit);
            console.log('ℹ️ Chỉ Admin mới có thể vẽ điểm trên bản đồ');
        }

        // Lưu danh sách ID cống mới thêm (trong session này)
        let newCongIds = JSON.parse(sessionStorage.getItem('newCongIds') || '[]');

        // Kiểm tra xem cống có phải mới không
        function isCongNew(props) {
            const congId = props.ogc_fid || props.id;
            const tenCong = props.ten_cong_dap || 'N/A';
            
            if (!congId && congId !== 0) return false;
            
            // Reload lại từ sessionStorage để đảm bảo có dữ liệu mới nhất
            const currentNewCongIds = JSON.parse(sessionStorage.getItem('newCongIds') || '[]');
            
            // Chuyển đổi sang number để so sánh chính xác (ogc_fid là number)
            const congIdNum = Number(congId);
            const currentNewCongIdsNum = currentNewCongIds.map(id => Number(id));
            
            // Kiểm tra trong danh sách cống mới thêm
            if (currentNewCongIdsNum.includes(congIdNum)) {
                console.log('✅ Cống', congIdNum, '(' + tenCong + ')', 'được phát hiện là cống mới từ sessionStorage');
                return true;
            }
            
            // Nếu có created_at, kiểm tra trong 24h gần đây
            if (props.created_at) {
                const createdDate = new Date(props.created_at);
                const now = new Date();
                const hoursDiff = (now - createdDate) / (1000 * 60 * 60);
                if (hoursDiff <= 24) {
                    console.log('✅ Cống', congIdNum, '(' + tenCong + ')', 'được phát hiện là cống mới từ created_at (trong 24h)');
                    return true;
                }
            }
            
            return false;
        }

        // Kiểm tra xem cống có phải cống test (ID 361) không
        function isTestCong(props) {
            const congId = props.ogc_fid || props.id;
            const stt = props.stt;
            
            // Chuyển đổi sang string để so sánh chính xác
            const congIdStr = String(congId);
            const sttStr = String(stt || '');
            
            // Kiểm tra ID hoặc STT = 361 (so sánh cả số và chuỗi)
            return congId == 361 || stt == 361 || 
                   congId === '361' || stt === '361' ||
                   congIdStr === '361' || sttStr === '361' ||
                   parseInt(congId) === 361 || parseInt(stt) === 361;
        }

        // Kiểm tra xem cống có phải cống đặc biệt (Cống ÔĐ4b - mã số C1615) không
        function isSpecialCong(props) {
            const tenCong = String(props.ten_cong_dap || '').trim();
            const maSo = String(props.id || '').trim();
            
            // Kiểm tra tên cống hoặc mã số
            return tenCong === 'Cống ÔĐ4b' ||
                   maSo === 'C1615' ||
                   tenCong.includes('ÔĐ4b') ||
                   maSo.includes('C1615');
        }

        // Thêm ID cống mới vào danh sách
        function markCongAsNew(congId) {
            if (!congId && congId !== 0) {
                console.error('❌ markCongAsNew: Không có ID', congId);
                return;
            }
            
            // Reload lại từ sessionStorage để đảm bảo có dữ liệu mới nhất
            let currentNewCongIds = JSON.parse(sessionStorage.getItem('newCongIds') || '[]');
            
            // Chuyển đổi sang number để so sánh chính xác (ogc_fid là number)
            const congIdNum = Number(congId);
            const currentNewCongIdsNum = currentNewCongIds.map(id => Number(id));
            
            if (!currentNewCongIdsNum.includes(congIdNum)) {
                currentNewCongIds.push(congIdNum);
                sessionStorage.setItem('newCongIds', JSON.stringify(currentNewCongIds));
                // Cập nhật lại biến global
                newCongIds = currentNewCongIds;
                console.log('✅ Đã đánh dấu cống mới với ID:', congIdNum, '(kiểu:', typeof congIdNum, ')');
                console.log('📋 Danh sách cống mới hiện tại:', newCongIds);
            } else {
                console.log('⚠️ Cống ID', congIdNum, 'đã có trong danh sách cống mới');
            }
        }

        // Cache popup content để tăng tốc độ
        const popupCache = new Map();

        // Utility: build popup HTML from properties - TỐI ƯU TỐC ĐỘ
        function buildPropsTable(props) {
            const congId = props.ogc_fid || props.id;
            
            // Kiểm tra cache trước
            if (popupCache.has(congId)) {
                return popupCache.get(congId);
            }
            
            // Chỉ hiển thị các trường QUAN TRỌNG NHẤT để tăng tốc độ
            const importantFields = {
                'ten_cong_dap': 'Tên công đập',
                'id': 'Mã số',
                'ly_trinh': 'Lý trình',
                'dia_diem': 'Địa điểm',
                'loai_cong_tr': 'Loại công trình',
                'duong_kinh': 'Đường kính (cm)',
                'chieu_dai': 'Chiều dài (m)',
                'cap_cong_tri': 'Cấp công trình',
                'ghi_chu': 'Ghi chú'
            };

            let rows = '';
            
            // Chỉ hiển thị các trường quan trọng (giảm số lượng để tăng tốc)
            for (const [key, label] of Object.entries(importantFields)) {
                const value = props[key];
                if (value !== null && value !== undefined && value !== '') {
                    rows += `<tr><th class="text-left pr-4 font-semibold text-sm">${label}:</th><td class="text-sm">${value}</td></tr>`;
                }
            }

            // File đính kèm - User và Admin đều có thể tải
            if (props.file_url) {
                const fileName = props.file_name || 'Tệp đính kèm';
                const fileSize = props.file_size ? ` (${(props.file_size / 1024).toFixed(2)} KB)` : '';
                const fileExtension = fileName.split('.').pop()?.toLowerCase() || '';
                const isImage = ['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(fileExtension);
                const isPdf = fileExtension === 'pdf';
                
                // Icon tùy theo loại file
                let fileIcon = '📎';
                if (isImage) fileIcon = '🖼️';
                else if (isPdf) fileIcon = '📄';
                
                rows += `<tr><th class="text-left pr-4 font-semibold text-sm">Tệp đính kèm:</th><td>
                    <a href="${props.file_url}" target="_blank" download="${fileName}" 
                       class="inline-flex items-center px-3 py-1.5 bg-blue-50 text-blue-700 rounded-md hover:bg-blue-100 transition shadow-sm border border-blue-200"
                       title="Tải file: ${fileName}">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        ${fileIcon} Tải tệp${fileSize}
                    </a>
                    ${isImage ? `<a href="${props.file_url}" target="_blank" class="ml-2 text-xs text-blue-600 hover:text-blue-800">(Xem ảnh)</a>` : ''}
                </td></tr>`;
            }

            let actions = '';
            if (canEdit && props.ogc_fid) {
                actions = `
                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <a href="/cong/manage?edit=${props.ogc_fid}" class="inline-block text-sm text-blue-600 hover:text-blue-800 mr-4">
                            ✏️ Sửa
                        </a>
                        <button onclick="deleteCong(${props.ogc_fid})" class="text-sm text-red-600 hover:text-red-800">
                            🗑️ Xóa
                        </button>
                    </div>
                `;
            }

            const popupHTML = `
                <div class="min-w-[250px] max-w-[400px]">
                    <h3 class="font-semibold mb-3 text-gray-900 text-lg">${props.ten_cong_dap || 'Thông tin cống'}</h3>
                    <table class="text-sm w-full">${rows}</table>
                    ${actions}
                </div>
            `;
            
            // Cache popup content
            popupCache.set(congId, popupHTML);
            return popupHTML;
        }
        
        // Xóa cache khi cập nhật dữ liệu
        function clearPopupCache() {
            popupCache.clear();
        }
        
        // Xóa cache cho một ID cụ thể
        function clearPopupCacheById(congId) {
            if (congId) {
                popupCache.delete(congId);
                console.log('✅ Đã xóa cache popup cho ID:', congId);
            }
        }

        // Tạo icon cho điểm cống - LỚN và RÕ RÀNG (màu đỏ - cống cũ)
        function createCongIcon() {
            return L.divIcon({
                className: 'cong-marker',
                html: '<div style="background-color: #FF0000; width: 18px; height: 18px; border-radius: 50%; border: 3px solid white; box-shadow: 0 3px 10px rgba(0,0,0,0.7); cursor: pointer;"></div>',
                iconSize: [18, 18],
                iconAnchor: [9, 9],
                popupAnchor: [0, -9]
            });
        }

        // Tạo icon cho cống mới - MÀU XANH DƯƠNG (dễ nhìn)
        function createNewCongIcon() {
            return L.divIcon({
                className: 'cong-marker-new',
                html: '<div style="background-color: #3B82F6; width: 20px; height: 20px; border-radius: 50%; border: 3px solid white; box-shadow: 0 3px 10px rgba(59,130,246,0.8); cursor: pointer; animation: pulse 2s infinite;"></div>',
                iconSize: [20, 20],
                iconAnchor: [10, 10],
                popupAnchor: [0, -10]
            });
        }

        // Tạo icon cho cống test (ID 361) - MÀU XANH NƯỚC BIỂN
        function createTestCongIcon() {
            return L.divIcon({
                className: 'cong-marker-test',
                html: '<div style="background-color: #0EA5E9; width: 22px; height: 22px; border-radius: 50%; border: 3px solid white; box-shadow: 0 3px 10px rgba(14,165,233,0.8); cursor: pointer; animation: pulse 2s infinite;"></div>',
                iconSize: [22, 22],
                iconAnchor: [11, 11],
                popupAnchor: [0, -11]
            });
        }
        
        // Icon khi hover - LỚN HƠN (cống cũ)
        function createCongIconHover() {
            return L.divIcon({
                className: 'cong-marker-hover',
                html: '<div style="background-color: #FF6B00; width: 24px; height: 24px; border-radius: 50%; border: 4px solid white; box-shadow: 0 4px 15px rgba(0,0,0,0.8); cursor: pointer;"></div>',
                iconSize: [24, 24],
                iconAnchor: [12, 12],
                popupAnchor: [0, -12]
            });
        }

        // Icon khi hover - CỐNG MỚI (màu xanh dương đậm hơn)
        function createNewCongIconHover() {
            return L.divIcon({
                className: 'cong-marker-new-hover',
                html: '<div style="background-color: #2563EB; width: 26px; height: 26px; border-radius: 50%; border: 4px solid white; box-shadow: 0 4px 15px rgba(59,130,246,0.9); cursor: pointer;"></div>',
                iconSize: [26, 26],
                iconAnchor: [13, 13],
                popupAnchor: [0, -13]
            });
        }

        // Icon khi hover - CỐNG TEST (màu xanh nước biển đậm hơn)
        function createTestCongIconHover() {
            return L.divIcon({
                className: 'cong-marker-test-hover',
                html: '<div style="background-color: #0284C7; width: 28px; height: 28px; border-radius: 50%; border: 4px solid white; box-shadow: 0 4px 15px rgba(14,165,233,0.9); cursor: pointer;"></div>',
                iconSize: [28, 28],
                iconAnchor: [14, 14],
                popupAnchor: [0, -14]
            });
        }

        // Tạo icon cho cống đặc biệt (Cống ÔĐ4b) - MÀU CAM
        function createSpecialCongIcon() {
            return L.divIcon({
                className: 'cong-marker-special',
                html: '<div style="background-color: #F97316; width: 22px; height: 22px; border-radius: 50%; border: 3px solid white; box-shadow: 0 3px 10px rgba(249,115,22,0.8); cursor: pointer; animation: pulse 2s infinite;"></div>',
                iconSize: [22, 22],
                iconAnchor: [11, 11],
                popupAnchor: [0, -11]
            });
        }

        // Icon khi hover - CỐNG ĐẶC BIỆT (màu cam đậm hơn)
        function createSpecialCongIconHover() {
            return L.divIcon({
                className: 'cong-marker-special-hover',
                html: '<div style="background-color: #EA580C; width: 28px; height: 28px; border-radius: 50%; border: 4px solid white; box-shadow: 0 4px 15px rgba(249,115,22,0.9); cursor: pointer;"></div>',
                iconSize: [28, 28],
                iconAnchor: [14, 14],
                popupAnchor: [0, -14]
            });
        }

        // Style for cong (lines - nếu có)
        const congStyle = {
            color: '#1976d2',
            weight: 3,
            opacity: 0.8
        };

        // Style cho điểm
        const pointStyle = {
            radius: 8,
            fillColor: '#1976d2',
            color: '#ffffff',
            weight: 2,
            opacity: 1,
            fillOpacity: 0.8
        };

        // Load cong data
        let congLayer;
        function loadCongData() {
            // Return Promise để có thể dùng .then()
            return new Promise((resolve, reject) => {
                // Hiển thị loading indicator
                const loadingEl = document.getElementById('map-loading');
                if (loadingEl) {
                    loadingEl.style.display = 'flex';
                }
                
                console.log('🔄 Loading cong data from /api/cong...');
                // Thêm timestamp để tránh cache - SỬA: Thêm random để force reload
                const timestamp = new Date().getTime();
                const random = Math.random().toString(36).substring(7);
                fetch(`/api/cong?t=${timestamp}&r=${random}`, {
                    cache: 'no-cache',
                    headers: {
                        'Cache-Control': 'no-cache',
                        'Pragma': 'no-cache',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                    .then(r => {
                        console.log('Response status:', r.status);
                        if (!r.ok) {
                            throw new Error('HTTP error! status: ' + r.status);
                        }
                        return r.json();
                    })
                    .then(geojson => {
                    console.log('Received GeoJSON:', geojson);
                    
                    // Ẩn loading indicator
                    if (loadingEl) {
                        loadingEl.style.display = 'none';
                    }
                    
                    if (!geojson || !geojson.features) {
                        console.error('No features found in GeoJSON');
                        // Ẩn loading indicator
                        if (loadingEl) {
                            loadingEl.style.display = 'none';
                        }
                        alert('Không tìm thấy dữ liệu cống. Vui lòng kiểm tra lại!');
                        resolve(geojson || { features: [] }); // Resolve với empty data
                        return;
                    }
                    
                    if (geojson.features.length === 0) {
                        console.warn('GeoJSON has 0 features');
                        // Ẩn loading indicator
                        if (loadingEl) {
                            loadingEl.style.display = 'none';
                        }
                        alert('Không có dữ liệu cống nào để hiển thị!');
                        resolve(geojson); // Resolve với empty features
                        return;
                    }
                    
                    console.log('✅ Loaded ' + geojson.features.length + ' features');
                    
                    // Debug: Log để kiểm tra cống mới
                    if (geojson.features.length > 0) {
                        // Log tất cả IDs để kiểm tra
                        const allIds = geojson.features.map(f => f.properties.ogc_fid || f.properties.id);
                        console.log('📋 Tất cả IDs trong response:', allIds);
                        
                        // Reload lại newCongIds từ sessionStorage
                        newCongIds = JSON.parse(sessionStorage.getItem('newCongIds') || '[]');
                        console.log('📋 Danh sách cống mới từ sessionStorage:', newCongIds);
                        
                        // Kiểm tra xem cống mới có trong response không
                        // Chuyển đổi sang number để so sánh chính xác
                        const allIdsNum = allIds.map(id => Number(id));
                        const newCongIdsNum = newCongIds.map(id => Number(id));
                        const newCongInResponse = newCongIdsNum.filter(id => allIdsNum.includes(id));
                        if (newCongInResponse.length > 0) {
                            console.log('✅ Có', newCongInResponse.length, 'cống mới trong response:', newCongInResponse);
                        } else if (newCongIds.length > 0) {
                            console.warn('⚠️ Có cống mới trong sessionStorage nhưng KHÔNG có trong response');
                            console.warn('⚠️ IDs trong response:', allIdsNum);
                            console.warn('⚠️ IDs trong sessionStorage:', newCongIdsNum);
                        }
                        
                        // Kiểm tra xem có geometry hợp lệ không
                        const featuresWithoutGeom = geojson.features.filter(f => !f.geometry || !f.geometry.coordinates);
                        if (featuresWithoutGeom.length > 0) {
                            console.warn('⚠️ Có', featuresWithoutGeom.length, 'cống không có geometry hợp lệ:', 
                                featuresWithoutGeom.map(f => f.properties.ogc_fid || f.properties.id));
                        }
                    }
                    
                    // Remove existing layer if any
                    if (congLayer) {
                        map.removeLayer(congLayer);
                    }

                    congLayer = L.geoJSON(geojson, {
                        style: function(feature) {
                            // Nếu là Point, dùng style cho điểm
                            if (feature.geometry.type === 'Point') {
                                return pointStyle;
                            }
                            // Nếu là LineString hoặc Polygon, dùng style cho đường
                            return congStyle;
                        },
                        pointToLayer: (feature, latlng) => {
                            const props = feature.properties || {};
                            const congId = props.ogc_fid || props.id;
                            
                            // Ưu tiên kiểm tra theo thứ tự: Test (361) > Đặc biệt (ÔĐ4b) > Mới > Cũ
                            const isTest = isTestCong(props);
                            const isSpecial = isSpecialCong(props);
                            
                            // Debug: Log để kiểm tra cống 361
                            if (congId == 361 || congId === '361' || parseInt(congId) === 361 || String(congId) === '361') {
                                console.log('🔵 Cống 361 được phát hiện:', {
                                    ogc_fid: props.ogc_fid,
                                    id: props.id,
                                    stt: props.stt,
                                    isTest: isTest,
                                    congId: congId,
                                    congIdType: typeof congId
                                });
                            }
                            
                            // Debug: Log để kiểm tra cống đặc biệt
                            if (isSpecial) {
                                console.log('🟠 Cống đặc biệt (ÔĐ4b) được phát hiện:', {
                                    ogc_fid: props.ogc_fid,
                                    id: props.id,
                                    ten_cong_dap: props.ten_cong_dap
                                });
                            }
                            
                            // Tạo marker với icon tùy chỉnh
                            // Cống test (361): màu xanh nước biển - ƯU TIÊN CAO NHẤT
                            // Cống đặc biệt (ÔĐ4b): màu cam - ƯU TIÊN THỨ 2
                            // Cống mới: màu xanh dương (blue) - dễ nhìn
                            // Cống cũ: màu đỏ
                            let icon;
                            let zIndex;
                            
                            if (isTest) {
                                // Cống test (361) - luôn màu xanh nước biển
                                icon = createTestCongIcon();
                                zIndex = 3000; // Cống test hiển thị trên cùng nhất
                                console.log('✅ Sử dụng icon XANH NƯỚC BIỂN cho cống:', congId);
                            } else if (isSpecial) {
                                // Cống đặc biệt (ÔĐ4b) - màu cam
                                icon = createSpecialCongIcon();
                                zIndex = 2500; // Cống đặc biệt hiển thị sau cống test
                                console.log('🟠 Sử dụng icon CAM cho cống đặc biệt:', congId, 'Tên:', props.ten_cong_dap);
                            } else {
                                // Kiểm tra xem cống có phải mới không (chỉ khi không phải cống test và đặc biệt)
                                // Reload lại newCongIds từ sessionStorage để đảm bảo có dữ liệu mới nhất
                                newCongIds = JSON.parse(sessionStorage.getItem('newCongIds') || '[]');
                                const isNew = isCongNew(props);
                                
                                if (isNew) {
                                    icon = createNewCongIcon();
                                    zIndex = 2000;
                                    console.log('🔵 Sử dụng icon XANH DƯƠNG cho cống mới:', congId, 'Tên:', props.ten_cong_dap, 'newCongIds:', newCongIds);
                                } else {
                                    icon = createCongIcon();
                                    zIndex = 1000;
                                }
                            }
                            
                            return L.marker(latlng, {
                                icon: icon,
                                zIndexOffset: zIndex
                            });
                        },
                        onEachFeature: (feature, layer) => {
                            // Tạo popup với thông tin chi tiết - TỐI ƯU: chỉ build khi cần
                            const props = feature.properties || {};
                            const congId = props.ogc_fid || props.id;
                            
                            // Bind popup với lazy loading - chỉ build khi mở
                            // SỬA: Xóa cache cho ID này trước khi build để đảm bảo dữ liệu mới
                            layer.bindPopup(() => {
                                // Xóa cache cho ID này trước khi build để đảm bảo dữ liệu mới
                                clearPopupCacheById(congId);
                                return buildPropsTable(props);
                            }, {
                                maxWidth: 400,
                                className: 'cong-popup'
                            });
                            
                            // Add hover effect cho điểm - tự động hiện popup khi hover
                            if (feature.geometry.type === 'Point') {
                                const isTest = isTestCong(props);
                                const isSpecial = isSpecialCong(props);
                                const isNew = isCongNew(props);
                                layer.on({
                                    mouseover: function(e) {
                                        const layer = e.target;
                                        if (layer.setIcon) {
                                            // Thay đổi icon khi hover
                                            if (isTest) {
                                                layer.setIcon(createTestCongIconHover());
                                            } else if (isSpecial) {
                                                layer.setIcon(createSpecialCongIconHover());
                                            } else if (isNew) {
                                                layer.setIcon(createNewCongIconHover());
                                            } else {
                                                layer.setIcon(createCongIconHover());
                                            }
                                        }
                                        // Tự động mở popup khi hover
                                        layer.openPopup();
                                    },
                                    mouseout: function(e) {
                                        const layer = e.target;
                                        if (layer.setIcon) {
                                            // Trả về icon ban đầu
                                            if (isTest) {
                                                layer.setIcon(createTestCongIcon());
                                            } else if (isSpecial) {
                                                layer.setIcon(createSpecialCongIcon());
                                            } else if (isNew) {
                                                layer.setIcon(createNewCongIcon());
                                            } else {
                                                layer.setIcon(createCongIcon());
                                            }
                                        }
                                        // Đóng popup khi rời chuột
                                        layer.closePopup();
                                    }
                                });
                            } else {
                                // Hover effect cho đường
                                layer.on({
                                    mouseover: function(e) {
                                        const layer = e.target;
                                        layer.setStyle({
                                            weight: 5,
                                            color: '#ff6b6b',
                                            opacity: 1
                                        });
                                    },
                                    mouseout: function(e) {
                                        congLayer.resetStyle(e.target);
                                    }
                                });
                            }
                        }
                    }).addTo(map);

                    // Fit map to bounds
                    if (geojson.features.length > 0) {
                        try {
                            map.fitBounds(congLayer.getBounds(), { 
                                padding: [50, 50],
                                maxZoom: 15
                            });
                        } catch (e) {
                            console.log('Could not fit bounds:', e);
                            // Nếu không fit được, set view mặc định
                            map.setView([10.762622, 106.660172], 12);
                        }
                        
                        // Hiển thị thống kê
                        updateStats(geojson.features);
                        
                        // Populate filters SAU KHI congLayer đã được tạo
                        setTimeout(() => {
                            console.log('🔄 Gọi populateFilters sau khi tạo congLayer...');
                            populateFilters();
                        }, 100);
                        
                        // Resolve Promise khi hoàn thành
                        resolve(geojson);
                    }
                })
                .catch(err => {
                    console.error('Error loading cong data:', err);
                    // Ẩn loading indicator khi có lỗi
                    const loadingEl = document.getElementById('map-loading');
                    if (loadingEl) {
                        loadingEl.style.display = 'none';
                    }
                    alert('Lỗi khi tải dữ liệu cống: ' + err.message);
                    // Reject Promise khi có lỗi
                    reject(err);
                });
            });
        }

        // Cập nhật thống kê
        function updateStats(features) {
            const statsDiv = document.getElementById('map-stats');
            const statsContent = document.getElementById('stats-content');
            
            if (!statsDiv || !statsContent) return;
            
            const total = features.length;
            const withLocation = features.filter(f => f.geometry && f.geometry.type === 'Point').length;
            
            statsContent.innerHTML = `
                <div>Tổng số: <strong>${total}</strong></div>
                <div>Có vị trí: <strong>${withLocation}</strong></div>
            `;
            
            statsDiv.style.display = 'block';
        }

        // Load initial data
        loadCongData().then(() => {
            // Kiểm tra URL parameter zoom để tự động zoom đến vị trí cống mới
            const urlParams = new URLSearchParams(window.location.search);
            const zoom = urlParams.get('zoom');
            if (zoom) {
                const [x, y] = zoom.split(',').map(Number);
                if (x && y && !isNaN(x) && !isNaN(y)) {
                    console.log('📍 Tự động zoom đến vị trí cống mới:', { x, y });
                    // Zoom đến vị trí với level 16 (zoom gần)
                    map.setView([y, x], 16);
                    
                    // Xóa parameter khỏi URL (clean URL)
                    window.history.replaceState({}, document.title, '/map');
                    
                    // Tìm và mở popup cống gần vị trí zoom nhất
                    setTimeout(() => {
                        if (congLayer) {
                            let minDistance = Infinity;
                            let nearestLayer = null;
                            
                            congLayer.eachLayer(function(layer) {
                                if (layer.feature && layer.feature.geometry && layer.feature.geometry.type === 'Point') {
                                    const coords = layer.feature.geometry.coordinates;
                                    if (coords && coords.length >= 2) {
                                        const layerX = coords[0];
                                        const layerY = coords[1];
                                        
                                        // Tính khoảng cách (đơn giản, không cần tính chính xác)
                                        const distance = Math.abs(x - layerX) + Math.abs(y - layerY);
                                        
                                        if (distance < minDistance) {
                                            minDistance = distance;
                                            nearestLayer = layer;
                                        }
                                    }
                                }
                            });
                            
                            if (nearestLayer) {
                                nearestLayer.openPopup();
                                console.log('✅ Đã mở popup cống mới tại vị trí:', { x, y });
                            } else {
                                console.log('⚠️ Không tìm thấy cống gần vị trí:', { x, y });
                            }
                        }
                    }, 1000); // Delay để đảm bảo dữ liệu đã load xong
                }
            }
        });

        // Edit/Delete functions (chỉ khi có quyền admin)
        function editCong(id) {
            if (!canEdit) {
                alert('Bạn không có quyền chỉnh sửa!');
                return;
            }
            
            // Load dữ liệu cống
            fetch(`/api/cong`)
                .then(r => r.json())
                .then(data => {
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

                    console.log('Tìm thấy cống để sửa:', cong);
                    const props = cong.properties;
                    
                    // Điền form
                    document.getElementById('edit_cong_id').value = id;
                    document.getElementById('edit_map_toa_do_x').value = props.toa_do_x || '';
                    document.getElementById('edit_map_toa_do_y').value = props.toa_do_y || '';
                    document.getElementById('edit_map_id').value = props.id || '';
                    document.getElementById('edit_map_ten_cong_dap').value = props.ten_cong_dap || '';
                    document.getElementById('edit_map_ly_trinh').value = props.ly_trinh || '';
                    document.getElementById('edit_map_dia_diem').value = props.dia_diem || '';
                    document.getElementById('edit_map_cap_cong_tri').value = props.cap_cong_tri || '';
                    document.getElementById('edit_map_chieu_dai').value = props.chieu_dai || '';
                    document.getElementById('edit_map_duong_kinh').value = props.duong_kinh || '';
                    document.getElementById('edit_map_hinh_thuc').value = props.hinh_thuc || '';
                    document.getElementById('edit_map_be_rong').value = props.be_rong || '';
                    document.getElementById('edit_map_chieu_cao').value = props.chieu_cao || '';
                    document.getElementById('edit_map_muc_tieu_nhi').value = props.muc_tieu_nhi || '';
                    document.getElementById('edit_map_goi_thau').value = props.goi_thau || '';
                    document.getElementById('edit_map_don_vi_quan_l').value = props.don_vi_quan_l || '';
                    document.getElementById('edit_map_ghi_chu').value = props.ghi_chu || '';
                    
                    // Hiển thị file hiện tại
                    const currentFileDiv = document.getElementById('edit_map_current_file');
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
                    
                    document.getElementById('edit_map_filePreview').innerHTML = '';
                    document.getElementById('editCongModal').classList.remove('hidden');
                })
                .catch(err => {
                    console.error('Error:', err);
                    alert('Lỗi khi tải dữ liệu: ' + err.message);
                });
        }

        function deleteCong(id) {
            if (!canEdit) {
                alert('Bạn không có quyền xóa!');
                return;
            }
            
            if (!confirm('Bạn có chắc muốn xóa cống này?')) {
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
                    loadCongData(); // Reload data
                } else {
                    alert('Lỗi: ' + (data.message || 'Không thể xóa'));
                }
            })
            .catch(err => {
                console.error('Error:', err);
                alert('Lỗi khi xóa: ' + err.message);
            });
        }

        // Modal thêm mới cống
        let clickedLatLng = null;
        
        // Lắng nghe click trên map để lấy tọa độ
        map.on('click', function(e) {
            if (document.getElementById('addCongModal') && !document.getElementById('addCongModal').classList.contains('hidden')) {
                clickedLatLng = e.latlng;
                document.getElementById('toa_do_x').value = e.latlng.lng.toFixed(6);
                document.getElementById('toa_do_y').value = e.latlng.lat.toFixed(6);
            }
        });

        // Biến để lưu trạng thái drag
        let isDragging = false;
        let dragOffset = { x: 0, y: 0 };
        let isMinimized = false;

        function showAddCongModal() {
            if (!canEdit) {
                alert('Bạn không có quyền thêm cống mới! Chỉ Admin mới có quyền này.');
                return;
            }
            const modal = document.getElementById('addCongModal');
            const mapContainer = document.getElementById('map');
            
            modal.classList.remove('hidden');
            // Reset form
            document.getElementById('addCongForm').reset();
            document.getElementById('filePreview').innerHTML = '';
            clickedLatLng = null;
            isMinimized = false;
            
            // Khởi tạo drag functionality
            initAddCongModalDrag();
            
            // Điều chỉnh bản đồ sau khi panel hiển thị
            setTimeout(() => {
                adjustMapSizeForPanel();
            }, 100);
        }

        function closeAddCongModal() {
            const modal = document.getElementById('addCongModal');
            const mapContainer = document.getElementById('map');
            
            modal.classList.add('hidden');
            
            // Reset margin-top cho bản đồ
            if (mapContainer) {
                mapContainer.style.marginTop = '0';
                mapContainer.style.height = '600px';
                // Trigger resize cho Leaflet
                setTimeout(() => {
                    if (map) {
                        map.invalidateSize();
                    }
                }, 100);
            }
        }
        
        function toggleAddCongModalMinimize() {
            const modal = document.getElementById('addCongModal');
            const content = document.getElementById('addCongModalContent');
            
            isMinimized = !isMinimized;
            
            if (isMinimized) {
                content.style.display = 'none';
                modal.style.height = 'auto';
            } else {
                content.style.display = 'block';
                modal.style.height = '500px';
            }
            
            // Điều chỉnh bản đồ
            adjustMapSizeForPanel();
        }
        
        // Hàm điều chỉnh bản đồ theo panel
        function adjustMapSizeForPanel() {
            const modal = document.getElementById('addCongModal');
            const mapContainer = document.getElementById('map');
            
            if (!modal || !mapContainer) return;
            
            if (isMinimized) {
                const headerHeight = 50;
                mapContainer.style.marginTop = headerHeight + 'px';
                mapContainer.style.height = `calc(100vh - ${headerHeight}px)`;
            } else {
                const modalHeight = modal.offsetHeight;
                const modalTop = parseInt(modal.style.top) || 0;
                mapContainer.style.marginTop = (modalTop + modalHeight) + 'px';
                mapContainer.style.height = `calc(100vh - ${modalTop + modalHeight}px)`;
            }
            
            // Trigger resize cho Leaflet
            setTimeout(() => {
                if (map) {
                    map.invalidateSize();
                }
            }, 50);
        }
        
        // Khởi tạo drag functionality cho modal
        function initAddCongModalDrag() {
            const modal = document.getElementById('addCongModal');
            const header = document.getElementById('addCongModalHeader');
            
            if (!modal || !header) return;
            
            // Observer để theo dõi thay đổi kích thước panel (khi resize)
            if (typeof ResizeObserver !== 'undefined') {
                const resizeObserver = new ResizeObserver(function() {
                    adjustMapSizeForPanel();
                });
                resizeObserver.observe(modal);
            }
            
            header.addEventListener('mousedown', function(e) {
                if (e.target.closest('button')) return; // Không drag khi click vào button
                isDragging = true;
                const rect = modal.getBoundingClientRect();
                dragOffset.x = e.clientX - rect.left;
                dragOffset.y = e.clientY - rect.top;
                modal.style.cursor = 'move';
            });
            
            document.addEventListener('mousemove', function(e) {
                if (!isDragging) return;
                const maxY = window.innerHeight - modal.offsetHeight;
                
                let newY = e.clientY - dragOffset.y;
                
                // Giới hạn trong viewport (chỉ di chuyển theo chiều dọc)
                newY = Math.max(0, Math.min(newY, maxY));
                
                modal.style.top = newY + 'px';
                modal.style.left = '0';
                modal.style.right = '0';
                
                // Điều chỉnh bản đồ theo vị trí panel
                adjustMapSizeForPanel();
            });
            
            document.addEventListener('mouseup', function() {
                if (isDragging) {
                    isDragging = false;
                    const modal = document.getElementById('addCongModal');
                    modal.style.cursor = '';
                    adjustMapSizeForPanel();
                }
            });
            
            // Lắng nghe khi user kéo góc để resize panel
            let resizeTimer;
            modal.addEventListener('mouseup', function() {
                clearTimeout(resizeTimer);
                resizeTimer = setTimeout(adjustMapSizeForPanel, 100);
            });
        }

        // Bật/tắt chế độ vẽ điểm trên bản đồ
        // Lưu ý: drawHandler và isDrawingMode đã được khai báo ở trên (dòng 220-221)
        function toggleDrawMode() {
            if (!canEdit) {
                alert('Bạn không có quyền vẽ điểm! Chỉ Admin mới có quyền này.');
                return;
            }
            
            // Kiểm tra Leaflet.draw đã load chưa
            if (typeof L === 'undefined' || typeof L.Draw === 'undefined' || typeof L.Draw.Marker === 'undefined') {
                alert('Leaflet.draw chưa được load. Vui lòng refresh trang.');
                return;
            }
            
            if (isDrawingMode) {
                // Tắt chế độ vẽ
                if (drawHandler) {
                    drawHandler.disable();
                    drawHandler = null;
                }
                isDrawingMode = false;
                document.getElementById('drawModeText').textContent = '🎯 Bật vẽ điểm';
                document.getElementById('drawModeText').style.color = '#DC2626';
                document.getElementById('drawModeText').style.fontWeight = 'bold';
                document.getElementById('toggleDrawMode').classList.remove('bg-red-600');
                document.getElementById('toggleDrawMode').classList.add('bg-blue-600');
                document.getElementById('toggleDrawMode').style.backgroundColor = '#2563EB';
                document.getElementById('toggleDrawMode').style.borderColor = '#93C5FD';
                
                // Ẩn hướng dẫn
                const hintEl = document.getElementById('drawModeHint');
                if (hintEl) {
                    hintEl.classList.add('hidden');
                }
                
                // Khôi phục cursor mặc định
                map.getContainer().style.cursor = '';
                
                console.log('🔴 Đã tắt chế độ vẽ điểm');
            } else {
                // Bật chế độ vẽ
                if (typeof L.Draw !== 'undefined' && typeof L.Draw.Marker !== 'undefined') {
                    // Đơn giản hóa: dùng cấu hình mặc định của Leaflet.draw
                    drawHandler = new L.Draw.Marker(map);
                    drawHandler.enable();
                    isDrawingMode = true;
                    document.getElementById('drawModeText').textContent = '⏸️ Tắt vẽ điểm';
                    document.getElementById('drawModeText').style.color = '#DC2626';
                    document.getElementById('drawModeText').style.fontWeight = 'bold';
                    document.getElementById('toggleDrawMode').classList.remove('bg-blue-600');
                    document.getElementById('toggleDrawMode').classList.add('bg-red-600');
                    document.getElementById('toggleDrawMode').style.backgroundColor = '#DC2626';
                    document.getElementById('toggleDrawMode').style.borderColor = '#FCA5A5';
                    
                    // Hiển thị hướng dẫn
                    const hintEl = document.getElementById('drawModeHint');
                    if (hintEl) {
                        hintEl.classList.remove('hidden');
                        hintEl.innerHTML = '<strong style="color: #92400E !important;">✏️ Đang ở chế độ vẽ:</strong> <span style="color: #78350F !important;">Click vào vị trí muốn thêm cống trên bản đồ. Nhấn ESC để hủy.</span>';
                    }
                    
                    // Thay đổi cursor khi đang ở chế độ vẽ
                    map.getContainer().style.cursor = 'crosshair';
                    
                    console.log('🟢 Đã bật chế độ vẽ điểm - Click vào bản đồ để vẽ');
                    
                    // Lắng nghe sự kiện khi vẽ xong (nếu chưa có)
                    map.off(L.Draw.Event.CREATED);
                    map.on(L.Draw.Event.CREATED, function(e) {
                        const layer = e.layer;
                        const latlng = layer.getLatLng();
                        
                        console.log('🎨 Đã vẽ điểm tại:', latlng);
                        
                        // Tắt chế độ vẽ sau khi vẽ xong
                        if (drawHandler) {
                            drawHandler.disable();
                            drawHandler = null;
                        }
                        isDrawingMode = false;
                        document.getElementById('drawModeText').textContent = '🎯 Bật vẽ điểm';
                        document.getElementById('drawModeText').style.color = '#DC2626';
                        document.getElementById('drawModeText').style.fontWeight = 'bold';
                        document.getElementById('toggleDrawMode').classList.remove('bg-red-600');
                        document.getElementById('toggleDrawMode').classList.add('bg-blue-600');
                        document.getElementById('toggleDrawMode').style.backgroundColor = '#2563EB';
                        document.getElementById('toggleDrawMode').style.borderColor = '#93C5FD';
                        
                        // Ẩn hướng dẫn
                        const hintEl2 = document.getElementById('drawModeHint');
                        if (hintEl2) {
                            hintEl2.classList.add('hidden');
                        }
                        
                        // Khôi phục cursor mặc định
                        map.getContainer().style.cursor = '';
                        
                        // Lấy tọa độ từ điểm vừa vẽ
                        const x = latlng.lng; // Kinh độ
                        const y = latlng.lat; // Vĩ độ
                        
                        console.log('📍 Đã vẽ điểm tại:', { x, y });
                        
                        // Xóa marker tạm thời ngay lập tức
                        map.removeLayer(layer);
                        
                        // Tự động chuyển SANG TRANG QUẢN LÝ với tọa độ đã setup
                        // KHÔNG mở modal trên trang map nữa
                        const url = `/cong/manage?add=1&x=${x.toFixed(6)}&y=${y.toFixed(6)}`;
                        console.log('🔄 Chuyển sang trang quản lý:', url);
                        console.log('📍 Tọa độ sẽ được điền tự động:', { x: x.toFixed(6), y: y.toFixed(6) });
                        
                        // Chuyển trang ngay lập tức
                        window.location.href = url;
                    });
                } else {
                    alert('Leaflet.draw chưa được load đúng. Vui lòng refresh trang.');
                }
            }
        }

        // Preview file trước khi upload
        document.addEventListener('DOMContentLoaded', function() {
            const fileInput = document.getElementById('file');
            if (fileInput) {
                fileInput.addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    const preview = document.getElementById('filePreview');
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

        // Submit form thêm mới cống
        function submitAddCongForm(e) {
            e.preventDefault();
            
            const form = document.getElementById('addCongForm');
            const formData = new FormData(form);
            
            // Validate tọa độ
            const x = formData.get('toa_do_x');
            const y = formData.get('toa_do_y');
            if (!x || !y) {
                alert('Vui lòng click trên bản đồ để chọn vị trí hoặc nhập tọa độ!');
                return;
            }

            // Show loading
            const submitBtn = document.getElementById('submitAddBtn');
            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = 'Đang lưu...';

            fetch('/api/cong', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                credentials: 'same-origin',
                body: formData
            })
            .then(r => r.json())
            .then(data => {
                if (data.status === 'success') {
                    console.log('✅ Response từ server:', data);
                    
                    // Đóng modal form
                    closeAddCongModal();
                    
                    // Đánh dấu cống mới để hiển thị màu xanh dương - SỬA: Ưu tiên ogc_fid
                    const newCongId = data.ogc_fid || data.id;
                    console.log('🔍 ID cống mới:', newCongId, 'từ data:', data);
                    
                    if (newCongId) {
                        markCongAsNew(newCongId);
                        console.log('✅ Đã đánh dấu cống mới với ID:', newCongId);
                        // Reload lại biến newCongIds từ sessionStorage
                        newCongIds = JSON.parse(sessionStorage.getItem('newCongIds') || '[]');
                        console.log('📋 Danh sách cống mới sau khi đánh dấu:', newCongIds);
                    } else {
                        console.error('❌ Không có ID cống mới trong response:', data);
                    }
                    
                    // Xóa cache popup
                    clearPopupCache();
                    
                    // Xóa layer cũ và reload để hiển thị cống mới với màu xanh dương
                    if (congLayer) {
                        map.removeLayer(congLayer);
                        congLayer = null;
                    }
                    
                    // Lưu tọa độ để zoom đến sau khi reload
                    const savedX = parseFloat(formData.get('toa_do_x'));
                    const savedY = parseFloat(formData.get('toa_do_y'));
                    
                    // Đợi để đảm bảo database đã lưu xong và geometry được tạo
                    // SỬA: Kiểm tra response có has_geometry không
                    if (data.has_geometry === false) {
                        console.warn('⚠️ Server báo geometry chưa được tạo, đợi lâu hơn...');
                        setTimeout(() => {
                            console.log('🔄 Bắt đầu reload dữ liệu sau 3 giây (geometry chưa sẵn sàng)...');
                            loadCongData().then(() => {
                                // Zoom đến vị trí cống mới sau khi reload
                                if (savedX && savedY) {
                                    map.setView([savedY, savedX], 16);
                                    console.log('📍 Đã zoom đến vị trí cống mới:', { x: savedX, y: savedY });
                                }
                            });
                        }, 3000);
                    } else {
                        setTimeout(() => {
                            console.log('🔄 Bắt đầu reload dữ liệu sau 1.5 giây...');
                            loadCongData().then(() => {
                                // Zoom đến vị trí cống mới sau khi reload
                                if (savedX && savedY) {
                                    map.setView([savedY, savedX], 16);
                                    console.log('📍 Đã zoom đến vị trí cống mới:', { x: savedX, y: savedY });
                                }
                            });
                        }, 1500);
                    }
                    
                    // Thông báo thành công
                    alert('✅ Đã thêm cống thành công! Bản đồ sẽ tự động cập nhật và zoom đến vị trí cống mới.');
                } else {
                    alert('Lỗi: ' + (data.message || JSON.stringify(data)));
                }
            })
            .catch(err => {
                console.error('Error:', err);
                alert('Lỗi khi thêm cống: ' + err.message);
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            });
        }
    </script>

    <!-- Panel thêm mới cống (phía trên bản đồ, có thể kéo thả và resize) -->
    <div id="addCongModal" class="hidden fixed top-0 left-0 w-full bg-white shadow-2xl z-[5000] border-b border-gray-300" 
         style="height: 500px; min-height: 300px; max-height: 90vh; resize: vertical; overflow: auto; pointer-events: auto;">
        <div class="h-full flex flex-col">
            <!-- Header có thể kéo -->
            <div id="addCongModalHeader" class="flex justify-between items-center p-3 border-b border-gray-300 bg-gray-50 cursor-move" 
                 style="user-select: none;">
                <h3 class="text-lg font-semibold text-gray-900">Thêm cống mới</h3>
                <div class="flex items-center gap-2">
                    <button onclick="toggleAddCongModalMinimize()" class="text-gray-400 hover:text-gray-600" title="Thu nhỏ">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                        </svg>
                    </button>
                    <button onclick="closeAddCongModal()" class="text-gray-400 hover:text-gray-600" title="Đóng">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
            
            <!-- Content (scrollable) -->
            <div id="addCongModalContent" class="flex-1 overflow-y-auto p-4">
                
                <form id="addCongForm" onsubmit="submitAddCongForm(event)">
                    <div class="space-y-4">
                        <!-- Tọa độ -->
                        <div class="bg-blue-50 p-3 rounded-lg border-2 border-blue-200">
                            <p class="text-sm text-blue-800 mb-2 font-semibold">
                                <strong>📍 QUAN TRỌNG:</strong> CHỈ CẦN TỌA ĐỘ X, Y là đủ! Không cần ID hay tên cống.
                            </p>
                            <p class="text-xs text-blue-600 mb-3">
                                💡 Click trên bản đồ để chọn vị trí, hoặc nhập tọa độ thủ công
                            </p>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Tọa độ X (Kinh độ) *</label>
                                    <input type="number" step="any" id="toa_do_x" name="toa_do_x" required
                                           class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                           placeholder="VD: 106.660172">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Tọa độ Y (Vĩ độ) *</label>
                                    <input type="number" step="any" id="toa_do_y" name="toa_do_y" required
                                           class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                           placeholder="VD: 10.762622">
                                </div>
                            </div>
                        </div>

                        <!-- Thông tin cơ bản -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Mã số (tùy chọn)</label>
                                <input type="text" name="id" maxlength="254"
                                       class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                       placeholder="Không bắt buộc">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tên công đập (tùy chọn)</label>
                                <input type="text" name="ten_cong_dap" maxlength="254"
                                       class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                       placeholder="Không bắt buộc">
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Lý trình</label>
                                <input type="text" name="ly_trinh" maxlength="254"
                                       class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Địa điểm</label>
                                <input type="text" name="dia_diem" maxlength="254"
                                       class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                        </div>

                        <!-- Cấp công trình, Chiều dài, Đường kính -->
                        <div class="grid grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Cấp công trình</label>
                                <input type="text" name="cap_cong_tri" maxlength="254"
                                       placeholder="VD: I, II"
                                       class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Chiều dài (m)</label>
                                <input type="number" step="1" name="chieu_dai"
                                       class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                       placeholder="VD: 100">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Đường kính (cm)</label>
                                <input type="number" step="1" name="duong_kinh"
                                       class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                       placeholder="VD: 50">
                            </div>
                        </div>

                        <!-- Hình thức, Bề rộng, Chiều cao -->
                        <div class="grid grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Hình thức</label>
                                <input type="text" name="hinh_thuc" maxlength="254"
                                       placeholder="VD: Cống hộp, Cống tròn"
                                       class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Bề rộng</label>
                                <input type="number" step="any" name="be_rong"
                                       class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                       placeholder="VD: 2.5">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Chiều cao</label>
                                <input type="number" step="any" name="chieu_cao"
                                       class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                       placeholder="VD: 3.0">
                            </div>
                        </div>

                        <!-- Mục tiêu, Gói thầu, Đơn vị quản lý -->
                        <div class="grid grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Mục tiêu</label>
                                <input type="text" name="muc_tieu_nhi" maxlength="254"
                                       class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Gói thầu</label>
                                <input type="text" name="goi_thau" maxlength="254"
                                       class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Đơn vị quản lý</label>
                                <input type="text" name="don_vi_quan_l" maxlength="254"
                                       class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                        </div>

                        <!-- Upload file đính kèm -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                📎 File đính kèm (ảnh hoặc PDF, tối đa 5MB)
                            </label>
                            <input type="file" id="file" name="file" accept="image/*,.pdf"
                                   class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <p class="text-xs text-gray-500 mt-1">Chấp nhận: JPG, PNG, PDF</p>
                            <div id="filePreview"></div>
                        </div>

                        <!-- Ghi chú -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Ghi chú</label>
                            <textarea name="ghi_chu" rows="3"
                                      class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end gap-3">
                        <button type="button" onclick="closeAddCongModal()" 
                                class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">
                            Hủy
                        </button>
                        <button type="submit" id="submitAddBtn"
                                class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                            Thêm cống
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal sửa cống (chỉ admin) - Fullscreen để che bản đồ -->
    <div id="editCongModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-90 overflow-y-auto h-full w-full z-[9999]">
        <div class="relative top-5 mx-auto p-5 border w-11/12 md:w-2/3 lg:w-1/2 xl:w-2/5 shadow-2xl rounded-lg bg-white max-h-[98vh] overflow-y-auto">
            <div class="mt-3">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Sửa cống</h3>
                    <button onclick="closeEditCongModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                
                <form id="editCongForm" onsubmit="submitEditCongForm(event)">
                    <input type="hidden" id="edit_cong_id" name="cong_id">
                    
                    <div class="space-y-4">
                        <!-- Tọa độ -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tọa độ X (Kinh độ) *</label>
                                <input type="number" step="any" id="edit_map_toa_do_x" name="toa_do_x" required
                                       class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tọa độ Y (Vĩ độ) *</label>
                                <input type="number" step="any" id="edit_map_toa_do_y" name="toa_do_y" required
                                       class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                        </div>

                        <!-- Thông tin cơ bản -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Mã số</label>
                                <input type="text" id="edit_map_id" name="id" maxlength="254"
                                       class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tên công đập *</label>
                                <input type="text" id="edit_map_ten_cong_dap" name="ten_cong_dap" maxlength="254" required
                                       class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Lý trình</label>
                                <input type="text" id="edit_map_ly_trinh" name="ly_trinh" maxlength="254"
                                       class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Địa điểm</label>
                                <input type="text" id="edit_map_dia_diem" name="dia_diem" maxlength="254"
                                       class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                        </div>

                        <!-- Cấp công trình, Chiều dài, Đường kính -->
                        <div class="grid grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Cấp công trình</label>
                                <input type="text" id="edit_map_cap_cong_tri" name="cap_cong_tri" maxlength="254"
                                       placeholder="VD: I, II"
                                       class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Chiều dài (m)</label>
                                <input type="number" step="1" id="edit_map_chieu_dai" name="chieu_dai"
                                       class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                       placeholder="VD: 100">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Đường kính (cm)</label>
                                <input type="number" step="1" id="edit_map_duong_kinh" name="duong_kinh"
                                       class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                       placeholder="VD: 50">
                            </div>
                        </div>

                        <!-- Hình thức, Bề rộng, Chiều cao -->
                        <div class="grid grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Hình thức</label>
                                <input type="text" id="edit_map_hinh_thuc" name="hinh_thuc" maxlength="254"
                                       placeholder="VD: Cống hộp, Cống tròn"
                                       class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Bề rộng</label>
                                <input type="number" step="any" id="edit_map_be_rong" name="be_rong"
                                       class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                       placeholder="VD: 2.5">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Chiều cao</label>
                                <input type="number" step="any" id="edit_map_chieu_cao" name="chieu_cao"
                                       class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                       placeholder="VD: 3.0">
                            </div>
                        </div>

                        <!-- Mục tiêu, Gói thầu, Đơn vị quản lý -->
                        <div class="grid grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Mục tiêu</label>
                                <input type="text" id="edit_map_muc_tieu_nhi" name="muc_tieu_nhi" maxlength="254"
                                       class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Gói thầu</label>
                                <input type="text" id="edit_map_goi_thau" name="goi_thau" maxlength="254"
                                       class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Đơn vị quản lý</label>
                                <input type="text" id="edit_map_don_vi_quan_l" name="don_vi_quan_l" maxlength="254"
                                       class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                        </div>

                        <!-- Upload file đính kèm -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                📎 File đính kèm (ảnh hoặc PDF, tối đa 5MB)
                            </label>
                            <input type="file" id="edit_map_file" name="file" accept="image/*,.pdf"
                                   class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <p class="text-xs text-gray-500 mt-1">Chấp nhận: JPG, PNG, PDF. Để trống nếu không muốn thay đổi file hiện tại.</p>
                            <div id="edit_map_filePreview"></div>
                            <div id="edit_map_current_file" class="mt-2"></div>
                        </div>

                        <!-- Ghi chú -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Ghi chú</label>
                            <textarea id="edit_map_ghi_chu" name="ghi_chu" rows="3"
                                      class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end gap-3">
                        <button type="button" onclick="closeEditCongModal()" 
                                class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">
                            Hủy
                        </button>
                        <button type="submit" id="submitEditBtn"
                                class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                            Cập nhật
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Preview file khi chọn (cho modal sửa)
        document.addEventListener('DOMContentLoaded', function() {
            const editFileInput = document.getElementById('edit_map_file');
            if (editFileInput) {
                editFileInput.addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    const preview = document.getElementById('edit_map_filePreview');
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

        function closeEditCongModal() {
            document.getElementById('editCongModal').classList.add('hidden');
        }

        // Submit form sửa cống
        function submitEditCongForm(e) {
            e.preventDefault();
            
            const form = document.getElementById('editCongForm');
            const formData = new FormData(form);
            const congId = document.getElementById('edit_cong_id').value;
            
            // Debug: Log tất cả dữ liệu form
            console.log('📤 Dữ liệu form trước khi gửi:');
            for (let [key, value] of formData.entries()) {
                console.log(`  ${key}: ${value}`);
            }
            
            const submitBtn = document.getElementById('submitEditBtn');
            const originalText = submitBtn.textContent;
            submitBtn.disabled = true;
            submitBtn.textContent = 'Đang cập nhật...';

            fetch(`/api/cong/${congId}`, {
                method: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    // Không set Content-Type để browser tự set với boundary cho FormData
                },
                credentials: 'same-origin',
                body: formData
            })
            .then(r => {
                if (!r.ok) {
                    return r.json().then(err => { throw err; });
                }
                return r.json();
            })
            .then(data => {
                if (data.status === 'success') {
                    alert('Đã cập nhật thành công!');
                    closeEditCongModal();
                    // Đóng tất cả popup trên bản đồ để tránh hiển thị dữ liệu cũ
                    map.closePopup();
                    // Xóa cache popup cho ID cụ thể và toàn bộ
                    const updatedCongId = data.id || data.ogc_fid || congId;
                    clearPopupCacheById(updatedCongId);
                    clearPopupCache(); // Xóa toàn bộ cache để đảm bảo
                    console.log('✅ Đã xóa cache popup cho cống ID:', updatedCongId);
                    
                    // SỬA: Xóa ID cống vừa sửa khỏi danh sách cống mới (vì nó không phải cống mới nữa)
                    let currentNewCongIds = JSON.parse(sessionStorage.getItem('newCongIds') || '[]');
                    const updatedCongIdStr = String(updatedCongId);
                    const filteredIds = currentNewCongIds.filter(id => String(id) !== updatedCongIdStr);
                    sessionStorage.setItem('newCongIds', JSON.stringify(filteredIds));
                    console.log('🔧 Đã xóa cống ID', updatedCongId, 'khỏi danh sách cống mới. Danh sách còn lại:', filteredIds);
                    
                    // Xóa layer cũ trước
                    if (congLayer) {
                        map.removeLayer(congLayer);
                        congLayer = null;
                    }
                    // Đợi lâu hơn để đảm bảo database đã cập nhật xong
                    setTimeout(() => {
                        loadCongData();
                    }, 800);
                } else {
                    alert('Lỗi: ' + (data.message || JSON.stringify(data)));
                }
            })
            .catch(err => {
                console.error('Error:', err);
                alert('Lỗi khi cập nhật: ' + err.message);
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.textContent = originalText;
            });
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const editModal = document.getElementById('editCongModal');
            if (event.target == editModal) {
                closeEditCongModal();
            }
        }

        // ============================================
        // PHẦN 4.5: TÌM KIẾM VÀ TRA CỨU CỐNG
        // ============================================
        
        // Biến toàn cục cho tìm kiếm
        let searchDebounceTimer = null;
        let searchResults = [];
        let originalMarkerStates = new Map(); // Lưu trạng thái marker gốc để restore
        let searchHistory = JSON.parse(localStorage.getItem('searchHistory') || '[]');
        let currentSuggestionIndex = -1;
        let allCongData = []; // Lưu tất cả dữ liệu cống để tìm kiếm
        
        // Hàm chuyển đổi tiếng Việt có dấu sang không dấu
        function removeVietnameseTones(str) {
            if (!str) return '';
            str = str.toLowerCase();
            str = str.replace(/à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ/g, 'a');
            str = str.replace(/è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ/g, 'e');
            str = str.replace(/ì|í|ị|ỉ|ĩ/g, 'i');
            str = str.replace(/ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ/g, 'o');
            str = str.replace(/ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ/g, 'u');
            str = str.replace(/ỳ|ý|ỵ|ỷ|ỹ/g, 'y');
            str = str.replace(/đ/g, 'd');
            return str;
        }
        
        // Hàm so sánh text (hỗ trợ không dấu)
        function matchText(text, query) {
            if (!text || !query) return false;
            const textLower = removeVietnameseTones(String(text));
            const queryLower = removeVietnameseTones(String(query));
            return textLower.includes(queryLower);
        }
        
        // Hàm parse tọa độ từ input
        function parseCoordinates(input) {
            if (!input) return null;
            const cleaned = input.trim();
            // Format: "106.808739, 10.868036" hoặc "106.808739 10.868036" hoặc "X: 106.808739 Y: 10.868036"
            const patterns = [
                /(-?\d+\.?\d*)\s*,\s*(-?\d+\.?\d*)/, // Có dấu phẩy
                /(-?\d+\.?\d*)\s+(-?\d+\.?\d*)/, // Có khoảng trắng
                /[xX]:\s*(-?\d+\.?\d*)\s*[yY]:\s*(-?\d+\.?\d*)/i // Có prefix X: Y:
            ];
            
            for (const pattern of patterns) {
                const match = cleaned.match(pattern);
                if (match) {
                    const x = parseFloat(match[1]);
                    const y = parseFloat(match[2]);
                    // Validate tọa độ Việt Nam (khoảng chừng)
                    if (x >= 102 && x <= 110 && y >= 8 && y <= 24) {
                        return { x, y };
                    }
                }
            }
            return null;
        }
        
        // Hàm tính khoảng cách giữa 2 điểm (Haversine formula đơn giản)
        function calculateDistance(lat1, lon1, lat2, lon2) {
            const R = 6371; // Bán kính Trái Đất (km)
            const dLat = (lat2 - lat1) * Math.PI / 180;
            const dLon = (lon2 - lon1) * Math.PI / 180;
            const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                Math.sin(dLon / 2) * Math.sin(dLon / 2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
            return R * c; // Khoảng cách tính bằng km
        }
        
        // Hàm tìm kiếm cống
        function searchCong(query, filters = {}) {
            if (!congLayer) {
                console.warn('⚠️ Không có dữ liệu cống để tìm kiếm');
                return [];
            }
            
            let results = [];
            const queryLower = query ? query.trim().toLowerCase() : '';
            const coords = query ? parseCoordinates(query) : null;
            
            // Nếu là tìm kiếm theo tọa độ
            if (coords) {
                console.log('🗺️ Tìm kiếm theo tọa độ:', coords);
                const distances = [];
                congLayer.eachLayer(function(layer) {
                    if (layer.feature && layer.feature.geometry && layer.feature.geometry.type === 'Point') {
                        const coords_layer = layer.feature.geometry.coordinates;
                        const distance = calculateDistance(coords.y, coords.x, coords_layer[1], coords_layer[0]);
                        distances.push({
                            layer: layer,
                            feature: layer.feature,
                            distance: distance
                        });
                    }
                });
                // Sắp xếp theo khoảng cách (gần nhất trước)
                distances.sort((a, b) => a.distance - b.distance);
                // Lấy 10 cống gần nhất
                results = distances.slice(0, 10).map(item => ({
                    ...item,
                    distanceKm: item.distance
                }));
            } else if (queryLower) {
                // Tìm kiếm text
                congLayer.eachLayer(function(layer) {
                    if (!layer.feature || !layer.feature.properties) return;
                    
                    const props = layer.feature.properties;
                    let matches = false;
                    
                    // Kiểm tra text search
                    if (queryLower) {
                        // Tự động nhận diện loại tìm kiếm
                        const isNumber = /^\d+$/.test(queryLower);
                        
                        if (isNumber) {
                            // Tìm theo mã số
                            const id = String(props.id || props.ogc_fid || '').toLowerCase();
                            matches = id.includes(queryLower);
                        } else {
                            // Tìm trong nhiều trường
                            matches = matchText(props.ten_cong_dap, queryLower) ||
                                     matchText(props.id, queryLower) ||
                                     matchText(props.dia_diem, queryLower) ||
                                     matchText(props.ly_trinh, queryLower) ||
                                     matchText(props.ghi_chu, queryLower);
                        }
                    }
                    
                    // Kiểm tra bộ lọc
                    if (matches && filters.area) {
                        matches = matchText(props.dia_diem, filters.area);
                    }
                    if (matches && filters.type) {
                        matches = String(props.loai_cong_tr || '').trim() === String(filters.type).trim();
                    }
                    if (matches && filters.level) {
                        matches = String(props.cap_cong_tri || '').trim() === String(filters.level).trim();
                    }
                    
                    if (matches) {
                        results.push({
                            layer: layer,
                            feature: layer.feature,
                            props: props
                        });
                    }
                });
            } else {
                // Chỉ có bộ lọc, không có text search
                congLayer.eachLayer(function(layer) {
                    if (!layer.feature || !layer.feature.properties) return;
                    
                    const props = layer.feature.properties;
                    let matches = true;
                    
                    // Kiểm tra bộ lọc
                    if (filters.area) {
                        matches = matchText(props.dia_diem, filters.area);
                    }
                    if (matches && filters.type) {
                        matches = String(props.loai_cong_tr || '').trim() === String(filters.type).trim();
                    }
                    if (matches && filters.level) {
                        matches = String(props.cap_cong_tri || '').trim() === String(filters.level).trim();
                    }
                    
                    if (matches) {
                        results.push({
                            layer: layer,
                            feature: layer.feature,
                            props: props
                        });
                    }
                });
            }
            
            return results;
        }
        
        // Hàm highlight marker
        function highlightMarkers(results) {
            if (!congLayer) return;
            
            // Lưu trạng thái marker gốc
            originalMarkerStates.clear();
            
            // Làm mờ tất cả marker
            congLayer.eachLayer(function(layer) {
                if (layer.setOpacity) {
                    // Lưu icon hiện tại (nếu có)
                    const currentIcon = layer.options.icon || (layer._icon ? layer._icon : null);
                    originalMarkerStates.set(layer, {
                        opacity: layer.options.opacity || 1,
                        icon: currentIcon
                    });
                    layer.setOpacity(0.3);
                }
            });
            
            // Highlight các marker kết quả
            results.forEach((result, index) => {
                const layer = result.layer;
                if (layer.setIcon && layer.feature.geometry.type === 'Point') {
                    // Tạo icon vàng/cam cho kết quả
                    const highlightIcon = L.icon({
                        iconUrl: '{{ asset("images/marker-icon-yellow.png") }}',
                        shadowUrl: '{{ asset("images/marker-shadow.png") }}',
                        iconSize: [25, 41],
                        iconAnchor: [12, 41],
                        popupAnchor: [1, -34]
                    });
                    layer.setIcon(highlightIcon);
                    layer.setOpacity(1);
                    
                    // Thêm animation pulse
                    if (layer._icon) {
                        layer._icon.style.animation = 'pulse 2s infinite';
                        layer._icon.style.boxShadow = '0 0 0 4px rgba(245, 158, 11, 0.5)';
                    }
                } else if (layer.setStyle) {
                    // Highlight đường
                    layer.setStyle({
                        weight: 5,
                        color: '#F59E0B',
                        opacity: 1
                    });
                }
            });
        }
        
        // Hàm restore marker về trạng thái gốc
        function restoreMarkers() {
            if (!congLayer) return;
            
            congLayer.eachLayer(function(layer) {
                if (originalMarkerStates.has(layer)) {
                    const original = originalMarkerStates.get(layer);
                    if (layer.setOpacity) {
                        layer.setOpacity(original.opacity);
                    }
                    if (layer.setIcon && original.icon) {
                        layer.setIcon(original.icon);
                    }
                    if (layer._icon) {
                        layer._icon.style.animation = '';
                        layer._icon.style.boxShadow = '';
                    }
                } else {
                    // Restore về mặc định
                    if (layer.setOpacity) {
                        layer.setOpacity(1);
                    }
                    if (layer.setStyle) {
                        congLayer.resetStyle(layer);
                    }
                }
            });
            originalMarkerStates.clear();
        }
        
        // Hàm hiển thị kết quả tìm kiếm
        function displaySearchResults(results, query = '') {
            const panel = document.getElementById('search-results-panel');
            const countEl = document.getElementById('search-results-count');
            const listEl = document.getElementById('search-results-list');
            
            if (!panel || !countEl || !listEl) return;
            
            if (results.length === 0) {
                panel.classList.remove('hidden');
                countEl.textContent = '❌ Không tìm thấy';
                listEl.innerHTML = `
                    <div class="px-3 py-6 text-center">
                        <div class="text-gray-400 mb-2">🔍</div>
                        <p class="text-xs text-gray-600 mb-1">Thử từ khóa khác</p>
                        <p class="text-xs text-gray-500">Tên, mã số, hoặc địa điểm</p>
                    </div>
                `;
                restoreMarkers();
                return;
            }
            
            panel.classList.remove('hidden');
            countEl.textContent = `✅ ${results.length} kết quả`;
            
            let html = '';
            results.forEach((result, index) => {
                const props = result.props || result.feature.properties;
                const tenCong = props.ten_cong_dap || 'N/A';
                const maSo = props.id || props.ogc_fid || 'N/A';
                const diaDiem = props.dia_diem || 'N/A';
                const distance = result.distanceKm ? 
                    (result.distanceKm < 1 ? `${(result.distanceKm * 1000).toFixed(0)}m` : `${result.distanceKm.toFixed(2)}km`) : 
                    '';
                
                html += `
                    <div class="px-3 py-2.5 border-b border-gray-100 hover:bg-blue-50 cursor-pointer transition-colors group" 
                         onclick="zoomToCong(${index})"
                         onmouseenter="highlightResult(${index})"
                         onmouseleave="unhighlightResult(${index})">
                        <div class="flex items-start gap-2.5">
                            <div class="text-lg mt-0.5">📍</div>
                            <div class="flex-1 min-w-0">
                                <div class="font-semibold text-blue-600 text-xs mb-0.5 truncate group-hover:text-blue-700">${tenCong}</div>
                                <div class="text-xs text-gray-500 mb-0.5">Mã: ${maSo}</div>
                                <div class="text-xs text-gray-600 truncate">${diaDiem}</div>
                                ${distance ? `<div class="text-xs text-orange-600 font-medium mt-1 inline-flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    ${distance}
                                </div>` : ''}
                            </div>
                        </div>
                    </div>
                `;
            });
            
            listEl.innerHTML = html;
            
            // Highlight markers
            highlightMarkers(results);
            
            // Zoom để hiển thị tất cả kết quả
            if (results.length > 0) {
                const bounds = L.latLngBounds(
                    results.map(r => {
                        const coords = r.feature.geometry.coordinates;
                        return [coords[1], coords[0]];
                    })
                );
                map.fitBounds(bounds, { padding: [50, 50], maxZoom: 15 });
                
                // Nếu chỉ có 1 kết quả, mở popup
                if (results.length === 1) {
                    setTimeout(() => {
                        results[0].layer.openPopup();
                    }, 500);
                }
            }
        }
        
        // Hàm zoom đến cống
        function zoomToCong(index) {
            if (searchResults[index]) {
                const result = searchResults[index];
                const coords = result.feature.geometry.coordinates;
                map.setView([coords[1], coords[0]], 16);
                setTimeout(() => {
                    result.layer.openPopup();
                }, 300);
            }
        }
        
        // Hàm highlight result khi hover
        function highlightResult(index) {
            if (searchResults[index]) {
                const layer = searchResults[index].layer;
                if (layer._icon) {
                    layer._icon.style.transform = 'scale(1.2)';
                    layer._icon.style.transition = 'transform 0.2s';
                }
            }
        }
        
        // Hàm unhighlight result
        function unhighlightResult(index) {
            if (searchResults[index]) {
                const layer = searchResults[index].layer;
                if (layer._icon) {
                    layer._icon.style.transform = 'scale(1)';
                }
            }
        }
        
        // Hàm thực hiện tìm kiếm
        function performSearch() {
            const query = document.getElementById('search-input').value.trim();
            const filterArea = document.getElementById('filter-area').value;
            const filterType = document.getElementById('filter-type').value;
            const filterLevel = document.getElementById('filter-level').value;
            
            const filters = {
                area: filterArea,
                type: filterType,
                level: filterLevel
            };
            
            // Hiển thị loading
            const loadingEl = document.getElementById('search-loading');
            if (loadingEl) {
                loadingEl.classList.remove('hidden');
            }
            
            // Thực hiện tìm kiếm
            setTimeout(() => {
                searchResults = searchCong(query, filters);
                displaySearchResults(searchResults, query);
                
                // Ẩn loading
                if (loadingEl) {
                    loadingEl.classList.add('hidden');
                }
                
                // Lưu vào lịch sử nếu có kết quả
                if (query && searchResults.length > 0) {
                    addToSearchHistory(query);
                }
            }, 100);
        }
        
        // Hàm xóa tìm kiếm
        function clearSearch() {
            document.getElementById('search-input').value = '';
            document.getElementById('search-results-panel').classList.add('hidden');
            document.getElementById('search-clear-btn').classList.add('hidden');
            document.getElementById('search-suggestions').classList.add('hidden');
            restoreMarkers();
            searchResults = [];
            
            // Reset zoom về toàn bộ cống
            if (congLayer) {
                try {
                    map.fitBounds(congLayer.getBounds(), { padding: [50, 50], maxZoom: 15 });
                } catch (e) {
                    console.log('Could not fit bounds:', e);
                }
            }
        }
        
        // Hàm xóa bộ lọc
        function clearFilters() {
            document.getElementById('filter-area').value = '';
            document.getElementById('filter-type').value = '';
            document.getElementById('filter-level').value = '';
            document.getElementById('clear-filters-btn').classList.add('hidden');
            performSearch();
        }
        
        // Hàm hiển thị modal hướng dẫn
        function showSearchHelpModal() {
            document.getElementById('search-help-modal').classList.remove('hidden');
        }
        
        // Hàm đóng modal hướng dẫn
        function closeSearchHelpModal() {
            document.getElementById('search-help-modal').classList.add('hidden');
        }
        
        // Hàm toggle panel Tìm kiếm & Bộ lọc
        function toggleFilterPanel() {
            const panel = document.getElementById('filter-panel');
            const arrow = document.getElementById('filter-arrow');
            const btn = document.getElementById('search-filter-btn');
            
            if (panel) {
                const isHidden = panel.classList.contains('hidden');
                panel.classList.toggle('hidden');
                
                if (arrow) {
                    arrow.style.transform = isHidden ? 'rotate(180deg)' : 'rotate(0deg)';
                }
                
                if (btn) {
                    if (isHidden) {
                        btn.classList.add('bg-blue-50', 'border-blue-400');
                        btn.classList.remove('bg-white');
                    } else {
                        btn.classList.remove('bg-blue-50', 'border-blue-400');
                        btn.classList.add('bg-white');
                    }
                }
            }
        }
        
        // Hàm reload dữ liệu bản đồ
        function reloadMapData() {
            // Hiển thị loading
            const loadingEl = document.getElementById('map-loading');
            if (loadingEl) {
                loadingEl.style.display = 'flex';
            }
            
            // Xóa layer cũ
            if (congLayer) {
                map.removeLayer(congLayer);
                congLayer = null;
            }
            
            // Xóa cache
            popupCache.clear();
            clearPopupCache();
            
            // Reload dữ liệu
            loadCongData().then(() => {
                console.log('✅ Đã reload dữ liệu bản đồ');
                // Populate lại bộ lọc
                setTimeout(() => {
                    populateFilters();
                }, 500);
            }).catch(err => {
                console.error('❌ Lỗi khi reload:', err);
                alert('Lỗi khi tải lại dữ liệu: ' + err.message);
            }).finally(() => {
                // Ẩn loading
                if (loadingEl) {
                    loadingEl.style.display = 'none';
                }
            });
        }
        
        // Hàm lấy danh sách gợi ý
        function getSuggestions(query) {
            if (!query || query.length < 1 || !congLayer) return [];
            
            const suggestions = [];
            const queryLower = removeVietnameseTones(query.toLowerCase());
            const seen = new Set();
            
            congLayer.eachLayer(function(layer) {
                if (!layer.feature || !layer.feature.properties) return;
                const props = layer.feature.properties;
                
                // Gợi ý tên cống
                if (props.ten_cong_dap && matchText(props.ten_cong_dap, queryLower)) {
                    const key = `name:${props.ten_cong_dap}`;
                    if (!seen.has(key)) {
                        suggestions.push({ type: 'name', text: props.ten_cong_dap, icon: '📝' });
                        seen.add(key);
                    }
                }
                
                // Gợi ý mã số
                if (props.id && matchText(props.id, queryLower)) {
                    const key = `id:${props.id}`;
                    if (!seen.has(key)) {
                        suggestions.push({ type: 'id', text: props.id, icon: '🔢' });
                        seen.add(key);
                    }
                }
                
                // Gợi ý địa điểm
                if (props.dia_diem && matchText(props.dia_diem, queryLower)) {
                    const key = `area:${props.dia_diem}`;
                    if (!seen.has(key)) {
                        suggestions.push({ type: 'area', text: props.dia_diem, icon: '📍' });
                        seen.add(key);
                    }
                }
            });
            
            return suggestions.slice(0, 8); // Tối đa 8 gợi ý
        }
        
        // Hàm hiển thị gợi ý
        function showSuggestions(query) {
            const suggestionsEl = document.getElementById('search-suggestions');
            if (!suggestionsEl) return;
            
            if (!query || query.length < 1) {
                suggestionsEl.classList.add('hidden');
                return;
            }
            
            const suggestions = getSuggestions(query);
            if (suggestions.length === 0) {
                suggestionsEl.classList.add('hidden');
                return;
            }
            
            let html = '';
            suggestions.forEach((suggestion, index) => {
                html += `
                    <div class="px-4 py-2 hover:bg-blue-50 cursor-pointer flex items-center gap-2 ${index === currentSuggestionIndex ? 'bg-blue-100' : ''}" 
                         onclick="selectSuggestion('${suggestion.text.replace(/'/g, "\\'")}')"
                         onmouseenter="currentSuggestionIndex = ${index}">
                        <span class="text-lg">${suggestion.icon}</span>
                        <span class="text-sm">${highlightMatch(suggestion.text, query)}</span>
                    </div>
                `;
            });
            
            suggestionsEl.innerHTML = html;
            suggestionsEl.classList.remove('hidden');
        }
        
        // Hàm highlight phần text khớp
        function highlightMatch(text, query) {
            const textLower = removeVietnameseTones(text.toLowerCase());
            const queryLower = removeVietnameseTones(query.toLowerCase());
            const index = textLower.indexOf(queryLower);
            
            if (index === -1) return text;
            
            const before = text.substring(0, index);
            const match = text.substring(index, index + query.length);
            const after = text.substring(index + query.length);
            
            return `${before}<mark class="bg-yellow-300">${match}</mark>${after}`;
        }
        
        // Hàm chọn gợi ý
        function selectSuggestion(text) {
            document.getElementById('search-input').value = text;
            document.getElementById('search-suggestions').classList.add('hidden');
            currentSuggestionIndex = -1;
            performSearch();
        }
        
        // Hàm thêm vào lịch sử tìm kiếm
        function addToSearchHistory(query) {
            // Xóa nếu đã tồn tại
            searchHistory = searchHistory.filter(item => item.query !== query);
            // Thêm vào đầu
            searchHistory.unshift({ query, timestamp: Date.now() });
            // Giới hạn 10 mục
            searchHistory = searchHistory.slice(0, 10);
            localStorage.setItem('searchHistory', JSON.stringify(searchHistory));
        }
        
        // Hàm populate bộ lọc từ dữ liệu
        function populateFilters() {
            if (!congLayer) {
                console.warn('⚠️ populateFilters: congLayer chưa có');
                return;
            }
            
            console.log('🔄 Đang populate filters...');
            const areas = new Set();
            const types = new Set();
            const levels = new Set();
            
            let count = 0;
            congLayer.eachLayer(function(layer) {
                if (!layer.feature || !layer.feature.properties) return;
                const props = layer.feature.properties;
                count++;
                
                // Lấy tất cả địa điểm (không filter)
                if (props.dia_diem && props.dia_diem.trim() !== '') {
                    areas.add(props.dia_diem.trim());
                }
                
                // Lấy tất cả loại công trình
                if (props.loai_cong_tr && props.loai_cong_tr.trim() !== '') {
                    types.add(props.loai_cong_tr.trim());
                }
                
                // Lấy tất cả cấp công trình
                if (props.cap_cong_tri && props.cap_cong_tri.trim() !== '') {
                    levels.add(props.cap_cong_tri.trim());
                }
            });
            
            console.log('📊 Đã duyệt', count, 'cống');
            console.log('📍 Khu vực:', Array.from(areas).length, 'giá trị');
            console.log('🏗️ Loại:', Array.from(types).length, 'giá trị');
            console.log('⭐ Cấp:', Array.from(levels).length, 'giá trị');
            
            // Populate dropdowns
            const areaSelect = document.getElementById('filter-area');
            const typeSelect = document.getElementById('filter-type');
            const levelSelect = document.getElementById('filter-level');
            
            if (!areaSelect || !typeSelect || !levelSelect) {
                console.error('❌ Không tìm thấy dropdown elements');
                return;
            }
            
            // Xóa các option cũ (trừ "Tất cả")
            while (areaSelect.children.length > 1) areaSelect.removeChild(areaSelect.lastChild);
            while (typeSelect.children.length > 1) typeSelect.removeChild(typeSelect.lastChild);
            while (levelSelect.children.length > 1) levelSelect.removeChild(levelSelect.lastChild);
            
            // Thêm options cho Khu vực
            Array.from(areas).sort().forEach(area => {
                const option = document.createElement('option');
                option.value = area;
                option.textContent = area;
                areaSelect.appendChild(option);
            });
            
            Array.from(types).sort().forEach(type => {
                const option = document.createElement('option');
                option.value = type;
                option.textContent = type;
                typeSelect.appendChild(option);
            });
            
            Array.from(levels).sort().forEach(level => {
                const option = document.createElement('option');
                option.value = level;
                option.textContent = level;
                levelSelect.appendChild(option);
            });
            
            console.log('✅ Đã populate filters thành công');
        }
        
        // Thêm event listeners cho các dropdown để tự động tìm kiếm khi thay đổi
        document.addEventListener('DOMContentLoaded', function() {
            const areaSelect = document.getElementById('filter-area');
            const typeSelect = document.getElementById('filter-type');
            const levelSelect = document.getElementById('filter-level');
            
            if (areaSelect) {
                areaSelect.addEventListener('change', function() {
                    performSearch();
                    updateClearFiltersButton();
                });
            }
            
            if (typeSelect) {
                typeSelect.addEventListener('change', function() {
                    performSearch();
                    updateClearFiltersButton();
                });
            }
            
            if (levelSelect) {
                levelSelect.addEventListener('change', function() {
                    performSearch();
                    updateClearFiltersButton();
                });
            }
        });
        
        // Hàm hiển thị/ẩn nút "Xóa bộ lọc"
        function updateClearFiltersButton() {
            const areaSelect = document.getElementById('filter-area');
            const typeSelect = document.getElementById('filter-type');
            const levelSelect = document.getElementById('filter-level');
            const clearBtnContainer = document.getElementById('clear-filters-container');
            
            if (!clearBtnContainer) return;
            
            const hasFilter = (areaSelect && areaSelect.value) || 
                            (typeSelect && typeSelect.value) || 
                            (levelSelect && levelSelect.value);
            
            if (hasFilter) {
                clearBtnContainer.classList.remove('hidden');
            } else {
                clearBtnContainer.classList.add('hidden');
            }
        }
        
        // Khởi tạo tìm kiếm sau khi load dữ liệu
        document.addEventListener('DOMContentLoaded', function() {
            // Event listeners cho ô tìm kiếm
            const searchInput = document.getElementById('search-input');
            if (searchInput) {
                searchInput.addEventListener('input', function(e) {
                    const query = e.target.value;
                    
                    // Hiển thị/ẩn nút xóa
                    const clearBtn = document.getElementById('search-clear-btn');
                    if (clearBtn) {
                        if (query) {
                            clearBtn.classList.remove('hidden');
                        } else {
                            clearBtn.classList.add('hidden');
                        }
                    }
                    
                    // Hiển thị gợi ý
                    showSuggestions(query);
                    
                    // Debounce tìm kiếm
                    clearTimeout(searchDebounceTimer);
                    searchDebounceTimer = setTimeout(() => {
                        performSearch();
                    }, 300);
                });
                
                // Keyboard shortcuts
                searchInput.addEventListener('keydown', function(e) {
                    const suggestionsEl = document.getElementById('search-suggestions');
                    const suggestions = suggestionsEl && !suggestionsEl.classList.contains('hidden') ? 
                        suggestionsEl.querySelectorAll('div') : [];
                    
                    if (e.key === 'ArrowDown') {
                        e.preventDefault();
                        currentSuggestionIndex = Math.min(currentSuggestionIndex + 1, suggestions.length - 1);
                        if (suggestions[currentSuggestionIndex]) {
                            suggestions[currentSuggestionIndex].scrollIntoView({ block: 'nearest' });
                        }
                    } else if (e.key === 'ArrowUp') {
                        e.preventDefault();
                        currentSuggestionIndex = Math.max(currentSuggestionIndex - 1, -1);
                    } else if (e.key === 'Enter') {
                        e.preventDefault();
                        if (currentSuggestionIndex >= 0 && suggestions[currentSuggestionIndex]) {
                            const text = suggestions[currentSuggestionIndex].textContent.replace(/[📝🔢📍]/g, '').trim();
                            selectSuggestion(text);
                        } else {
                            performSearch();
                        }
                    } else if (e.key === 'Escape') {
                        clearSearch();
                    }
                });
            }
            
            // Event listeners cho bộ lọc
            ['filter-area', 'filter-type', 'filter-level'].forEach(id => {
                const el = document.getElementById(id);
                if (el) {
                    el.addEventListener('change', function() {
                        // Hiển thị nút xóa bộ lọc nếu có filter active
                        const clearFiltersBtn = document.getElementById('clear-filters-btn');
                        if (clearFiltersBtn) {
                            const hasFilter = document.getElementById('filter-area').value || 
                                            document.getElementById('filter-type').value || 
                                            document.getElementById('filter-level').value;
                            if (hasFilter) {
                                clearFiltersBtn.classList.remove('hidden');
                            } else {
                                clearFiltersBtn.classList.add('hidden');
                            }
                        }
                        performSearch();
                    });
                }
            });
            
            // Keyboard shortcut Ctrl+F / Cmd+F
            document.addEventListener('keydown', function(e) {
                if ((e.ctrlKey || e.metaKey) && e.key === 'f') {
                    e.preventDefault();
                    const searchInput = document.getElementById('search-input');
                    if (searchInput) {
                        searchInput.focus();
                        searchInput.select();
                    }
                }
            });
            
        });
        
        // Lưu dữ liệu cống sau khi load
        const originalLoadCongData = loadCongData;
        loadCongData = function() {
            return originalLoadCongData().then(geojson => {
                if (geojson && geojson.features) {
                    allCongData = geojson.features;
                    // populateFilters đã được gọi trong originalLoadCongData sau khi tạo congLayer
                    // Không cần gọi lại ở đây để tránh duplicate
                }
                return geojson;
            });
        };
        
    </script>
</x-app-layout>
