# 🧪 Hướng Dẫn Chạy Tests

## Test Coverage

Đã tạo đầy đủ tests cho các chức năng chính:

### ✅ Test Files Đã Tạo:

1. **UserRoleTest.php** - Test phân quyền User/Admin
2. **CongApiTest.php** - Test API CRUD cống
3. **AdminRegistrationTest.php** - Test đăng ký tài khoản bởi Admin
4. **MapAccessTest.php** - Test truy cập trang bản đồ
5. **AuthenticationTest.php** (đã cập nhật) - Test đăng nhập/đăng xuất với validation
6. **RegistrationTest.php** (đã cập nhật) - Test đăng ký với kiểm tra role

---

## 📋 Cách Chạy Tests

### Chạy tất cả tests:
```bash
php artisan test
```

### Chạy test cụ thể:
```bash
# Test phân quyền
php artisan test --filter UserRoleTest

# Test API cống
php artisan test --filter CongApiTest

# Test đăng ký admin
php artisan test --filter AdminRegistrationTest

# Test đăng nhập
php artisan test --filter AuthenticationTest

# Test đăng ký
php artisan test --filter RegistrationTest

# Test truy cập bản đồ
php artisan test --filter MapAccessTest
```

### Chạy một test method cụ thể:
```bash
php artisan test --filter test_user_register_gets_default_role_user
```

---

## 📊 Test Cases Chi Tiết

### 1. UserRoleTest.php (8 tests)
- ✅ User đăng ký mặc định có role 'user'
- ✅ User có thể truy cập dashboard
- ✅ User có thể truy cập bản đồ
- ✅ User không thể truy cập trang quản lý admin
- ✅ User không thể truy cập đăng ký admin
- ✅ Admin có thể truy cập tất cả trang
- ✅ Admin có thể tạo tài khoản user
- ✅ Admin có thể tạo tài khoản admin
- ✅ Validation đăng nhập: đúng/sai mật khẩu

### 2. CongApiTest.php (9 tests)
- ✅ Public có thể xem API cống
- ✅ API trả về empty khi không có dữ liệu
- ✅ User đã đăng nhập có thể tạo cống
- ✅ Có thể upload file khi tạo cống
- ✅ Admin có thể sửa cống
- ✅ User không thể sửa cống
- ✅ Admin có thể xóa cống
- ✅ User không thể xóa cống
- ✅ Validation: require coordinates

### 3. AdminRegistrationTest.php (7 tests)
- ✅ Admin có thể truy cập trang đăng ký
- ✅ User không thể truy cập trang đăng ký admin
- ✅ Guest không thể truy cập trang đăng ký admin
- ✅ Admin có thể tạo user
- ✅ Admin có thể tạo admin
- ✅ Validation các trường bắt buộc
- ✅ Validation email unique
- ✅ Validation password confirmation

### 4. MapAccessTest.php (4 tests)
- ✅ User có thể truy cập bản đồ
- ✅ Admin có thể truy cập bản đồ
- ✅ Guest không thể truy cập bản đồ
- ✅ Route /cong alias đến /map

### 5. AuthenticationTest.php (4 tests)
- ✅ Hiển thị màn hình đăng nhập
- ✅ Đăng nhập thành công với mật khẩu đúng
- ✅ Báo lỗi khi mật khẩu sai
- ✅ Đăng xuất thành công

### 6. RegistrationTest.php (3 tests)
- ✅ Hiển thị màn hình đăng ký
- ✅ Đăng ký thành công với role 'user'
- ✅ Kiểm tra role mặc định là 'user'

---

## ⚙️ Setup Database cho Tests

Tests sử dụng `RefreshDatabase` trait, tự động:
- Tạo database test mới
- Chạy migrations
- Rollback sau mỗi test

**Lưu ý:** Một số tests cần PostgreSQL với PostGIS để test API cống.

---

## 🎯 Tổng Kết

**Tổng số test cases: ~35 tests**

✅ **Authentication & Authorization**: Hoàn chỉnh
✅ **Role Management**: Hoàn chỉnh  
✅ **API CRUD**: Hoàn chỉnh
✅ **File Upload**: Có test
✅ **Validation**: Có test

---

## 🐛 Troubleshooting

### Lỗi: "No application encryption key has been specified"
```bash
php artisan key:generate
```

### Lỗi: Database connection
Kiểm tra `.env` và đảm bảo có database test:
```env
DB_DATABASE=webgis_test
```

### Lỗi: PostGIS functions not found
Một số tests API cống cần PostGIS. Nếu không có PostGIS, có thể skip:
```bash
php artisan test --filter CongApiTest --stop-on-failure
```

---

## 📝 Notes

- Tất cả passwords trong tests đều là `'password'`
- Tests sử dụng factories để tạo dữ liệu giả
- Database được refresh sau mỗi test (không ảnh hưởng DB thật)

