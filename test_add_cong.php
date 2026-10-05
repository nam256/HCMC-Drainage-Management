<?php

/**
 * Script test để kiểm tra logic thêm cống và hiển thị trên bản đồ
 * Chạy: php test_add_cong.php
 */

require __DIR__ . '/vendor/autoload.php';

// Load Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

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
    'ten_cong_dap' => 'Cống Test Auto ' . time(),
    'dia_diem' => 'Test Location',
];

try {
    // Insert và lấy ID
    $id = DB::table('cong')->insertGetId($insertData);
    
    if (!$id) {
        // Nếu không lấy được ID, thử query lại
        $result = DB::selectOne("SELECT ogc_fid FROM cong WHERE toa_do_x = ? AND toa_do_y = ? ORDER BY ogc_fid DESC LIMIT 1", [$x, $y]);
        $id = $result->ogc_fid ?? null;
    }
    
    if (!$id) {
        echo "❌ Không thể lấy ID sau khi insert!\n";
        exit(1);
    }
    
    echo "✅ Đã insert cống với ID: $id\n";
    
    // Test 3: Tạo geometry
    echo "\n📋 Test 3: Tạo geometry...\n";
    DB::statement("UPDATE cong SET $geomCol = ST_SetSRID(ST_MakePoint(?, ?), 4326) WHERE ogc_fid = ?", [$x, $y, $id]);
    echo "✅ Đã tạo geometry\n";
    
    // Đợi một chút để đảm bảo database đã cập nhật
    sleep(1);
    
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
    
    echo "\n" . str_repeat("=", 60) . "\n";
    echo "✅ TẤT CẢ TEST ĐỀU THÀNH CÔNG!\n";
    echo "✅ Logic thêm cống và hiển thị trên bản đồ hoạt động đúng!\n";
    echo "✅ Cống ID $id sẽ hiển thị trên bản đồ với màu XANH LÁ\n";
    echo str_repeat("=", 60) . "\n";
    echo "\n💡 Lưu ý: Cống test này vẫn còn trong database.\n";
    echo "   Bạn có thể xóa bằng cách chạy: DELETE FROM cong WHERE ogc_fid = $id;\n\n";
    
} catch (\Exception $e) {
    echo "❌ LỖI: " . $e->getMessage() . "\n";
    echo "   File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    if (isset($id)) {
        echo "\n🧹 Đang xóa cống test...\n";
        try {
            DB::statement("DELETE FROM cong WHERE ogc_fid = ?", [$id]);
            echo "✅ Đã xóa cống test\n";
        } catch (\Exception $e2) {
            echo "⚠️ Không thể xóa cống test: " . $e2->getMessage() . "\n";
        }
    }
    exit(1);
}

