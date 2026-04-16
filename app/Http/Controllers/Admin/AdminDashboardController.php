<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ad;
use App\Models\Report;
use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;

class AdminDashboardController extends Controller
{
    public function __invoke(): Response
    {
        $latestPendingAds = Ad::query()
            ->with(['user:id,name', 'category:id,name'])
            ->where('status', 'pending')
            ->latest()
            ->limit(8)
            ->get();

        $latestReports = Report::query()
            ->with(['ad:id,title,slug', 'user:id,name'])
            ->latest()
            ->limit(8)
            ->get();

        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'users' => User::query()->count(),
                'active_users' => User::query()->where('is_active', true)->count(),
                'ads' => Ad::query()->count(),
                'approved_ads' => Ad::query()->where('status', 'approved')->count(),
                'pending_ads' => Ad::query()->where('status', 'pending')->count(),
                'reports' => Report::query()->count(),
                'pending_reports' => Report::query()->where('status', 'pending')->count(),
            ],
            'latestPendingAds' => $latestPendingAds,
            'latestReports' => $latestReports,
        ]);
    }
}

