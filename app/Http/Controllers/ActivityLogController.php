<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    /**
     * Hiển thị danh sách logs (chỉ Admin)
     */
    public function index(Request $request)
    {
        // Chỉ Admin mới được xem logs
        if (!auth()->user() || !auth()->user()->isAdmin()) {
            abort(403, 'Chỉ Admin mới được xem logs');
        }

        $query = ActivityLog::with('user')->orderBy('created_at', 'desc');

        // Tìm kiếm theo mô tả
        if ($request->has('search') && !empty($request->search)) {
            $search = trim($request->search);
            $driver = \Illuminate\Support\Facades\DB::connection()->getDriverName();
            
            if ($driver === 'pgsql') {
                // PostgreSQL: dùng ILIKE (case-insensitive)
                $query->where('description', 'ILIKE', "%{$search}%");
            } else {
                // MySQL/MariaDB: dùng LIKE với LOWER
                $query->whereRaw('LOWER(description) LIKE ?', ['%' . strtolower($search) . '%']);
            }
        }

        // Lọc theo user
        if ($request->has('user_id') && !empty($request->user_id)) {
            $query->where('user_id', $request->user_id);
        }

        // Lọc theo action
        if ($request->has('action') && !empty($request->action)) {
            $query->where('action', $request->action);
        }

        // Lọc theo model type
        if ($request->has('model_type') && !empty($request->model_type)) {
            $query->where('model_type', $request->model_type);
        }

        // Lọc theo khoảng thời gian
        if ($request->has('date_from') && !empty($request->date_from)) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->has('date_to') && !empty($request->date_to)) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Phân trang
        $perPage = $request->get('per_page', 50);
        $logs = $query->paginate($perPage)->withQueryString();

        // Lấy danh sách users và actions để hiển thị trong filter
        $users = User::orderBy('name')->get();
        $actions = ActivityLog::distinct()->pluck('action')->sort();
        $modelTypes = ActivityLog::distinct()->pluck('model_type')->filter()->sort();

        return view('admin.logs', [
            'logs' => $logs,
            'users' => $users,
            'actions' => $actions,
            'modelTypes' => $modelTypes,
            'search' => $request->get('search', ''),
            'selectedUserId' => $request->get('user_id', ''),
            'selectedAction' => $request->get('action', ''),
            'selectedModelType' => $request->get('model_type', ''),
            'dateFrom' => $request->get('date_from', ''),
            'dateTo' => $request->get('date_to', ''),
        ]);
    }

    /**
     * Xem chi tiết một log
     */
    public function show($id)
    {
        // Chỉ Admin mới được xem logs
        if (!auth()->user() || !auth()->user()->isAdmin()) {
            abort(403, 'Chỉ Admin mới được xem logs');
        }

        $log = ActivityLog::with('user')->findOrFail($id);

        return view('admin.log-detail', [
            'log' => $log,
        ]);
    }
}
