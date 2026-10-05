# Kiến trúc hệ thống WebGIS Quản lý Cống

## 📋 Tổng quan

Hệ thống WebGIS Quản lý Cống là một ứng dụng web cho phép quản lý và hiển thị dữ liệu cống trên bản đồ tương tác. Hệ thống hỗ trợ hai loại người dùng: **Admin** (có đầy đủ quyền CRUD) và **User** (chỉ xem).

## 🏗️ Kiến trúc tổng thể

```
┌─────────────────────────────────────────────────────────┐
│                    FRONTEND LAYER                        │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐  │
│  │   Blade      │  │   Tailwind   │  │  JavaScript  │  │
│  │  Templates   │  │     CSS      │  │   LeafletJS  │  │
│  └──────────────┘  └──────────────┘  └──────────────┘  │
└─────────────────────────────────────────────────────────┘
                          ↕ HTTP/HTTPS
┌─────────────────────────────────────────────────────────┐
│                    BACKEND LAYER                         │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐  │
│  │   Laravel    │  │ Controllers  │  │   Services   │  │
│  │   Framework  │  │   & Routes   │  │   & Models   │  │
│  └──────────────┘  └──────────────┘  └──────────────┘  │
└─────────────────────────────────────────────────────────┘
                          ↕ SQL/PostGIS
┌─────────────────────────────────────────────────────────┐
│                   DATABASE LAYER                         │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐  │
│  │ PostgreSQL   │  │   PostGIS    │  │ File Storage │  │
│  │   Database   │  │  Extension   │  │   (Public)   │  │
│  └──────────────┘  └──────────────┘  └──────────────┘  │
└─────────────────────────────────────────────────────────┘
```

## 🛠️ Công nghệ sử dụng

### Backend
- **Framework:** Laravel 12.x
- **Language:** PHP 8.2+
- **ORM:** Eloquent ORM
- **Validation:** Laravel Form Request Validation
- **Authentication:** Laravel Breeze (Session-based)

### Frontend
- **Templating:** Blade Templates
- **CSS Framework:** Tailwind CSS
- **JavaScript Libraries:**
  - LeafletJS (v1.9.4) - Bản đồ tương tác
  - Leaflet.draw (v1.0.4) - Công cụ vẽ trên bản đồ
- **Map Tiles:** OpenStreetMap (OSM)

### Database
- **Database:** PostgreSQL 12+
- **Spatial Extension:** PostGIS 3.0+
- **Storage:** Laravel Storage (Public disk) cho file uploads

### Development Tools
- **Package Manager:** Composer
- **Build Tool:** Vite (cho assets)
- **Version Control:** Git

## 📁 Cấu trúc thư mục dự án

```
webgis-laravel/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── CongController.php          # API CRUD cống
│   │   │   ├── MapController.php          # Trang bản đồ & quản lý
│   │   │   ├── ActivityLogController.php  # Xem logs
│   │   │   └── Auth/
│   │   │       └── AuthenticatedSessionController.php
│   │   ├── Middleware/
│   │   │   └── EnsureUserIsAdmin.php     # Middleware kiểm tra Admin
│   │   └── Requests/
│   ├── Models/
│   │   ├── User.php                       # Model User
│   │   ├── Cong.php                       # Model Cống
│   │   └── ActivityLog.php               # Model Log
│   ├── Services/
│   │   └── ActivityLogger.php            # Service ghi log
│   └── Providers/
├── database/
│   ├── migrations/
│   │   ├── create_users_table.php
│   │   ├── create_cong_table.php
│   │   └── create_activity_logs_table.php
│   └── seeders/
├── resources/
│   ├── views/
│   │   ├── map.blade.php                  # Trang bản đồ chính
│   │   ├── cong/
│   │   │   └── manage.blade.php          # Trang quản lý cống
│   │   ├── admin/
│   │   │   └── logs.blade.php            # Trang xem logs
│   │   └── layouts/
│   │       └── app.blade.php             # Layout chính
│   ├── js/
│   └── css/
├── routes/
│   ├── web.php                           # Web routes
│   └── auth.php                          # Auth routes
├── public/
│   ├── storage/                          # File uploads (symlink)
│   └── index.php
├── storage/
│   └── app/
│       └── public/
│           └── uploads/
│               └── cong/                 # File cống
├── docs/
│   └── diagrams/                         # Sơ đồ hệ thống
└── config/
    ├── database.php
    └── filesystems.php
```

## 🔌 API Endpoints

### Public API (Không cần authentication)

#### GET `/api/cong`
- **Mô tả:** Lấy tất cả cống dưới dạng GeoJSON
- **Response:** GeoJSON FeatureCollection
- **Example:**
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
        "file_url": "http://..."
      }
    }
  ]
}
```

### Protected API (Cần authentication + admin role)

#### POST `/api/cong`
- **Mô tả:** Tạo cống mới
- **Authentication:** Required (Admin only)
- **Request Body:** FormData
  - `toa_do_x` (required): Longitude
  - `toa_do_y` (required): Latitude
  - `id`: Mã số cống
  - `ten_cong_dap`: Tên cống
  - `file`: File đính kèm (image/PDF, max 5MB)
  - ... (các trường khác)
- **Response:**
```json
{
  "status": "success",
  "id": 123,
  "ogc_fid": 123,
  "toa_do_x": 106.660172,
  "toa_do_y": 10.762622,
  "file_url": "http://...",
  "has_geometry": true
}
```

#### PUT `/api/cong/{id}`
- **Mô tả:** Cập nhật cống
- **Authentication:** Required (Admin only)
- **Request Body:** FormData (tương tự POST)
- **Response:** Tương tự POST

#### DELETE `/api/cong/{id}`
- **Mô tả:** Xóa cống
- **Authentication:** Required (Admin only)
- **Response:**
```json
{
  "status": "success",
  "message": "Đã xóa cống thành công"
}
```

## 🗄️ Database Schema

### Bảng `users`
- **Mục đích:** Lưu trữ thông tin người dùng
- **Primary Key:** `id`
- **Các trường chính:**
  - `id`: bigint, auto increment
  - `name`: string
  - `email`: string, unique
  - `password`: string (hashed)
  - `role`: enum('admin', 'user')
  - `email_verified_at`: timestamp
  - `created_at`, `updated_at`: timestamps

### Bảng `cong`
- **Mục đích:** Lưu trữ dữ liệu cống
- **Primary Key:** `ogc_fid`
- **Các trường chính:**
  - `ogc_fid`: integer, auto increment (PostGIS convention)
  - `id`: string (mã số cống)
  - `ten_cong_dap`: string (tên cống)
  - `toa_do_x`, `toa_do_y`: decimal (tọa độ backup)
  - `geom`: geometry (PostGIS Point, SRID 4326)
  - `file_path`, `file_name`, `file_mime`, `file_size`: file info
  - ... (nhiều trường metadata khác)
- **Indexes:**
  - Primary: `ogc_fid`
  - Spatial: `geom` (GIST index)
  - Regular: `id`, `ten_cong_dap`

### Bảng `activity_logs`
- **Mục đích:** Lưu trữ lịch sử hoạt động
- **Primary Key:** `id`
- **Các trường chính:**
  - `id`: bigint, auto increment
  - `user_id`: bigint, foreign key → users.id (nullable)
  - `action`: string (create, update, delete, view, login, logout)
  - `model_type`: string (polymorphic)
  - `model_id`: bigint (polymorphic)
  - `description`: text
  - `ip_address`: string
  - `user_agent`: text
  - `changes`: json (old/new values)
  - `created_at`: timestamp
- **Indexes:**
  - `user_id`
  - `action`
  - `(model_type, model_id)` composite
  - `created_at`

## 🔐 Authentication & Authorization

### Authentication
- **Method:** Session-based authentication (Laravel Breeze)
- **Middleware:** `auth`
- **Login:** `/login` (POST)
- **Logout:** `/logout` (POST)

### Authorization
- **Role-based:** 
  - `admin`: Đầy đủ quyền (CRUD cống, xem logs, tạo admin)
  - `user`: Chỉ xem (xem bản đồ, tìm kiếm, xem chi tiết)
- **Middleware:** `admin` (kiểm tra `role === 'admin'`)
- **Method:** `User::isAdmin()` - kiểm tra role

### Protected Routes
- `/cong/manage` - Chỉ Admin
- `/admin/logs` - Chỉ Admin
- `/api/cong` (POST/PUT/DELETE) - Chỉ Admin

## 🗺️ Map Integration

### LeafletJS Configuration
- **Tile Layer:** OpenStreetMap
- **Default Center:** [10.762622, 106.660172] (TP.HCM)
- **Default Zoom:** 13
- **Max Zoom:** 19
- **Coordinate System:** WGS84 (EPSG:4326)

### GeoJSON Data Flow
1. Frontend gọi `GET /api/cong`
2. Backend query từ PostgreSQL với PostGIS
3. Convert geometry sang GeoJSON format
4. Trả về FeatureCollection
5. Frontend render bằng `L.geoJSON()`

### Drawing Tools (Leaflet.draw)
- **Enabled for:** Admin only
- **Drawing Mode:** Marker only (Point)
- **Flow:**
  1. Admin click "Bật vẽ điểm"
  2. Vẽ điểm trên bản đồ
  3. Lấy tọa độ (lat, lng)
  4. Redirect đến `/cong/manage?add=1&x=...&y=...`
  5. Form tự động điền tọa độ
  6. Sau khi tạo, redirect về map với zoom

## 📝 Logging System

### Activity Logger Service
- **Location:** `app/Services/ActivityLogger.php`
- **Methods:**
  - `logCreate($model)` - Ghi log khi tạo mới
  - `logUpdate($model, $changes)` - Ghi log khi cập nhật
  - `logDelete($model)` - Ghi log khi xóa
  - `logView($model)` - Ghi log khi xem
  - `logLogin($user)` - Ghi log đăng nhập
  - `logLogout($user)` - Ghi log đăng xuất

### Log Information Captured
- User ID và thông tin
- Action type
- Model type và ID
- Description (tự động generate)
- IP Address
- User Agent
- Changes (old/new values cho update)
- Timestamp

## 📦 File Upload System

### Storage Configuration
- **Disk:** `public`
- **Path:** `storage/app/public/uploads/cong/`
- **Symlink:** `public/storage` → `storage/app/public`
- **Max Size:** 5MB
- **Allowed Types:** JPG, JPEG, PNG, PDF

### File Naming
- Format: `{timestamp}_{random8chars}.{ext}`
- Example: `1700123456_aBcD1234.jpg`

## 🔄 Data Flow

### Create Culvert Flow
1. User vẽ điểm hoặc điền form
2. Frontend gửi FormData đến `/api/cong` (POST)
3. Backend validate input
4. Upload file (nếu có)
5. Tạo geometry từ coordinates: `ST_SetSRID(ST_MakePoint(x, y), 4326)`
6. Insert vào database với geometry
7. Ghi log activity
8. Return response với ID
9. Frontend redirect về map và zoom đến vị trí mới

### Update Culvert Flow
1. User click "Sửa" và điền form
2. Frontend gửi FormData đến `/api/cong/{id}` (PUT)
3. Backend load model hiện tại
4. Track changes (dirty attributes)
5. Update geometry nếu coordinates thay đổi
6. Update file nếu có upload mới
7. Save to database
8. Ghi log với changes
9. Return success

### Search Flow
1. User nhập từ khóa
2. Frontend filter GeoJSON data client-side
3. Highlight matching culverts
4. Zoom to results
5. Show results list

## 🚀 Performance Considerations

### Database
- Spatial indexes trên `geom` column (GIST)
- Regular indexes trên các cột thường query
- Pagination cho danh sách lớn

### Frontend
- Lazy loading cho map tiles
- Client-side filtering cho search
- Debounce cho search input
- Cache GeoJSON data trong session

### File Storage
- Symlink để serve files trực tiếp
- Validate file size và type trước khi upload

## 🔒 Security

### Authentication
- Password hashing: bcrypt
- CSRF protection: Laravel CSRF tokens
- Session security: HttpOnly cookies

### Authorization
- Role-based access control
- Middleware protection cho admin routes
- API endpoint protection

### Input Validation
- Laravel Form Request Validation
- SQL injection protection: Eloquent ORM (parameterized queries)
- XSS protection: Blade auto-escaping

### File Upload Security
- File type validation
- File size limits
- Unique filename generation
- Storage outside web root (via symlink)

## 📊 Monitoring & Logging

### Application Logs
- Laravel log: `storage/logs/laravel.log`
- Activity logs: Database table `activity_logs`
- Error tracking: Laravel exception handler

### Log Retention
- Application logs: Rotate theo Laravel config
- Activity logs: Lưu vĩnh viễn (có thể thêm cleanup job sau)

## 🔄 Deployment Considerations

### Environment Variables
- `APP_ENV`: production/development
- `DB_CONNECTION`: pgsql
- `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`
- `FILESYSTEM_DISK`: public

### Required Services
- PHP 8.2+ với extensions: pdo_pgsql, pgsql, gd, fileinfo
- PostgreSQL 12+ với PostGIS 3.0+
- Web server: Apache/Nginx
- Composer

### Post-Deployment
1. Run migrations: `php artisan migrate`
2. Create storage symlink: `php artisan storage:link`
3. Set permissions cho storage
4. Clear cache: `php artisan config:cache`, `php artisan route:cache`

## 📚 References

- [Laravel Documentation](https://laravel.com/docs)
- [PostGIS Documentation](https://postgis.net/documentation/)
- [Leaflet Documentation](https://leafletjs.com/)
- [Leaflet.draw Documentation](https://github.com/Leaflet/Leaflet.draw)

