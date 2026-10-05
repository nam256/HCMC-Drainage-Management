<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

// Đọc DBF file để lấy tọa độ gốc
$dbfPath = base_path('shapefile_output/cong_fc.dbf');
$handle = fopen($dbfPath, 'rb');

if (!$handle) {
    die("Cannot open DBF file\n");
}

// Đọc header
$headerBytes = fread($handle, 32);
$header = unpack('Cversion/Cyear/Cmonth/Cday/VrecordCount/vheaderLength/vrecordLength', $headerBytes);

// Đọc field descriptors
$fields = [];
$currentPos = 32;

while ($currentPos < $header['headerLength'] - 1) {
    $fieldData = fread($handle, 32);
    if (strlen($fieldData) < 32) break;
    if (ord($fieldData[0]) == 0x0D) break;
    
    $field = unpack('a11name/Ctype/Vaddress/Clength/Cdecimals/a14reserved', $fieldData);
    $field['name'] = trim($field['name']);
    if (empty($field['name']) || $field['name'][0] == "\x00") break;
    if ($field['length'] > 0 && $field['length'] < 255) {
        $fields[] = $field;
    }
    $currentPos += 32;
}

// Bỏ qua terminator
fread($handle, $header['headerLength'] - $currentPos);

echo "=== TỌA ĐỘ GỐC TRONG DBF FILE (3 dòng đầu) ===\n\n";

// Đọc 3 records đầu tiên
for ($i = 0; $i < min(3, $header['recordCount']); $i++) {
    $deleted = fread($handle, 1);
    if (strlen($deleted) === 0) break;
    
    if ($deleted === ' ' || $deleted === "\x20") {
        $record = [];
        foreach ($fields as $field) {
            if ($field['length'] <= 0) continue;
            $value = trim(fread($handle, $field['length']));
            if ($field['type'] === 'N') {
                $value = $value !== '' ? (float)$value : null;
            } elseif ($field['type'] === 'C') {
                $value = trim($value);
                if (function_exists('mb_convert_encoding') && $value !== '') {
                    $value = mb_convert_encoding($value, 'UTF-8', 'Windows-1252');
                }
            }
            $record[$field['name']] = $value;
        }
        
        echo "Record " . ($i + 1) . ":\n";
        echo "  ID: " . ($record['Id'] ?? 'N/A') . "\n";
        echo "  TenCongDap: " . ($record['TenCongDap'] ?? 'N/A') . "\n";
        echo "  ToaDoX (X): " . ($record['ToaDoX'] ?? 'N/A') . "\n";
        echo "  ToaDoY (Y): " . ($record['ToaDoY'] ?? 'N/A') . "\n";
        echo "\n";
    } else {
        fseek($handle, $header['recordLength'] - 1, SEEK_CUR);
    }
}

fclose($handle);

echo "\n=== TỌA ĐỘ HIỆN TẠI TRONG DATABASE (3 dòng đầu) ===\n\n";

$records = DB::table('cong')
    ->whereNotNull('toa_do_x')
    ->whereNotNull('toa_do_y')
    ->whereNotNull('geom')
    ->orderBy('ogc_fid')
    ->limit(3)
    ->get();

foreach ($records as $index => $record) {
    $geom = DB::selectOne("SELECT ST_X(geom) as lon, ST_Y(geom) as lat FROM cong WHERE ogc_fid = ?", [$record->ogc_fid]);
    
    echo "Record " . ($index + 1) . " (ogc_fid: {$record->ogc_fid}):\n";
    echo "  ID: " . ($record->id ?? 'N/A') . "\n";
    echo "  TenCongDap: " . ($record->ten_cong_dap ?? 'N/A') . "\n";
    echo "  ToaDoX (trong DB): " . ($record->toa_do_x ?? 'N/A') . "\n";
    echo "  ToaDoY (trong DB): " . ($record->toa_do_y ?? 'N/A') . "\n";
    echo "  Geometry Lon (ST_X): " . ($geom->lon ?? 'N/A') . "\n";
    echo "  Geometry Lat (ST_Y): " . ($geom->lat ?? 'N/A') . "\n";
    echo "\n";
}

