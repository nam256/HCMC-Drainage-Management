<?php
/**
 * Script test cập nhật cống
 * Chạy: php test_update_cong.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\Cong;

echo "🔍 Kiểm tra logic cập nhật cống...\n\n";

// Lấy một cống để test (ví dụ ID 379)
$congId = 379;
$cong = Cong::find($congId);

if (!$cong) {
    echo "❌ Không tìm thấy cống với ID: $congId\n";
    exit(1);
}

echo "📋 Dữ liệu hiện tại của cống ID $congId:\n";
echo "  - ten_cong_dap: " . ($cong->ten_cong_dap ?? 'NULL') . "\n";
echo "  - ly_trinh: " . ($cong->ly_trinh ?? 'NULL') . "\n";
echo "  - dia_diem: " . ($cong->dia_diem ?? 'NULL') . "\n";
echo "  - ghi_chu: " . ($cong->ghi_chu ?? 'NULL') . "\n\n";

// Tạo dữ liệu test (giống như từ form)
$testData = [
    'ten_cong_dap' => 'test12',
    'ly_trinh' => 'teest',
    'dia_diem' => 'zsss',
    'ghi_chu' => '',
    'toa_do_x' => 106.87,
    'toa_do_y' => 10.812,
];

echo "📤 Dữ liệu test (giống như từ form):\n";
print_r($testData);
echo "\n";

// Test 1: Kiểm tra fill()
echo "🧪 Test 1: Sử dụng fill()...\n";
$cong->fill($testData);
$dirty = $cong->getDirty();
echo "  - Dirty attributes: " . (empty($dirty) ? 'KHÔNG CÓ' : json_encode($dirty, JSON_UNESCAPED_UNICODE)) . "\n";

if (!empty($dirty)) {
    echo "  - Sẽ cập nhật các trường: " . implode(', ', array_keys($dirty)) . "\n";
} else {
    echo "  ⚠️ Không có dirty attributes - có thể giá trị giống nhau hoặc fill() không hoạt động\n";
}

// Test 2: Kiểm tra save()
echo "\n🧪 Test 2: Gọi save()...\n";
$saved = $cong->save();
echo "  - save() trả về: " . ($saved ? 'true' : 'false') . "\n";

// Reload từ database
$cong->refresh();
echo "\n📋 Dữ liệu sau khi save() và refresh():\n";
echo "  - ten_cong_dap: " . ($cong->ten_cong_dap ?? 'NULL') . "\n";
echo "  - ly_trinh: " . ($cong->ly_trinh ?? 'NULL') . "\n";
echo "  - dia_diem: " . ($cong->dia_diem ?? 'NULL') . "\n";
echo "  - ghi_chu: " . ($cong->ghi_chu ?? 'NULL') . "\n\n";

// Test 3: Update trực tiếp bằng DB
if (empty($dirty)) {
    echo "🧪 Test 3: Update trực tiếp bằng DB::table()->update()...\n";
    $updated = DB::table('cong')
        ->where('ogc_fid', $congId)
        ->update($testData);
    echo "  - Số dòng được cập nhật: $updated\n";
    
    if ($updated > 0) {
        $cong->refresh();
        echo "\n📋 Dữ liệu sau khi update trực tiếp:\n";
        echo "  - ten_cong_dap: " . ($cong->ten_cong_dap ?? 'NULL') . "\n";
        echo "  - ly_trinh: " . ($cong->ly_trinh ?? 'NULL') . "\n";
        echo "  - dia_diem: " . ($cong->dia_diem ?? 'NULL') . "\n";
        echo "  - ghi_chu: " . ($cong->ghi_chu ?? 'NULL') . "\n";
    }
}

// Test 4: Kiểm tra fillable
echo "\n🧪 Test 4: Kiểm tra fillable attributes...\n";
$fillable = $cong->getFillable();
echo "  - Các trường fillable: " . implode(', ', $fillable) . "\n";

$missingFillable = array_diff(array_keys($testData), $fillable);
if (!empty($missingFillable)) {
    echo "  ⚠️ Các trường không có trong fillable: " . implode(', ', $missingFillable) . "\n";
} else {
    echo "  ✅ Tất cả trường đều có trong fillable\n";
}

echo "\n✅ Test hoàn tất!\n";

