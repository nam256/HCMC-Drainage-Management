<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FixAdjustedCoordinatesCommand extends Command
{
    protected $signature = 'fix:adjusted-coordinates';
    protected $description = 'Fix coordinates with adjustment formula';

    public function handle()
    {
        $this->info("Fixing coordinates with adjustment...");
        $this->info("Formula: Lon = X/100000 + 100, Lat = Y/100000 - 2");
        
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
                    
                    // Công thức điều chỉnh: Lon = X/100000 + 100, Lat = Y/100000 - 2
                    $lon = ($x / 100000) + 100;
                    $lat = ($y / 100000) - 2;
                    
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

