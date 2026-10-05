<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "🔍 KIỂM TRA CỐNG THEO ID\n";
echo str_repeat("=", 60) . "\n\n";

// Kiểm tra cống có thể tìm theo id (string) hoặc ogc_fid (number)
$searchId = '362'; // Có thể là string hoặc number

echo "📋 Tìm cống với ID/ogc_fid = $searchId\n\n";

// Tìm theo ogc_fid (number)
$congByOgcFid = DB::selectOne("SELECT ogc_fid, id, ten_cong_dap, toa_do_x, toa_do_y FROM cong WHERE ogc_fid = ?", [$searchId]);

// Tìm theo id (string)
$congById = DB::selectOne("SELECT ogc_fid, id, ten_cong_dap, toa_do_x, toa_do_y FROM cong WHERE id = ?", [$searchId]);

if ($congByOgcFid) {
    echo "✅ Tìm thấy cống theo ogc_fid:\n";
    echo "   - ogc_fid: {$congByOgcFid->ogc_fid}\n";
    echo "   - id: " . ($congByOgcFid->id ?? 'N/A') . "\n";
    echo "   - Tên: " . ($congByOgcFid->ten_cong_dap ?? 'N/A') . "\n";
    echo "   - Tọa độ: ({$congByOgcFid->toa_do_x}, {$congByOgcFid->toa_do_y})\n";
    
    // Kiểm tra geometry
    $gc = DB::selectOne("SELECT f_geometry_column AS geom_col FROM geometry_columns WHERE f_table_name = 'cong' LIMIT 1;");
    $geomCol = $gc->geom_col ?? 'geom';
    $checkGeom = DB::selectOne("SELECT ST_AsText($geomCol) as geom_text FROM cong WHERE ogc_fid = ?", [$congByOgcFid->ogc_fid]);
    
    if ($checkGeom && $checkGeom->geom_text) {
        echo "   - Geometry: ✅ CÓ (" . $checkGeom->geom_text . ")\n";
        echo "   → Cống này SẼ hiện trên bản đồ\n";
    } else {
        echo "   - Geometry: ❌ KHÔNG CÓ\n";
        echo "   → Cống này KHÔNG hiện trên bản đồ (cần tạo geometry)\n";
    }
} elseif ($congById) {
    echo "✅ Tìm thấy cống theo id (string):\n";
    echo "   - ogc_fid: {$congById->ogc_fid}\n";
    echo "   - id: " . ($congById->id ?? 'N/A') . "\n";
    echo "   - Tên: " . ($congById->ten_cong_dap ?? 'N/A') . "\n";
    echo "   - Tọa độ: ({$congById->toa_do_x}, {$congById->toa_do_y})\n";
    
    // Kiểm tra geometry
    $gc = DB::selectOne("SELECT f_geometry_column AS geom_col FROM geometry_columns WHERE f_table_name = 'cong' LIMIT 1;");
    $geomCol = $gc->geom_col ?? 'geom';
    $checkGeom = DB::selectOne("SELECT ST_AsText($geomCol) as geom_text FROM cong WHERE ogc_fid = ?", [$congById->ogc_fid]);
    
    if ($checkGeom && $checkGeom->geom_text) {
        echo "   - Geometry: ✅ CÓ (" . $checkGeom->geom_text . ")\n";
        echo "   → Cống này SẼ hiện trên bản đồ\n";
    } else {
        echo "   - Geometry: ❌ KHÔNG CÓ\n";
        echo "   → Cống này KHÔNG hiện trên bản đồ (cần tạo geometry)\n";
    }
} else {
    echo "❌ KHÔNG tìm thấy cống với ID/ogc_fid = $searchId\n";
    echo "\n💡 Có thể:\n";
    echo "   1. Cống chưa được insert vào database\n";
    echo "   2. ID không đúng\n";
    echo "   3. Cần kiểm tra lại quá trình thêm cống\n";
}

echo "\n" . str_repeat("=", 60) . "\n";
echo "📝 TÓM TẮT LOGIC:\n";
echo "   1. Để cống HIỆN LÊN BẢN ĐỒ: Cần có GEOMETRY (cột geom)\n";
echo "   2. Để đánh dấu cống MỚI (màu xanh lá): Dùng ogc_fid (ưu tiên) hoặc id\n";
echo "   3. API chỉ trả về cống có geometry: WHERE geom IS NOT NULL\n";
echo str_repeat("=", 60) . "\n";

