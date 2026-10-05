# Hướng dẫn sử dụng cho User

## 📋 Tổng quan

Tài liệu này hướng dẫn User (người dùng thường) sử dụng hệ thống WebGIS Quản lý Cống. User chỉ có quyền **xem** dữ liệu, không thể thêm, sửa, xóa cống.

## 🔐 Đăng nhập

1. Truy cập trang đăng nhập: `http://your-domain.com/login`
2. Nhập **Email** và **Password**
3. Click nút **"Đăng nhập"**
4. Sau khi đăng nhập thành công, bạn sẽ được chuyển đến **Dashboard**

## 🗺️ Xem bản đồ

### Truy cập bản đồ

1. Click menu **"Bản đồ"** hoặc truy cập `/map`
2. Bản đồ sẽ hiển thị với:
   - Tất cả cống trong hệ thống (marker màu đỏ)
   - Tile layer OpenStreetMap

### Tương tác với bản đồ

- **Zoom:** Sử dụng scroll wheel hoặc nút +/- trên bản đồ
- **Pan:** Click và kéo bản đồ
- **Xem chi tiết cống:** Click vào marker để xem popup với thông tin
- **Hover popup:** Di chuột qua marker để tự động mở popup

### Lưu ý

- User **không thể** thêm cống mới
- User **không thể** sửa hoặc xóa cống
- Các nút "Thêm cống", "Bật vẽ điểm" sẽ **không hiển thị** cho User

## 🔍 Tìm kiếm cống

### Tìm kiếm cơ bản

1. Sử dụng ô **"Tìm kiếm"** phía trên bản đồ
2. Nhập từ khóa:
   - **ID cống:** Ví dụ: C1615, SC8-1
   - **Tên cống:** Ví dụ: Cống ÔĐ4b
   - **Địa điểm:** Ví dụ: Quận 1, Quận 2
   - **Lý trình:** Ví dụ: KM 10+500
3. Kết quả sẽ được highlight trên bản đồ
4. Bản đồ tự động zoom đến kết quả
5. Click vào kết quả để xem chi tiết

### Lọc theo khu vực, loại, cấp

1. Click nút **"Tìm kiếm & Bộ lọc"** (màu đỏ)
2. Chọn:
   - **Khu vực:** Lọc theo địa điểm
   - **Loại:** Lọc theo loại công trình
   - **Cấp:** Lọc theo cấp công trình
3. Bản đồ sẽ tự động filter và hiển thị kết quả
4. Click **"Xóa bộ lọc"** để reset

### Ví dụ tìm kiếm

- Tìm theo ID: Nhập `C1615`
- Tìm theo tên: Nhập `Cống ÔĐ4b`
- Tìm theo địa điểm: Nhập `Quận 1`
- Tìm theo tọa độ: Nhập `106.660172, 10.762622`

## 📄 Xem chi tiết cống

### Từ bản đồ

1. Click vào marker cống trên bản đồ
2. Popup sẽ hiển thị với thông tin:
   - **ID:** Mã số cống
   - **Tên cống:** Tên công đập
   - **Địa điểm:** Vị trí cống
   - **Lý trình:** Lý trình (nếu có)
   - **Tọa độ:** X, Y
   - **File đính kèm:** Link tải file (nếu có)
   - **Ghi chú:** Ghi chú (nếu có)

### Thông tin hiển thị

- **ID:** Mã số cống (ví dụ: C1615)
- **Tên cống:** Tên công đập (ví dụ: Cống ÔĐ4b)
- **Địa điểm:** Khu vực (ví dụ: Quận 1, TP.HCM)
- **Lý trình:** Lý trình đường (ví dụ: KM 10+500)
- **Tọa độ X, Y:** Tọa độ địa lý (WGS84)
- **File đính kèm:** 
  - Icon hiển thị loại file (🖼️ ảnh, 📄 PDF, 📎 khác)
  - Kích thước file
  - Nút **"Tải tệp"** để download
  - Link **"(Xem ảnh)"** nếu là ảnh

## 📎 Tải file đính kèm

1. Click vào cống trên bản đồ
2. Trong popup, tìm phần **"File đính kèm"**
3. Click **"Tải tệp"** để download file về máy
4. Nếu là ảnh, có thể click **"(Xem ảnh)"** để mở trong tab mới

### Lưu ý

- File có thể là ảnh (JPG, PNG) hoặc PDF
- File được lưu trong storage của hệ thống
- Chỉ có thể tải file, không thể upload file mới

## 🎯 Chức năng chính

### Xem bản đồ
- ✅ Xem tất cả cống trên bản đồ
- ✅ Zoom, pan bản đồ
- ✅ Xem chi tiết từng cống

### Tìm kiếm
- ✅ Tìm kiếm theo ID, tên, địa điểm, lý trình
- ✅ Lọc theo khu vực, loại, cấp
- ✅ Tìm kiếm theo tọa độ

### Xem thông tin
- ✅ Xem chi tiết cống
- ✅ Xem tọa độ
- ✅ Tải file đính kèm

### Không thể
- ❌ Thêm cống mới
- ❌ Sửa cống
- ❌ Xóa cống
- ❌ Upload file
- ❌ Xem logs

## 👤 Quản lý Profile

### Chỉnh sửa thông tin

1. Click vào tên user ở góc trên bên phải
2. Chọn **"Profile"**
3. Chỉnh sửa:
   - **Tên**
   - **Email**
4. Click **"Lưu"**

### Đổi mật khẩu

1. Trong trang Profile, scroll xuống phần **"Cập nhật mật khẩu"**
2. Nhập:
   - **Mật khẩu hiện tại**
   - **Mật khẩu mới**
   - **Xác nhận mật khẩu mới**
3. Click **"Lưu"**

## 🚪 Đăng xuất

1. Click vào tên user ở góc trên bên phải
2. Chọn **"Log Out"**
3. Bạn sẽ được đăng xuất và chuyển về trang chủ

## 💡 Mẹo sử dụng

### Tìm cống nhanh

1. Sử dụng ô tìm kiếm với từ khóa ngắn gọn
2. Hệ thống sẽ tự động tìm và highlight kết quả
3. Bản đồ tự động zoom đến vị trí

### Xem nhiều cống cùng lúc

1. Sử dụng bộ lọc để lọc theo khu vực hoặc loại
2. Tất cả cống thỏa điều kiện sẽ được hiển thị
3. Click vào từng cống để xem chi tiết

### Làm mới dữ liệu

1. Click nút **"Làm mới"** (Reload) trên bản đồ
2. Dữ liệu sẽ được tải lại từ server
3. Các cống mới (nếu có) sẽ được hiển thị

## ⚠️ Lưu ý

1. **Chỉ xem:** User chỉ có quyền xem, không thể chỉnh sửa
2. **Dữ liệu:** Dữ liệu được cập nhật bởi Admin
3. **File:** Chỉ có thể tải file, không thể upload
4. **Bản đồ:** Bản đồ sử dụng OpenStreetMap, cần kết nối internet

## 🆘 Hỗ trợ

Nếu gặp vấn đề:

1. Kiểm tra lại thông tin đăng nhập
2. Kiểm tra kết nối internet
3. Thử làm mới trang (F5)
4. Liên hệ Admin để được hỗ trợ

## 📞 Liên hệ

Nếu cần thêm cống hoặc chỉnh sửa thông tin, vui lòng liên hệ **Admin** của hệ thống.

