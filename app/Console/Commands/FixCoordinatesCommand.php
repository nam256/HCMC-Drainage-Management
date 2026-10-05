<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class FixCoordinatesCommand extends Command
{
    protected $signature = 'fix:coordinates {--shp-path=}';
    protected $description = 'Fix coordinates by reading geometry from SHP file';

    public function handle()
    {
        $shpPath = $this->option('shp-path') ?? base_path('shapefile_output/cong_fc.shp');
        
        if (!File::exists($shpPath)) {
            $this->error("SHP file not found: {$shpPath}");
            return 1;
        }

        $this->info("Reading SHP file: {$shpPath}");
        
        // Đọc SHP file
        $geometries = $this->readShpFile($shpPath);
        
        if (empty($geometries)) {
            $this->error("No geometries found in SHP file");
            return 1;
        }

        $this->info("Found " . count($geometries) . " geometries");
        
        // Đọc DBF để map với ogc_fid
        $basePath = dirname($shpPath);
        $baseName = pathinfo($shpPath, PATHINFO_FILENAME);
        $dbfPath = $basePath . '/' . $baseName . '.dbf';
        
        $dbfData = [];
        if (File::exists($dbfPath)) {
            $dbfData = $this->readDbfFile($dbfPath);
            $this->info("Found " . count($dbfData) . " DBF records");
        }

        if (!$this->confirm("Do you want to update " . count($geometries) . " records?", true)) {
            return 0;
        }

        $bar = $this->output->createProgressBar(count($geometries));
        $bar->start();

        $updated = 0;
        $errors = 0;

        DB::beginTransaction();
        try {
            foreach ($geometries as $index => $geometry) {
                try {
                    // Tìm record tương ứng trong database
                    // Giả sử thứ tự trong SHP file khớp với thứ tự trong DBF
                    $record = null;
                    if (isset($dbfData[$index])) {
                        $dbfRecord = $dbfData[$index];
                        // Tìm theo ID hoặc các trường khác
                        $record = DB::table('cong')
                            ->where('id', $dbfRecord['Id'] ?? null)
                            ->orWhere('stt', $dbfRecord['Stt'] ?? null)
                            ->first();
                    }
                    
                    // Nếu không tìm thấy, dùng index
                    if (!$record) {
                        $records = DB::table('cong')
                            ->orderBy('ogc_fid')
                            ->skip($index)
                            ->take(1)
                            ->get();
                        $record = $records->first();
                    }
                    
                    if (!$record) {
                        continue;
                    }

                    // Cập nhật geometry
                    // Geometry từ SHP file có thể là WGS84 hoặc cần chuyển đổi
                    // Thử insert trực tiếp với SRID 4326
                    if ($geometry) {
                        DB::statement("
                            UPDATE cong 
                            SET geom = ST_GeomFromText(?, 4326)
                            WHERE ogc_fid = ?
                        ", [$geometry, $record->ogc_fid]);
                        $updated++;
                    }
                } catch (\Exception $e) {
                    $errors++;
                    if ($errors <= 5) {
                        $this->newLine();
                        $this->warn("Error updating record " . ($index + 1) . ": " . $e->getMessage());
                    }
                }
                
                $bar->advance();
            }

            DB::commit();
            $bar->finish();
            $this->newLine(2);
            $this->info("Update completed!");
            $this->info("Updated: {$updated} records");
            if ($errors > 0) {
                $this->warn("Errors: {$errors} records");
            }
        } catch (\Exception $e) {
            DB::rollBack();
            $bar->finish();
            $this->newLine(2);
            $this->error("Update failed: " . $e->getMessage());
            return 1;
        }

        return 0;
    }

    private function readShpFile($path)
    {
        $geometries = [];
        $handle = fopen($path, 'rb');
        
        if (!$handle) {
            return $geometries;
        }

        // Đọc header (100 bytes)
        $header = fread($handle, 100);
        
        // Bỏ qua phần đầu, đọc records
        // Format: Record Header (8 bytes) + Record Contents
        while (!feof($handle)) {
            // Đọc record header
            $recordHeader = fread($handle, 8);
            if (strlen($recordHeader) < 8) {
                break;
            }
            
            $headerData = unpack('NrecordNumber/NcontentLength', $recordHeader);
            $contentLength = $headerData['contentLength'] * 2; // Content length is in 16-bit words
            
            if ($contentLength <= 0) {
                break;
            }
            
            // Đọc shape type (4 bytes)
            $shapeTypeBytes = fread($handle, 4);
            if (strlen($shapeTypeBytes) < 4) {
                break;
            }
            $shapeType = unpack('V', $shapeTypeBytes)[1];
            
            // Point (type 1)
            if ($shapeType == 1) {
                $coords = fread($handle, 16);
                if (strlen($coords) >= 16) {
                    $point = unpack('dx/dy', $coords);
                    // Giả sử đây là WGS84 (cần kiểm tra)
                    $wkt = "POINT({$point['x']} {$point['y']})";
                    $geometries[] = $wkt;
                }
            }
            // PointZ (type 11)
            elseif ($shapeType == 11) {
                $coords = fread($handle, 24);
                if (strlen($coords) >= 24) {
                    $point = unpack('dx/dy/dz', $coords);
                    $wkt = "POINT({$point['x']} {$point['y']})";
                    $geometries[] = $wkt;
                }
            }
            // Null shape hoặc các loại khác
            elseif ($shapeType == 0) {
                $geometries[] = null;
            }
            else {
                // Skip other geometry types for now
                fseek($handle, $contentLength - 4, SEEK_CUR);
                $geometries[] = null;
            }
        }
        
        fclose($handle);
        return $geometries;
    }

    private function readDbfFile($path)
    {
        // Reuse from ImportShapefileCommand
        $data = [];
        $handle = fopen($path, 'rb');
        
        if (!$handle) {
            return $data;
        }

        $headerBytes = fread($handle, 32);
        if (strlen($headerBytes) < 32) {
            fclose($handle);
            return $data;
        }
        
        $header = unpack('Cversion/Cyear/Cmonth/Cday/VrecordCount/vheaderLength/vrecordLength', $headerBytes);
        
        if ($header['recordCount'] == 0 || $header['headerLength'] < 32) {
            fclose($handle);
            return $data;
        }

        $fields = [];
        $currentPos = 32;
        
        while ($currentPos < $header['headerLength'] - 1) {
            $fieldData = fread($handle, 32);
            if (strlen($fieldData) < 32) {
                break;
            }
            
            if (ord($fieldData[0]) == 0x0D) {
                break;
            }
            
            $field = unpack('a11name/Ctype/Vaddress/Clength/Cdecimals/a14reserved', $fieldData);
            $field['name'] = trim($field['name']);
            
            if (empty($field['name']) || $field['name'][0] == "\x00") {
                break;
            }
            
            if ($field['length'] > 0 && $field['length'] < 255) {
                $fields[] = $field;
            }
            
            $currentPos += 32;
        }
        
        if ($currentPos < $header['headerLength']) {
            fread($handle, $header['headerLength'] - $currentPos);
        }
        
        for ($i = 0; $i < $header['recordCount']; $i++) {
            $deleted = fread($handle, 1);
            if (strlen($deleted) === 0) {
                break;
            }
            
            if ($deleted === ' ' || $deleted === "\x20") {
                $record = [];
                foreach ($fields as $field) {
                    if ($field['length'] <= 0) {
                        continue;
                    }
                    $value = fread($handle, $field['length']);
                    if (strlen($value) < $field['length']) {
                        break 2;
                    }
                    $value = trim($value);
                    if ($field['type'] === 'N' && $field['decimals'] == 0) {
                        $value = $value !== '' ? (int)$value : null;
                    } elseif ($field['type'] === 'N') {
                        $value = $value !== '' ? (float)$value : null;
                    } elseif ($field['type'] === 'C') {
                        $value = trim($value);
                        if (function_exists('mb_convert_encoding') && $value !== '') {
                            $value = mb_convert_encoding($value, 'UTF-8', 'Windows-1252');
                        }
                    }
                    $record[$field['name']] = $value;
                }
                if (!empty($record)) {
                    $data[] = $record;
                }
            } else {
                fseek($handle, $header['recordLength'] - 1, SEEK_CUR);
            }
        }
        
        fclose($handle);
        return $data;
    }
}

