<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "🔍 KIỂM TRA CỐNG MỚI NHẤT\n";
echo str_repeat("=", 60) . "\n\n";

// Lấy 10 cống mới nhất
$latestCongs = DB::select("SELECT ogc_fid, ten_cong_dap, toa_do_x, toa_do_y, 
    (SELECT ST_AsText(geom) FROM cong c2 WHERE c2.ogc_fid = cong.ogc_fid) as geom_text
    FROM cong 
    ORDER BY ogc_fid DESC 
    LIMIT 10");

echo "📋 10 cống mới nhất:\n";
foreach ($latestCongs as $cong) {
    $hasGeom = !empty($cong->geom_text);
    $geomStatus = $hasGeom ? "✅" : "❌";
    echo "   $geomStatus ID: {$cong->ogc_fid} - Tên: " . ($cong->ten_cong_dap ?? 'N/A') . 
         " - Tọa độ: ({$cong->toa_do_x}, {$cong->toa_do_y}) - Geometry: " . ($hasGeom ? "Có" : "Không") . "\n";
}

// Kiểm tra cống ID 362 cụ thể
echo "\n📋 Kiểm tra cống ID 362:\n";
$cong362 = DB::selectOne("SELECT ogc_fid, ten_cong_dap, toa_do_x, toa_do_y FROM cong WHERE ogc_fid = 362");
if ($cong362) {
    echo "✅ Cống ID 362 CÓ trong database\n";
    echo "   - Tên: " . ($cong362->ten_cong_dap ?? 'N/A') . "\n";
    echo "   - Tọa độ: ({$cong362->toa_do_x}, {$cong362->toa_do_y})\n";
    
    // Kiểm tra geometry
    $gc = DB::selectOne("SELECT f_geometry_column AS geom_col FROM geometry_columns WHERE f_table_name = 'cong' LIMIT 1;");
    $geomCol = $gc->geom_col ?? 'geom';
    $checkGeom = DB::selectOne("SELECT ST_AsText($geomCol) as geom_text FROM cong WHERE ogc_fid = 362");
    if ($checkGeom && $checkGeom->geom_text) {
        echo "   - Geometry: ✅ Có (" . $checkGeom->geom_text . ")\n";
    } else {
        echo "   - Geometry: ❌ Không có\n";
        echo "   Đang tạo geometry...\n";
        DB::statement("UPDATE cong SET $geomCol = ST_SetSRID(ST_MakePoint(?, ?), 4326) WHERE ogc_fid = 362", 
            [$cong362->toa_do_x, $cong362->toa_do_y]);
        echo "   ✅ Đã tạo geometry\n";
    }
} else {
    echo "❌ Cống ID 362 KHÔNG có trong database!\n";
    echo "   Có thể ID không đúng hoặc insert thất bại.\n";
}

echo "\n" . str_repeat("=", 60) . "\n";

