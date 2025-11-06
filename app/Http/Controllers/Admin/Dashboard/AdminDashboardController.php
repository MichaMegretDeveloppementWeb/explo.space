<?php

namespace App\Http\Controllers\Admin\Dashboard;

use App\Http\Controllers\Controller;
use App\Services\Admin\Dashboard\DashboardStatsService;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    /**
     * Service de statistiques du dashboard
     */
    protected DashboardStatsService $dashboardStatsService;

    /**
     * Injection du service
     */
    public function __construct(DashboardStatsService $dashboardStatsService)
    {
        $this->dashboardStatsService = $dashboardStatsService;
    }

    /**
     * Afficher le tableau de bord admin
     */
    public function index(): View
    {
        $stats = $this->dashboardStatsService->getDashboardStats();
        $recentPlaceRequests = $this->dashboardStatsService->getRecentPlaceRequests(5);
        $recentEditRequests = $this->dashboardStatsService->getRecentEditRequests(5);

        return view('admin.dashboard.index', [
            'stats' => $stats,
            'recentPlaceRequests' => $recentPlaceRequests,
            'recentEditRequests' => $recentEditRequests,
        ]);
    }
}
