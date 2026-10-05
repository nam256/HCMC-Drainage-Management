<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MapController extends Controller
{
    /**
     * Hiển thị trang bản đồ
     * User và Admin đều có thể xem
     */
    public function index()
    {
        $user = auth()->user();
        $canEdit = $user && $user->isAdmin(); // Chỉ admin mới được chỉnh sửa
        
        return view('map', [
            'canEdit' => $canEdit,
            'userRole' => $user ? $user->role : 'guest',
        ]);
    }

    /**
     * Trang quản lý cống (chỉ Admin)
     */
    public function manage(Request $request)
    {
        $user = auth()->user();
        
        // Query builder
        $query = \Illuminate\Support\Facades\DB::table('cong');
        
        // Tìm kiếm
        if ($request->has('search') && !empty($request->search)) {
            $search = trim($request->search);
            $driver = \Illuminate\Support\Facades\DB::connection()->getDriverName();
            
            // Kiểm tra xem search có phải là số không (để tìm theo ogc_fid)
            $isNumeric = is_numeric($search);
            
            if ($driver === 'pgsql') {
                // PostgreSQL: dùng ILIKE (case-insensitive)
                $query->where(function($q) use ($search, $isNumeric) {
                    // Tìm theo text fields
                    $q->where('id', 'ILIKE', "%{$search}%")
                      ->orWhere('ten_cong_dap', 'ILIKE', "%{$search}%")
                      ->orWhere('dia_diem', 'ILIKE', "%{$search}%")
                      ->orWhere('ly_trinh', 'ILIKE', "%{$search}%")
                      ->orWhere('ghi_chu', 'ILIKE', "%{$search}%");
                    
                    // Nếu là số, tìm theo ogc_fid
                    if ($isNumeric) {
                        $q->orWhere('ogc_fid', '=', (int)$search);
                    }
                });
            } else {
                // MySQL/MariaDB: dùng LIKE với LOWER
                $query->where(function($q) use ($search, $isNumeric) {
                    // Tìm theo text fields
                    $q->whereRaw('LOWER(id) LIKE ?', ['%' . strtolower($search) . '%'])
                      ->orWhereRaw('LOWER(ten_cong_dap) LIKE ?', ['%' . strtolower($search) . '%'])
                      ->orWhereRaw('LOWER(dia_diem) LIKE ?', ['%' . strtolower($search) . '%'])
                      ->orWhereRaw('LOWER(ly_trinh) LIKE ?', ['%' . strtolower($search) . '%'])
                      ->orWhereRaw('LOWER(ghi_chu) LIKE ?', ['%' . strtolower($search) . '%']);
                    
                    // Nếu là số, tìm theo ogc_fid
                    if ($isNumeric) {
                        $q->orWhere('ogc_fid', '=', (int)$search);
                    }
                });
            }
        }
        
        // Sắp xếp
        $sortBy = $request->get('sort_by', 'ogc_fid');
        $sortDir = $request->get('sort_dir', 'asc');
        $allowedSort = ['ogc_fid', 'id', 'ten_cong_dap', 'dia_diem', 'toa_do_x', 'toa_do_y', 'created_at'];
        if (in_array($sortBy, $allowedSort)) {
            $query->orderBy($sortBy, $sortDir);
        } else {
            $query->orderBy('ogc_fid', 'desc'); // Mặc định sắp xếp theo ogc_fid giảm dần (mới nhất trước)
        }
        
        // Phân trang
        $perPage = $request->get('per_page', 20);
        $congList = $query->paginate($perPage)->withQueryString();
        
        // Thống kê
        $stats = [
            'total' => \Illuminate\Support\Facades\DB::table('cong')->count(),
            'with_file' => \Illuminate\Support\Facades\DB::table('cong')->whereNotNull('file_path')->count(),
            'total_users' => \App\Models\User::count(),
        ];
        
        return view('cong.manage', [
            'congList' => $congList,
            'canEdit' => true,
            'user' => $user,
            'stats' => $stats,
            'search' => $request->get('search', ''),
            'sortBy' => $sortBy,
            'sortDir' => $sortDir,
        ]);
    }
}
