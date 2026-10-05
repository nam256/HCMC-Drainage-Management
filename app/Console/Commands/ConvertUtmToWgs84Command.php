<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ConvertUtmToWgs84Command extends Command
{
    protected $signature = 'convert:utm-to-wgs84 {--zone=48} {--hemisphere=N}';
    protected $description = 'Convert UTM coordinates to WGS84 (lat/lon)';

    public function handle()
    {
        $zone = (int)$this->option('zone');
        $hemisphere = $this->option('hemisphere');
        
        $this->info("Converting UTM Zone {$zone}{$hemisphere} to WGS84...");
        
        $count = DB::table('cong')
            ->whereNotNull('toa_do_x')
            ->whereNotNull('toa_do_y')
            ->count();
            
        $this->info("Found {$count} records with coordinates");
        
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
                    
                    // Chuyển đổi UTM sang WGS84
                    $latLon = $this->utmToLatLon($x, $y, $zone, $hemisphere);
                    
                    if ($latLon) {
                        // Cập nhật geometry với tọa độ WGS84
                        DB::statement("
                            UPDATE cong 
                            SET geom = ST_SetSRID(ST_MakePoint(?, ?), 4326)
                            WHERE ogc_fid = ?
                        ", [$latLon['lon'], $latLon['lat'], $record->ogc_fid]);
                        
                        $converted++;
                    }
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

    /**
     * Chuyển đổi UTM sang Lat/Lon (WGS84)
     * Sử dụng công thức UTM to Lat/Lon
     */
    private function utmToLatLon($easting, $northing, $zone, $hemisphere = 'N')
    {
        // UTM constants
        $k0 = 0.9996; // Scale factor
        $a = 6378137.0; // WGS84 semi-major axis
        $e2 = 0.00669438; // WGS84 first eccentricity squared
        $e1 = (1 - sqrt(1 - $e2)) / (1 + sqrt(1 - $e2));
        
        // Central meridian
        $long0 = ($zone - 1) * 6 - 180 + 3;
        
        // Remove false easting and northing
        $x = $easting - 500000;
        $y = $hemisphere == 'S' ? $northing - 10000000 : $northing;
        
        // Calculate latitude
        $M = $y / $k0;
        $mu = $M / ($a * (1 - $e2/4 - 3*$e2*$e2/64 - 5*$e2*$e2*$e2/256));
        
        $e1_2 = $e1 * $e1;
        $e1_3 = $e1_2 * $e1;
        $e1_4 = $e1_3 * $e1;
        
        $J1 = 3*$e1/2 - 27*$e1_3/32;
        $J2 = 21*$e1_2/16 - 55*$e1_4/32;
        $J3 = 151*$e1_3/96;
        $J4 = 1097*$e1_4/512;
        
        $fp = $mu + $J1*sin(2*$mu) + $J2*sin(4*$mu) + $J3*sin(6*$mu) + $J4*sin(8*$mu);
        
        $e_2 = $e2 / (1 - $e2);
        $C1 = $e_2 * cos($fp) * cos($fp);
        $T1 = tan($fp) * tan($fp);
        $N1 = $a / sqrt(1 - $e2 * sin($fp) * sin($fp));
        $R1 = $a * (1 - $e2) / pow(1 - $e2 * sin($fp) * sin($fp), 1.5);
        $D = $x / ($N1 * $k0);
        
        $lat = $fp - ($N1 * tan($fp) / $R1) * ($D*$D/2 - (5 + 3*$T1 + 10*$C1 - 4*$C1*$C1 - 9*$e_2)*$D*$D*$D*$D/24 + (61 + 90*$T1 + 298*$C1 + 45*$T1*$T1 - 252*$e_2 - 3*$C1*$C1)*$D*$D*$D*$D*$D*$D/720);
        $lon = $long0 + ($D - (1 + 2*$T1 + $C1)*$D*$D*$D/6 + (5 - 2*$C1 + 28*$T1 - 3*$C1*$C1 + 8*$e_2 + 24*$T1*$T1)*$D*$D*$D*$D*$D/120) / cos($fp);
        
        return [
            'lat' => rad2deg($lat),
            'lon' => rad2deg($lon)
        ];
    }
}

