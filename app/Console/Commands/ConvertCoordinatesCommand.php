<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ConvertCoordinatesCommand extends Command
{
    protected $signature = 'convert:coordinates {--from-srid=32648} {--to-srid=4326}';
    protected $description = 'Convert coordinates from UTM/VN-2000 to WGS84';

    public function handle()
    {
        $fromSrid = $this->option('from-srid');
        $toSrid = $this->option('to-srid');
        
        $this->info("Converting coordinates from SRID {$fromSrid} to SRID {$toSrid}...");
        
        // Đếm số records có geometry
        $count = DB::table('cong')->whereNotNull('geom')->count();
        $this->info("Found {$count} records with geometry");
        
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
            // Lấy tất cả records có geometry
            $records = DB::table('cong')
                ->whereNotNull('geom')
                ->whereNotNull('toa_do_x')
                ->whereNotNull('toa_do_y')
                ->get();
            
            foreach ($records as $record) {
                try {
                    // Tạo geometry từ tọa độ X, Y với SRID nguồn
                    $x = (float)$record->toa_do_x;
                    $y = (float)$record->toa_do_y;
                    
                    // Chuyển đổi từ SRID nguồn sang WGS84
                    // Kiểm tra xem SRID có tồn tại không
                    $checkSrid = DB::selectOne("
                        SELECT COUNT(*) as count 
                        FROM spatial_ref_sys 
                        WHERE srid = ? OR srid = ?
                    ", [$fromSrid, $toSrid]);
                    
                    if ($checkSrid->count < 2) {
                        throw new \Exception("SRID {$fromSrid} or {$toSrid} not found in spatial_ref_sys");
                    }
                    
                    DB::update("
                        UPDATE cong 
                        SET geom = ST_Transform(
                            ST_SetSRID(ST_MakePoint(?, ?), ?),
                            ?
                        )
                        WHERE ogc_fid = ?
                    ", [$x, $y, (int)$fromSrid, (int)$toSrid, $record->ogc_fid]);
                    
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
