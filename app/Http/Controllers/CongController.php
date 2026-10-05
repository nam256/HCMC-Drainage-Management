<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Cong;
use App\Services\ActivityLogger;

class CongController extends Controller
{
    public function index()
    {
        // Thêm header để tránh cache
        // 0) Xác định bảng tồn tại (bất kể schema nào trong search_path)
        $regclass = DB::selectOne("SELECT to_regclass('cong') AS exists");
        if (!$regclass || !$regclass->exists) {
            return response()->json([
                'type' => 'FeatureCollection',
                'features' => []
            ]);
        }

        // 0.1) Lấy schema và cột hình học từ geometry_columns trước, fallback information_schema
        $gc = DB::selectOne("SELECT f_table_schema AS schema, f_geometry_column AS geom_col FROM geometry_columns WHERE f_table_name = 'cong' LIMIT 1;");
        $schema = $gc->schema ?? null;
        $geomCol = $gc->geom_col ?? null;
        if (!$geomCol) {
            $geomColRow = DB::selectOne("
                SELECT column_name
                FROM information_schema.columns
                WHERE table_name = 'cong'
                  AND udt_name IN ('geometry','geography')
                LIMIT 1;
            ");
            $geomCol = $geomColRow->column_name ?? null;
        }
        if (!$geomCol) {
            // Thử các tên phổ biến
            $tryCols = ['geom','wkb_geometry','the_geom'];
            foreach ($tryCols as $c) {
                $exists = DB::selectOne("SELECT 1 FROM information_schema.columns WHERE table_name='cong' AND column_name=? LIMIT 1", [$c]);
                if ($exists) { $geomCol = $c; break; }
            }
        }
        if (!$geomCol) {
            return response()->json(['type'=>'FeatureCollection','features'=>[]]);
        }

        // Quote identifier properly for schema and table
        if ($schema) {
            $qualified = '"' . $schema . '"."cong"';
        } else {
            $qualified = '"cong"';
        }

        // 1) Trả GeoJSON - SỬA: Chỉ lấy các cống có geometry (không NULL)
        $data = DB::select("
            SELECT jsonb_build_object(
                'type', 'FeatureCollection',
                'features', COALESCE(jsonb_agg(feature), '[]'::jsonb)
            ) AS geojson
            FROM (
                SELECT jsonb_build_object(
                    'type', 'Feature',
                    'geometry', ST_AsGeoJSON(".$geomCol.")::jsonb,
                    'properties', to_jsonb(row) - '".$geomCol."'
                ) AS feature
                FROM (
                    SELECT * FROM $qualified
                    WHERE ".$geomCol." IS NOT NULL
                    ORDER BY ogc_fid DESC
                ) row
            ) features;
        ");

        $geojson = $data[0]->geojson ?? null;
        $arr = json_decode($geojson, true);

        if (!empty($arr['features'])) {
            foreach ($arr['features'] as &$feature) {
                $props = $feature['properties'];
                $feature['properties']['file_url'] = !empty($props['file_path'])
                    ? asset('storage/' . $props['file_path'])
                    : null;
            }
        }

        // Thêm headers để tránh cache
        return response()->json($arr)
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    public function store(Request $request)
    {
        $request->validate([
            // Thay đổi: Nhận toa_do_x, toa_do_y thay vì coordinates array
            // CHỈ CẦN TỌA ĐỘ X, Y - KHÔNG CẦN ID HAY MÃ SỐ
            'toa_do_x' => 'required|numeric',
            'toa_do_y' => 'required|numeric',
            'id' => 'nullable|string|max:254', // ID/Mã số KHÔNG BẮT BUỘC
            'ten_cong_dap' => 'nullable|string|max:254', // Tên cống KHÔNG BẮT BUỘC
            'ly_trinh' => 'nullable|string|max:254',
            'stt' => 'nullable|integer',
            'cum_cong_tri' => 'nullable|string|max:254',
            'goi_thau' => 'nullable|string|max:254',
            'loai_cong_tr' => 'nullable|string|max:254',
            'hinh_thuc' => 'nullable|string|max:254',
            'chieu_dai' => 'nullable|integer',
            'duong_kinh' => 'nullable|integer',
            'be_rong' => 'nullable|numeric',
            'chieu_cao' => 'nullable|numeric',
            'so_cua' => 'nullable|integer',
            'cao_trinh_da' => 'nullable|string|max:254',
            'cao_trinh_di' => 'nullable|string|max:254',
            'hinh_thuc_va' => 'nullable|string|max:254',
            'muc_tieu_nhi' => 'nullable|string|max:254',
            'dien_tich_ph' => 'nullable|integer',
            'dia_diem' => 'nullable|string|max:254',
            'nam_su_dung' => 'nullable|integer',
            'cap_cong_tri' => 'nullable|string|max:254',
            'he_thong_con' => 'nullable|string|max:254',
            'don_vi_quan_l' => 'nullable|string|max:254',
            'ghi_chu' => 'nullable|string',
            'nam_cap_nhat' => 'nullable|integer',
            'file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        $filePath = null;
        $fileName = null;
        $fileMime = null;
        $fileSize = null;

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $ext = $file->getClientOriginalExtension();
            $fileName = time() . '_' . Str::random(8) . '.' . $ext;
            $filePath = $file->storeAs('uploads/cong', $fileName, 'public');
            $fileMime = $file->getClientMimeType();
            $fileSize = $file->getSize();
        }

        // Tạo Point geometry thay vì LineString
        $x = (float)$request->input('toa_do_x');
        $y = (float)$request->input('toa_do_y');
        
        // Lấy tên cột geometry TRƯỚC khi insert
        $gc = DB::selectOne("SELECT f_geometry_column AS geom_col FROM geometry_columns WHERE f_table_name = 'cong' LIMIT 1;");
        $geomCol = $gc->geom_col ?? 'geom';
        
        // SỬA LẠI: Dùng DB::table()->insertGetId() nhưng đảm bảo geometry được tạo
        $insertData = [];
        
        // Thêm các trường thông thường
        $fieldMap = [
            'stt', 'id', 'ten_cong_dap', 'ly_trinh', 'toa_do_x', 'toa_do_y',
            'cum_cong_tri', 'goi_thau', 'loai_cong_tr', 'hinh_thuc',
            'chieu_dai', 'duong_kinh', 'be_rong', 'chieu_cao', 'so_cua',
            'cao_trinh_da', 'cao_trinh_di', 'hinh_thuc_va', 'muc_tieu_nhi',
            'dien_tich_ph', 'dia_diem', 'nam_su_dung', 'cap_cong_tri',
            'he_thong_con', 'don_vi_quan_l', 'ghi_chu', 'nam_cap_nhat',
            'file_path', 'file_name', 'file_mime', 'file_size'
        ];
        
        foreach ($fieldMap as $field) {
            $value = $request->input($field);
            if ($field === 'toa_do_x') $value = $x;
            if ($field === 'toa_do_y') $value = $y;
            if ($value !== null && $value !== '') {
                $insertData[$field] = $value;
            }
        }
        
        // SỬA TRIỆT ĐỂ: Tạo geometry TRỰC TIẾP trong INSERT statement để đảm bảo 100%
        DB::beginTransaction();
        try {
            // Xây dựng danh sách cột và giá trị cho INSERT
            $columns = array_keys($insertData);
            $values = array_values($insertData);
            
            // Thêm geometry vào INSERT statement TRỰC TIẾP với parameter binding đúng cách
            $columns[] = $geomCol;
            
            // Tạo câu SQL INSERT với geometry - dùng ? cho x và y
            $columnsStr = implode(', ', array_map(function($col) {
                return '"' . $col . '"';
            }, $columns));
            
            // Tạo placeholders: ? cho các giá trị thường, và ST_SetSRID(ST_MakePoint(?, ?), 4326) cho geometry
            $regularPlaceholders = array_fill(0, count($insertData), '?');
            $geometryPlaceholder = "ST_SetSRID(ST_MakePoint(?, ?), 4326)";
            $placeholdersStr = implode(', ', array_merge($regularPlaceholders, [$geometryPlaceholder]));
            
            // Thêm x và y vào cuối mảng values cho geometry
            $values[] = $x;
            $values[] = $y;
            
            // Dùng RETURNING để lấy ogc_fid ngay sau khi insert
            $sql = "INSERT INTO cong ($columnsStr) VALUES ($placeholdersStr) RETURNING ogc_fid";
            
            \Log::info('🔧 Thực hiện INSERT với geometry:', ['x' => $x, 'y' => $y, 'columns_count' => count($columns), 'values_count' => count($values)]);
            
            // Thực thi INSERT và lấy ID
            $result = DB::selectOne($sql, $values);
            
            if (!$result || !isset($result->ogc_fid)) {
                throw new \Exception('Không thể lấy ID sau khi insert');
            }
            
            $id = $result->ogc_fid;
            
            \Log::info('✅ Đã insert cống với ID và geometry:', ['id' => $id, 'x' => $x, 'y' => $y]);
            
            // Kiểm tra geometry đã được tạo chưa - ĐẢM BẢO 100%
            $checkGeom = DB::selectOne("SELECT ST_AsText($geomCol) as geom_text, ogc_fid FROM cong WHERE ogc_fid = ?", [$id]);
            
            if (!$checkGeom || !$checkGeom->geom_text) {
                \Log::error('❌ Geometry KHÔNG được tạo sau khi INSERT:', ['id' => $id]);
                // Thử UPDATE lại
                DB::statement("UPDATE cong SET $geomCol = ST_SetSRID(ST_MakePoint(?, ?), 4326) WHERE ogc_fid = ?", [$x, $y, $id]);
                // Kiểm tra lại
                $checkGeom2 = DB::selectOne("SELECT ST_AsText($geomCol) as geom_text FROM cong WHERE ogc_fid = ?", [$id]);
                if (!$checkGeom2 || !$checkGeom2->geom_text) {
                    throw new \Exception('KHÔNG THỂ TẠO GEOMETRY sau INSERT và UPDATE');
                } else {
                    \Log::info('✅ Geometry đã được tạo thành công sau UPDATE:', ['id' => $id]);
                }
            } else {
                \Log::info('✅ Geometry đã được tạo thành công trong INSERT:', ['id' => $id, 'geom' => $checkGeom->geom_text]);
            }
            
            // Commit transaction
            DB::commit();
            
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('❌ Lỗi khi thêm cống:', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'status' => 'error',
                'message' => 'Không thể thêm cống: ' . $e->getMessage()
            ], 500);
        }

        // Kiểm tra lần cuối trước khi trả response
        $finalCheck = DB::selectOne("SELECT ST_AsText($geomCol) as geom_text, ogc_fid, toa_do_x, toa_do_y FROM cong WHERE ogc_fid = ?", [$id]);
        
        if (!$finalCheck || !$finalCheck->geom_text) {
            \Log::error('❌❌❌ CRITICAL: Geometry KHÔNG tồn tại trước khi trả response!', ['id' => $id]);
            // Thử tạo lại lần cuối (ngoài transaction)
            try {
                DB::statement("UPDATE cong SET $geomCol = ST_SetSRID(ST_MakePoint(?, ?), 4326) WHERE ogc_fid = ?", [$x, $y, $id]);
                \Log::info('🔧 Đã thử tạo geometry lần cuối:', ['id' => $id]);
            } catch (\Exception $e) {
                \Log::error('❌ Không thể tạo geometry lần cuối:', ['error' => $e->getMessage()]);
            }
        }
        
        // Ghi log hoạt động
        try {
            $cong = Cong::find($id);
            if ($cong) {
                ActivityLogger::logCreate($cong);
            }
        } catch (\Exception $e) {
            \Log::warning('Không thể ghi log cho việc tạo cống:', ['error' => $e->getMessage()]);
        }

        return response()->json([
            'status' => 'success',
            'id' => $id,
            'ogc_fid' => $id,
            'toa_do_x' => $x,
            'toa_do_y' => $y,
            'file_url' => $filePath ? asset('storage/' . $filePath) : null,
            'message' => 'Đã thêm cống thành công với geometry',
            'has_geometry' => $finalCheck && $finalCheck->geom_text ? true : false
        ], 201);
    }

    public function update(Request $request, $id)
    {
        // Log ngay từ đầu để kiểm tra request có đến không
        \Log::info('🚀 ========== UPDATE REQUEST RECEIVED ==========');
        \Log::info('🚀 Update cống - ID:', ['id' => $id]);
        \Log::info('🚀 Request method:', ['method' => $request->method()]);
        \Log::info('🚀 Content-Type:', ['content_type' => $request->header('Content-Type')]);
        \Log::info('🚀 Request all (trước khi parse):', $request->all());
        \Log::info('🚀 Request input (trước khi parse):', $request->input());
        
        // Với POST + _method=PUT, Laravel sẽ parse FormData đúng cách
        $allInputs = $request->all();
        
        \Log::info('🚀 Request all (sau khi parse):', $allInputs);
        \Log::info('🚀 Request input (sau khi parse):', $request->input());
        
        $cong = Cong::findOrFail($id);
        
        \Log::info('🚀 Cống hiện tại:', [
            'ogc_fid' => $cong->ogc_fid,
            'ten_cong_dap' => $cong->ten_cong_dap,
            'ly_trinh' => $cong->ly_trinh,
            'dia_diem' => $cong->dia_diem,
        ]);

        $request->validate([
            'toa_do_x' => 'sometimes|numeric', // Thay đổi từ coordinates
            'toa_do_y' => 'sometimes|numeric',
            'id' => 'nullable|string|max:254',
            'ten_cong_dap' => 'nullable|string|max:254',
            'ly_trinh' => 'nullable|string|max:254',
            'stt' => 'nullable|integer',
            'cum_cong_tri' => 'nullable|string|max:254',
            'goi_thau' => 'nullable|string|max:254',
            'loai_cong_tr' => 'nullable|string|max:254',
            'hinh_thuc' => 'nullable|string|max:254',
            'chieu_dai' => 'nullable|integer',
            'duong_kinh' => 'nullable|integer',
            'be_rong' => 'nullable|numeric',
            'chieu_cao' => 'nullable|numeric',
            'so_cua' => 'nullable|integer',
            'cao_trinh_da' => 'nullable|string|max:254',
            'cao_trinh_di' => 'nullable|string|max:254',
            'hinh_thuc_va' => 'nullable|string|max:254',
            'muc_tieu_nhi' => 'nullable|string|max:254',
            'dien_tich_ph' => 'nullable|integer',
            'dia_diem' => 'nullable|string|max:254',
            'nam_su_dung' => 'nullable|integer',
            'cap_cong_tri' => 'nullable|string|max:254',
            'he_thong_con' => 'nullable|string|max:254',
            'don_vi_quan_l' => 'nullable|string|max:254',
            'ghi_chu' => 'nullable|string',
            'nam_cap_nhat' => 'nullable|integer',
            'file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        // Xử lý file upload
        if ($request->hasFile('file')) {
            if ($cong->file_path && Storage::disk('public')->exists($cong->file_path)) {
                Storage::disk('public')->delete($cong->file_path);
            }

            $file = $request->file('file');
            $ext = $file->getClientOriginalExtension();
            $fileName = time() . '_' . Str::random(8) . '.' . $ext;
            $filePath = $file->storeAs('uploads/cong', $fileName, 'public');

            $cong->file_path = $filePath;
            $cong->file_name = $fileName;
            $cong->file_mime = $file->getClientMimeType();
            $cong->file_size = $file->getSize();
        }

        // SỬA LẠI: Cập nhật từng trường một cách rõ ràng để đảm bảo hoạt động
        // Lấy tất cả dữ liệu từ request (bao gồm cả FormData)
        // Với PUT và FormData, cần dùng $request->all() hoặc $request->input()
        $allInputs = $request->all();
        
        // Debug: Log để kiểm tra
        \Log::info('🔧 Update cống ID: ' . $id);
        \Log::info('📥 Dữ liệu nhận được (all):', $allInputs);
        \Log::info('📥 Dữ liệu nhận được (input):', $request->input());
        
        // Danh sách các trường có thể cập nhật
        $fieldsToUpdate = [
            'stt', 'id', 'ten_cong_dap', 'ly_trinh', 'toa_do_x', 'toa_do_y',
            'cum_cong_tri', 'goi_thau', 'loai_cong_tr', 'hinh_thuc',
            'chieu_dai', 'duong_kinh', 'be_rong', 'chieu_cao', 'so_cua',
            'cao_trinh_da', 'cao_trinh_di', 'hinh_thuc_va', 'muc_tieu_nhi',
            'dien_tich_ph', 'dia_diem', 'nam_su_dung', 'cap_cong_tri',
            'he_thong_con', 'don_vi_quan_l', 'ghi_chu', 'nam_cap_nhat'
        ];
        
        // Tạo mảng dữ liệu để cập nhật
        $updateData = [];
        
        // Cập nhật từng trường - xử lý cả giá trị rỗng
        foreach ($fieldsToUpdate as $field) {
            // Kiểm tra xem trường có trong request không (kể cả giá trị rỗng)
            // Dùng array_key_exists để đảm bảo hoạt động với FormData
            if (array_key_exists($field, $allInputs)) {
                $value = $request->input($field);
                // Xử lý giá trị rỗng: nếu là string rỗng, set thành null cho các trường nullable
                if ($value === '' || $value === null) {
                    // Với các trường số, giữ nguyên null; với các trường text, set thành null
                    if (in_array($field, ['stt', 'chieu_dai', 'duong_kinh', 'be_rong', 'chieu_cao', 'so_cua', 'dien_tich_ph', 'nam_su_dung', 'nam_cap_nhat'])) {
                        $value = null;
                    } else {
                        $value = $value === '' ? null : $value;
                    }
                }
                // Thêm vào mảng cập nhật
                $updateData[$field] = $value;
                \Log::info("  ✅ Sẽ cập nhật $field:", ['old' => $cong->getOriginal($field), 'new' => $value]);
            } else {
                \Log::info("  ⏭️ Bỏ qua $field (không có trong request)");
            }
        }
        
        // Sử dụng fill() để cập nhật nhiều trường cùng lúc
        if (!empty($updateData)) {
            $cong->fill($updateData);
            \Log::info('📝 Đã fill dữ liệu vào model:', $updateData);
            \Log::info('📝 Dirty attributes:', $cong->getDirty());
        }

        // Cập nhật geometry nếu có tọa độ mới - SỬA: đảm bảo luôn cập nhật geometry
        $x = $request->input('toa_do_x');
        $y = $request->input('toa_do_y');
        
        if ($x !== null && $y !== null) {
            $x = (float)$x;
            $y = (float)$y;
            // Laravel sẽ tự động cast float sang decimal thông qua $casts trong model
            $cong->setAttribute('toa_do_x', $x);
            $cong->setAttribute('toa_do_y', $y);
            
            // Lấy tên cột geometry
            $gc = DB::selectOne("SELECT f_geometry_column AS geom_col FROM geometry_columns WHERE f_table_name = 'cong' LIMIT 1;");
            $geomCol = $gc->geom_col ?? 'geom';
            
            // Cập nhật geometry bằng raw SQL để đảm bảo
            DB::statement("UPDATE cong SET $geomCol = ST_SetSRID(ST_MakePoint(?, ?), 4326) WHERE ogc_fid = ?", [$x, $y, $cong->ogc_fid]);
        }

        // Debug: Log trước khi save
        \Log::info('📝 Dữ liệu model trước khi save:', [
            'ten_cong_dap' => $cong->ten_cong_dap,
            'id' => $cong->id,
            'ly_trinh' => $cong->ly_trinh,
            'dia_diem' => $cong->dia_diem,
            'ghi_chu' => $cong->ghi_chu,
        ]);
        
        // Kiểm tra dirty attributes sau khi fill()
        $dirty = $cong->getDirty();
        \Log::info('🔍 Dirty attributes (các trường sẽ được cập nhật):', $dirty);
        
        // Nếu không có dirty attributes nhưng có dữ liệu cần cập nhật, dùng update trực tiếp
        if (empty($dirty) && !empty($updateData)) {
            \Log::warning('⚠️ Không có dirty attributes sau fill() - dùng update trực tiếp bằng DB');
            $updated = DB::table('cong')
                ->where('ogc_fid', $cong->ogc_fid)
                ->update($updateData);
            \Log::info('🔧 Update trực tiếp kết quả:', ['updated' => $updated, 'rows' => $updated]);
            if ($updated > 0) {
                $cong->refresh();
                \Log::info('✅ Đã cập nhật trực tiếp và refresh model');
                $saved = true; // Đánh dấu là đã lưu thành công
            } else {
                \Log::error('❌ Update trực tiếp không cập nhật được dòng nào!');
                $saved = false;
            }
        } else {
            // Có dirty attributes, dùng save() bình thường
            if (!empty($dirty)) {
                \Log::info('✅ Có dirty attributes, sẽ dùng save():', $dirty);
            }
            $saved = $cong->save();
            if ($saved && !empty($dirty)) {
                \Log::info('✅ save() đã cập nhật các trường dirty:', $dirty);
            }
        }
        
        // Debug: Log sau khi save
        \Log::info('💾 Đã lưu vào DB:', [
            'saved' => $saved,
            'ten_cong_dap' => $cong->ten_cong_dap,
            'id' => $cong->id,
            'ly_trinh' => $cong->ly_trinh,
            'dia_diem' => $cong->dia_diem,
            'ghi_chu' => $cong->ghi_chu,
        ]);
        
        if (!$saved) {
            // Log lỗi chi tiết
            \Log::error('❌ Không thể lưu vào DB:', [
                'attributes' => $cong->getAttributes(),
                'dirty' => $cong->getDirty(),
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'Không thể cập nhật dữ liệu vào database'
            ], 500);
        }
        
        // Reload lại từ database để đảm bảo dữ liệu mới nhất
        $cong->refresh();
        \Log::info('🔄 Sau khi refresh - Dữ liệu từ DB:', [
            'ten_cong_dap' => $cong->ten_cong_dap,
            'id' => $cong->id,
            'ly_trinh' => $cong->ly_trinh,
            'dia_diem' => $cong->dia_diem,
            'ghi_chu' => $cong->ghi_chu,
        ]);

        // Ghi log hoạt động cập nhật
        try {
            $changes = [];
            if (!empty($dirty)) {
                foreach ($dirty as $key => $value) {
                    $changes[$key] = [
                        'old' => $cong->getOriginal($key),
                        'new' => $value,
                    ];
                }
            }
            ActivityLogger::logUpdate($cong, $changes);
        } catch (\Exception $e) {
            \Log::warning('Không thể ghi log cho việc cập nhật cống:', ['error' => $e->getMessage()]);
        }
        
        // Kiểm tra geometry đã được cập nhật chưa (nếu có tọa độ mới)
        if ($x !== null && $y !== null) {
            $gc = DB::selectOne("SELECT f_geometry_column AS geom_col FROM geometry_columns WHERE f_table_name = 'cong' LIMIT 1;");
            $geomCol = $gc->geom_col ?? 'geom';
            $checkGeom = DB::selectOne("SELECT ST_AsText($geomCol) as geom_text FROM cong WHERE ogc_fid = ?", [$cong->ogc_fid]);
            
            if (!$checkGeom || !$checkGeom->geom_text) {
                // Nếu geometry không có, tạo lại
                DB::statement("UPDATE cong SET $geomCol = ST_SetSRID(ST_MakePoint(?, ?), 4326) WHERE ogc_fid = ?", [$x, $y, $cong->ogc_fid]);
            }
        }

        return response()->json([
            'status' => 'success',
            'id' => $cong->ogc_fid,
            'ogc_fid' => $cong->ogc_fid,
            'file_url' => $cong->file_path ? asset('storage/' . $cong->file_path) : null,
            'message' => 'Đã cập nhật cống thành công',
            'ten_cong_dap' => $cong->ten_cong_dap // Trả về để debug
        ], 200);
    }

    public function destroy($id)
    {
        $cong = Cong::findOrFail($id);

        // Ghi log trước khi xóa (để lưu thông tin)
        try {
            ActivityLogger::logDelete($cong);
        } catch (\Exception $e) {
            \Log::warning('Không thể ghi log cho việc xóa cống:', ['error' => $e->getMessage()]);
        }

        // Xóa file đính kèm nếu có
        if ($cong->hasFile()) {
            Storage::disk('public')->delete($cong->file_path);
        }

        $cong->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Đã xóa cống thành công'
        ], 200);
    }
}
