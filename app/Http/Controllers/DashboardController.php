<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    protected $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    /**
     * Get aggregate dashboard data for the authenticated user.
     */
    public function index(Request $request)
    {
        $month = $request->integer('month', (int)now()->month);
        $year = $request->integer('year', (int)now()->year);

        $data = $this->dashboardService->getDashboardData(
            $request->user()->id,
            $month,
            $year
        );

        return response()->json($data);
    }
}
