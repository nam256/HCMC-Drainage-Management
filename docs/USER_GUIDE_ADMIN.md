# Hướng dẫn sử dụng cho Admin

## 📋 Tổng quan

Tài liệu này hướng dẫn Admin sử dụng hệ thống WebGIS Quản lý Cống. Admin có đầy đủ quyền để quản lý cống, xem logs, và quản lý người dùng.

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
   - Cống mới được thêm (marker màu xanh)
   - Tile layer OpenStreetMap

### Tương tác với bản đồ

- **Zoom:** Sử dụng scroll wheel hoặc nút +/- trên bản đồ
- **Pan:** Click và kéo bản đồ
- **Xem chi tiết cống:** Click vào marker để xem popup với thông tin
- **Hover popup:** Di chuột qua marker để tự động mở popup

## ➕ Thêm cống mới

### Cách 1: Vẽ điểm trên bản đồ (Khuyến nghị)

1. Trên trang bản đồ, click nút **"Bật vẽ điểm"**
2. Con trỏ chuột sẽ chuyển thành dấu thập (+)
3. Click vào vị trí muốn thêm cống trên bản đồ
4. Hệ thống tự động:
   - Lấy tọa độ từ điểm vẽ
   - Chuyển đến trang form với tọa độ đã điền sẵn
   - Tọa độ sẽ ở chế độ **readonly** (không thể chỉnh sửa)

5. Điền thông tin cống:
   - **Mã số:** Mã số cống (tùy chọn)
   - **Tên công đập:** * (bắt buộc)
   - **Lý trình:** Lý trình cống
   - **Địa điểm:** Địa điểm cống
   - **File đính kèm:** Upload ảnh hoặc PDF (tối đa 5MB)
   - **Ghi chú:** Ghi chú thêm

6. Click **"Thêm cống"**
7. Sau khi tạo thành công:
   - Hệ thống tự động chuyển về trang bản đồ
   - Bản đồ tự động zoom đến vị trí cống mới
   - Popup của cống mới sẽ tự động mở
   - Cống mới hiển thị với màu **xanh** (để phân biệt)

### Cách 2: Thêm từ trang quản lý

1. Truy cập `/cong/manage`
2. Click nút **"Thêm mới"** (trong thẻ Quick Actions)
3. Điền form tương tự như trên
4. **Lưu ý:** Phải nhập tọa độ X, Y thủ công

## ✏️ Sửa cống

### Từ bản đồ

1. Click vào marker cống trên bản đồ
2. Trong popup, click nút **"Sửa"**
3. Form sẽ mở với dữ liệu hiện tại
4. Chỉnh sửa thông tin cần thiết
5. Click **"Cập nhật"**

### Từ trang quản lý

1. Truy cập `/cong/manage`
2. Tìm cống cần sửa trong bảng
3. Click nút **"Sửa"** ở cột "Thao tác"
4. Chỉnh sửa thông tin
5. Click **"Cập nhật"**

### Lưu ý khi sửa

- Tọa độ có thể thay đổi (sẽ cập nhật geometry)
- File đính kèm: Nếu upload file mới, file cũ sẽ bị xóa
- Để giữ file cũ, không chọn file mới

## 🗑️ Xóa cống

### Từ bản đồ

1. Click vào marker cống
2. Trong popup, click nút **"Xóa"**
3. Xác nhận xóa trong dialog
4. Cống sẽ bị xóa khỏi hệ thống

### Từ trang quản lý

1. Truy cập `/cong/manage`
2. Tìm cống cần xóa
3. Click nút **"Xóa"** ở cột "Thao tác"
4. Xác nhận xóa

### Lưu ý

- **Hành động không thể hoàn tác!**
- File đính kèm cũng sẽ bị xóa
- Hoạt động xóa sẽ được ghi log

## 🔍 Tìm kiếm và lọc cống

### Trên bản đồ

1. Sử dụng ô **"Tìm kiếm"** phía trên bản đồ
2. Nhập từ khóa: ID, tên cống, địa điểm, lý trình
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

### Trên trang quản lý

1. Truy cập `/cong/manage`
2. Sử dụng form tìm kiếm:
   - **Tìm kiếm:** Nhập từ khóa
   - **Sắp xếp theo:** Chọn tiêu chí (ID, Tên cống, Địa điểm, Ngày tạo)
   - **Thứ tự:** Tăng dần hoặc Giảm dần
3. Click **"Tìm"**
4. Kết quả hiển thị trong bảng

## 📎 Upload file đính kèm

### Khi thêm cống mới

1. Trong form thêm cống, scroll xuống phần **"File đính kèm"**
2. Click **"Chọn file"**
3. Chọn file ảnh (JPG, PNG) hoặc PDF
4. **Lưu ý:** File tối đa 5MB
5. File sẽ được upload khi submit form

### Khi sửa cống

1. Trong form sửa, phần **"File đính kèm"** sẽ hiển thị:
   - File hiện tại (nếu có) với link "Xem file"
   - Ô chọn file mới
2. Nếu muốn thay đổi file:
   - Chọn file mới
   - File cũ sẽ bị xóa và thay bằng file mới
3. Nếu muốn giữ file cũ:
   - Không chọn file mới
   - File cũ sẽ được giữ nguyên

### Xem và tải file

1. Click vào cống trên bản đồ hoặc trong bảng
2. Trong popup/chi tiết, tìm phần **"File đính kèm"**
3. Click **"Tải tệp"** để download
4. Nếu là ảnh, có thể click **"(Xem ảnh)"** để mở trong tab mới

## 📊 Quản lý cống

### Trang quản lý (`/cong/manage`)

Trang này hiển thị:

1. **Thống kê:**
   - Tổng số cống
   - Số cống có file đính kèm
   - Tổng người dùng
   - Nút thao tác nhanh

2. **Bảng dữ liệu:**
   - ID, Tên cống, Địa điểm, Tọa độ, File, Thao tác
   - Có phân trang
   - Có thể sắp xếp và tìm kiếm

3. **Chức năng:**
   - Thêm cống mới
   - Sửa cống
   - Xóa cống
   - Tìm kiếm và lọc
   - Xem chi tiết

## 📝 Xem lịch sử hoạt động

### Truy cập logs

1. Click nút **"Quản lý hoạt động người dùng"** (màu tím) trên trang quản lý
2. Hoặc truy cập trực tiếp: `/admin/logs`

### Tính năng

1. **Xem tất cả logs:**
   - Thời gian
   - Người dùng (Admin/User)
   - Hành động (Create, Update, Delete, Login, Logout)
   - Mô tả chi tiết
   - IP Address

2. **Tìm kiếm:**
   - Nhập từ khóa vào ô tìm kiếm
   - Tìm theo mô tả

3. **Lọc:**
   - **Người dùng:** Chọn user cụ thể
   - **Hành động:** Chọn action (create, update, delete, login, logout)
   - **Từ ngày / Đến ngày:** Lọc theo khoảng thời gian

4. **Phân trang:**
   - Mặc định 50 logs/trang
   - Có thể điều chỉnh

### Màu sắc action

- **Create:** Xanh lá (Green)
- **Update:** Xanh dương (Blue)
- **Delete:** Đỏ (Red)
- **View:** Xám (Gray)
- **Login:** Tím (Purple)
- **Logout:** Cam (Orange)

## 👥 Tạo tài khoản Admin

1. Truy cập `/register-admin`
2. Điền thông tin:
   - **Tên:** Tên Admin
   - **Email:** Email đăng nhập
   - **Password:** Mật khẩu
   - **Confirm Password:** Xác nhận mật khẩu
3. Click **"Đăng ký"**
4. Tài khoản Admin mới sẽ được tạo

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

### Thêm nhiều cống nhanh

1. Sử dụng chức năng **"Vẽ điểm"** trên bản đồ
2. Vẽ điểm → Điền form → Tự động quay về bản đồ
3. Lặp lại cho cống tiếp theo

### Tìm cống theo tọa độ

1. Nếu biết tọa độ, có thể nhập vào ô tìm kiếm
2. Hệ thống sẽ tìm cống gần nhất với tọa độ đó

### Export dữ liệu (tùy chọn)

- Hiện tại chưa có chức năng export
- Có thể query trực tiếp từ database nếu cần

## ⚠️ Lưu ý quan trọng

1. **Backup dữ liệu:** Nên backup database thường xuyên
2. **File upload:** Chỉ upload file cần thiết, tránh upload file quá lớn
3. **Xóa cống:** Hành động không thể hoàn tác, cẩn thận khi xóa
4. **Tọa độ:** Đảm bảo tọa độ chính xác khi thêm cống
5. **Logs:** Logs được lưu vĩnh viễn, có thể xem lại lịch sử bất cứ lúc nào

## 🆘 Hỗ trợ

Nếu gặp vấn đề:

1. Kiểm tra lại thông tin đăng nhập
2. Kiểm tra kết nối internet
3. Xem logs trong `/admin/logs` để tìm lỗi
4. Liên hệ quản trị viên hệ thống

