<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ImportShapefileCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:shapefile {path?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import shapefile data into cong table';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $path = $this->argument('path') ?? base_path('shapefile_output/cong_fc.shp');
        
        if (!File::exists($path)) {
            $this->error("Shapefile not found: {$path}");
            return 1;
        }

        $basePath = dirname($path);
        $baseName = pathinfo($path, PATHINFO_FILENAME);
        $dbfPath = $basePath . '/' . $baseName . '.dbf';
        $prjPath = $basePath . '/' . $baseName . '.prj';

        if (!File::exists($dbfPath)) {
            $this->error("DBF file not found: {$dbfPath}");
            return 1;
        }

        $this->info("Reading shapefile: {$path}");
        $this->info("Reading DBF file: {$dbfPath}");
        
        // Đọc file .prj để xác định hệ tọa độ
        $sourceSrid = $this->detectCoordinateSystem($prjPath);
        if ($sourceSrid) {
            $this->info("Detected coordinate system: SRID {$sourceSrid}");
        } else {
            $this->warn("Could not detect coordinate system from .prj file. Assuming VN2000 (SRID 9210)");
            $sourceSrid = 9210; // Default to VN2000
        }

        // Đọc DBF file
        $dbfData = $this->readDbfFile($dbfPath);
        
        if (empty($dbfData)) {
            $this->error("No data found in DBF file");
            return 1;
        }

        $this->info("Found " . count($dbfData) . " records");

        // Hỏi người dùng có muốn xóa dữ liệu cũ không
        if ($this->confirm('Do you want to clear existing data in cong table?', false)) {
            DB::table('cong')->truncate();
            $this->info("Cleared existing data");
        }

        $bar = $this->output->createProgressBar(count($dbfData));
        $bar->start();

        $imported = 0;
        $errors = 0;

        DB::beginTransaction();
        try {
            foreach ($dbfData as $index => $record) {
                try {
                    // Map các trường từ shapefile sang database (case-insensitive)
                    $getField = function($key) use ($record) {
                        // Thử nhiều cách viết hoa/thường
                        $variants = [
                            $key,
                            strtoupper($key),
                            strtolower($key),
                            ucfirst(strtolower($key)),
                        ];
                        foreach ($variants as $variant) {
                            if (isset($record[$variant])) {
                                return $record[$variant];
                            }
                        }
                        return null;
                    };
                    
                    // Helper function để chuyển đổi giá trị sang integer an toàn
                    $safeInt = function($value) {
                        if (empty($value) || $value === '' || $value === null) {
                            return null;
                        }
                        // Loại bỏ các ký tự không phải số
                        $cleaned = preg_replace('/[^0-9.-]/', '', (string)$value);
                        if ($cleaned === '' || $cleaned === '-') {
                            return null;
                        }
                        $int = (int)$cleaned;
                        return $int > 0 ? $int : null;
                    };
                    
                    $data = [
                        // Bỏ objectid_1 vì không có trong bảng mới
                        'stt' => $safeInt($getField('Stt')),
                        'id' => $getField('Id'),
                        'ten_cong_dap' => $getField('TenCongDap'),
                        'ly_trinh' => $getField('LyTrinh'),
                        'toa_do_x' => $getField('ToaDoX'),
                        'toa_do_y' => $getField('ToaDoY'),
                        'cum_cong_tri' => $getField('CumCongTri'),
                        'goi_thau' => $getField('GoiThau'),
                        'loai_cong_tr' => $getField('LoaiCongTr'),
                        'hinh_thuc' => $getField('HinhThuc'),
                        'chieu_dai' => $safeInt($getField('ChieuDai')),
                        'duong_kinh' => $safeInt($getField('DuongKinh')),
                        'be_rong' => $getField('BeRong'),
                        'chieu_cao' => $getField('ChieuCao'),
                        'so_cua' => $safeInt($getField('SoCua')),
                        'cao_trinh_da' => $getField('CaoTrinhDa'),
                        'cao_trinh_di' => $getField('CaoTrinhDi'),
                        'hinh_thuc_va' => $getField('HinhThucVa'),
                        'muc_tieu_nhi' => $getField('MucTieuNhi'),
                        'dien_tich_ph' => $safeInt($getField('DienTichPh')),
                        'dia_diem' => $getField('DiaDiem'),
                        'nam_su_dung' => $safeInt($getField('NamSuDung')),
                        'cap_cong_tri' => $getField('CapCongTri'),
                        'he_thong_con' => $getField('HeThongCon'),
                        'don_vi_quan_l' => $getField('DonViQuanL'),
                        'ghi_chu' => $getField('GhiChu'),
                        'nam_cap_nhat' => $safeInt($getField('NamCapNhat')),
                    ];

                    // Tạo geometry từ tọa độ X, Y và chuyển đổi sang WGS84
                    if (!empty($data['toa_do_x']) && !empty($data['toa_do_y'])) {
                        $x = (float)$data['toa_do_x'];
                        $y = (float)$data['toa_do_y'];
                        
                        // Nếu tọa độ là VN2000 (mét), chuyển đổi sang WGS84 (độ)
                        // Kiểm tra xem tọa độ có phải là mét không (lớn hơn 180)
                        if (abs($x) > 180 || abs($y) > 90) {
                            // Tọa độ là VN2000 (mét), cần chuyển đổi sang WGS84
                            // Đảm bảo SRID VN2000 tồn tại trong database
                            $this->ensureVn2000Srid();
                            
                            // Tạo geometry với SRID nguồn và chuyển đổi sang WGS84 (4326)
                            $data['geom'] = DB::raw("ST_Transform(ST_SetSRID(ST_MakePoint({$x}, {$y}), {$sourceSrid}), 4326)");
                        } else {
                            // Tọa độ đã là WGS84 (độ)
                            $data['geom'] = DB::raw("ST_SetSRID(ST_MakePoint({$x}, {$y}), 4326)");
                        }
                    }

                    // Insert vào database
                    DB::table('cong')->insert($data);
                    $imported++;
                } catch (\Exception $e) {
                    $errors++;
                    if ($errors <= 5) { // Chỉ hiển thị 5 lỗi đầu tiên
                        $this->newLine();
                        $this->warn("Error importing record " . ($index + 1) . ": " . $e->getMessage());
                    }
                }
                
                $bar->advance();
            }

            DB::commit();
            $bar->finish();
            $this->newLine(2);
            $this->info("Import completed!");
            $this->info("Imported: {$imported} records");
            if ($errors > 0) {
                $this->warn("Errors: {$errors} records");
            }
        } catch (\Exception $e) {
            DB::rollBack();
            $bar->finish();
            $this->newLine(2);
            $this->error("Import failed: " . $e->getMessage());
            return 1;
        }

        return 0;
    }

    /**
     * Đọc DBF file
     */
    private function readDbfFile($path)
    {
        $data = [];
        
        // Sử dụng extension dbase nếu có
        if (function_exists('dbase_open')) {
            $db = dbase_open($path, 0);
            if ($db) {
                $recordCount = dbase_numrecords($db);
                $fieldCount = dbase_numfields($db);
                
                for ($i = 1; $i <= $recordCount; $i++) {
                    $record = dbase_get_record_with_names($db, $i);
                    $data[] = $record;
                }
                
                dbase_close($db);
            }
        } else {
            // Fallback: Đọc DBF file thủ công (đơn giản)
            $this->warn("PHP dbase extension not available. Trying alternative method...");
            $data = $this->readDbfManually($path);
        }
        
        return $data;
    }

    /**
     * Đọc DBF file thủ công (basic implementation)
     */
    private function readDbfManually($path)
    {
        $data = [];
        $handle = fopen($path, 'rb');
        
        if (!$handle) {
            $this->warn("Cannot open DBF file: {$path}");
            return $data;
        }

        // Đọc header (32 bytes)
        $headerBytes = fread($handle, 32);
        if (strlen($headerBytes) < 32) {
            fclose($handle);
            $this->warn("DBF file too short");
            return $data;
        }
        
        // Unpack header - DBF format: byte 0=version, 1-3=date, 4-7=record count, 8-9=header length, 10-11=record length
        $header = unpack('Cversion/Cyear/Cmonth/Cday/VrecordCount/vheaderLength/vrecordLength', $headerBytes);
        
        if ($header['recordCount'] == 0 || $header['headerLength'] < 32) {
            fclose($handle);
            $this->warn("Invalid DBF header");
            return $data;
        }
        
        // Đọc field descriptors (bắt đầu từ byte 32)
        // Mỗi field descriptor là 32 bytes, kết thúc bằng 0x0D
        $fields = [];
        $currentPos = 32;
        
        while ($currentPos < $header['headerLength'] - 1) {
            $fieldData = fread($handle, 32);
            if (strlen($fieldData) < 32) {
                break;
            }
            
            // Kiểm tra terminator (0x0D)
            if (ord($fieldData[0]) == 0x0D) {
                break;
            }
            
            $field = unpack('a11name/Ctype/Vaddress/Clength/Cdecimals/a14reserved', $fieldData);
            $field['name'] = trim($field['name']);
            
            if (empty($field['name']) || $field['name'][0] == "\x00") {
                break; // End of field descriptors
            }
            
            if ($field['length'] > 0 && $field['length'] < 255) {
                $fields[] = $field;
            }
            
            $currentPos += 32;
        }
        
        // Bỏ qua terminator (0x0D) nếu chưa đọc
        if ($currentPos < $header['headerLength']) {
            fread($handle, $header['headerLength'] - $currentPos);
        }
        
        if (empty($fields)) {
            fclose($handle);
            $this->warn("No fields found in DBF file");
            return $data;
        }
        
        // Đọc records
        for ($i = 0; $i < $header['recordCount']; $i++) {
            $deleted = fread($handle, 1);
            if (strlen($deleted) === 0) {
                break; // End of file
            }
            
            if ($deleted === ' ' || $deleted === "\x20") {
                $record = [];
                foreach ($fields as $field) {
                    if ($field['length'] <= 0) {
                        continue;
                    }
                    $value = fread($handle, $field['length']);
                    if (strlen($value) < $field['length']) {
                        break 2; // End of file
                    }
                    $value = trim($value);
                    // Convert based on type
                    if ($field['type'] === 'N' && $field['decimals'] == 0) {
                        $value = $value !== '' ? (int)$value : null;
                    } elseif ($field['type'] === 'N') {
                        $value = $value !== '' ? (float)$value : null;
                    } elseif ($field['type'] === 'C') {
                        // String field - trim và xử lý encoding
                        $value = trim($value);
                        // Thử decode từ Windows-1252 (encoding phổ biến của DBF)
                        if (function_exists('mb_convert_encoding') && $value !== '') {
                            $value = mb_convert_encoding($value, 'UTF-8', 'Windows-1252');
                        }
                    }
                    $record[$field['name']] = $value;
                }
                if (!empty($record)) {
                    $data[] = $record;
                }
            } else {
                // Skip deleted record
                fseek($handle, $header['recordLength'] - 1, SEEK_CUR);
            }
        }
        
        fclose($handle);
        return $data;
    }

    /**
     * Đọc file .prj để xác định hệ tọa độ
     */
    private function detectCoordinateSystem($prjPath)
    {
        if (!File::exists($prjPath)) {
            return null;
        }
        
        $prjContent = File::get($prjPath);
        
        // Kiểm tra VN2000 HCM
        if (strpos($prjContent, 'HCM-VN2000') !== false || strpos($prjContent, 'VN2000') !== false) {
            return 9210; // SRID cho VN2000
        }
        
        // Kiểm tra WGS84
        if (strpos($prjContent, 'WGS_1984') !== false || strpos($prjContent, 'WGS84') !== false) {
            // Nếu là GEOGCS (Geographic), trả về 4326
            if (strpos($prjContent, 'GEOGCS') !== false) {
                return 4326;
            }
        }
        
        return null;
    }

    /**
     * Đảm bảo SRID VN2000 tồn tại trong database
     */
    private function ensureVn2000Srid()
    {
        static $checked = false;
        if ($checked) {
            return;
        }
        
        // Kiểm tra xem SRID 9210 đã tồn tại chưa
        $exists = DB::selectOne("
            SELECT COUNT(*) as count 
            FROM spatial_ref_sys 
            WHERE srid = 9210
        ");
        
        if ($exists->count == 0) {
            // Tạo SRID 9210 cho VN2000 HCM
            // Proj4text cho VN2000 HCM (múi chiếu 3°, kinh tuyến trục 105.75°)
            $proj4text = '+proj=tmerc +lat_0=0 +lon_0=105.75 +k=0.9999 +x_0=500000 +y_0=0 +ellps=WGS84 +towgs84=0,0,0,0,0,0,0 +units=m +no_defs';
            $srtext = 'PROJCS["HCM-VN2000",GEOGCS["HCM-VN2000",DATUM["<custom>",SPHEROID["WGS_1984",6378137.0,298.257223563]],PRIMEM["Greenwich",0.0],UNIT["Degree",0.0174532925199433]],PROJECTION["Transverse_Mercator"],PARAMETER["False_Easting",500000.0],PARAMETER["False_Northing",0.0],PARAMETER["Central_Meridian",105.75],PARAMETER["Scale_Factor",0.9999],PARAMETER["Latitude_Of_Origin",0.0],UNIT["Meter",1.0]]';
            
            // Kiểm tra xem có phải PostgreSQL không
            $driver = DB::connection()->getDriverName();
            
            if ($driver === 'pgsql') {
                // PostgreSQL - sử dụng ON CONFLICT
                DB::statement("
                    INSERT INTO spatial_ref_sys (srid, auth_name, auth_srid, proj4text, srtext)
                    VALUES (9210, 'EPSG', 9210, ?, ?)
                    ON CONFLICT (srid) DO NOTHING
                ", [$proj4text, $srtext]);
            } else {
                // SQLite hoặc database khác - kiểm tra trước khi insert
                try {
                    DB::statement("
                        INSERT INTO spatial_ref_sys (srid, auth_name, auth_srid, proj4text, srtext)
                        VALUES (9210, 'EPSG', 9210, ?, ?)
                    ", [$proj4text, $srtext]);
                } catch (\Exception $e) {
                    // Nếu đã tồn tại thì bỏ qua
                    if (strpos($e->getMessage(), 'UNIQUE constraint') === false && 
                        strpos($e->getMessage(), 'duplicate') === false) {
                        throw $e;
                    }
                }
            }
            
            $this->info("Created SRID 9210 for VN2000 HCM coordinate system");
        }
        
        $checked = true;
    }

}
