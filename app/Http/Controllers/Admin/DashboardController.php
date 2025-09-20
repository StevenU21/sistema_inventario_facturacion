<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;

class DashboardController extends Controller
{
    protected DashboardService $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    public function index(\Illuminate\Http\Request $request)
    {
        $data = $this->dashboardService->getDashboardData();
        return view('dashboard', $data);
    }
}
