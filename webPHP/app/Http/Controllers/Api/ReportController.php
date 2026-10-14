<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\DashboardController;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    /** Mirror de DashboardController::reports (web), export CSV exclu (reste web-only). */
    public function index(Request $request)
    {
        $period = $request->get('period', 'month');

        return response()->json(
            app(DashboardController::class)->reportsStats($period)
        );
    }
}
