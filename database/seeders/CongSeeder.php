<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class CongSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Import GeoJSON mẫu vào PostGIS bảng cong
     * CHỈ CHẠY KHI BẢNG CÒN TRỐNG - Tránh tạo duplicate khi đã có dữ liệu thật
     */
    public function run(): void
    {
        // Kiểm tra bảng cong có tồn tại không
        $tableExists = DB::selectOne("SELECT to_regclass('cong') AS exists");
        if (!$tableExists || !$tableExists->exists) {
            $this->command->warn('Bảng cong chưa tồn tại. Vui lòng tạo bảng trước.');
            return;
        }

        // Kiểm tra xem đã có dữ liệu trong bảng chưa
        $count = DB::table('cong')->count();
        if ($count > 0) {
            $this->command->info("Bảng cong đã có {$count} bản ghi. Bỏ qua việc tạo dữ liệu mẫu.");
            $this->command->info('Nếu muốn import GeoJSON, vui lòng sử dụng file: storage/app/geojson/cong_sample.geojson');
            return;
        }

        $this->command->info('Bảng cong đang trống. Bắt đầu import dữ liệu mẫu...');

        // Đường dẫn file GeoJSON mẫu (có thể đặt trong storage/app/geojson/)
        $geojsonPath = storage_path('app/geojson/cong_sample.geojson');
        
        // Nếu có file GeoJSON, import từ file
        if (File::exists($geojsonPath)) {
            $this->command->info('Tìm thấy file GeoJSON mẫu. Đang import...');
            $this->importFromGeoJSON($geojsonPath);
            return;
        }

        // Nếu không có file GeoJSON, tạo dữ liệu mẫu tối thiểu để test
        $this->command->info('Không tìm thấy file GeoJSON mẫu.');
        $this->command->info('Tạo dữ liệu mẫu tối thiểu (2 bản ghi) để test hệ thống...');
        $this->createSampleData();
    }

    /**
     * Import từ file GeoJSON
     */
    private function importFromGeoJSON(string $geojsonPath): void
    {
        // Đọc và parse GeoJSON
        $geojsonContent = File::get($geojsonPath);
        $geojson = json_decode($geojsonContent, true);

        if (!isset($geojson['features']) || empty($geojson['features'])) {
            $this->command->warn('File GeoJSON không có features hợp lệ.');
            return;
        }

        $this->command->info('Đang import ' . count($geojson['features']) . ' features...');

        $successCount = 0;
        foreach ($geojson['features'] as $feature) {
            if ($this->importFeature($feature)) {
                $successCount++;
            }
        }

        $this->command->info("Import hoàn thành! Đã import {$successCount}/" . count($geojson['features']) . " features.");
    }

    /**
     * Import một feature từ GeoJSON vào database
     * 
     * @return bool True nếu thành công
     */
    private function importFeature(array $feature): bool
    {
        if (!isset($feature['geometry']) || !isset($feature['properties'])) {
            return false;
        }

        $geometry = $feature['geometry'];
        $properties = $feature['properties'];

        // Convert GeoJSON geometry sang WKT
        $wkt = $this->geojsonToWKT($geometry);

        if (!$wkt) {
            $this->command->warn('Không thể convert geometry sang WKT. Bỏ qua feature.');
            return false;
        }

        // Chuẩn bị dữ liệu insert
        $data = [
            'objectid' => $properties['objectid'] ?? null,
            'id' => $properties['id'] ?? null,
            'quan_huyen' => $properties['quan_huyen'] ?? 'Chưa xác định',
            'ten_tuyen' => $properties['ten_tuyen'] ?? null,
            'duong_kinh_mm' => $properties['duong_kinh_mm'] ?? null,
            'vat_lieu' => $properties['vat_lieu'] ?? null,
            'chieudai_m' => $properties['chieudai_m'] ?? null,
            'tinh_trang' => $properties['tinh_trang'] ?? 'Chưa xác định',
            'geom' => DB::raw("ST_GeomFromText('$wkt', 4326)"),
        ];

        try {
            DB::table('cong')->insert($data);
            return true;
        } catch (\Exception $e) {
            $this->command->error('Lỗi khi insert: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Convert GeoJSON geometry sang WKT (Well-Known Text)
     */
    private function geojsonToWKT(array $geometry): ?string
    {
        $type = strtoupper($geometry['type'] ?? '');
        $coordinates = $geometry['coordinates'] ?? [];

        if (empty($coordinates)) {
            return null;
        }

        switch ($type) {
            case 'POINT':
                return "POINT({$coordinates[0]} {$coordinates[1]})";
            
            case 'LINESTRING':
                $coords = array_map(function ($coord) {
                    return "{$coord[0]} {$coord[1]}";
                }, $coordinates);
                return "LINESTRING(" . implode(', ', $coords) . ")";
            
            case 'POLYGON':
                $rings = [];
                foreach ($coordinates as $ring) {
                    $coords = array_map(function ($coord) {
                        return "{$coord[0]} {$coord[1]}";
                    }, $ring);
                    $rings[] = "(" . implode(', ', $coords) . ")";
                }
                return "POLYGON(" . implode(', ', $rings) . ")";
            
            default:
                return null;
        }
    }

    /**
     * Tạo dữ liệu mẫu tối thiểu để test (chỉ khi bảng trống)
     */
    private function createSampleData(): void
    {
        // Dữ liệu mẫu: 2 tuyến cống ở TP.HCM
        $samples = [
            [
                'objectid' => 1,
                'id' => 'CONG-001',
                'quan_huyen' => 'Quận 1',
                'ten_tuyen' => 'Tuyến Nguyễn Huệ',
                'duong_kinh_mm' => 500,
                'vat_lieu' => 'Bê tông',
                'chieudai_m' => 250.5,
                'tinh_trang' => 'Tốt',
                'coordinates' => [[106.7000, 10.7756], [106.7020, 10.7760], [106.7040, 10.7765]],
            ],
            [
                'objectid' => 2,
                'id' => 'CONG-002',
                'quan_huyen' => 'Quận 3',
                'ten_tuyen' => 'Tuyến Lê Lợi',
                'duong_kinh_mm' => 600,
                'vat_lieu' => 'Nhựa HDPE',
                'chieudai_m' => 180.3,
                'tinh_trang' => 'Cần bảo trì',
                'coordinates' => [[106.6900, 10.7700], [106.6920, 10.7710], [106.6940, 10.7720]],
            ],
        ];

        foreach ($samples as $sample) {
            $coords = $sample['coordinates'];
            unset($sample['coordinates']);
            
            $wktCoords = array_map(function ($p) {
                return $p[0] . ' ' . $p[1];
            }, $coords);
            $wkt = "LINESTRING(" . implode(', ', $wktCoords) . ")";

            $data = array_merge($sample, [
                'geom' => DB::raw("ST_GeomFromText('$wkt', 4326)"),
            ]);

            try {
                DB::table('cong')->insert($data);
                $this->command->info("✓ Đã tạo mẫu: {$sample['ten_tuyen']}");
            } catch (\Exception $e) {
                $this->command->error("✗ Lỗi tạo mẫu {$sample['ten_tuyen']}: " . $e->getMessage());
            }
        }

        $this->command->info('');
        $this->command->info('✓ Đã tạo ' . count($samples) . ' bản ghi mẫu để test.');
        $this->command->info('');
        $this->command->info('💡 Lưu ý: Để import dữ liệu thật từ ArcGIS:');
        $this->command->info('   1. Export từ ArcGIS ra file GeoJSON');
        $this->command->info('   2. Đặt file vào: storage/app/geojson/cong_sample.geojson');
        $this->command->info('   3. Xóa dữ liệu mẫu và chạy lại seeder');
    }
}
