# Hướng dẫn cài đặt WebGIS Quản lý Cống

## 📋 Yêu cầu hệ thống

### Server Requirements
- **PHP:** 8.2 hoặc cao hơn
- **Database:** PostgreSQL 12+ với PostGIS 3.0+
- **Web Server:** Apache 2.4+ hoặc Nginx
- **Composer:** 2.0+
- **Node.js:** 18+ (cho Vite build assets)

### PHP Extensions Required
- `pdo_pgsql` - PostgreSQL PDO driver
- `pgsql` - PostgreSQL extension
- `mbstring` - Multibyte string support
- `xml` - XML support
- `gd` hoặc `imagick` - Image processing
- `fileinfo` - File type detection
- `openssl` - SSL support
- `json` - JSON support
- `bcmath` - BCMath support

## 🚀 Cài đặt

### Bước 1: Clone hoặc tải project

```bash
cd /path/to/your/project
# Hoặc clone từ Git repository
```

### Bước 2: Cài đặt dependencies

```bash
cd webgis-laravel
composer install
```

### Bước 3: Cấu hình môi trường

```bash
cp .env.example .env
php artisan key:generate
```

Chỉnh sửa file `.env` với thông tin của bạn:

```env
APP_NAME="WebGIS Quản lý Cống"
APP_ENV=local
APP_KEY=base64:... (đã generate)
APP_DEBUG=true
APP_URL=http://localhost:8000

# Database Configuration
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=webgis
DB_USERNAME=postgres
DB_PASSWORD=your_password

# File Storage
FILESYSTEM_DISK=public
```

### Bước 4: Cấu hình PostgreSQL và PostGIS

#### 4.1. Tạo database

```sql
-- Kết nối PostgreSQL
psql -U postgres

-- Tạo database
CREATE DATABASE webgis;

-- Kết nối vào database
\c webgis

-- Enable PostGIS extension
CREATE EXTENSION IF NOT EXISTS postgis;
CREATE EXTENSION IF NOT EXISTS postgis_topology;

-- Kiểm tra PostGIS đã được cài đặt
SELECT PostGIS_version();
```

#### 4.2. Kiểm tra PostGIS

```sql
-- Kiểm tra PostGIS version
SELECT PostGIS_version();

-- Kiểm tra geometry_columns
SELECT * FROM geometry_columns;
```

### Bước 5: Chạy migrations

```bash
php artisan migrate
```

Lệnh này sẽ tạo các bảng:
- `users`
- `cong` (với geometry column)
- `activity_logs`

### Bước 6: Tạo storage symlink

```bash
php artisan storage:link
```

Lệnh này tạo symlink từ `public/storage` đến `storage/app/public` để có thể truy cập file uploads qua web.

### Bước 7: Set permissions (Linux/Mac)

```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### Bước 8: Tạo user Admin đầu tiên

Có 2 cách:

#### Cách 1: Sử dụng route đăng ký Admin (nếu có)

Truy cập: `http://localhost:8000/register-admin`

#### Cách 2: Tạo bằng tinker

```bash
php artisan tinker
```

Trong tinker:

```php
$user = new App\Models\User();
$user->name = 'Admin';
$user->email = 'admin@example.com';
$user->password = Hash::make('password123');
$user->role = 'admin';
$user->email_verified_at = now();
$user->save();
```

Hoặc dùng SQL trực tiếp:

```sql
INSERT INTO users (name, email, password, role, email_verified_at, created_at, updated_at)
VALUES (
    'Admin',
    'admin@example.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password: password
    'admin',
    NOW(),
    NOW(),
    NOW()
);
```

### Bước 9: Build assets (nếu có)

```bash
npm install
npm run build
```

Hoặc development mode:

```bash
npm run dev
```

### Bước 10: Chạy server

#### Development

```bash
php artisan serve
```

Truy cập: `http://localhost:8000`

#### Production

Cấu hình web server (Apache/Nginx) để point đến thư mục `public/`

## ✅ Kiểm tra cài đặt

### 1. Kiểm tra database connection

```bash
php artisan tinker
```

```php
DB::connection()->getPdo();
// Nếu không có lỗi, database đã kết nối thành công
```

### 2. Kiểm tra PostGIS

```bash
php artisan tinker
```

```php
DB::select("SELECT PostGIS_version()");
// Nên trả về version PostGIS
```

### 3. Kiểm tra bảng đã tạo

```bash
php artisan tinker
```

```php
DB::select("SELECT table_name FROM information_schema.tables WHERE table_schema = 'public'");
// Nên thấy: users, cong, activity_logs
```

### 4. Kiểm tra geometry column

```bash
php artisan tinker
```

```php
DB::select("SELECT * FROM geometry_columns WHERE f_table_name = 'cong'");
// Nên thấy geometry column của bảng cong
```

### 5. Kiểm tra storage symlink

```bash
ls -la public/storage
# Nên thấy symlink đến storage/app/public
```

## 🔧 Cấu hình bổ sung

### Cấu hình timezone

Trong `.env`:

```env
APP_TIMEZONE=Asia/Ho_Chi_Minh
```

### Cấu hình session

Trong `.env`:

```env
SESSION_DRIVER=file
SESSION_LIFETIME=120
```

### Cấu hình file upload

Trong `config/filesystems.php` hoặc `.env`:

```env
FILESYSTEM_DISK=public
```

Max file size trong `php.ini`:

```ini
upload_max_filesize = 10M
post_max_size = 10M
```

## 🐛 Troubleshooting

### Lỗi: "Class 'PDO' not found"
- **Giải pháp:** Cài đặt PHP PDO extension: `sudo apt-get install php-pgsql`

### Lỗi: "PostGIS extension not found"
- **Giải pháp:** 
  - Cài đặt PostGIS: `sudo apt-get install postgis`
  - Enable extension: `CREATE EXTENSION postgis;`

### Lỗi: "Storage symlink failed"
- **Giải pháp:** 
  - Xóa symlink cũ: `rm public/storage`
  - Tạo lại: `php artisan storage:link`
  - Kiểm tra permissions

### Lỗi: "Permission denied" khi upload file
- **Giải pháp:**
  - Set permissions: `chmod -R 775 storage`
  - Set ownership: `chown -R www-data:www-data storage`

### Lỗi: "Geometry column not found"
- **Giải pháp:**
  - Kiểm tra PostGIS đã enable: `SELECT PostGIS_version();`
  - Kiểm tra bảng cong có geometry column: `SELECT * FROM geometry_columns WHERE f_table_name = 'cong';`

## 📝 Ghi chú

- Đảm bảo PostgreSQL service đang chạy
- Đảm bảo PostGIS extension đã được cài đặt và enable
- Kiểm tra firewall nếu database ở server khác
- Backup database trước khi chạy migrations trên production

## 🔄 Update từ version cũ

Nếu đang update từ version cũ:

```bash
# Pull code mới
git pull

# Cài đặt dependencies mới
composer install

# Chạy migrations mới
php artisan migrate

# Clear cache
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
```

## 🎯 Next Steps

Sau khi cài đặt xong:

1. Đăng nhập với tài khoản Admin
2. Truy cập `/map` để xem bản đồ
3. Truy cập `/cong/manage` để quản lý cống
4. Xem tài liệu hướng dẫn sử dụng trong `docs/USER_GUIDE_ADMIN.md`

