<?php

namespace App\Http\Controllers;

use App\Http\Resources\AdResource;
use App\Models\Ad;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class FavoriteController extends Controller
{
    public function index(Request $request): Response
    {
        $favorites = $request->user()
            ->favorites()
            ->with(['category:id,name,slug', 'user:id,name,city', 'primaryImage'])
            ->approved()
            ->orderByPivot('created_at', 'desc')
            ->paginate(12)
            ->withQueryString()
            ->through(fn (Ad $ad): array => (new AdResource($ad))->toArray($request));

        return Inertia::render('Favorites/Index', [
            'ads' => $favorites,
        ]);
    }

    public function store(Request $request, Ad $ad): RedirectResponse
    {
        if ($ad->status !== 'approved') {
            abort(404);
        }

        if ($ad->user_id === $request->user()->id) {
            return back()->with('error', 'لا يمكنك إضافة إعلانك إلى المفضلة.');
        }

        $request->user()->favorites()->syncWithoutDetaching([$ad->id]);

        return back()->with('success', 'تمت إضافة الإعلان إلى المفضلة.');
    }

    public function destroy(Request $request, Ad $ad): RedirectResponse
    {
        $request->user()->favorites()->detach($ad->id);

        return back()->with('success', 'تمت إزالة الإعلان من المفضلة.');
    }
}
