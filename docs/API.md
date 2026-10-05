# API Documentation

## 📋 Tổng quan

API của hệ thống WebGIS Quản lý Cống cung cấp các endpoint để quản lý dữ liệu cống. API sử dụng JSON format và hỗ trợ GeoJSON cho dữ liệu địa lý.

## 🔐 Authentication

### Session-based Authentication

API sử dụng Laravel session-based authentication. Các request cần:
- Đăng nhập trước để có session
- Gửi CSRF token trong header hoặc form data
- Sử dụng cookies để maintain session

### CSRF Token

Để lấy CSRF token:

```html
<meta name="csrf-token" content="{{ csrf_token() }}">
```

Hoặc trong JavaScript:

```javascript
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
```

### Headers

```http
X-CSRF-TOKEN: {csrf_token}
Accept: application/json
Content-Type: application/json (hoặc multipart/form-data cho file upload)
```

## 📍 Base URL

```
http://your-domain.com/api
```

## 🔗 Endpoints

### 1. GET `/api/cong`

Lấy tất cả cống dưới dạng GeoJSON.

**Authentication:** Không cần (Public)

**Response:**

```json
{
  "type": "FeatureCollection",
  "features": [
    {
      "type": "Feature",
      "geometry": {
        "type": "Point",
        "coordinates": [106.660172, 10.762622]
      },
      "properties": {
        "ogc_fid": 1,
        "id": "C1615",
        "ten_cong_dap": "Cống ÔĐ4b",
        "dia_diem": "Quận 1",
        "ly_trinh": "KM 10+500",
        "toa_do_x": 106.660172,
        "toa_do_y": 10.762622,
        "file_url": "http://your-domain.com/storage/uploads/cong/1234567890_abc123.jpg",
        "file_name": "1234567890_abc123.jpg",
        "file_mime": "image/jpeg",
        "file_size": 102400,
        "ghi_chu": "Ghi chú về cống",
        ...
      }
    }
  ]
}
```

**Status Codes:**
- `200 OK`: Thành công
- `500 Internal Server Error`: Lỗi server

**Notes:**
- Chỉ trả về cống có geometry (không NULL)
- Sắp xếp theo `ogc_fid DESC` (mới nhất trước)
- Coordinates format: `[longitude, latitude]` (GeoJSON standard)

---

### 2. POST `/api/cong`

Tạo cống mới.

**Authentication:** Required (Admin only)

**Request Body:** `multipart/form-data` (FormData)

**Fields:**

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `toa_do_x` | number | ✅ Yes | Longitude (Kinh độ) |
| `toa_do_y` | number | ✅ Yes | Latitude (Vĩ độ) |
| `id` | string | No | Mã số cống |
| `ten_cong_dap` | string | No | Tên công đập |
| `ly_trinh` | string | No | Lý trình |
| `dia_diem` | string | No | Địa điểm |
| `file` | file | No | File đính kèm (image/PDF, max 5MB) |
| `ghi_chu` | text | No | Ghi chú |
| ... | ... | No | Các trường khác |

**Example Request:**

```javascript
const formData = new FormData();
formData.append('toa_do_x', 106.660172);
formData.append('toa_do_y', 10.762622);
formData.append('ten_cong_dap', 'Cống mới');
formData.append('file', fileInput.files[0]);

fetch('/api/cong', {
    method: 'POST',
    headers: {
        'X-CSRF-TOKEN': csrfToken,
        'Accept': 'application/json'
    },
    body: formData
});
```

**Response:**

```json
{
  "status": "success",
  "id": 123,
  "ogc_fid": 123,
  "toa_do_x": 106.660172,
  "toa_do_y": 10.762622,
  "file_url": "http://your-domain.com/storage/uploads/cong/1234567890_abc123.jpg",
  "message": "Đã thêm cống thành công với geometry",
  "has_geometry": true
}
```

**Status Codes:**
- `201 Created`: Tạo thành công
- `422 Unprocessable Entity`: Validation error
- `403 Forbidden`: Không có quyền (không phải Admin)
- `500 Internal Server Error`: Lỗi server

**Error Response:**

```json
{
  "status": "error",
  "message": "Validation error message",
  "errors": {
    "toa_do_x": ["The toa_do_x field is required."]
  }
}
```

---

### 3. PUT `/api/cong/{id}`

Cập nhật cống.

**Authentication:** Required (Admin only)

**Request Body:** `multipart/form-data` (FormData) với `_method=PUT`

**Fields:** Tương tự POST, tất cả đều optional (chỉ gửi trường cần cập nhật)

**Example Request:**

```javascript
const formData = new FormData();
formData.append('_method', 'PUT');
formData.append('ten_cong_dap', 'Tên cống mới');
formData.append('file', fileInput.files[0]);

fetch(`/api/cong/${id}`, {
    method: 'POST', // Laravel xử lý PUT qua _method
    headers: {
        'X-CSRF-TOKEN': csrfToken,
        'Accept': 'application/json'
    },
    body: formData
});
```

**Response:**

```json
{
  "status": "success",
  "id": 123,
  "ogc_fid": 123,
  "file_url": "http://your-domain.com/storage/uploads/cong/1234567890_abc123.jpg",
  "message": "Đã cập nhật cống thành công",
  "ten_cong_dap": "Tên cống mới"
}
```

**Status Codes:**
- `200 OK`: Cập nhật thành công
- `404 Not Found`: Không tìm thấy cống
- `422 Unprocessable Entity`: Validation error
- `403 Forbidden`: Không có quyền
- `500 Internal Server Error`: Lỗi server

**Notes:**
- Nếu upload file mới, file cũ sẽ bị xóa
- Nếu thay đổi tọa độ, geometry sẽ được cập nhật

---

### 4. DELETE `/api/cong/{id}`

Xóa cống.

**Authentication:** Required (Admin only)

**Example Request:**

```javascript
fetch(`/api/cong/${id}`, {
    method: 'DELETE',
    headers: {
        'X-CSRF-TOKEN': csrfToken,
        'Accept': 'application/json'
    }
});
```

**Response:**

```json
{
  "status": "success",
  "message": "Đã xóa cống thành công"
}
```

**Status Codes:**
- `200 OK`: Xóa thành công
- `404 Not Found`: Không tìm thấy cống
- `403 Forbidden`: Không có quyền
- `500 Internal Server Error`: Lỗi server

**Notes:**
- File đính kèm cũng sẽ bị xóa
- Hoạt động xóa được ghi log trước khi xóa

---

## 📊 Response Format

### Success Response

Tất cả success response đều có format:

```json
{
  "status": "success",
  "data": {...},
  "message": "Success message"
}
```

### Error Response

```json
{
  "status": "error",
  "message": "Error message",
  "errors": {
    "field": ["Error message for field"]
  }
}
```

## 🔒 Authorization

### Admin Only Endpoints

Các endpoint sau chỉ Admin mới được truy cập:
- `POST /api/cong`
- `PUT /api/cong/{id}`
- `DELETE /api/cong/{id}`

**Middleware:** `auth:web` + `admin`

**Check:** `auth()->user()->isAdmin() === true`

### Public Endpoints

- `GET /api/cong` - Mọi người đều có thể xem

## 📝 Validation Rules

### Create/Update Culvert

| Field | Rules |
|-------|-------|
| `toa_do_x` | required, numeric |
| `toa_do_y` | required, numeric |
| `id` | nullable, string, max:254 |
| `ten_cong_dap` | nullable, string, max:254 |
| `file` | nullable, file, mimes:jpg,jpeg,png,pdf, max:5120 (5MB) |

## 🗺️ GeoJSON Format

### Geometry

- **Type:** Point
- **Coordinates:** `[longitude, latitude]` (WGS84, EPSG:4326)
- **Example:**
```json
{
  "type": "Point",
  "coordinates": [106.660172, 10.762622]
}
```

### Properties

Tất cả thông tin cống được lưu trong `properties`:
- `ogc_fid`: Primary key
- `id`: Mã số cống
- `ten_cong_dap`: Tên cống
- `toa_do_x`, `toa_do_y`: Tọa độ backup
- `file_url`: URL file đính kèm (nếu có)
- ... (các trường khác)

## 📎 File Upload

### Supported Formats
- **Images:** JPG, JPEG, PNG
- **Documents:** PDF
- **Max Size:** 5MB

### File Storage
- **Path:** `storage/app/public/uploads/cong/`
- **Public URL:** `http://your-domain.com/storage/uploads/cong/{filename}`
- **Naming:** `{timestamp}_{random8chars}.{ext}`

### Example

```javascript
const formData = new FormData();
formData.append('toa_do_x', 106.660172);
formData.append('toa_do_y', 10.762622);
formData.append('file', fileInput.files[0]);

fetch('/api/cong', {
    method: 'POST',
    headers: {
        'X-CSRF-TOKEN': csrfToken,
        'Accept': 'application/json'
        // Không set Content-Type, browser sẽ tự set với boundary
    },
    body: formData
});
```

## ⚠️ Error Handling

### Common Errors

#### 401 Unauthorized
```json
{
  "message": "Unauthenticated."
}
```
**Giải pháp:** Đăng nhập trước

#### 403 Forbidden
```json
{
  "message": "Unauthorized. Admin access required."
}
```
**Giải pháp:** Cần quyền Admin

#### 422 Validation Error
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "toa_do_x": ["The toa_do_x field is required."]
  }
}
```
**Giải pháp:** Kiểm tra và sửa dữ liệu gửi lên

#### 404 Not Found
```json
{
  "status": "error",
  "message": "Không tìm thấy cống với ID: 123"
}
```
**Giải pháp:** Kiểm tra ID có đúng không

#### 500 Internal Server Error
```json
{
  "status": "error",
  "message": "Không thể thêm cống: Error message"
}
```
**Giải pháp:** Kiểm tra server logs

## 🔄 Rate Limiting

Hiện tại chưa có rate limiting. Có thể thêm sau nếu cần.

## 📚 Examples

### JavaScript (Fetch API)

```javascript
// Get all culverts
fetch('/api/cong')
    .then(response => response.json())
    .then(data => {
        console.log('Culverts:', data.features);
    });

// Create culvert
const formData = new FormData();
formData.append('toa_do_x', 106.660172);
formData.append('toa_do_y', 10.762622);
formData.append('ten_cong_dap', 'Cống mới');

fetch('/api/cong', {
    method: 'POST',
    headers: {
        'X-CSRF-TOKEN': csrfToken,
        'Accept': 'application/json'
    },
    body: formData
})
.then(response => response.json())
.then(data => {
    if (data.status === 'success') {
        console.log('Created:', data);
    }
});

// Update culvert
const formData = new FormData();
formData.append('_method', 'PUT');
formData.append('ten_cong_dap', 'Tên mới');

fetch(`/api/cong/${id}`, {
    method: 'POST',
    headers: {
        'X-CSRF-TOKEN': csrfToken,
        'Accept': 'application/json'
    },
    body: formData
});

// Delete culvert
fetch(`/api/cong/${id}`, {
    method: 'DELETE',
    headers: {
        'X-CSRF-TOKEN': csrfToken,
        'Accept': 'application/json'
    }
});
```

### cURL Examples

```bash
# Get all culverts
curl -X GET http://localhost:8000/api/cong

# Create culvert
curl -X POST http://localhost:8000/api/cong \
  -H "X-CSRF-TOKEN: {token}" \
  -H "Cookie: {session_cookie}" \
  -F "toa_do_x=106.660172" \
  -F "toa_do_y=10.762622" \
  -F "ten_cong_dap=Cống mới" \
  -F "file=@/path/to/image.jpg"

# Update culvert
curl -X POST http://localhost:8000/api/cong/123 \
  -H "X-CSRF-TOKEN: {token}" \
  -H "Cookie: {session_cookie}" \
  -F "_method=PUT" \
  -F "ten_cong_dap=Tên mới"

# Delete culvert
curl -X DELETE http://localhost:8000/api/cong/123 \
  -H "X-CSRF-TOKEN: {token}" \
  -H "Cookie: {session_cookie}"
```

## 📌 Notes

1. **CSRF Protection:** Tất cả POST/PUT/DELETE requests cần CSRF token
2. **Session-based:** API dùng session, không phải Bearer token
3. **FormData:** POST/PUT cần dùng FormData (multipart/form-data) để upload file
4. **GeoJSON:** Response format là GeoJSON FeatureCollection
5. **Coordinates:** Format `[longitude, latitude]` (GeoJSON standard)

