# 📋 KẾ HOẠCH GIAI ĐOẠN 4: FRONTEND TƯƠNG TÁC BẢN ĐỒ

## 📊 TỔNG QUAN

Giai đoạn 4 tập trung vào việc xây dựng giao diện người dùng tương tác với bản đồ, cho phép người dùng xem, vẽ, và quản lý dữ liệu cống trực tiếp trên bản đồ.

**Tiến độ hiện tại: 3/6 (50%)**

---

## ✅ ĐÃ HOÀN THÀNH

### 4.1: Dùng Leaflet hiển thị Tile Layer (OSM)
**Trạng thái:** ✅ **HOÀN THÀNH**

**Chi tiết:**
- Đã tích hợp OpenStreetMap (OSM) tile layer vào bản đồ
- Sử dụng LeafletJS để hiển thị bản đồ nền
- Cấu hình zoom level, center point phù hợp với khu vực Việt Nam
- File: `webgis-laravel/resources/views/map.blade.php`

**Kết quả:**
- Bản đồ hiển thị đầy đủ với tile layer OSM
- Có các control zoom in/out mặc định của Leaflet
- Bản đồ responsive, hoạt động tốt trên desktop và mobile

---

### 4.2: Gọi API ➜ hiển thị dữ liệu GeoJSON
**Trạng thái:** ✅ **HOÀN THÀNH**

**Chi tiết:**
- Đã tạo hàm `loadCongData()` để gọi API `/api/cong`
- API trả về GeoJSON FeatureCollection
- Sử dụng `L.geoJSON()` để hiển thị dữ liệu trên bản đồ
- Tự động tạo marker/icon cho từng cống
- File: `webgis-laravel/resources/views/map.blade.php`

**Kết quả:**
- Tất cả cống từ database hiển thị trên bản đồ
- Mỗi cống có icon marker riêng
- Có styling khác nhau cho cống mới (màu xanh) và cống cũ (màu đỏ)
- Dữ liệu được load tự động khi trang map được mở

---

### 4.3: Tích hợp Leaflet.draw
**Trạng thái:** ✅ **HOÀN THÀNH**

**Mục tiêu:**
Tích hợp thư viện Leaflet.draw để cho phép Admin vẽ điểm (marker) trên bản đồ, sau đó tự động chuyển sang trang quản lý với tọa độ đã điền sẵn.

**Yêu cầu:**
- Chỉ Admin mới có thể vẽ điểm
- Vẽ điểm trên bản đồ bằng cách click
- Tự động lấy tọa độ từ điểm vẽ
- Tự động chuyển sang trang `/cong/manage` với tọa độ đã điền
- Có nút toggle để bật/tắt chế độ vẽ

---

## 📋 KẾ HOẠCH CHI TIẾT 4.3

### BƯỚC 1: Thêm Leaflet.draw vào trang map

#### 1.1: Thêm CDN links
**File:** `webgis-laravel/resources/views/map.blade.php`

**Vị trí:** Trong phần `<head>` hoặc trước thẻ `</body>`

**Code cần thêm:**
```html
<!-- Leaflet.draw CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet-draw@1.0.4/dist/leaflet.draw.css" />

<!-- Leaflet.draw JS -->
<script src="https://cdn.jsdelivr.net/npm/leaflet-draw@1.0.4/dist/leaflet.draw.js"></script>
```

**Checklist:**
- [x] ✅ Thêm CSS link trước thẻ `</head>` hoặc trong phần head
- [x] ✅ Thêm JS script sau Leaflet.js (phải load sau Leaflet)
- [x] ✅ Kiểm tra CDN link có hoạt động không (test trong browser console)

---

### BƯỚC 2: Tạo nút "Bật vẽ điểm"

#### 2.1: Thêm nút vào UI
**File:** `webgis-laravel/resources/views/map.blade.php`

**Vị trí:** Trong phần hiển thị nút (cùng với nút "Thêm cống mới" nếu có)

**Code cần thêm:**
```html
@if($canEdit ?? false)
<div class="absolute top-4 left-4 z-[1000] flex gap-2">
    <button id="toggleDrawMode" onclick="toggleDrawMode()" 
            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow-lg flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
        </svg>
        <span id="drawModeText">Bật vẽ điểm</span>
    </button>
</div>
@endif
```

**Checklist:**
- [x] ✅ Nút chỉ hiển thị khi `$canEdit` là true (Admin)
- [x] ✅ Nút có ID `toggleDrawMode` để JavaScript có thể điều khiển
- [x] ✅ Nút có text động `drawModeText` để thay đổi khi bật/tắt
- [x] ✅ Style nút rõ ràng, dễ nhìn

---

### BƯỚC 3: Khởi tạo Leaflet.draw

#### 3.1: Tạo biến và kiểm tra quyền
**File:** `webgis-laravel/resources/views/map.blade.php`

**Vị trí:** Trong phần JavaScript, sau khi map đã được khởi tạo

**Code cần thêm:**
```javascript
// Leaflet.draw: Chỉ cho phép Admin vẽ điểm
let drawControl;
let drawHandler = null;
let isDrawingMode = false;

console.log('🔍 Kiểm tra canEdit:', canEdit, 'userRole:', userRole);

if (canEdit) {
    console.log('✅ Bắt đầu khởi tạo Leaflet.draw...');
    // Tiếp tục ở bước 3.2
} else {
    console.log('ℹ️ Leaflet.draw không được kích hoạt - canEdit =', canEdit);
    console.log('ℹ️ Chỉ Admin mới có thể vẽ điểm trên bản đồ');
}
```

**Checklist:**
- [x] ✅ Khai báo biến `drawControl`, `drawHandler`, `isDrawingMode`
- [x] ✅ Kiểm tra `canEdit` trước khi khởi tạo
- [x] ✅ Thêm console.log để debug

---

#### 3.2: Tạo Draw FeatureGroup
**File:** `webgis-laravel/resources/views/map.blade.php`

**Vị trí:** Trong block `if (canEdit)`

**Code cần thêm:**
```javascript
// Tạo Draw FeatureGroup để quản lý các feature vẽ được
const drawFeatureGroup = new L.FeatureGroup();
map.addLayer(drawFeatureGroup);
```

**Checklist:**
- [x] ✅ Tạo FeatureGroup mới
- [x] ✅ Thêm FeatureGroup vào map

---

#### 3.3: Tạo Draw Control (không thêm vào map)
**File:** `webgis-laravel/resources/views/map.blade.php`

**Vị trí:** Sau khi tạo FeatureGroup

**Code cần thêm:**
```javascript
// Tạo Draw Control - KHÔNG thêm vào bản đồ (chỉ dùng để quản lý)
// Vì đã có nút "Bật vẽ điểm" riêng, không cần toolbar mặc định
drawControl = new L.Control.Draw({
    position: 'topleft',
    edit: {
        featureGroup: drawFeatureGroup,
        remove: false  // Không cho phép xóa
    },
    draw: {
        marker: {
            icon: L.icon({
                iconUrl: 'https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/images/marker-icon.png',
                iconSize: [25, 41],
                iconAnchor: [12, 41],
                popupAnchor: [1, -34]
            })
        },
        polyline: false,    // Tắt vẽ đường
        polygon: false,     // Tắt vẽ vùng
        rectangle: false,   // Tắt vẽ hình chữ nhật
        circle: false,     // Tắt vẽ hình tròn
        circlemarker: false // Tắt vẽ marker tròn
    }
});

// KHÔNG thêm Draw Control toolbar vào bản đồ
// Chỉ dùng nút "Bật vẽ điểm" riêng để vẽ
// map.addControl(drawControl); // Đã comment để ẩn toolbar mặc định

console.log('✅ Leaflet.draw đã được khởi tạo (toolbar ẩn, dùng nút "Bật vẽ điểm" riêng)');
```

**Checklist:**
- [x] ✅ Tạo `L.Control.Draw` với cấu hình đúng
- [x] ✅ Chỉ bật `marker`, tắt các loại vẽ khác
- [x] ✅ KHÔNG gọi `map.addControl(drawControl)` (ẩn toolbar mặc định)
- [x] ✅ Thêm console.log để xác nhận

---

### BƯỚC 4: Tạo hàm toggleDrawMode()

#### 4.1: Tạo hàm cơ bản
**File:** `webgis-laravel/resources/views/map.blade.php`

**Vị trí:** Trong phần JavaScript, sau khi khởi tạo drawControl

**Code cần thêm:**
```javascript
function toggleDrawMode() {
    // Kiểm tra quyền
    if (!canEdit) {
        alert('Bạn không có quyền vẽ điểm! Chỉ Admin mới có quyền này.');
        return;
    }
    
    // Kiểm tra drawControl đã được khởi tạo chưa
    if (!drawControl) {
        alert('Leaflet.draw chưa được khởi tạo. Vui lòng refresh trang.');
        return;
    }
    
    // Logic bật/tắt sẽ thêm ở bước 4.2 và 4.3
}
```

**Checklist:**
- [x] ✅ Kiểm tra `canEdit` trước khi cho phép vẽ
- [x] ✅ Kiểm tra `drawControl` đã tồn tại chưa
- [x] ✅ Hiển thị thông báo lỗi nếu không đủ điều kiện

---

#### 4.2: Logic tắt chế độ vẽ
**File:** `webgis-laravel/resources/views/map.blade.php`

**Vị trí:** Trong hàm `toggleDrawMode()`, phần xử lý khi `isDrawingMode === true`

**Code cần thêm:**
```javascript
if (isDrawingMode) {
    // Tắt chế độ vẽ
    if (drawHandler) {
        drawHandler.disable();
        drawHandler = null;
    }
    isDrawingMode = false;
    document.getElementById('drawModeText').textContent = 'Bật vẽ điểm';
    document.getElementById('toggleDrawMode').classList.remove('bg-red-600');
    document.getElementById('toggleDrawMode').classList.add('bg-blue-600');
    console.log('🔴 Đã tắt chế độ vẽ điểm');
}
```

**Checklist:**
- [x] ✅ Disable `drawHandler` nếu đang tồn tại
- [x] ✅ Set `isDrawingMode = false`
- [x] ✅ Cập nhật text nút về "Bật vẽ điểm"
- [x] ✅ Đổi màu nút về xanh (bg-blue-600)
- [x] ✅ Khôi phục cursor mặc định
- [x] ✅ Thêm console.log để debug

---

#### 4.3: Logic bật chế độ vẽ
**File:** `webgis-laravel/resources/views/map.blade.php`

**Vị trí:** Trong hàm `toggleDrawMode()`, phần `else` (khi `isDrawingMode === false`)

**Code cần thêm:**
```javascript
else {
    // Bật chế độ vẽ
    if (typeof L.Draw.Marker !== 'undefined') {
        // Tạo Draw Handler mới cho marker
        drawHandler = new L.Draw.Marker(map, {
            icon: L.icon({
                iconUrl: 'https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/images/marker-icon.png',
                iconSize: [25, 41],
                iconAnchor: [12, 41],
                popupAnchor: [1, -34]
            })
        });
        
        // Bật chế độ vẽ
        drawHandler.enable();
        isDrawingMode = true;
        
        // Cập nhật UI
        document.getElementById('drawModeText').textContent = 'Tắt vẽ điểm';
        document.getElementById('toggleDrawMode').classList.remove('bg-blue-600');
        document.getElementById('toggleDrawMode').classList.add('bg-red-600');
        console.log('🟢 Đã bật chế độ vẽ điểm - Click vào bản đồ để vẽ');
        
        // Xử lý sự kiện khi vẽ xong (sẽ thêm ở bước 5)
        
    } else {
        alert('Leaflet.draw chưa được load đúng. Vui lòng refresh trang.');
    }
}
```

**Checklist:**
- [x] ✅ Kiểm tra `L.Draw.Marker` có tồn tại không
- [x] ✅ Tạo `new L.Draw.Marker()` với icon phù hợp
- [x] ✅ Gọi `drawHandler.enable()` để bật chế độ vẽ
- [x] ✅ Set `isDrawingMode = true`
- [x] ✅ Cập nhật text nút về "Tắt vẽ điểm (Click vào bản đồ để vẽ)"
- [x] ✅ Đổi màu nút sang đỏ (bg-red-600)
- [x] ✅ Thay đổi cursor thành crosshair
- [x] ✅ Thêm console.log để debug

---

### BƯỚC 5: Xử lý sự kiện khi vẽ xong

#### 5.1: Lắng nghe sự kiện CREATED
**File:** `webgis-laravel/resources/views/map.blade.php`

**Vị trí:** Trong phần bật chế độ vẽ (sau khi `drawHandler.enable()`)

**Code cần thêm:**
```javascript
// Lắng nghe sự kiện khi vẽ xong (nếu chưa có)
map.off(L.Draw.Event.CREATED); // Xóa listener cũ nếu có
map.on(L.Draw.Event.CREATED, function(e) {
    const layer = e.layer;
    const latlng = layer.getLatLng();
    
    console.log('✅ Đã vẽ điểm tại:', latlng);
    
    // Xóa marker tạm thời khỏi bản đồ
    map.removeLayer(layer);
    
    // Tắt chế độ vẽ sau khi vẽ xong
    if (drawHandler) {
        drawHandler.disable();
        drawHandler = null;
    }
    isDrawingMode = false;
    document.getElementById('drawModeText').textContent = 'Bật vẽ điểm';
    document.getElementById('toggleDrawMode').classList.remove('bg-red-600');
    document.getElementById('toggleDrawMode').classList.add('bg-blue-600');
    
    // Lấy tọa độ từ điểm vừa vẽ
    const x = latlng.lng; // Kinh độ
    const y = latlng.lat;  // Vĩ độ
    
    console.log('📍 Tọa độ:', 'X =', x, ', Y =', y);
    
    // Chuyển sang trang manage với tọa độ đã điền (sẽ thêm ở bước 5.2)
});
```

**Checklist:**
- [x] ✅ Xóa listener cũ bằng `map.off()` để tránh duplicate
- [x] ✅ Thêm listener mới bằng `map.on(L.Draw.Event.CREATED)`
- [x] ✅ Lấy `latlng` từ layer
- [x] ✅ Xóa marker tạm thời khỏi map
- [x] ✅ Tắt chế độ vẽ sau khi vẽ xong
- [x] ✅ Khôi phục cursor mặc định
- [x] ✅ Lấy tọa độ X (lng) và Y (lat)
- [x] ✅ Thêm console.log để debug

---

#### 5.2: Chuyển sang trang manage với tọa độ
**File:** `webgis-laravel/resources/views/map.blade.php`

**Vị trí:** Trong event handler `L.Draw.Event.CREATED`, sau khi lấy tọa độ

**Code cần thêm:**
```javascript
// Chuyển sang trang manage với tọa độ đã điền
const url = `/cong/manage?add=1&x=${x}&y=${y}`;
console.log('🔄 Chuyển sang trang:', url);
window.location.href = url;
```

**Checklist:**
- [x] ✅ Tạo URL với parameter `add=1&x=...&y=...`
- [x] ✅ Sử dụng `window.location.href` để chuyển trang
- [x] ✅ Thêm console.log để debug

---

### BƯỚC 6: Xử lý trên trang manage

#### 6.1: Đọc parameter từ URL
**File:** `webgis-laravel/resources/views/cong/manage.blade.php`

**Vị trí:** Trong phần JavaScript, trong event `DOMContentLoaded`

**Code cần thêm:**
```javascript
// Kiểm tra nếu có parameter add=1&x=...&y=... từ trang map
const urlParams = new URLSearchParams(window.location.search);
const addMode = urlParams.get('add');
const x = urlParams.get('x');
const y = urlParams.get('y');

if (addMode === '1' && x && y) {
    console.log('📍 Nhận tọa độ từ bản đồ:', 'X =', x, ', Y =', y);
    
    // Xóa parameter khỏi URL (clean URL)
    window.history.replaceState({}, document.title, window.location.pathname);
    
    // Mở modal thêm cống và điền tọa độ (sẽ thêm ở bước 6.2)
}
```

**Checklist:**
- [x] ✅ Đọc parameter `add`, `x`, `y` từ URL
- [x] ✅ Kiểm tra đủ điều kiện (`add === '1'` và có `x`, `y`)
- [x] ✅ Clean URL bằng `window.history.replaceState()`
- [x] ✅ Thêm console.log để debug

---

#### 6.2: Mở modal và điền tọa độ
**File:** `webgis-laravel/resources/views/cong/manage.blade.php`

**Vị trí:** Trong block `if (addMode === '1' && x && y)`

**Code cần thêm:**
```javascript
// Mở modal thêm cống
showAddModal();

// Điền tọa độ vào form
setTimeout(() => {
    const xInput = document.getElementById('edit_toa_do_x');
    const yInput = document.getElementById('edit_toa_do_y');
    
    if (xInput && yInput) {
        xInput.value = x;
        yInput.value = y;
        
        // Set readonly và style
        xInput.readOnly = true;
        yInput.readOnly = true;
        xInput.style.backgroundColor = '#E0F2FE'; // Light blue
        xInput.style.cursor = 'not-allowed';
        yInput.style.backgroundColor = '#E0F2FE';
        yInput.style.cursor = 'not-allowed';
        
        // Thêm label "(Tự động từ bản đồ)"
        const xLabel = xInput.previousElementSibling;
        const yLabel = yInput.previousElementSibling;
        if (xLabel && !xLabel.textContent.includes('Tự động')) {
            xLabel.textContent += ' (Tự động từ bản đồ)';
        }
        if (yLabel && !yLabel.textContent.includes('Tự động')) {
            yLabel.textContent += ' (Tự động từ bản đồ)';
        }
        
        console.log('✅ Đã điền tọa độ vào form');
        
        // Focus vào trường đầu tiên (ID hoặc tên cống)
        const firstInput = document.getElementById('edit_id') || document.getElementById('edit_ten_cong_dap');
        if (firstInput) {
            firstInput.focus();
        }
    }
}, 300); // Delay để đảm bảo modal đã render
```

**Checklist:**
- [x] ✅ Gọi `showAddModal()` để mở modal
- [x] ✅ Dùng `setTimeout` để đợi modal render
- [x] ✅ Tìm input `edit_toa_do_x` và `edit_toa_do_y`
- [x] ✅ Điền giá trị `x` và `y` vào input
- [x] ✅ Set `readOnly = true` để không cho chỉnh sửa
- [x] ✅ Thêm style (background color, cursor)
- [x] ✅ Thêm text "(Tự động từ bản đồ)" vào label
- [x] ✅ Focus vào trường đầu tiên
- [x] ✅ Thêm console.log để debug

---

### BƯỚC 7: Xử lý sau khi submit form

#### 7.1: Redirect về map với tọa độ
**File:** `webgis-laravel/resources/views/cong/manage.blade.php`

**Vị trí:** Trong hàm `submitCongForm()`, sau khi submit thành công (cho trường hợp thêm mới)

**Code cần thêm:**
```javascript
// Nếu là thêm mới và thành công
if (!isEdit && response.success) {
    // Lấy tọa độ từ form
    const x = document.getElementById('edit_toa_do_x').value;
    const y = document.getElementById('edit_toa_do_y').value;
    
    // Redirect về map với parameter zoom
    if (x && y) {
        window.location.href = `/map?zoom=${x},${y}`;
    } else {
        window.location.href = '/map';
    }
}
```

**Checklist:**
- [x] ✅ Kiểm tra `!isEdit` (thêm mới) và `response.success`
- [x] ✅ Lấy tọa độ từ form
- [x] ✅ Tạo URL `/map?zoom=x,y`
- [x] ✅ Redirect bằng `window.location.href`

---

#### 7.2: Xử lý parameter zoom trên trang map
**File:** `webgis-laravel/resources/views/map.blade.php`

**Vị trí:** Sau khi load dữ liệu cống thành công

**Code cần thêm:**
```javascript
// Kiểm tra nếu có parameter zoom=x,y từ trang manage
const urlParams = new URLSearchParams(window.location.search);
const zoomParam = urlParams.get('zoom');

if (zoomParam) {
    const [x, y] = zoomParam.split(',');
    if (x && y) {
        console.log('🔍 Zoom đến tọa độ:', x, y);
        
        // Zoom đến vị trí
        map.setView([parseFloat(y), parseFloat(x)], 16);
        
        // Clean URL
        window.history.replaceState({}, document.title, window.location.pathname);
        
        // Tìm và mở popup của cống mới (sẽ thêm ở bước 7.3)
    }
}
```

**Checklist:**
- [x] ✅ Đọc parameter `zoom` từ URL
- [x] ✅ Parse tọa độ `x, y`
- [x] ✅ Zoom đến vị trí với level 16
- [x] ✅ Clean URL
- [x] ✅ Thêm console.log để debug

---

#### 7.3: Tìm và mở popup cống mới
**File:** `webgis-laravel/resources/views/map.blade.php`

**Vị trí:** Trong block `if (zoomParam)`, sau khi zoom

**Code cần thêm:**
```javascript
// Tìm cống gần vị trí zoom nhất và mở popup
setTimeout(() => {
    let minDistance = Infinity;
    let nearestLayer = null;
    
    congLayer.eachLayer(function(layer) {
        if (layer.feature && layer.feature.geometry) {
            const coords = layer.feature.geometry.coordinates;
            const layerX = coords[0];
            const layerY = coords[1];
            
            // Tính khoảng cách (đơn giản, không cần tính chính xác)
            const distance = Math.abs(parseFloat(x) - layerX) + Math.abs(parseFloat(y) - layerY);
            
            if (distance < minDistance) {
                minDistance = distance;
                nearestLayer = layer;
            }
        }
    });
    
    if (nearestLayer) {
        nearestLayer.openPopup();
        console.log('✅ Đã mở popup cống mới');
    }
}, 1000); // Delay để đảm bảo dữ liệu đã load xong
```

**Checklist:**
- [x] ✅ Dùng `setTimeout` để đợi dữ liệu load
- [x] ✅ Duyệt qua `congLayer` để tìm cống gần nhất
- [x] ✅ Tính khoảng cách đơn giản
- [x] ✅ Tìm cống có khoảng cách nhỏ nhất
- [x] ✅ Gọi `openPopup()` trên layer gần nhất
- [x] ✅ Thêm console.log để debug

---

## ✅ CHECKLIST TỔNG HỢP

### Phần 1: Setup Leaflet.draw
- [x] ✅ Thêm CDN CSS cho Leaflet.draw
- [x] ✅ Thêm CDN JS cho Leaflet.draw
- [x] ✅ Kiểm tra CDN load thành công

### Phần 2: UI
- [x] ✅ Tạo nút "Bật vẽ điểm"
- [x] ✅ Nút chỉ hiển thị cho Admin
- [x] ✅ Nút có style rõ ràng

### Phần 3: Khởi tạo
- [x] ✅ Tạo biến `drawControl`, `drawHandler`, `isDrawingMode`
- [x] ✅ Kiểm tra quyền `canEdit`
- [x] ✅ Tạo Draw FeatureGroup
- [x] ✅ Tạo Draw Control (chỉ marker, tắt các loại khác)
- [x] ✅ KHÔNG thêm toolbar mặc định vào map

### Phần 4: Toggle function
- [x] ✅ Tạo hàm `toggleDrawMode()`
- [x] ✅ Logic tắt chế độ vẽ
- [x] ✅ Logic bật chế độ vẽ
- [x] ✅ Cập nhật UI (text, màu nút, cursor)

### Phần 5: Xử lý sự kiện vẽ
- [x] ✅ Lắng nghe `L.Draw.Event.CREATED`
- [x] ✅ Lắng nghe `L.Draw.Event.DRAWCANCEL` (hủy vẽ)
- [x] ✅ Lấy tọa độ từ điểm vẽ
- [x] ✅ Xóa marker tạm thời
- [x] ✅ Tắt chế độ vẽ sau khi vẽ xong
- [x] ✅ Chuyển sang trang `/cong/manage?add=1&x=...&y=...`

### Phần 6: Xử lý trên trang manage
- [x] ✅ Đọc parameter từ URL
- [x] ✅ Clean URL
- [x] ✅ Mở modal thêm cống
- [x] ✅ Điền tọa độ vào form
- [x] ✅ Set readonly và style cho input tọa độ
- [x] ✅ Thêm label "(Tự động từ bản đồ)"

### Phần 7: Xử lý sau khi submit
- [x] ✅ Redirect về `/map?zoom=x,y` sau khi thêm thành công
- [x] ✅ Đọc parameter `zoom` trên trang map
- [x] ✅ Zoom đến vị trí cống mới
- [x] ✅ Tìm và mở popup cống mới

---

## 🧪 CÁCH TEST

1. **Test quyền:**
   - Đăng nhập với user thường → Nút "Bật vẽ điểm" không hiển thị
   - Đăng nhập với admin → Nút "Bật vẽ điểm" hiển thị

2. **Test vẽ điểm:**
   - Click nút "Bật vẽ điểm" → Nút đổi màu đỏ, text thành "Tắt vẽ điểm"
   - Click vào bản đồ → Tạo marker tạm thời
   - Marker tự động biến mất và chuyển sang trang manage

3. **Test điền tọa độ:**
   - Sau khi vẽ, kiểm tra URL có `?add=1&x=...&y=...`
   - Modal tự động mở
   - Trường tọa độ X, Y đã được điền và readonly

4. **Test redirect:**
   - Điền form và submit
   - Kiểm tra redirect về `/map?zoom=x,y`
   - Map tự động zoom đến vị trí cống mới
   - Popup tự động mở

---

## 📝 LƯU Ý

1. **CDN có thể bị chặn:** Nếu Leaflet.draw không load, thử đổi CDN hoặc download về local
2. **Thứ tự load script:** Leaflet.draw phải load sau Leaflet.js
3. **Event listener:** Nhớ xóa listener cũ bằng `map.off()` trước khi thêm mới
4. **Timing:** Dùng `setTimeout` khi cần đợi DOM render (modal, dữ liệu load)
5. **Debug:** Thêm nhiều `console.log()` để theo dõi flow

---

## 🎯 KẾT QUẢ MONG ĐỢI

Sau khi hoàn thành, Admin có thể:
1. Click nút "Bật vẽ điểm" để bật chế độ vẽ
2. Click vào bản đồ để vẽ điểm
3. Tự động chuyển sang trang manage với tọa độ đã điền
4. Điền thông tin cống và submit
5. Tự động quay lại map, zoom đến vị trí cống mới và mở popup

---

### 4.4: Gửi dữ liệu về API ➜ lưu vào DB
**Trạng thái:** ⚠️ **MỚI MỚI** (Có flow nhưng cần kiểm tra và hoàn thiện)

**Chi tiết:**
- Đã có flow: Vẽ điểm → Chuyển trang manage → Điền form → Submit → Lưu vào DB
- Form trên trang `/cong/manage` tự động điền tọa độ khi nhận parameter từ URL
- Tọa độ được set readonly để người dùng không thể chỉnh sửa
- Sau khi submit thành công, tự động quay lại map và zoom đến vị trí cống mới
- File: 
  - `webgis-laravel/resources/views/map.blade.php` (xử lý vẽ điểm)
  - `webgis-laravel/resources/views/cong/manage.blade.php` (form thêm cống)
  - `webgis-laravel/app/Http/Controllers/CongController.php` (API xử lý)

**Kết quả:**
- Flow cơ bản đã có từ vẽ điểm đến lưu vào database
- Tọa độ được tự động điền, không cần nhập thủ công
- Cống mới hiển thị ngay trên bản đồ sau khi tạo
- Có popup tự động mở khi quay lại map

**Các tính năng đã có:**
- ✅ Vẽ điểm trên map → tự động điền tọa độ vào form
- ✅ Form tự động mở khi nhận parameter `?add=1&x=...&y=...`
- ✅ Tọa độ readonly (không thể chỉnh sửa)
- ✅ Submit form → lưu vào database với geometry
- ✅ Quay lại map → tự động zoom và mở popup cống mới

**Cần kiểm tra:**
- ⚠️ Flow có hoạt động ổn định không?
- ⚠️ Có lỗi gì khi submit form không?
- ⚠️ Geometry có được tạo đúng không?
- ⚠️ Có cần cải thiện UX không?

---

### 4.6: Hiển thị popup thông tin chi tiết
**Trạng thái:** ✅ **HOÀN THÀNH**

**Chi tiết:**
- Đã tạo hàm `buildPropsTable()` để hiển thị thông tin chi tiết cống trong popup
- Popup hiển thị đầy đủ 27 trường dữ liệu từ database
- Có chức năng hover để tự động mở popup (không cần click)
- Popup tự động đóng khi rê chuột ra ngoài
- Hiển thị file đính kèm (nếu có) với nút download
- File: `webgis-laravel/resources/views/map.blade.php`

**Kết quả:**
- Popup hiển thị đầy đủ thông tin cống
- Hover để xem thông tin nhanh
- Có thể tải file đính kèm (ảnh, PDF)
- Popup responsive, hiển thị tốt trên mọi thiết bị

**Các tính năng:**
- ✅ Click vào marker → mở popup
- ✅ Hover vào marker → tự động mở popup
- ✅ Rê chuột ra ngoài → tự động đóng popup
- ✅ Hiển thị đầy đủ thông tin cống (27 trường)
- ✅ Hiển thị file đính kèm với nút download
- ✅ Link xem ảnh (mở tab mới)

---

## ❌ CHƯA HOÀN THÀNH

### 4.5: Tìm kiếm và tra cứu cống
**Trạng thái:** ❌ **CHƯA LÀM**

**Mục tiêu:**
Tạo chức năng tìm kiếm thân thiện, dễ sử dụng cho **cả Admin và User**, đặc biệt là người dùng chưa có kinh nghiệm về cống. Tập trung vào UX trực quan và hỗ trợ người dùng.

**Yêu cầu:**
- **Áp dụng cho cả Admin và User** - Tất cả người dùng đều có thể tìm kiếm và tra cứu cống
- **Thân thiện với người dùng mới** - Không cần kiến thức chuyên môn về cống
- Cho phép tìm kiếm linh hoạt:
  - Tìm kiếm thông minh (tự động tìm trong tất cả trường)
  - Tìm theo tên cống (tên công đập)
  - Tìm theo mã số cống (ID)
  - Tìm theo địa điểm (quận, huyện, đường)
  - Tìm theo tọa độ X, Y (cho người dùng có kinh nghiệm)
- Kết quả tìm kiếm:
  - Highlight marker của cống tìm được (đổi màu, thêm animation)
  - Tự động zoom đến vị trí cống
  - Mở popup thông tin cống
  - Hiển thị danh sách kết quả với thông tin rõ ràng

**Kế hoạch thực hiện:**

#### Bước 1: Tạo UI tìm kiếm thân thiện và trực quan
- [ ] **Ô tìm kiếm chính:**
  - Thêm ô tìm kiếm lớn, dễ thấy ở góc trên bên phải bản đồ
  - Kích thước: `w-80 md:w-96` (responsive), `h-12` (cao đủ để dễ click)
  - Background trắng với shadow, border rõ ràng
  - Placeholder text gợi ý: "🔍 Tìm kiếm cống theo tên, mã số, địa điểm..."
  - Icon tìm kiếm (🔍) bên trái trong ô input
  - Nút "✖️" bên phải để xóa nhanh (chỉ hiện khi có text)
  - Tìm kiếm tự động khi gõ (debounce 300ms) - không cần nhấn Enter
- [ ] **Bộ lọc đơn giản (Simple Filters) - Dành cho người dùng mới:**
  - **Dropdown "📍 Theo khu vực":**
    - Tự động lấy danh sách các quận/huyện từ dữ liệu cống
    - Hiển thị: "Tất cả khu vực", "Quận 1", "Quận 2", "Hóc Môn", "Q.12", v.v.
    - Khi chọn → Lọc ngay lập tức, không cần gõ text
    - Icon địa điểm rõ ràng
  - **Dropdown "🏗️ Loại công trình":**
    - Tự động lấy danh sách loại công trình từ dữ liệu
    - Hiển thị: "Tất cả loại", "Cống điều tiết", "Cống thoát nước", v.v.
    - Khi chọn → Lọc ngay lập tức
  - **Dropdown "⭐ Cấp công trình":**
    - Tự động lấy danh sách cấp công trình từ dữ liệu
    - Hiển thị: "Tất cả cấp", "I", "II", "III", "IV", v.v.
    - Khi chọn → Lọc ngay lập tức
  - **Nút "🔄 Xóa bộ lọc":**
    - Reset tất cả bộ lọc về "Tất cả"
    - Chỉ hiện khi có ít nhất 1 bộ lọc đang active
- [ ] **Tìm kiếm nâng cao (Ẩn mặc định - cho người dùng có kinh nghiệm):**
  - Nút "⚙️ Tìm kiếm nâng cao" để mở/đóng panel
  - Panel ẩn mặc định, chỉ hiện khi click
  - Trong panel có:
    - Dropdown loại tìm kiếm: "🔍 Tất cả", "📝 Theo tên", "🔢 Theo mã số", "📍 Theo địa điểm", "🗺️ Theo tọa độ"
    - Checkbox "Tìm kiếm chính xác" (exact match)
    - Input "Tọa độ X, Y" (cho tìm kiếm theo tọa độ)
- [ ] **UI hiển thị cho cả Admin và User** (không cần kiểm tra quyền)
- [ ] **Hướng dẫn trực quan:**
  - Tooltip khi hover vào ô tìm kiếm: "💡 Ví dụ: 'Cống ÔĐ4b', 'C1615', 'Hóc Môn'"
  - Banner nhỏ phía trên ô tìm kiếm (có thể đóng): "💡 Mẹo: Bạn có thể tìm theo tên, mã số, địa điểm, hoặc dùng bộ lọc bên dưới"
  - Icon "❓" bên cạnh ô tìm kiếm → Mở modal hướng dẫn chi tiết

#### Bước 2: Xử lý tìm kiếm thông minh và bộ lọc
- [ ] **Tạo hàm `searchCong()` để xử lý tìm kiếm:**
  - Tìm kiếm trong dữ liệu đã load từ `congLayer` (không cần gọi API lại)
  - Hỗ trợ kết hợp: text search + bộ lọc (khu vực, loại, cấp)
  - Trả về mảng các cống khớp với điều kiện
- [ ] **Tìm kiếm thông minh (Smart Search):**
  - **Tự động nhận diện loại tìm kiếm:**
    - Nếu là số thuần túy (ví dụ: "1615") → tìm theo mã số (tìm "C1615", "1615", v.v.)
    - Nếu có dấu phẩy hoặc khoảng trắng giữa 2 số (ví dụ: "106.808739, 10.868036") → tìm theo tọa độ
    - Nếu là text → tìm trong tên, địa điểm, lý trình, ghi chú
  - **Hỗ trợ tìm kiếm linh hoạt:**
    - Không phân biệt hoa thường (case-insensitive): "CỐNG" = "cống"
    - Tìm kiếm một phần (partial match): "Cống ÔĐ" → tìm thấy "Cống ÔĐ4b", "Cống ÔĐ5"
    - Tìm kiếm tiếng Việt có dấu/không dấu: "cong OD" → tìm thấy "Cống ÔĐ"
    - Tìm kiếm fuzzy (gần đúng): "cong OD4" → tìm thấy "Cống ÔĐ4b" (bỏ qua ký tự nhỏ)
  - **Tìm kiếm trong nhiều trường cùng lúc:**
    - Tên cống (`ten_cong_dap`)
    - Mã số (`id`)
    - Địa điểm (`dia_diem`)
    - Lý trình (`ly_trinh`)
    - Ghi chú (`ghi_chu`) - nếu có
- [ ] **Xử lý bộ lọc:**
  - **Lọc theo khu vực:** Tìm trong `dia_diem`, so khớp một phần (ví dụ: chọn "Hóc Môn" → tìm tất cả cống có "Hóc Môn" trong địa điểm)
  - **Lọc theo loại công trình:** So khớp chính xác với `loai_cong_tr`
  - **Lọc theo cấp công trình:** So khớp chính xác với `cap_cong_tri`
  - **Kết hợp bộ lọc:** Nếu có nhiều bộ lọc, tất cả phải khớp (AND logic)
  - **Kết hợp text search + bộ lọc:** Text search VÀ bộ lọc đều phải khớp
- [ ] **Highlight marker của cống tìm được:**
  - Đổi màu marker thành vàng/cam nổi bật (`#F59E0B` hoặc `#FF6B35`)
  - Thêm animation pulse (nhấp nháy) để dễ nhận biết
  - Thêm border vàng dày (3-4px) và shadow lớn để nổi bật
  - **Làm mờ các marker khác** (opacity: 0.3) để tập trung vào kết quả
  - Lưu trạng thái marker gốc để restore khi xóa tìm kiếm
- [ ] **Zoom và hiển thị kết quả:**
  - Nếu có 1 kết quả: Zoom đến vị trí (level 16), mở popup tự động
  - Nếu có nhiều kết quả: Zoom để hiển thị tất cả (fitBounds), không mở popup tự động
  - Smooth animation khi zoom (duration: 1s)
- [ ] **Tìm kiếm bằng bản đồ (Map-based search) - Dành cho người dùng mới:**
  - Nút "📍 Tìm cống gần đây" → Cho phép click vào bản đồ để tìm cống gần nhất
  - Khi click vào bản đồ (nếu đang ở chế độ này):
    - Hiển thị marker tạm thời (màu xanh) tại vị trí click
    - Tìm tất cả cống trong bán kính 500m (hoặc 1km)
    - Hiển thị danh sách kết quả với khoảng cách
    - Sắp xếp theo khoảng cách (gần nhất trước)

#### Bước 3: Tìm kiếm theo tọa độ (cho người dùng có kinh nghiệm)
- [ ] Cho phép nhập tọa độ X, Y với nhiều format:
  - "106.808739, 10.868036" (có dấu phẩy)
  - "106.808739 10.868036" (có khoảng trắng)
  - "X: 106.808739 Y: 10.868036" (có prefix)
- [ ] Parse tọa độ từ input một cách linh hoạt
- [ ] Validate tọa độ (kiểm tra trong phạm vi hợp lệ của Việt Nam)
- [ ] Tìm cống gần nhất với tọa độ đó (sử dụng công thức Haversine hoặc khoảng cách đơn giản)
- [ ] Hiển thị khoảng cách từ điểm tìm đến cống:
  - Nếu < 1km: hiển thị bằng mét (ví dụ: "250m")
  - Nếu >= 1km: hiển thị bằng km (ví dụ: "2.5km")
- [ ] Nếu có nhiều cống gần, hiển thị danh sách sắp xếp theo khoảng cách (gần nhất trước)
- [ ] Hiển thị vị trí tìm kiếm trên bản đồ bằng marker tạm thời (màu xanh)

#### Bước 4: Hiển thị kết quả trực quan
- [ ] **Panel kết quả tìm kiếm:**
  - Hiển thị ở góc trên bên phải, dưới ô tìm kiếm
  - Background trắng với shadow, dễ nhìn
  - Scroll được nếu có nhiều kết quả
- [ ] **Mỗi kết quả hiển thị:**
  - Icon marker màu vàng/cam để dễ nhận biết
  - **Tên cống** (in đậm, màu xanh)
  - Mã số (màu xám nhạt)
  - Địa điểm (màu đen)
  - Khoảng cách (nếu tìm theo tọa độ, màu cam)
  - Nút "📍 Xem trên bản đồ" để zoom và mở popup
- [ ] **Thông tin tổng quan:**
  - Hiển thị số lượng kết quả: "Tìm thấy 5 cống"
  - Hiển thị loại tìm kiếm đang dùng
- [ ] **Thông báo:**
  - Nếu không có kết quả: "❌ Không tìm thấy cống nào. Thử tìm kiếm với từ khóa khác."
  - Gợi ý: "💡 Bạn có thể tìm theo: tên cống, mã số, hoặc địa điểm"
- [ ] **Nút điều khiển:**
  - Nút "✖️ Xóa tìm kiếm" để reset và hiển thị lại tất cả cống
  - Nút "🔄 Tìm lại" để tìm lại với từ khóa hiện tại
- [ ] **Tương tác:**
  - Click vào kết quả → zoom đến vị trí và mở popup
  - Hover vào kết quả → highlight marker tương ứng trên bản đồ
  - Click ngoài panel → đóng panel (optional)

#### Bước 5: Cải thiện UX cho người dùng mới (Ưu tiên cao)
- [ ] **Autocomplete/Gợi ý khi gõ (Smart Suggestions):**
  - Hiển thị dropdown gợi ý khi đang gõ (tối đa 8-10 gợi ý)
  - Gợi ý dựa trên:
    - Tên cống phổ biến (sắp xếp theo tần suất xuất hiện)
    - Mã số cống
    - Địa điểm phổ biến (quận, huyện)
  - **Highlight phần text khớp** trong gợi ý (màu vàng/cam)
  - **Icon phân biệt loại gợi ý:**
    - 📝 Tên cống
    - 🔢 Mã số
    - 📍 Địa điểm
  - Click vào gợi ý → Tự động điền và tìm kiếm ngay
  - Keyboard navigation: Dùng mũi tên ↑↓ để chọn, Enter để chọn
- [ ] **Hướng dẫn sử dụng trực quan:**
  - **Tooltip khi hover:**
    - Ô tìm kiếm: "💡 Ví dụ: 'Cống ÔĐ4b', 'C1615', 'Hóc Môn'"
    - Bộ lọc khu vực: "Chọn khu vực để xem tất cả cống ở đó"
    - Bộ lọc loại: "Lọc theo loại công trình"
  - **Banner hướng dẫn (có thể đóng):**
    - Hiện lần đầu khi người dùng vào trang map
    - Nội dung: "💡 **Cách tìm kiếm cống:**\n1. Gõ tên, mã số, hoặc địa điểm vào ô tìm kiếm\n2. Hoặc dùng bộ lọc bên dưới để lọc theo khu vực/loại\n3. Click vào kết quả để xem chi tiết trên bản đồ"
    - Nút "Đã hiểu" để đóng và không hiện lại
    - Lưu vào localStorage để không hiện lại
  - **Modal hướng dẫn chi tiết (Icon ❓):**
    - Mở khi click vào icon "❓"
    - Nội dung chi tiết với ví dụ cụ thể
    - Có hình ảnh minh họa (nếu có)
    - Nút "Đóng" để đóng modal
- [ ] **Lịch sử tìm kiếm (Search History):**
  - Lưu 8-10 từ khóa tìm kiếm gần đây vào localStorage
  - Hiển thị dropdown khi click vào ô tìm kiếm (nếu có lịch sử)
  - Mỗi mục hiển thị:
    - Icon loại tìm kiếm (📝/🔢/📍)
    - Từ khóa
    - Thời gian (ví dụ: "2 phút trước")
  - **Tương tác:**
    - Click vào mục → Tự động tìm kiếm lại
    - Icon "🗑️" để xóa từng mục
    - Nút "Xóa tất cả" ở cuối danh sách
  - Chỉ lưu các tìm kiếm thành công (có kết quả)
- [ ] **Keyboard shortcuts (Phím tắt):**
  - `Ctrl+F` hoặc `Cmd+F`: Focus vào ô tìm kiếm (ngăn chặn browser search)
  - `Enter`: Thực hiện tìm kiếm (nếu đang focus vào ô tìm kiếm)
  - `Esc`: 
    - Nếu đang có kết quả → Xóa tìm kiếm, reset bản đồ
    - Nếu đang mở dropdown gợi ý → Đóng dropdown
    - Nếu đang mở modal hướng dẫn → Đóng modal
  - `↑↓`: Điều hướng trong dropdown gợi ý/lịch sử
- [ ] **Visual feedback (Phản hồi trực quan):**
  - **Loading indicator:**
    - Spinner nhỏ bên cạnh icon tìm kiếm khi đang xử lý
    - Chỉ hiện nếu tìm kiếm mất > 100ms
  - **Animation khi hiển thị kết quả:**
    - Fade-in cho panel kết quả
    - Pulse animation cho marker được highlight
    - Smooth transition khi zoom (duration: 1s, easing: ease-out)
  - **Thông báo trạng thái:**
    - "🔍 Đang tìm kiếm..." (khi đang xử lý)
    - "✅ Tìm thấy X cống" (khi có kết quả)
    - "❌ Không tìm thấy cống nào" (khi không có kết quả)
- [ ] **Tối ưu cho mobile (Responsive):**
  - **Layout:**
    - Trên mobile: Ô tìm kiếm full width, bộ lọc xếp dọc
    - Trên desktop: Ô tìm kiếm và bộ lọc xếp ngang
  - **Touch-friendly:**
    - Nút lớn (min-height: 44px) để dễ click
    - Khoảng cách giữa các nút đủ rộng (min: 8px)
    - Panel kết quả có thể scroll mượt mà trên mobile
  - **Performance:**
    - Debounce tăng lên 500ms trên mobile (tiết kiệm pin)
    - Giới hạn số lượng kết quả hiển thị trên mobile (tối đa 20)

**File cần sửa:**
- `webgis-laravel/resources/views/map.blade.php` (thêm UI và logic tìm kiếm)

**Ước tính thời gian:** 3-4 giờ (do có nhiều tính năng UX)

**Ví dụ sử dụng cho người dùng mới (User Journey):**

1. **Tìm kiếm đơn giản bằng text (Không cần kiến thức chuyên môn):**
   - Người dùng gõ "Cống ÔĐ" → Hệ thống tự động tìm tất cả cống có tên chứa "Cống ÔĐ"
   - Không cần biết mã số chính xác, chỉ cần nhớ một phần tên
   - **Kết quả:** Danh sách các cống khớp, tự động highlight trên bản đồ

2. **Tìm kiếm bằng bộ lọc (Rất dễ cho người mới):**
   - Người dùng chọn "📍 Theo khu vực" → Chọn "Hóc Môn" từ dropdown
   - Hệ thống tự động lọc và hiển thị tất cả cống ở Hóc Môn
   - Không cần gõ text, chỉ cần click
   - **Kết quả:** Tất cả cống ở Hóc Môn được highlight, bản đồ tự động zoom để hiển thị tất cả

3. **Tìm kiếm kết hợp (Text + Bộ lọc):**
   - Người dùng gõ "Cống" trong ô tìm kiếm
   - Đồng thời chọn "🏗️ Loại công trình" → "Cống điều tiết"
   - Hệ thống tìm tất cả cống có tên chứa "Cống" VÀ loại là "Cống điều tiết"
   - **Kết quả:** Kết quả chính xác hơn, dễ tìm thấy cống cần thiết

4. **Tìm kiếm với gợi ý (Autocomplete):**
   - Người dùng gõ "C" → Dropdown tự động hiển thị gợi ý:
     - 📝 "Cống ÔĐ4b"
     - 📝 "Cống điều tiết"
     - 🔢 "C1615"
     - 📍 "Cầu Giấy"
   - Click vào gợi ý → Tự động điền và tìm kiếm ngay
   - **Lợi ích:** Không cần gõ đầy đủ, tiết kiệm thời gian

5. **Tìm kiếm thông minh (Smart Detection):**
   - Gõ "1615" → Tự động nhận diện là mã số, tìm cống có mã C1615 hoặc chứa "1615"
   - Gõ "cong OD" (không dấu) → Vẫn tìm thấy "Cống ÔĐ4b" (hỗ trợ không dấu)
   - Gõ "106.808739, 10.868036" → Tự động nhận diện là tọa độ, tìm cống gần nhất
   - **Lợi ích:** Người dùng không cần biết format chính xác, hệ thống tự nhận diện

6. **Tìm kiếm bằng bản đồ (Map-based - Rất trực quan):**
   - Người dùng click nút "📍 Tìm cống gần đây"
   - Click vào một điểm trên bản đồ (ví dụ: gần nhà mình)
   - Hệ thống tự động tìm tất cả cống trong bán kính 500m
   - **Kết quả:** Danh sách cống với khoảng cách, sắp xếp từ gần đến xa
   - **Lợi ích:** Không cần biết tên hay mã số, chỉ cần biết vị trí trên bản đồ

7. **Tìm kiếm với lịch sử (Tiện lợi):**
   - Người dùng đã tìm "Cống ÔĐ4b" trước đó
   - Lần sau, click vào ô tìm kiếm → Dropdown hiển thị lịch sử
   - Click vào "Cống ÔĐ4b" trong lịch sử → Tự động tìm lại
   - **Lợi ích:** Không cần gõ lại, tiết kiệm thời gian

**Lưu ý quan trọng:**
- **Không yêu cầu quyền Admin:** Cả User và Admin đều có thể sử dụng tìm kiếm
- **Chỉ để tra cứu:** Tìm kiếm chỉ để xem thông tin, không liên quan đến quyền chỉnh sửa
- **Ưu tiên UX cho người dùng mới:**
  - Tất cả tính năng đều được thiết kế để người dùng **không cần kiến thức chuyên môn về cống** vẫn có thể sử dụng dễ dàng
  - **Bộ lọc dropdown** là cách dễ nhất cho người mới (không cần gõ text)
  - **Tìm kiếm bằng bản đồ** rất trực quan (chỉ cần click vào vị trí)
  - **Gợi ý tự động** giúp người dùng không cần nhớ chính xác tên/mã số
- **Hướng dẫn rõ ràng:**
  - Mọi tính năng đều có **tooltip, placeholder, và ví dụ** để người dùng hiểu cách sử dụng
  - **Banner hướng dẫn** cho người dùng lần đầu
  - **Modal hướng dẫn chi tiết** với ví dụ cụ thể
- **Thiết kế responsive:**
  - Hoạt động tốt trên cả desktop và mobile
  - Touch-friendly cho mobile (nút lớn, dễ click)
- **Performance:**
  - Tìm kiếm client-side (không cần gọi API) → Nhanh, mượt mà
  - Debounce để tránh tìm kiếm quá nhiều lần
  - Giới hạn số lượng kết quả hiển thị để tránh lag

---

## 📝 GHI CHÚ

### Các tính năng bổ sung đã làm (ngoài kế hoạch ban đầu):
1. **Hover popup**: Tự động mở popup khi hover vào marker
2. **File download trong popup**: Hiển thị và cho phép tải file đính kèm
3. **Auto-zoom sau khi tạo cống**: Tự động zoom đến vị trí cống mới sau khi tạo
4. **Readonly tọa độ**: Tọa độ từ bản đồ không thể chỉnh sửa trong form
5. **Styling cống mới**: Cống mới có màu xanh để dễ phân biệt

### Các vấn đề đã giải quyết:
1. ✅ Geometry không được tạo khi thêm cống mới → Đã sửa bằng cách tạo geometry trực tiếp trong INSERT
2. ✅ Cập nhật cống không hoạt động → Đã sửa bằng cách dùng POST với `_method=PUT`
3. ✅ Modal chồng lên bản đồ → Đã chuyển sang flow chuyển trang riêng
4. ✅ Tọa độ không tự động điền → Đã sửa bằng cách điền trước khi mở modal và dùng setTimeout

---

## 🎯 KẾ HOẠCH TIẾP THEO

### Ưu tiên cao:
1. **Hoàn thiện chức năng tìm kiếm (4.5)** - Cần thiết cho trải nghiệm người dùng
   - Tìm kiếm theo tên cống
   - Tìm kiếm theo mã số
   - Tìm kiếm theo tọa độ (nâng cao)

### Ưu tiên trung bình:
2. **Cải thiện UI/UX**
   - Thêm loading indicator khi đang tải dữ liệu
   - Thêm thông báo lỗi rõ ràng hơn
   - Tối ưu hiệu suất khi có nhiều cống (clustering)

### Ưu tiên thấp:
3. **Tính năng nâng cao**
   - Vẽ vùng (polygon) để tìm tất cả cống trong vùng đó
   - Export dữ liệu cống ra file GeoJSON
   - In bản đồ với các cống đã chọn

---

## 📊 TỔNG KẾT

**Đã hoàn thành:** 3/6 (50%)
- ✅ 4.1: Hiển thị Tile Layer
- ✅ 4.2: Hiển thị GeoJSON
- ⚠️ 4.3: Tích hợp Leaflet.draw (chỉ có vẽ điểm, chưa có vẽ vùng)
- ⚠️ 4.4: Gửi dữ liệu về API (có flow nhưng chưa hoàn thiện)
- ❌ 4.5: Tìm kiếm (chưa làm)
- ✅ 4.6: Hiển thị popup

**Còn lại:** 3/6 (50%)
- ⚠️ 4.3: Hoàn thiện Leaflet.draw (thêm vẽ vùng nếu cần)
- ⚠️ 4.4: Hoàn thiện flow gửi dữ liệu (kiểm tra và sửa lỗi nếu có)
- ❌ 4.5: Tìm kiếm theo tên/tọa độ

**Giai đoạn 4 đang ở mức 50%, cần hoàn thiện thêm!**

