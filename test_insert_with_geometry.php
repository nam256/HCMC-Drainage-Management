<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "🧪 TEST INSERT CỐNG VỚI GEOMETRY TRỰC TIẾP\n";
echo str_repeat("=", 60) . "\n\n";

// Lấy tên cột geometry
$gc = DB::selectOne("SELECT f_geometry_column AS geom_col FROM geometry_columns WHERE f_table_name = 'cong' LIMIT 1;");
$geomCol = $gc->geom_col ?? 'geom';

// Dữ liệu test
$x = 106.666777;
$y = 10.888999;
$testName = 'Test Geometry Direct ' . time();

echo "📋 Dữ liệu test:\n";
echo "   - Tọa độ X: $x\n";
echo "   - Tọa độ Y: $y\n";
echo "   - Tên: $testName\n\n";

// Tạo insertData giống như trong controller
$insertData = [
    'ten_cong_dap' => $testName,
    'toa_do_x' => $x,
    'toa_do_y' => $y,
    'dia_diem' => 'Test Location'
];

try {
    DB::beginTransaction();
    
    // Xây dựng danh sách cột và giá trị cho INSERT
    $columns = array_keys($insertData);
    $values = array_values($insertData);
    
    // Thêm geometry vào INSERT statement TRỰC TIẾP
    $columns[] = $geomCol;
    
    // Tạo câu SQL INSERT với geometry
    $columnsStr = implode(', ', array_map(function($col) {
        return '"' . $col . '"';
    }, $columns));
    
    // Tạo placeholders
    $regularPlaceholders = array_fill(0, count($insertData), '?');
    $geometryPlaceholder = "ST_SetSRID(ST_MakePoint(?, ?), 4326)";
    $placeholdersStr = implode(', ', array_merge($regularPlaceholders, [$geometryPlaceholder]));
    
    // Thêm x và y vào cuối mảng values cho geometry
    $values[] = $x;
    $values[] = $y;
    
    // Dùng RETURNING để lấy ogc_fid ngay sau khi insert
    $sql = "INSERT INTO cong ($columnsStr) VALUES ($placeholdersStr) RETURNING ogc_fid";
    
    echo "🔧 Thực hiện INSERT với geometry:\n";
    echo "   SQL: INSERT INTO cong ($columnsStr) VALUES (...)\n";
    echo "   Values count: " . count($values) . "\n\n";
    
    // Thực thi INSERT và lấy ID
    $result = DB::selectOne($sql, $values);
    
    if (!$result || !isset($result->ogc_fid)) {
        throw new \Exception('Không thể lấy ID sau khi insert');
    }
    
    $id = $result->ogc_fid;
    
    echo "✅ Đã insert cống với ID: $id\n\n";
    
    // Kiểm tra geometry đã được tạo chưa
    $checkGeom = DB::selectOne("SELECT ST_AsText($geomCol) as geom_text, ogc_fid FROM cong WHERE ogc_fid = ?", [$id]);
    
    if (!$checkGeom || !$checkGeom->geom_text) {
        echo "❌ Geometry KHÔNG được tạo sau khi INSERT!\n";
        DB::rollBack();
        exit(1);
    } else {
        echo "✅ Geometry đã được tạo thành công:\n";
        echo "   - Geometry: " . $checkGeom->geom_text . "\n";
    }
    
    // Commit transaction
    DB::commit();
    
    // Kiểm tra lại sau khi commit
    $finalCheck = DB::selectOne("SELECT ST_AsText($geomCol) as geom_text, ogc_fid, ten_cong_dap FROM cong WHERE ogc_fid = ?", [$id]);
    
    if ($finalCheck && $finalCheck->geom_text) {
        echo "\n✅ KIỂM TRA CUỐI CÙNG:\n";
        echo "   - ID: {$finalCheck->ogc_fid}\n";
        echo "   - Tên: {$finalCheck->ten_cong_dap}\n";
        echo "   - Geometry: {$finalCheck->geom_text}\n";
        echo "\n✅✅✅ TEST THÀNH CÔNG! Geometry được tạo trực tiếp trong INSERT!\n";
        
        // Xóa cống test
        echo "\n🗑️  Xóa cống test...\n";
        DB::statement("DELETE FROM cong WHERE ogc_fid = ?", [$id]);
        echo "✅ Đã xóa cống test ID $id\n";
    } else {
        echo "\n❌ Geometry không tồn tại sau khi commit!\n";
    }
    
} catch (\Exception $e) {
    DB::rollBack();
    echo "\n❌ LỖI: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
    exit(1);
}

echo "\n" . str_repeat("=", 60) . "\n";

