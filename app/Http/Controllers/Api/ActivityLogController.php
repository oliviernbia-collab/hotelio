<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $query = ActivityLog::with('user:id,name')->latest();

        if ($request->filled('action')) $query->where('action', $request->action);
        if ($request->filled('user_id')) $query->where('user_id', $request->user_id);

        return response()->json($query->paginate(30));
    }

    public function clear()
    {
        ActivityLog::truncate();

        return response()->json(['message' => 'Journal vidé.']);
    }
}
