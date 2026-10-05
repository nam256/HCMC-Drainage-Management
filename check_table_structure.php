<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "🔍 KIỂM TRA CẤU TRÚC BẢNG CONG\n";
echo str_repeat("=", 60) . "\n\n";

// 1. Kiểm tra tất cả các cột trong bảng cong
echo "📋 1. Tất cả các cột trong bảng cong:\n";
$columns = DB::select("
    SELECT 
        column_name,
        data_type,
        udt_name,
        is_nullable,
        column_default
    FROM information_schema.columns
    WHERE table_name = 'cong'
    ORDER BY ordinal_position;
");

foreach ($columns as $col) {
    $isGeom = in_array($col->udt_name, ['geometry', 'geography']);
    $marker = $isGeom ? "📍" : "  ";
    echo "   $marker {$col->column_name}: {$col->data_type} ({$col->udt_name}) - Nullable: {$col->is_nullable}\n";
}

// 2. Kiểm tra cột geometry cụ thể
echo "\n📋 2. Kiểm tra cột geometry:\n";
$geomCols = DB::select("
    SELECT 
        f_table_schema AS schema,
        f_table_name AS table_name,
        f_geometry_column AS geom_col,
        coord_dimension,
        srid,
        type
    FROM geometry_columns
    WHERE f_table_name = 'cong';
");

if (count($geomCols) > 0) {
    foreach ($geomCols as $gc) {
        echo "   ✅ Tìm thấy cột geometry:\n";
        echo "      - Tên cột: {$gc->geom_col}\n";
        echo "      - Schema: {$gc->schema}\n";
        echo "      - SRID: {$gc->srid}\n";
        echo "      - Type: {$gc->type}\n";
        echo "      - Dimension: {$gc->coord_dimension}D\n";
    }
} else {
    echo "   ❌ KHÔNG tìm thấy cột geometry trong geometry_columns!\n";
    echo "   → Có thể cột geom chưa được đăng ký với PostGIS\n";
}

// 3. Kiểm tra xem có cột tên "geom" không
echo "\n📋 3. Kiểm tra cột tên 'geom':\n";
$geomCol = DB::selectOne("
    SELECT column_name, data_type, udt_name
    FROM information_schema.columns
    WHERE table_name = 'cong' AND column_name = 'geom';
");

if ($geomCol) {
    echo "   ✅ Có cột 'geom':\n";
    echo "      - Data type: {$geomCol->data_type}\n";
    echo "      - UDT: {$geomCol->udt_name}\n";
    
    if ($geomCol->udt_name === 'geometry') {
        echo "      → Đây là cột geometry PostGIS ✅\n";
    } else {
        echo "      → Đây KHÔNG phải cột geometry PostGIS ❌\n";
    }
} else {
    echo "   ❌ KHÔNG có cột tên 'geom'!\n";
    
    // Tìm các cột có thể là geometry
    $possibleGeomCols = DB::select("
        SELECT column_name, udt_name
        FROM information_schema.columns
        WHERE table_name = 'cong' 
        AND udt_name IN ('geometry', 'geography');
    ");
    
    if (count($possibleGeomCols) > 0) {
        echo "   💡 Nhưng có các cột geometry khác:\n";
        foreach ($possibleGeomCols as $col) {
            echo "      - {$col->column_name} ({$col->udt_name})\n";
        }
    }
}

// 4. Kiểm tra số lượng cống có geometry
echo "\n📋 4. Thống kê geometry:\n";
$gc = DB::selectOne("SELECT f_geometry_column AS geom_col FROM geometry_columns WHERE f_table_name = 'cong' LIMIT 1;");
$geomColName = $gc->geom_col ?? 'geom';

$stats = DB::selectOne("
    SELECT 
        COUNT(*) as total,
        COUNT($geomColName) as has_geom,
        COUNT(*) - COUNT($geomColName) as no_geom
    FROM cong;
");

if ($stats) {
    echo "   - Tổng số cống: {$stats->total}\n";
    echo "   - Cống có geometry: {$stats->has_geom} ✅\n";
    echo "   - Cống KHÔNG có geometry: {$stats->no_geom} ❌\n";
}

echo "\n" . str_repeat("=", 60) . "\n";

