<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "🔧 SỬA CỐNG ID 376 (CỐNG MỚI NHẤT)\n";
echo str_repeat("=", 60) . "\n\n";

$id = 376;

// Kiểm tra cống
$cong = DB::selectOne("SELECT * FROM cong WHERE ogc_fid = ?", [$id]);
if (!$cong) {
    echo "❌ Cống ID $id không tồn tại!\n";
    exit(1);
}

echo "✅ Tìm thấy cống ID $id\n";
echo "   - Tên: " . ($cong->ten_cong_dap ?? 'N/A') . "\n";
echo "   - Tọa độ X: " . ($cong->toa_do_x ?? 'N/A') . "\n";
echo "   - Tọa độ Y: " . ($cong->toa_do_y ?? 'N/A') . "\n";

// Kiểm tra geometry
$gc = DB::selectOne("SELECT f_geometry_column AS geom_col FROM geometry_columns WHERE f_table_name = 'cong' LIMIT 1;");
$geomCol = $gc->geom_col ?? 'geom';

$checkGeom = DB::selectOne("SELECT ST_AsText($geomCol) as geom_text FROM cong WHERE ogc_fid = ?", [$id]);

if (!$checkGeom || !$checkGeom->geom_text) {
    echo "\n❌ Geometry KHÔNG có! Đang tạo...\n";
    
    if ($cong->toa_do_x && $cong->toa_do_y) {
        DB::statement("UPDATE cong SET $geomCol = ST_SetSRID(ST_MakePoint(?, ?), 4326) WHERE ogc_fid = ?", 
            [$cong->toa_do_x, $cong->toa_do_y, $id]);
        
        // Kiểm tra lại
        $checkGeom2 = DB::selectOne("SELECT ST_AsText($geomCol) as geom_text FROM cong WHERE ogc_fid = ?", [$id]);
        if ($checkGeom2 && $checkGeom2->geom_text) {
            echo "✅ Đã tạo geometry: " . $checkGeom2->geom_text . "\n";
        } else {
            echo "❌ Không thể tạo geometry!\n";
            exit(1);
        }
    } else {
        echo "❌ Không có tọa độ để tạo geometry!\n";
        exit(1);
    }
} else {
    echo "\n✅ Geometry đã có: " . $checkGeom->geom_text . "\n";
}

// Kiểm tra API
echo "\n📋 Kiểm tra API có trả về không...\n";
$data = DB::select("
    SELECT jsonb_build_object(
        'type', 'FeatureCollection',
        'features', COALESCE(jsonb_agg(feature), '[]'::jsonb)
    ) AS geojson
    FROM (
        SELECT jsonb_build_object(
            'type', 'Feature',
            'geometry', ST_AsGeoJSON($geomCol)::jsonb,
            'properties', to_jsonb(row) - '$geomCol'
        ) AS feature
        FROM (
            SELECT * FROM cong
            WHERE $geomCol IS NOT NULL
            ORDER BY ogc_fid DESC
        ) row
    ) features;
");

$geojson = $data[0]->geojson ?? null;
$arr = json_decode($geojson, true);

if ($arr && isset($arr['features'])) {
    $found = false;
    foreach ($arr['features'] as $feature) {
        $props = $feature['properties'] ?? [];
        if (($props['ogc_fid'] ?? null) == $id) {
            $found = true;
            echo "✅ Cống ID $id CÓ trong API response\n";
            break;
        }
    }
    
    if (!$found) {
        echo "❌ Cống ID $id KHÔNG có trong API response!\n";
    }
}

echo "\n" . str_repeat("=", 60) . "\n";
echo "✅ Đã sửa xong!\n";
echo "💡 Để cống này hiện màu xanh lá trên bản đồ:\n";
echo "   1. Mở Console (F12)\n";
echo "   2. Chạy: sessionStorage.setItem('newCongIds', JSON.stringify([$id]))\n";
echo "   3. Reload trang\n";
echo str_repeat("=", 60) . "\n";

