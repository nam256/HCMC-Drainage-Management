<?php

/**
 * Script test để kiểm tra logic thêm cống và hiển thị trên bản đồ
 * Chạy: php artisan test:add-cong
 * Hoặc: php tests/TestAddCong.php
 */

require __DIR__ . '/../vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

// Load Laravel
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🧪 BẮT ĐẦU TEST THÊM CỐNG VÀ HIỂN THỊ TRÊN BẢN ĐỒ\n";
echo str_repeat("=", 60) . "\n\n";

// Test 1: Kiểm tra geometry column
echo "📋 Test 1: Kiểm tra geometry column...\n";
$gc = DB::selectOne("SELECT f_geometry_column AS geom_col FROM geometry_columns WHERE f_table_name = 'cong' LIMIT 1;");
$geomCol = $gc->geom_col ?? 'geom';
echo "✅ Geometry column: $geomCol\n\n";

// Test 2: Thêm cống test
echo "📋 Test 2: Thêm cống test...\n";
$x = 106.666667;
$y = 10.888889;

$insertData = [
    'toa_do_x' => $x,
    'toa_do_y' => $y,
    'ten_cong_dap' => 'Cống Test Auto',
    'dia_diem' => 'Test Location',
];

try {
    $id = DB::table('cong')->insertGetId($insertData);
    echo "✅ Đã insert cống với ID: $id\n";
    
    // Test 3: Tạo geometry
    echo "\n📋 Test 3: Tạo geometry...\n";
    DB::statement("UPDATE cong SET $geomCol = ST_SetSRID(ST_MakePoint(?, ?), 4326) WHERE ogc_fid = ?", [$x, $y, $id]);
    echo "✅ Đã tạo geometry\n";
    
    // Test 4: Kiểm tra geometry
    echo "\n📋 Test 4: Kiểm tra geometry...\n";
    $checkGeom = DB::selectOne("SELECT ST_AsText($geomCol) as geom_text, ogc_fid FROM cong WHERE ogc_fid = ?", [$id]);
    
    if ($checkGeom && $checkGeom->geom_text) {
        echo "✅ Geometry đã được tạo: " . $checkGeom->geom_text . "\n";
    } else {
        echo "❌ Geometry KHÔNG được tạo!\n";
        exit(1);
    }
    
    // Test 5: Kiểm tra API index() có trả về cống này không
    echo "\n📋 Test 5: Kiểm tra API index() có trả về cống này không...\n";
    
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
            exit(1);
        }
    } else {
        echo "❌ API response không hợp lệ!\n";
        exit(1);
    }
    
    // Test 6: Xóa cống test
    echo "\n📋 Test 6: Xóa cống test...\n";
    DB::statement("DELETE FROM cong WHERE ogc_fid = ?", [$id]);
    echo "✅ Đã xóa cống test\n";
    
    echo "\n" . str_repeat("=", 60) . "\n";
    echo "✅ TẤT CẢ TEST ĐỀU THÀNH CÔNG!\n";
    echo "✅ Logic thêm cống và hiển thị trên bản đồ hoạt động đúng!\n";
    echo str_repeat("=", 60) . "\n";
    
} catch (\Exception $e) {
    echo "❌ LỖI: " . $e->getMessage() . "\n";
    echo "   File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    exit(1);
}

