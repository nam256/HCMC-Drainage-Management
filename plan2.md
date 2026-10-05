# 📋 KẾ HOẠCH CẢI THIỆN CHỨC NĂNG VẼ ĐIỂM VÀ TẠO CỐNG

## 🎯 MỤC TIÊU

Cải thiện trải nghiệm người dùng khi vẽ điểm và tạo cống mới:
1. **Tự động điền tọa độ**: Khi đánh dấu (vẽ điểm), tự động lấy tọa độ và điền vào form - người dùng không cần điền tọa độ nữa
2. **Tự động chuyển trang**: Đánh dấu xong tự động chuyển sang trang tạo cống với tọa độ đã setup

---

## 📌 YÊU CẦU 1: TỰ ĐỘNG ĐIỀN TỌA ĐỘ KHI ĐÁNH DẤU

### Mục tiêu
Khi người dùng vẽ điểm trên bản đồ, tọa độ X, Y tự động được điền vào form. Người dùng chỉ cần điền các thông tin khác (tên, lý trình, địa điểm...).

### Hiện trạng
- ✅ Đã có logic điền tọa độ khi vẽ điểm
- ⚠️ Cần đảm bảo tọa độ được điền đúng và không bị mất
- ⚠️ Cần disable hoặc readonly các trường tọa độ để người dùng không thể chỉnh sửa (hoặc cho phép chỉnh sửa nếu cần)

### Các bước thực hiện

#### Bước 1: Đảm bảo tọa độ được điền đúng
- [x] Khi vẽ điểm, lấy tọa độ từ `latlng.lng` (X) và `latlng.lat` (Y)
- [x] Điền vào form TRƯỚC khi mở modal
- [x] Kiểm tra và điền lại nếu bị mất (đã có setTimeout check)

#### Bước 2: Tùy chọn - Disable/Readonly trường tọa độ
- [ ] **Option A**: Disable các trường tọa độ khi được điền tự động (người dùng không thể chỉnh sửa)
- [ ] **Option B**: Giữ nguyên cho phép chỉnh sửa (nếu người dùng muốn điều chỉnh)
- [ ] **Option C**: Thêm nút "Chọn lại vị trí" để vẽ lại điểm

#### Bước 3: Hiển thị thông báo
- [ ] Thêm thông báo trong form: "Tọa độ đã được tự động điền từ điểm vẽ trên bản đồ"
- [ ] Highlight các trường tọa độ để người dùng biết đã được điền tự động

### Ước tính thời gian: 30 phút - 1 giờ

### File cần sửa:
- `webgis-laravel/resources/views/map.blade.php` (form "Thêm cống mới")

---

## 📌 YÊU CẦU 2: TỰ ĐỘNG CHUYỂN TRANG SAU KHI ĐÁNH DẤU

### Mục tiêu
Sau khi đánh dấu (vẽ điểm) xong, tự động chuyển sang trang tạo cống (`/cong/manage`) với tọa độ đã được setup sẵn.

### Hiện trạng
- ✅ Hiện tại: Vẽ điểm → Mở modal form trên cùng trang bản đồ
- ⚠️ Yêu cầu mới: Vẽ điểm → Chuyển sang trang `/cong/manage` với tọa độ đã điền

### Các bước thực hiện

#### Bước 1: Thay đổi hành vi sau khi vẽ điểm
- [x] Thay vì mở modal `showAddCongModal()`, chuyển sang trang `/cong/manage`
- [x] Truyền tọa độ qua URL parameter hoặc sessionStorage
- [x] Format: `/cong/manage?add=1&x=106.660172&y=10.762622`

#### Bước 2: Xử lý trên trang manage
- [x] Kiểm tra URL parameters khi trang load
- [x] Nếu có `add=1` và tọa độ X, Y → Tự động mở modal "Thêm cống mới"
- [x] Tự động điền tọa độ vào form
- [x] Đặt readonly cho các trường tọa độ
- [x] Thêm style và thông báo "(Tự động từ bản đồ)"
- [x] Focus vào trường đầu tiên (tên cống hoặc mã số)
- [x] Clean URL sau khi xử lý

#### Bước 3: Sau khi tạo xong
- [x] Sau khi submit form thành công, chuyển về trang bản đồ (`/map`)
- [x] Truyền tọa độ qua URL parameter: `/map?zoom=x,y`
- [x] Tự động zoom đến vị trí cống mới (level 16)
- [x] Tự động mở popup của cống mới
- [x] Clean URL sau khi zoom

### Ước tính thời gian: 1-2 giờ

### File cần sửa:
- `webgis-laravel/resources/views/map.blade.php` (sự kiện vẽ điểm)
- `webgis-laravel/resources/views/cong/manage.blade.php` (xử lý URL parameters và tự động mở modal)

---

## 🔄 FLOW MỚI

### Flow hiện tại:
```
1. Click "Bật vẽ điểm"
2. Click vào bản đồ → Vẽ điểm
3. Tọa độ tự động điền vào form
4. Modal mở trên cùng trang bản đồ
5. Điền thông tin và lưu
6. Bản đồ reload, zoom đến vị trí mới
```

### Flow mới (theo yêu cầu):
```
1. Click "Bật vẽ điểm"
2. Click vào bản đồ → Vẽ điểm
3. Tọa độ tự động được lưu
4. Tự động chuyển sang trang /cong/manage?add=1&x=...&y=...
5. Trang manage tự động mở modal "Thêm cống mới"
6. Tọa độ đã được điền sẵn (readonly hoặc disabled)
7. Người dùng chỉ cần điền: tên, lý trình, địa điểm, ghi chú...
8. Click "Lưu"
9. Tự động chuyển về trang bản đồ (/map)
10. Tự động zoom đến vị trí cống mới
11. Cống mới hiển thị với màu xanh dương
```

---

## 📝 CHI TIẾT KỸ THUẬT

### 1. Truyền tọa độ giữa các trang

#### Cách 1: URL Parameters (Khuyến nghị)
```javascript
// Trong map.blade.php - sau khi vẽ điểm
const x = latlng.lng;
const y = latlng.lat;
window.location.href = `/cong/manage?add=1&x=${x}&y=${y}`;
```

#### Cách 2: SessionStorage
```javascript
// Trong map.blade.php
sessionStorage.setItem('newCongCoords', JSON.stringify({ x, y }));

// Trong manage.blade.php
const coords = JSON.parse(sessionStorage.getItem('newCongCoords') || '{}');
if (coords.x && coords.y) {
    // Điền tọa độ và mở modal
    sessionStorage.removeItem('newCongCoords'); // Xóa sau khi dùng
}
```

**Khuyến nghị**: Dùng URL Parameters vì đơn giản và dễ debug.

### 2. Xử lý trên trang manage

```javascript
// Trong manage.blade.php - khi trang load
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const add = urlParams.get('add');
    const x = urlParams.get('x');
    const y = urlParams.get('y');
    
    if (add === '1' && x && y) {
        // Điền tọa độ vào form
        document.getElementById('edit_toa_do_x').value = x;
        document.getElementById('edit_toa_do_y').value = y;
        
        // Disable hoặc readonly các trường tọa độ
        document.getElementById('edit_toa_do_x').readOnly = true;
        document.getElementById('edit_toa_do_y').readOnly = true;
        
        // Mở modal "Thêm cống mới"
        showAddModal();
        
        // Focus vào trường đầu tiên
        setTimeout(() => {
            document.getElementById('edit_ten_cong_dap').focus();
        }, 300);
        
        // Xóa parameters khỏi URL (clean URL)
        window.history.replaceState({}, document.title, '/cong/manage');
    }
});
```

### 3. Chuyển về bản đồ sau khi tạo xong

```javascript
// Trong manage.blade.php - sau khi submit form thành công
if (data.status === 'success') {
    const x = formData.get('toa_do_x');
    const y = formData.get('toa_do_y');
    
    // Chuyển về trang bản đồ với tọa độ
    window.location.href = `/map?zoom=${x},${y}`;
    
    // Hoặc đơn giản hơn:
    // window.location.href = '/map';
}
```

### 4. Xử lý trên trang bản đồ khi có parameter zoom

```javascript
// Trong map.blade.php - khi trang load
const urlParams = new URLSearchParams(window.location.search);
const zoom = urlParams.get('zoom');
if (zoom) {
    const [x, y] = zoom.split(',').map(Number);
    if (x && y) {
        // Zoom đến vị trí sau khi load xong
        loadCongData().then(() => {
            map.setView([y, x], 16);
            // Tìm và mở popup của cống mới nếu có
        });
    }
}
```

---

## ✅ CHECKLIST THỰC HIỆN

### Yêu cầu 1: Tự động điền tọa độ
- [x] Đảm bảo tọa độ được điền khi vẽ điểm
- [ ] Tùy chọn: Disable/Readonly trường tọa độ
- [ ] Thêm thông báo trong form
- [ ] Highlight trường tọa độ

### Yêu cầu 2: Tự động chuyển trang
- [x] Sửa logic sau khi vẽ điểm: chuyển sang `/cong/manage` thay vì mở modal
- [x] Truyền tọa độ qua URL parameters
- [x] Xử lý trên trang manage: tự động mở modal và điền tọa độ
- [x] Đặt readonly cho các trường tọa độ (người dùng không thể chỉnh sửa)
- [x] Thêm style và thông báo cho trường tọa độ
- [x] Sau khi tạo xong: chuyển về trang bản đồ
- [x] Tự động zoom đến vị trí cống mới
- [x] Tự động mở popup của cống mới

---

## 🎯 KẾT QUẢ MONG ĐỢI

Sau khi hoàn thành:
- ✅ Khi vẽ điểm, tọa độ tự động được điền vào form
- ✅ Người dùng không cần điền tọa độ, chỉ cần điền thông tin khác
- ✅ Sau khi vẽ điểm, tự động chuyển sang trang tạo cống
- ✅ Trang tạo cống tự động mở modal với tọa độ đã setup
- ✅ Sau khi tạo xong, tự động chuyển về bản đồ và zoom đến vị trí mới
- ✅ Trải nghiệm người dùng mượt mà và tự động hóa

---

## ⏱️ ƯỚC TÍNH THỜI GIAN

- **Yêu cầu 1**: 30 phút - 1 giờ
- **Yêu cầu 2**: 1-2 giờ
- **Tổng cộng**: 1.5 - 3 giờ

---

## 🔧 GHI CHÚ

- Có thể giữ lại cả 2 cách: mở modal trên bản đồ HOẶC chuyển sang trang manage
- Thêm nút "Tạo trên trang quản lý" trong modal trên bản đồ để người dùng có lựa chọn
- Hoặc thêm setting để người dùng chọn hành vi mặc định

---

**Ngày tạo:** 2025-01-15
**Cập nhật lần cuối:** 2025-01-15

