<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FixSwapCoordinatesCommand extends Command
{
    protected $signature = 'fix:swap-coordinates';
    protected $description = 'Swap X and Y coordinates (fix reversed coordinates)';

    public function handle()
    {
        $this->info("Swapping X and Y coordinates...");
        
        $count = DB::table('cong')
            ->whereNotNull('toa_do_x')
            ->whereNotNull('toa_do_y')
            ->count();
            
        $this->info("Found {$count} records");
        
        if ($count == 0) {
            $this->warn("No records to fix!");
            return 0;
        }
        
        if (!$this->confirm("Do you want to swap coordinates for {$count} records?", true)) {
            return 0;
        }
        
        $bar = $this->output->createProgressBar($count);
        $bar->start();

        $updated = 0;
        $errors = 0;

        DB::beginTransaction();
        try {
            $records = DB::table('cong')
                ->whereNotNull('toa_do_x')
                ->whereNotNull('toa_do_y')
                ->get();
            
            foreach ($records as $record) {
                try {
                    // Lấy tọa độ hiện tại
                    $currentGeom = DB::selectOne("SELECT ST_X(geom) as lon, ST_Y(geom) as lat FROM cong WHERE ogc_fid = ?", [$record->ogc_fid]);
                    
                    if (!$currentGeom) {
                        continue;
                    }
                    
                    // Đảo ngược: Y (lat) thành X (lon), X (lon) thành Y (lat)
                    // Hoặc thử: dùng toa_do_y làm lon, toa_do_x làm lat
                    // Nhưng vì lat đã đúng (10.85), có thể chỉ cần chuyển đổi X đúng
                    
                    // Thử: dùng toa_do_y/100000 làm lon (vì 1200714 có thể là 120.0714 * 10000)
                    // Hoặc đơn giản: đảo ngược lat và lon hiện tại
                    
                    // Cách 1: Đảo ngược lat và lon
                    $newLon = $currentGeom->lat; // Lat hiện tại làm lon
                    $newLat = $currentGeom->lon; // Lon hiện tại làm lat
                    
                    // Cách 2: Thử với tọa độ gốc chia cho scale factor
                    // Vì tọa độ gốc X=681349, Y=1200714
                    // Có thể Y/10000 = 120.0714 (lon) và X/10000 = 68.1349 (lat - sai)
                    // Hoặc X/10000 = 68.1349 (lon - vẫn sai)
                    
                    // Thử cách đơn giản nhất: đảo ngược lat/lon hiện tại
                    DB::statement("
                        UPDATE cong 
                        SET geom = ST_SetSRID(ST_MakePoint(?, ?), 4326)
                        WHERE ogc_fid = ?
                    ", [$newLon, $newLat, $record->ogc_fid]);
                    
                    $updated++;
                } catch (\Exception $e) {
                    $errors++;
                    if ($errors <= 5) {
                        $this->newLine();
                        $this->warn("Error updating record {$record->ogc_fid}: " . $e->getMessage());
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
            $this->error("Update failed: " . $e->getMessage());
            return 1;
        }

        return 0;
    }
}

