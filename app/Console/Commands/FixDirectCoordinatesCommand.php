<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FixDirectCoordinatesCommand extends Command
{
    protected $signature = 'fix:direct-coordinates {--method=divide}';
    protected $description = 'Fix coordinates by using original X,Y with different methods';

    public function handle()
    {
        $method = $this->option('method');
        
        $this->info("Fixing coordinates using method: {$method}");
        $this->info("Methods: divide (X/10000, Y/10000), swap-divide (Y/10000, X/10000)");
        
        $count = DB::table('cong')
            ->whereNotNull('toa_do_x')
            ->whereNotNull('toa_do_y')
            ->count();
            
        $this->info("Found {$count} records");
        
        if ($count == 0) {
            $this->warn("No records to fix!");
            return 0;
        }
        
        if (!$this->confirm("Do you want to fix {$count} records?", true)) {
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
                    $x = (float)$record->toa_do_x;
                    $y = (float)$record->toa_do_y;
                    
                    if ($method === 'divide') {
                        // Chia cho 10000: X/10000 làm lon, Y/10000 làm lat
                        $lon = $x / 10000;
                        $lat = $y / 10000;
                    } elseif ($method === 'swap-divide') {
                        // Đảo ngược và chia: Y/10000 làm lon, X/10000 làm lat
                        $lon = $y / 10000;
                        $lat = $x / 10000;
                    } elseif ($method === 'divide-100000') {
                        // Chia cho 100000
                        $lon = $x / 100000;
                        $lat = $y / 100000;
                    } elseif ($method === 'swap-divide-100000') {
                        // Đảo ngược và chia cho 100000
                        $lon = $y / 100000;
                        $lat = $x / 100000;
                    } else {
                        throw new \Exception("Unknown method: {$method}");
                    }
                    
                    DB::statement("
                        UPDATE cong 
                        SET geom = ST_SetSRID(ST_MakePoint(?, ?), 4326)
                        WHERE ogc_fid = ?
                    ", [$lon, $lat, $record->ogc_fid]);
                    
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
                $this->info("  Original X: " . DB::table('cong')->where('ogc_fid', 1)->value('toa_do_x'));
                $this->info("  Original Y: " . DB::table('cong')->where('ogc_fid', 1)->value('toa_do_y'));
                $this->info("  Lon: " . $sample->lon);
                $this->info("  Lat: " . $sample->lat);
                
                if ($sample->lat >= 8 && $sample->lat <= 24 && $sample->lon >= 102 && $sample->lon <= 110) {
                    $this->info("  ✓ Coordinates are in Vietnam range!");
                } else {
                    $this->warn("  ✗ Coordinates are NOT in Vietnam range");
                    $this->warn("  Expected: Lat ~10.7-10.9, Lon ~106.6-106.7 for HCMC");
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

