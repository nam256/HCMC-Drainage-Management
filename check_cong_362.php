<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "🔍 KIỂM TRA CỐNG ID 362\n";
echo str_repeat("=", 60) . "\n\n";

$id = 362;

// 1. Kiểm tra cống có trong database không
echo "📋 1. Kiểm tra cống có trong database...\n";
$cong = DB::selectOne("SELECT * FROM cong WHERE ogc_fid = ?", [$id]);
if ($cong) {
    echo "✅ Cống ID $id CÓ trong database\n";
    echo "   - Tên: " . ($cong->ten_cong_dap ?? 'N/A') . "\n";
    echo "   - Tọa độ X: " . ($cong->toa_do_x ?? 'N/A') . "\n";
    echo "   - Tọa độ Y: " . ($cong->toa_do_y ?? 'N/A') . "\n";
} else {
    echo "❌ Cống ID $id KHÔNG có trong database!\n";
    exit(1);
}

// 2. Kiểm tra geometry
echo "\n📋 2. Kiểm tra geometry...\n";
$gc = DB::selectOne("SELECT f_geometry_column AS geom_col FROM geometry_columns WHERE f_table_name = 'cong' LIMIT 1;");
$geomCol = $gc->geom_col ?? 'geom';

$checkGeom = DB::selectOne("SELECT ST_AsText($geomCol) as geom_text FROM cong WHERE ogc_fid = ?", [$id]);
if ($checkGeom && $checkGeom->geom_text) {
    echo "✅ Geometry CÓ: " . $checkGeom->geom_text . "\n";
} else {
    echo "❌ Geometry KHÔNG có!\n";
    echo "   Đang tạo geometry...\n";
    if ($cong->toa_do_x && $cong->toa_do_y) {
        DB::statement("UPDATE cong SET $geomCol = ST_SetSRID(ST_MakePoint(?, ?), 4326) WHERE ogc_fid = ?", [$cong->toa_do_x, $cong->toa_do_y, $id]);
        echo "✅ Đã tạo geometry\n";
    } else {
        echo "❌ Không có tọa độ để tạo geometry!\n";
    }
}

// 3. Kiểm tra API có trả về không
echo "\n📋 3. Kiểm tra API có trả về cống này không...\n";
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
        $ogcFid = $props['ogc_fid'] ?? null;
        if ($ogcFid == $id) {
            $found = true;
            echo "✅ Cống ID $id CÓ trong API response\n";
            echo "   - ogc_fid: " . ($props['ogc_fid'] ?? 'N/A') . "\n";
            echo "   - id: " . ($props['id'] ?? 'N/A') . "\n";
            echo "   - Tên: " . ($props['ten_cong_dap'] ?? 'N/A') . "\n";
            echo "   - Geometry: " . ($feature['geometry'] ? 'Có' : 'Không') . "\n";
            if ($feature['geometry']) {
                echo "   - Coordinates: " . json_encode($feature['geometry']['coordinates']) . "\n";
            }
            break;
        }
    }
    
    if (!$found) {
        echo "❌ Cống ID $id KHÔNG có trong API response!\n";
        echo "   Tổng số cống trong response: " . count($arr['features']) . "\n";
        echo "   Các ID gần đó: ";
        $nearbyIds = [];
        foreach (array_slice($arr['features'], 0, 10) as $f) {
            $nearbyIds[] = $f['properties']['ogc_fid'] ?? 'N/A';
        }
        echo implode(', ', $nearbyIds) . "\n";
    }
} else {
    echo "❌ API response không hợp lệ!\n";
}

echo "\n" . str_repeat("=", 60) . "\n";
echo "💡 Để cống này hiện màu xanh lá trên bản đồ:\n";
echo "   1. Mở Console (F12)\n";
echo "   2. Chạy: sessionStorage.setItem('newCongIds', JSON.stringify([362]))\n";
echo "   3. Reload trang hoặc gọi loadCongData()\n";
echo str_repeat("=", 60) . "\n";

