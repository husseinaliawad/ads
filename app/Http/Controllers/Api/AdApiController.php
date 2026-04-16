<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AdResource;
use App\Models\Ad;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AdApiController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Ad::query()
            ->approved()
            ->with(['category:id,name,slug', 'user:id,name,city,phone', 'primaryImage']);

        if ($request->filled('search')) {
            $search = trim((string) $request->string('search'));
            $query->where(function ($inner) use ($search): void {
                $inner
                    ->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', (int) $request->integer('category_id'));
        }

        if ($request->filled('city')) {
            $query->where('city', (string) $request->string('city'));
        }

        if ($request->filled('condition')) {
            $query->where('condition', (string) $request->string('condition'));
        }

        $ads = $query->latest('published_at')->paginate(15)->withQueryString();

        return AdResource::collection($ads);
    }

    public function show(Ad $ad): AdResource
    {
        abort_unless($ad->status === 'approved', 404);

        $ad->load(['category:id,name,slug', 'user:id,name,city,phone', 'images', 'primaryImage']);

        return new AdResource($ad);
    }
}

