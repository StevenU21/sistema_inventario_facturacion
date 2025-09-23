<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\DashboardService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    use AuthorizesRequests;
    protected DashboardService $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    public function index(Request $request)
    {
        $this->authorize('read dashboard', User::class);
        $data = $this->dashboardService->getDashboardData();
        return view('dashboard', $data);
    }
}
