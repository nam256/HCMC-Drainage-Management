<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('cong')) {
            Schema::create('cong', function (Blueprint $table) {
                // Primary key cho PostGIS - tự động tăng
                $table->integer('ogc_fid')->primary();
                
                // Các trường từ shapefile DBF (theo đúng cấu trúc file mới)
                // Stt (N, length: 10, decimals: 0)
                $table->integer('stt')->nullable();
                
                // Id (C, length: 254)
                $table->string('id', 254)->nullable();
                
                // TenCongDap (C, length: 254)
                $table->string('ten_cong_dap', 254)->nullable();
                
                // LyTrinh (C, length: 254)
                $table->string('ly_trinh', 254)->nullable();
                
                // ToaDoX (N, length: 24, decimals: 15) - WGS84 Longitude
                $table->decimal('toa_do_x', 24, 15)->nullable();
                
                // ToaDoY (N, length: 24, decimals: 15) - WGS84 Latitude
                $table->decimal('toa_do_y', 24, 15)->nullable();
                
                // CumCongTri (C, length: 254)
                $table->string('cum_cong_tri', 254)->nullable();
                
                // GoiThau (C, length: 254)
                $table->string('goi_thau', 254)->nullable();
                
                // LoaiCongTr (C, length: 254)
                $table->string('loai_cong_tr', 254)->nullable();
                
                // HinhThuc (C, length: 254)
                $table->string('hinh_thuc', 254)->nullable();
                
                // ChieuDai (N, length: 10, decimals: 0)
                $table->integer('chieu_dai')->nullable();
                
                // DuongKinh (N, length: 10, decimals: 0)
                $table->integer('duong_kinh')->nullable();
                
                // BeRong (N, length: 24, decimals: 15)
                $table->decimal('be_rong', 24, 15)->nullable();
                
                // ChieuCao (N, length: 24, decimals: 15)
                $table->decimal('chieu_cao', 24, 15)->nullable();
                
                // SoCua (N, length: 10, decimals: 0)
                $table->integer('so_cua')->nullable();
                
                // CaoTrinhDa (C, length: 254)
                $table->string('cao_trinh_da', 254)->nullable();
                
                // CaoTrinhDi (C, length: 254)
                $table->string('cao_trinh_di', 254)->nullable();
                
                // HinhThucVa (C, length: 254)
                $table->string('hinh_thuc_va', 254)->nullable();
                
                // MucTieuNhi (C, length: 254)
                $table->string('muc_tieu_nhi', 254)->nullable();
                
                // DienTichPh (N, length: 10, decimals: 0)
                $table->integer('dien_tich_ph')->nullable();
                
                // DiaDiem (C, length: 254)
                $table->string('dia_diem', 254)->nullable();
                
                // NamSuDung (N, length: 10, decimals: 0)
                $table->integer('nam_su_dung')->nullable();
                
                // CapCongTri (C, length: 254)
                $table->string('cap_cong_tri', 254)->nullable();
                
                // HeThongCon (C, length: 254)
                $table->string('he_thong_con', 254)->nullable();
                
                // DonViQuanL (C, length: 254)
                $table->string('don_vi_quan_l', 254)->nullable();
                
                // GhiChu (C, length: 254)
                $table->text('ghi_chu')->nullable();
                
                // NamCapNhat (N, length: 10, decimals: 0)
                $table->integer('nam_cap_nhat')->nullable();
                
                // Các trường file đính kèm (nếu có)
                $table->string('file_path')->nullable();
                $table->string('file_name')->nullable();
                $table->string('file_mime')->nullable();
                $table->unsignedBigInteger('file_size')->nullable();
                
                // Timestamps
                $table->timestamps();
            });
            
            // Tạo trường geometry bằng raw SQL cho PostGIS
            // SRID 4326 = WGS84 (Geographic Coordinate System)
            DB::statement('ALTER TABLE cong ADD COLUMN geom geometry(Point, 4326)');
            
            // Tạo index cho geometry để tăng tốc truy vấn không gian
            DB::statement('CREATE INDEX idx_cong_geom ON cong USING GIST (geom)');
            
            // Tạo index cho các trường thường dùng để tìm kiếm
            DB::statement('CREATE INDEX idx_cong_id ON cong (id)');
            DB::statement('CREATE INDEX idx_cong_ten_cong_dap ON cong (ten_cong_dap)');
            
            // Tạo sequence cho ogc_fid (PostGIS standard)
            $driver = DB::connection()->getDriverName();
            if ($driver === 'pgsql') {
                DB::statement("CREATE SEQUENCE IF NOT EXISTS cong_ogc_fid_seq");
                DB::statement("ALTER TABLE cong ALTER COLUMN ogc_fid SET DEFAULT nextval('cong_ogc_fid_seq')");
                DB::statement("ALTER SEQUENCE cong_ogc_fid_seq OWNED BY cong.ogc_fid");
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cong');
        DB::statement('DROP SEQUENCE IF EXISTS cong_ogc_fid_seq');
    }
};
