<?php

namespace App\Http\Controllers;

use App\Http\Resources\AdResource;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(): Response
    {
        $user = auth()->user();

        $latestMyAds = $user->ads()
            ->with(['category:id,name,slug', 'primaryImage'])
            ->latest()
            ->limit(6)
            ->get();

        return Inertia::render('Dashboard', [
            'stats' => [
                'ads_count' => $user->ads()->count(),
                'approved_ads_count' => $user->ads()->where('status', 'approved')->count(),
                'favorites_count' => $user->favorites()->count(),
                'unread_messages_count' => $user->receivedMessages()->whereNull('read_at')->count(),
            ],
            'latestMyAds' => AdResource::collection($latestMyAds)->resolve(),
        ]);
    }
}
