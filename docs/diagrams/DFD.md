# Sơ đồ DFD (Data Flow Diagram)

## Mô tả
Sơ đồ DFD mô tả luồng dữ liệu trong hệ thống WebGIS quản lý cống.

## DFD Level 0 - Context Diagram

```mermaid
graph LR
    Admin[Admin]
    User[User]
    System[WebGIS System]
    
    Admin -->|Request| System
    User -->|Request| System
    System -->|Response| Admin
    System -->|Response| User
```

### Mô tả Level 0
- **External Entities:** Admin, User
- **System:** WebGIS System
- **Data Flows:**
  - Admin → System: Requests (CRUD operations, view logs)
  - User → System: Requests (view map, search)
  - System → Admin: Responses (data, confirmations)
  - System → User: Responses (map data, search results)

## DFD Level 1 - Main Processes

```mermaid
graph TB
    Admin[Admin]
    User[User]
    
    subgraph System["WebGIS System"]
        P1[1.0 Authentication]
        P2[2.0 Map Display]
        P3[3.0 CRUD Operations]
        P4[4.0 Search/Filter]
        P5[5.0 File Upload]
        P6[6.0 Activity Logging]
    end
    
    subgraph Storage["Data Storage"]
        D1[(Users DB)]
        D2[(Cong DB)]
        D3[(Activity Logs DB)]
        D4[(File Storage)]
    end
    
    Admin -->|Login/Logout| P1
    User -->|Login/Logout| P1
    P1 -->|Verify| D1
    D1 -->|User Data| P1
    
    Admin -->|View Map| P2
    User -->|View Map| P2
    P2 -->|Query| D2
    D2 -->|GeoJSON| P2
    
    Admin -->|Create/Update/Delete| P3
    P3 -->|Save| D2
    P3 -->|Upload| P5
    P5 -->|Store| D4
    P3 -->|Log| P6
    
    Admin -->|Search| P4
    User -->|Search| P4
    P4 -->|Query| D2
    D2 -->|Results| P4
    
    Admin -->|View Logs| P6
    P6 -->|Query| D3
    D3 -->|Logs| P6
```

### Chi tiết các Process

#### 1.0 Authentication (Xác thực)
- **Input:** Email, Password
- **Process:**
  - Validate credentials
  - Create session
  - Set user role
- **Output:** Session token, User data
- **Data Store:** Users DB
- **Logging:** Activity Logs DB

#### 2.0 Map Display (Hiển thị bản đồ)
- **Input:** Map view request
- **Process:**
  - Load GeoJSON data from database
  - Convert geometry to GeoJSON format
  - Render on Leaflet map
- **Output:** Map with markers
- **Data Store:** Cong DB

#### 3.0 CRUD Operations (Thao tác CRUD)
- **Input:** Create/Update/Delete requests
- **Process:**
  - Validate input data
  - Create/Update geometry from coordinates
  - Save to database
  - Handle file uploads
  - Log activities
- **Output:** Success/Error messages
- **Data Store:** Cong DB, File Storage
- **Logging:** Activity Logs DB

#### 4.0 Search/Filter (Tìm kiếm/Lọc)
- **Input:** Search keywords, filters
- **Process:**
  - Parse search query
  - Filter data by criteria
  - Calculate distances (if needed)
  - Sort results
- **Output:** Filtered results
- **Data Store:** Cong DB

#### 5.0 File Upload (Upload file)
- **Input:** File data
- **Process:**
  - Validate file type and size
  - Generate unique filename
  - Store file
  - Save file metadata
- **Output:** File URL
- **Data Store:** File Storage

#### 6.0 Activity Logging (Ghi log)
- **Input:** Activity data
- **Process:**
  - Capture user action
  - Record IP address, user agent
  - Store changes (for update/delete)
  - Save to logs
- **Output:** Log entry
- **Data Store:** Activity Logs DB

## DFD Level 2 - Detailed Processes

### 3.0 CRUD Operations - Chi tiết

```mermaid
graph TB
    Admin[Admin]
    
    subgraph CRUD["3.0 CRUD Operations"]
        P31[3.1 Validate Input]
        P32[3.2 Process Geometry]
        P33[3.3 Save Data]
        P34[3.4 Handle Files]
        P35[3.5 Log Activity]
    end
    
    D2[(Cong DB)]
    D4[(File Storage)]
    D3[(Activity Logs DB)]
    
    Admin -->|Form Data| P31
    P31 -->|Valid Data| P32
    P32 -->|Coordinates| P33
    P33 -->|Geometry| D2
    P31 -->|File| P34
    P34 -->|Store| D4
    P33 -->|Activity| P35
    P35 -->|Log| D3
```

### 4.0 Search/Filter - Chi tiết

```mermaid
graph TB
    User[User/Admin]
    
    subgraph Search["4.0 Search/Filter"]
        P41[4.1 Parse Query]
        P42[4.2 Apply Filters]
        P43[4.3 Calculate Distance]
        P44[4.4 Sort Results]
    end
    
    D2[(Cong DB)]
    
    User -->|Search Query| P41
    P41 -->|Keywords| P42
    P42 -->|Filtered| P43
    P43 -->|With Distance| P44
    P44 -->|Results| User
    P42 -->|Query| D2
    D2 -->|Data| P42
```

## Data Dictionary

### Data Stores

#### D1: Users DB
- **Description:** Lưu trữ thông tin người dùng
- **Contents:** id, name, email, password, role, timestamps

#### D2: Cong DB
- **Description:** Lưu trữ dữ liệu cống
- **Contents:** ogc_fid, id, ten_cong_dap, coordinates, geometry, file info, metadata

#### D3: Activity Logs DB
- **Description:** Lưu trữ lịch sử hoạt động
- **Contents:** id, user_id, action, model_type, model_id, description, ip_address, changes, timestamp

#### D4: File Storage
- **Description:** Lưu trữ file đính kèm
- **Contents:** Image files, PDF files

### Data Flows

#### User Request
- **From:** Admin/User
- **To:** System Processes
- **Contents:** HTTP request với parameters, form data

#### GeoJSON Data
- **From:** Cong DB
- **To:** Map Display
- **Contents:** FeatureCollection với geometry và properties

#### Search Results
- **From:** Search Process
- **To:** User
- **Contents:** Filtered list of culverts with metadata

#### Log Entry
- **From:** Activity Logging
- **To:** Activity Logs DB
- **Contents:** User action, timestamp, IP, changes

