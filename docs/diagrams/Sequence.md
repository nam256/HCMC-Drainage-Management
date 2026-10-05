# Sơ đồ Sequence Diagram

## Mô tả
Sơ đồ Sequence mô tả trình tự tương tác giữa các thành phần trong hệ thống khi thực hiện các use case chính.

## 1. Sequence Diagram - Đăng nhập

```mermaid
sequenceDiagram
    participant User
    participant Browser
    participant AuthController
    participant UserModel
    participant ActivityLogger
    participant Session
    
    User->>Browser: Nhập email & password
    Browser->>AuthController: POST /login
    AuthController->>UserModel: authenticate()
    UserModel->>UserModel: Verify credentials
    alt Credentials valid
        UserModel-->>AuthController: User object
        AuthController->>Session: Create session
        AuthController->>ActivityLogger: logLogin(user)
        ActivityLogger->>ActivityLogger: Create log entry
        AuthController-->>Browser: Redirect to dashboard
        Browser-->>User: Show dashboard
    else Credentials invalid
        UserModel-->>AuthController: Error
        AuthController-->>Browser: Error message
        Browser-->>User: Show error
    end
```

## 2. Sequence Diagram - Thêm cống mới

```mermaid
sequenceDiagram
    participant Admin
    participant Browser
    participant MapController
    participant Leaflet
    participant CongController
    participant CongModel
    participant DB
    participant PostGIS
    participant ActivityLogger
    participant FileStorage
    
    Admin->>Browser: Vẽ điểm trên bản đồ
    Browser->>Leaflet: Draw marker event
    Leaflet->>Browser: Get coordinates
    Browser->>MapController: Redirect với coordinates
    MapController->>Browser: Show form với tọa độ
    
    Admin->>Browser: Điền form & submit
    Browser->>CongController: POST /api/cong
    CongController->>CongController: Validate input
    alt Has file
        CongController->>FileStorage: Upload file
        FileStorage-->>CongController: File path
    end
    
    CongController->>DB: Begin transaction
    CongController->>PostGIS: Create geometry from coordinates
    PostGIS-->>CongController: Geometry object
    CongController->>DB: INSERT với geometry
    DB-->>CongController: ogc_fid
    CongController->>DB: Commit transaction
    
    CongController->>ActivityLogger: logCreate(cong)
    ActivityLogger->>ActivityLogger: Create log entry
    
    CongController-->>Browser: Success response
    Browser->>MapController: Redirect to map với zoom
    MapController->>Browser: Load map & show new culvert
    Browser-->>Admin: Show new culvert on map
```

## 3. Sequence Diagram - Tìm kiếm cống

```mermaid
sequenceDiagram
    participant User
    participant Browser
    participant MapPage
    participant CongAPI
    participant CongModel
    participant DB
    
    User->>Browser: Nhập từ khóa tìm kiếm
    Browser->>MapPage: Search input event
    MapPage->>MapPage: Debounce search
    MapPage->>CongAPI: GET /api/cong
    CongAPI->>CongModel: Query all culverts
    CongModel->>DB: SELECT * FROM cong
    DB-->>CongModel: All culverts data
    CongModel-->>CongAPI: GeoJSON FeatureCollection
    CongAPI-->>MapPage: GeoJSON data
    
    MapPage->>MapPage: Filter by search keyword
    MapPage->>MapPage: Calculate distances
    MapPage->>MapPage: Sort results
    MapPage->>Browser: Display results
    Browser->>Browser: Highlight matching culverts
    Browser->>Browser: Zoom to results
    Browser-->>User: Show search results on map
```

## 4. Sequence Diagram - Cập nhật cống

```mermaid
sequenceDiagram
    participant Admin
    participant Browser
    participant ManagePage
    participant CongController
    participant CongModel
    participant DB
    participant PostGIS
    participant ActivityLogger
    participant FileStorage
    
    Admin->>Browser: Click "Sửa" cống
    Browser->>ManagePage: Load culvert data
    ManagePage->>CongAPI: GET /api/cong
    CongAPI-->>ManagePage: Culvert data
    ManagePage->>Browser: Show edit form
    
    Admin->>Browser: Điền form & submit
    Browser->>CongController: PUT /api/cong/{id}
    CongController->>CongModel: Find culvert
    CongModel->>DB: SELECT culvert
    DB-->>CongModel: Culvert data
    CongModel-->>CongController: Cong object
    
    CongController->>CongController: Validate & prepare data
    alt Has file upload
        CongController->>FileStorage: Delete old file
        CongController->>FileStorage: Upload new file
        FileStorage-->>CongController: New file path
    end
    
    alt Coordinates changed
        CongController->>PostGIS: Update geometry
        PostGIS-->>CongController: Updated geometry
    end
    
    CongController->>DB: UPDATE culvert
    DB-->>CongController: Success
    CongController->>CongModel: Refresh model
    CongController->>ActivityLogger: logUpdate(cong, changes)
    ActivityLogger->>ActivityLogger: Create log with changes
    
    CongController-->>Browser: Success response
    Browser->>ManagePage: Reload page
    ManagePage-->>Admin: Show updated data
```

## 5. Sequence Diagram - Xóa cống

```mermaid
sequenceDiagram
    participant Admin
    participant Browser
    participant ManagePage
    participant CongController
    participant CongModel
    participant DB
    participant ActivityLogger
    participant FileStorage
    
    Admin->>Browser: Click "Xóa" cống
    Browser->>Browser: Show confirm dialog
    Admin->>Browser: Confirm delete
    Browser->>CongController: DELETE /api/cong/{id}
    
    CongController->>CongModel: Find culvert
    CongModel->>DB: SELECT culvert
    DB-->>CongModel: Culvert data
    CongModel-->>CongController: Cong object
    
    CongController->>ActivityLogger: logDelete(cong)
    Note over ActivityLogger: Lưu thông tin trước khi xóa
    ActivityLogger->>ActivityLogger: Create log with deleted data
    
    alt Has file
        CongController->>FileStorage: Delete file
        FileStorage-->>CongController: Success
    end
    
    CongController->>DB: DELETE culvert
    DB-->>CongController: Success
    CongController-->>Browser: Success response
    Browser->>ManagePage: Reload page
    ManagePage-->>Admin: Show updated list
```

## 6. Sequence Diagram - Xem lịch sử hoạt động

```mermaid
sequenceDiagram
    participant Admin
    participant Browser
    participant LogsPage
    participant ActivityLogController
    participant ActivityLogModel
    participant UserModel
    participant DB
    
    Admin->>Browser: Truy cập /admin/logs
    Browser->>ActivityLogController: GET /admin/logs
    ActivityLogController->>ActivityLogController: Check admin permission
    
    ActivityLogController->>ActivityLogModel: Query logs với filters
    ActivityLogModel->>DB: SELECT logs
    DB-->>ActivityLogModel: Logs data
    ActivityLogModel->>UserModel: Load user relationships
    UserModel->>DB: SELECT users
    DB-->>UserModel: Users data
    UserModel-->>ActivityLogModel: User objects
    ActivityLogModel-->>ActivityLogController: Logs with users
    
    ActivityLogController->>ActivityLogController: Apply filters (search, user, action, date)
    ActivityLogController->>ActivityLogController: Paginate results
    ActivityLogController-->>LogsPage: Logs data + filters
    LogsPage->>Browser: Render logs table
    Browser-->>Admin: Show logs list
```

## Tóm tắt các Sequence Diagrams

| Diagram | Mô tả | Thành phần chính |
|---------|-------|------------------|
| 1. Đăng nhập | Luồng xác thực người dùng | User, AuthController, Session, ActivityLogger |
| 2. Thêm cống | Luồng tạo cống mới từ bản đồ | Admin, Leaflet, CongController, PostGIS, FileStorage |
| 3. Tìm kiếm | Luồng tìm kiếm và filter cống | User, MapPage, CongAPI, DB |
| 4. Cập nhật cống | Luồng chỉnh sửa thông tin cống | Admin, CongController, PostGIS, ActivityLogger |
| 5. Xóa cống | Luồng xóa cống khỏi hệ thống | Admin, CongController, ActivityLogger, FileStorage |
| 6. Xem logs | Luồng xem lịch sử hoạt động | Admin, ActivityLogController, DB |

