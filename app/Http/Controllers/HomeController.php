<?php

namespace App\Http\Controllers;

use App\Http\Resources\AdResource;
use App\Models\Ad;
use App\Models\Category;
use App\Models\Setting;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function index(): Response
    {
        $featuredLimit = (int) Setting::getValue('featured_ads_limit', 8);

        $categories = Category::query()
            ->active()
            ->whereNull('parent_id')
            ->whereHas('ads', fn ($adsQuery) => $adsQuery->approved())
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['id', 'name', 'slug', 'icon'])
            ->unique(fn (Category $category): string => mb_strtolower((string) $category->name))
            ->take(8)
            ->values();

        $featuredAds = Ad::query()
            ->approved()
            ->featured()
            ->with(['category:id,name,slug', 'user:id,name,city,phone', 'primaryImage'])
            ->latest('published_at')
            ->limit(max($featuredLimit, 1))
            ->get();

        $latestAds = Ad::query()
            ->approved()
            ->with(['category:id,name,slug', 'user:id,name,city,phone', 'primaryImage'])
            ->latest('published_at')
            ->limit(12)
            ->get();

        $cities = Ad::query()
            ->approved()
            ->select('city')
            ->distinct()
            ->orderBy('city')
            ->pluck('city');

        return Inertia::render('Home', [
            'categories' => $categories,
            'featuredAds' => AdResource::collection($featuredAds)->resolve(),
            'latestAds' => AdResource::collection($latestAds)->resolve(),
            'cities' => $cities,
        ]);
    }
}
