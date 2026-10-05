# WebGIS Quản lý Cống

Hệ thống WebGIS quản lý và hiển thị dữ liệu cống trên bản đồ tương tác. Hệ thống hỗ trợ quản lý CRUD cống, tìm kiếm, lọc, và theo dõi lịch sử hoạt động.

## ✨ Tính năng chính

### Cho Admin
- ✅ Quản lý cống (Thêm, Sửa, Xóa)
- ✅ Vẽ điểm trên bản đồ để thêm cống nhanh
- ✅ Tìm kiếm và lọc cống
- ✅ Upload file đính kèm (ảnh, PDF)
- ✅ Xem lịch sử hoạt động (logs)
- ✅ Quản lý người dùng

### Cho User
- ✅ Xem bản đồ với tất cả cống
- ✅ Tìm kiếm và lọc cống
- ✅ Xem chi tiết cống
- ✅ Tải file đính kèm

## 🛠️ Công nghệ

- **Backend:** Laravel 12.x
- **Frontend:** Blade Templates, Tailwind CSS, JavaScript
- **Map:** LeafletJS, Leaflet.draw
- **Database:** PostgreSQL với PostGIS
- **Authentication:** Laravel Breeze (Session-based)

## 📋 Yêu cầu

- PHP 8.2+
- PostgreSQL 12+ với PostGIS 3.0+
- Composer 2.0+
- Node.js 18+ (cho Vite)

## 🚀 Cài đặt nhanh

```bash
# Clone repository
git clone <repository-url>
cd webgis-laravel

# Cài đặt dependencies
composer install
npm install

# Cấu hình môi trường
cp .env.example .env
php artisan key:generate

# Cấu hình database trong .env
# DB_CONNECTION=pgsql
# DB_DATABASE=webgis
# ...

# Tạo database và enable PostGIS
psql -U postgres
CREATE DATABASE webgis;
\c webgis
CREATE EXTENSION postgis;

# Chạy migrations
php artisan migrate

# Tạo storage symlink
php artisan storage:link

# Tạo Admin đầu tiên
php artisan tinker
# (xem docs/INSTALLATION.md)

# Chạy server
php artisan serve
```

Truy cập: `http://localhost:8000`

## 📚 Tài liệu

- **[Kiến trúc hệ thống](docs/ARCHITECTURE.md)** - Mô tả kiến trúc và công nghệ
- **[Hướng dẫn cài đặt](docs/INSTALLATION.md)** - Hướng dẫn cài đặt chi tiết
- **[Hướng dẫn Admin](docs/USER_GUIDE_ADMIN.md)** - Hướng dẫn sử dụng cho Admin
- **[Hướng dẫn User](docs/USER_GUIDE_USER.md)** - Hướng dẫn sử dụng cho User
- **[API Documentation](docs/API.md)** - Tài liệu API endpoints

## 📊 Sơ đồ hệ thống

- **[ERD](docs/diagrams/ERD.md)** - Entity Relationship Diagram
- **[Use Case](docs/diagrams/UseCase.md)** - Use Case Diagram
- **[DFD](docs/diagrams/DFD.md)** - Data Flow Diagram
- **[Sequence](docs/diagrams/Sequence.md)** - Sequence Diagrams

## 🗺️ Cấu trúc dự án

```
webgis-laravel/
├── app/
│   ├── Http/Controllers/    # Controllers
│   ├── Models/             # Eloquent Models
│   └── Services/           # Business Logic Services
├── database/
│   └── migrations/         # Database migrations
├── resources/
│   ├── views/             # Blade templates
│   └── js/                # JavaScript files
├── routes/
│   └── web.php            # Web routes
└── docs/                  # Tài liệu
```

## 🔐 Authentication

- **Admin:** Đầy đủ quyền CRUD
- **User:** Chỉ xem

## 📡 API Endpoints

### Public
- `GET /api/cong` - Lấy tất cả cống (GeoJSON)

### Admin Only
- `POST /api/cong` - Tạo cống mới
- `PUT /api/cong/{id}` - Cập nhật cống
- `DELETE /api/cong/{id}` - Xóa cống

Xem chi tiết: [API Documentation](docs/API.md)

## 🗄️ Database

### Bảng chính
- `users` - Người dùng
- `cong` - Dữ liệu cống (với PostGIS geometry)
- `activity_logs` - Lịch sử hoạt động

## 📝 Logging

Hệ thống tự động ghi log các hoạt động:
- Tạo, sửa, xóa cống
- Đăng nhập, đăng xuất
- Xem chi tiết

Admin có thể xem logs tại `/admin/logs`

## 🧪 Testing

```bash
php artisan test
```

## 📄 License

[Specify your license here]

## 👥 Contributors

[Your name/team]

## 📞 Support

Nếu có vấn đề, vui lòng:
1. Kiểm tra tài liệu trong `docs/`
2. Xem logs trong `/admin/logs` (nếu là Admin)
3. Liên hệ quản trị viên hệ thống

## 🔄 Changelog

### Version 1.0.0
- ✅ Quản lý CRUD cống
- ✅ Hiển thị bản đồ với LeafletJS
- ✅ Tìm kiếm và lọc
- ✅ Upload file đính kèm
- ✅ Hệ thống logging
- ✅ Phân quyền Admin/User

## 🎯 Roadmap

- [ ] Export dữ liệu ra Excel/CSV
- [ ] Import dữ liệu từ Excel/CSV
- [ ] Thống kê và báo cáo
- [ ] Mobile responsive improvements
- [ ] Offline map support

---

**Made with ❤️ using Laravel & PostGIS**

