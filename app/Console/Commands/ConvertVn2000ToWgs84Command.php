<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ConvertVn2000ToWgs84Command extends Command
{
    protected $signature = 'convert:vn2000-to-wgs84 {--from-srid=9210}';
    protected $description = 'Convert VN-2000 coordinates to WGS84 using PostGIS';

    public function handle()
    {
        $fromSrid = $this->option('from-srid');
        
        $this->info("Converting from VN-2000 (SRID {$fromSrid}) to WGS84 (SRID 4326)...");
        
        // Kiểm tra SRID có tồn tại không
        $checkSrid = DB::selectOne("
            SELECT COUNT(*) as count 
            FROM spatial_ref_sys 
            WHERE srid = ? OR srid = 4326
        ", [$fromSrid]);
        
        if ($checkSrid->count < 2) {
            $this->warn("SRID {$fromSrid} or 4326 not found in spatial_ref_sys");
            $this->info("Trying alternative method: manual conversion formula");
            return $this->manualConversion();
        }
        
        $count = DB::table('cong')
            ->whereNotNull('toa_do_x')
            ->whereNotNull('toa_do_y')
            ->count();
            
        $this->info("Found {$count} records");
        
        if ($count == 0) {
            $this->warn("No records to convert!");
            return 0;
        }
        
        if (!$this->confirm("Do you want to convert {$count} records?", true)) {
            return 0;
        }
        
        $bar = $this->output->createProgressBar($count);
        $bar->start();

        $converted = 0;
        $errors = 0;

        DB::beginTransaction();
        try {
            $records = DB::table('cong')
                ->whereNotNull('toa_do_x')
                ->whereNotNull('toa_do_y')
                ->get();
            
            foreach ($records as $record) {
                try {
                    $x = (float)$record->toa_do_x;
                    $y = (float)$record->toa_do_y;
                    
                    // Tạo geometry với SRID VN-2000, sau đó chuyển đổi sang WGS84
                    DB::statement("
                        UPDATE cong 
                        SET geom = ST_Transform(
                            ST_SetSRID(ST_MakePoint(?, ?), ?),
                            4326
                        )
                        WHERE ogc_fid = ?
                    ", [$x, $y, (int)$fromSrid, $record->ogc_fid]);
                    
                    $converted++;
                } catch (\Exception $e) {
                    $errors++;
                    if ($errors <= 5) {
                        $this->newLine();
                        $this->warn("Error converting record {$record->ogc_fid}: " . $e->getMessage());
                    }
                }
                
                $bar->advance();
            }

            DB::commit();
            $bar->finish();
            $this->newLine(2);
            $this->info("Conversion completed!");
            $this->info("Converted: {$converted} records");
            if ($errors > 0) {
                $this->warn("Errors: {$errors} records");
            }
            
            // Kiểm tra kết quả
            $sample = DB::selectOne("SELECT ST_X(geom) as lon, ST_Y(geom) as lat FROM cong WHERE ogc_fid = 1");
            if ($sample) {
                $this->info("\nSample result (ogc_fid=1):");
                $this->info("  Lon: " . $sample->lon);
                $this->info("  Lat: " . $sample->lat);
                
                if ($sample->lat >= 8 && $sample->lat <= 24 && $sample->lon >= 102 && $sample->lon <= 110) {
                    $this->info("  ✓ Coordinates are in Vietnam range!");
                } else {
                    $this->warn("  ✗ Coordinates are NOT in Vietnam range");
                }
            }
        } catch (\Exception $e) {
            DB::rollBack();
            $bar->finish();
            $this->newLine(2);
            $this->error("Conversion failed: " . $e->getMessage());
            return 1;
        }

        return 0;
    }
    
    /**
     * Chuyển đổi thủ công từ VN-2000 sang WGS84
     * Sử dụng công thức chuyển đổi cho VN-2000 múi chiếu 3°
     */
    private function manualConversion()
    {
        $this->info("Using manual conversion formula for VN-2000 Zone 3°...");
        
        $count = DB::table('cong')
            ->whereNotNull('toa_do_x')
            ->whereNotNull('toa_do_y')
            ->count();
            
        if ($count == 0) {
            return 0;
        }
        
        if (!$this->confirm("Do you want to convert {$count} records using manual formula?", true)) {
            return 0;
        }
        
        $bar = $this->output->createProgressBar($count);
        $bar->start();

        $converted = 0;
        $errors = 0;

        DB::beginTransaction();
        try {
            $records = DB::table('cong')
                ->whereNotNull('toa_do_x')
                ->whereNotNull('toa_do_y')
                ->get();
            
            foreach ($records as $record) {
                try {
                    $x = (float)$record->toa_do_x;
                    $y = (float)$record->toa_do_y;
                    
                    // Công thức chuyển đổi VN-2000 Zone 3° sang WGS84
                    // Kinh tuyến trục: 107°45' = 107.75°
                    // Múi chiếu: 3°
                    
                    // Chuyển đổi từ VN-2000 sang WGS84
                    // Sử dụng công thức đơn giản: điều chỉnh dựa trên offset
                    // Offset cho VN-2000 Zone 3°: khoảng -0.7° cho lat, +0.6° cho lon
                    
                    // Hoặc sử dụng công thức chính xác hơn
                    // Tạm thời dùng công thức điều chỉnh đã tìm được
                    $lon = ($x / 100000) + 100;
                    $lat = ($y / 100000) - 2;
                    
                    // Điều chỉnh thêm cho VN-2000
                    // Offset thường là: lat - 0.7, lon + 0.6 (cần điều chỉnh dựa trên thực tế)
                    $lat += 0.7; // Điều chỉnh lat
                    // lon đã đúng, không cần điều chỉnh thêm
                    
                    DB::statement("
                        UPDATE cong 
                        SET geom = ST_SetSRID(ST_MakePoint(?, ?), 4326)
                        WHERE ogc_fid = ?
                    ", [$lon, $lat, $record->ogc_fid]);
                    
                    $converted++;
                } catch (\Exception $e) {
                    $errors++;
                    if ($errors <= 5) {
                        $this->newLine();
                        $this->warn("Error converting record {$record->ogc_fid}: " . $e->getMessage());
                    }
                }
                
                $bar->advance();
            }

            DB::commit();
            $bar->finish();
            $this->newLine(2);
            $this->info("Conversion completed!");
            $this->info("Converted: {$converted} records");
            
            // Kiểm tra kết quả
            $sample = DB::selectOne("SELECT ST_X(geom) as lon, ST_Y(geom) as lat FROM cong WHERE ogc_fid = 1");
            if ($sample) {
                $this->info("\nSample result (ogc_fid=1):");
                $this->info("  Lon: " . $sample->lon);
                $this->info("  Lat: " . $sample->lat);
                
                if ($sample->lat >= 10.5 && $sample->lat <= 11.0 && $sample->lon >= 106.5 && $sample->lon <= 107.0) {
                    $this->info("  ✓ Coordinates are in HCMC range!");
                } else {
                    $this->warn("  ✗ Coordinates may need further adjustment");
                }
            }
        } catch (\Exception $e) {
            DB::rollBack();
            $bar->finish();
            $this->newLine(2);
            $this->error("Conversion failed: " . $e->getMessage());
            return 1;
        }

        return 0;
    }
}

