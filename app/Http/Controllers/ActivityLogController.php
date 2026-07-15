<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $query = ActivityLog::query()->latest();

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }
        if ($request->filled('user')) {
            $query->where('user_name', 'like', '%' . $request->user . '%');
        }
        if ($request->filled('model')) {
            $query->where('model_type', $request->model);
        }

        $logs  = $query->paginate(50)->withQueryString();
        $total = ActivityLog::count();
        $today = ActivityLog::whereDate('created_at', today())->count();

        $actions = ActivityLog::select('action')
            ->distinct()->orderBy('action')->pluck('action');

        $models = ActivityLog::select('model_type')
            ->whereNotNull('model_type')
            ->distinct()->orderBy('model_type')->pluck('model_type');

        return view('admin.journal', compact('logs', 'total', 'today', 'actions', 'models'));
    }

    public function clear(Request $request)
    {
        if ($request->filled('before')) {
            ActivityLog::whereDate('created_at', '<', $request->before)->delete();
        } else {
            ActivityLog::truncate();
        }

        return redirect()->route('admin.journal')->with('success', 'Journal vidé avec succès.');
    }
}
