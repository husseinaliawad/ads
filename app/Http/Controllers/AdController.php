<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAdRequest;
use App\Http\Requests\UpdateAdRequest;
use App\Http\Resources\AdResource;
use App\Models\Ad;
use App\Models\Category;
use App\Models\Image;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class AdController extends Controller
{
    public function index(Request $request): Response
    {
        $filters = collect($request->only([
            'search',
            'category',
            'city',
            'condition',
            'min_price',
            'max_price',
            'sort',
            'view',
        ]))
            ->map(fn ($value) => is_string($value) ? trim($value) : $value)
            ->all();

        $query = Ad::query()
            ->approved()
            ->with(['category:id,name,slug', 'user:id,name,city', 'primaryImage']);

        if (! empty($filters['search'])) {
            $search = trim((string) $filters['search']);
            $query->where(function ($inner) use ($search): void {
                $inner
                    ->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if (! empty($filters['category'])) {
            $categoryInput = $filters['category'];
            $query->whereHas('category', function ($inner) use ($categoryInput): void {
                if (is_numeric($categoryInput)) {
                    $inner->where('id', (int) $categoryInput);
                } else {
                    $selectedCategory = Category::query()
                        ->select(['id', 'name'])
                        ->where('slug', (string) $categoryInput)
                        ->first();

                    if ($selectedCategory) {
                        $inner->whereRaw('lower(name) = ?', [mb_strtolower((string) $selectedCategory->name)]);
                    } else {
                        $inner->where('slug', (string) $categoryInput);
                    }
                }
            });
        }

        if (! empty($filters['city'])) {
            $query->whereRaw('trim(city) = ?', [(string) $filters['city']]);
        }

        if (! empty($filters['condition']) && in_array($filters['condition'], ['new', 'used'], true)) {
            $query->where('condition', $filters['condition']);
        }

        if (! empty($filters['min_price'])) {
            $query->where('price', '>=', (float) $filters['min_price']);
        }

        if (! empty($filters['max_price'])) {
            $query->where('price', '<=', (float) $filters['max_price']);
        }

        $sort = $filters['sort'] ?? 'newest';
        match ($sort) {
            'price_low' => $query->orderBy('price'),
            'price_high' => $query->orderByDesc('price'),
            'most_viewed' => $query->orderByDesc('views_count'),
            'oldest' => $query->orderBy('published_at'),
            default => $query->orderByDesc('published_at'),
        };

        $ads = $query
            ->paginate(12)
            ->withQueryString()
            ->through(fn (Ad $ad): array => (new AdResource($ad))->toArray($request));

        $categories = Category::query()
            ->active()
            ->whereHas('ads', fn ($adsQuery) => $adsQuery->approved())
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['id', 'name', 'slug'])
            ->unique(fn (Category $category): string => mb_strtolower((string) $category->name))
            ->values();

        $cities = Ad::query()
            ->approved()
            ->select('city')
            ->distinct()
            ->orderBy('city')
            ->pluck('city');

        $favoriteAdIds = $request->user()
            ? $request->user()->favorites()->pluck('ads.id')
            : [];

        return Inertia::render('Ads/Index', [
            'ads' => $ads,
            'filters' => $filters,
            'categories' => $categories,
            'cities' => $cities,
            'favoriteAdIds' => $favoriteAdIds,
        ]);
    }

    public function myAds(Request $request): Response
    {
        $ads = $request->user()
            ->ads()
            ->with(['category:id,name,slug', 'primaryImage'])
            ->latest()
            ->paginate(12)
            ->withQueryString()
            ->through(fn (Ad $ad): array => (new AdResource($ad))->toArray($request));

        return Inertia::render('Ads/MyAds', [
            'ads' => $ads,
        ]);
    }

    public function create(): Response
    {
        $categories = Category::query()
            ->active()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['id', 'name'])
            ->unique(fn (Category $category): string => mb_strtolower((string) $category->name))
            ->values();

        return Inertia::render('Ads/Create', [
            'categories' => $categories,
            'conditions' => [
                ['value' => 'new', 'label' => 'جديد'],
                ['value' => 'used', 'label' => 'مستعمل'],
            ],
        ]);
    }

    public function store(StoreAdRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $ad = Ad::query()->create([
            'user_id' => $request->user()->id,
            'category_id' => $validated['category_id'],
            'title' => $validated['title'],
            'slug' => $this->generateUniqueSlug($validated['title']),
            'description' => $validated['description'],
            'price' => $validated['price'] ?? null,
            'currency' => 'SAR',
            'city' => $validated['city'],
            'area' => $validated['area'] ?? null,
            'condition' => $validated['condition'],
            'status' => 'pending',
            'contact_phone' => $validated['contact_phone'] ?? $request->user()->phone,
            'whatsapp_number' => $validated['whatsapp_number'] ?? null,
        ]);

        foreach ($request->file('images', []) as $index => $file) {
            $path = $file->store('ads', 'public');

            $ad->images()->create([
                'path' => $path,
                'sort_order' => $index,
                'is_primary' => $index === 0,
            ]);
        }

        return to_route('ads.show', $ad)->with('success', 'تم إرسال إعلانك للمراجعة بنجاح.');
    }

    public function show(Request $request, Ad $ad): Response
    {
        if (! $ad->canBeViewedBy($request->user())) {
            abort(404);
        }

        $ad->load([
            'category:id,name,slug',
            'user:id,name,city,phone,created_at',
            'images',
            'primaryImage',
        ]);

        $ad->increment('views_count');

        $similarAds = Ad::query()
            ->approved()
            ->where('category_id', $ad->category_id)
            ->whereKeyNot($ad->id)
            ->with(['category:id,name,slug', 'user:id,name,city', 'primaryImage'])
            ->latest('published_at')
            ->limit(6)
            ->get();

        $isFavorite = $request->user()
            ? $request->user()->favorites()->where('ads.id', $ad->id)->exists()
            : false;

        return Inertia::render('Ads/Show', [
            'ad' => (new AdResource($ad))->resolve(),
            'similarAds' => AdResource::collection($similarAds)->resolve(),
            'isFavorite' => $isFavorite,
            'canMessageSeller' => $request->user() && $request->user()->id !== $ad->user_id,
        ]);
    }

    public function edit(Ad $ad): Response
    {
        $this->authorize('update', $ad);

        $ad->load(['images', 'category:id,name']);

        $categories = Category::query()
            ->active()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['id', 'name'])
            ->unique(fn (Category $category): string => mb_strtolower((string) $category->name))
            ->values();

        return Inertia::render('Ads/Edit', [
            'ad' => [
                ...$ad->toArray(),
                'images' => $ad->images->map(fn (Image $image): array => [
                    'id' => $image->id,
                    'path' => $image->path,
                    'url' => $image->url,
                    'is_primary' => $image->is_primary,
                ]),
            ],
            'categories' => $categories,
            'conditions' => [
                ['value' => 'new', 'label' => 'جديد'],
                ['value' => 'used', 'label' => 'مستعمل'],
            ],
        ]);
    }

    public function update(UpdateAdRequest $request, Ad $ad): RedirectResponse
    {
        $this->authorize('update', $ad);

        $validated = $request->validated();

        $ad->update([
            'category_id' => $validated['category_id'],
            'title' => $validated['title'],
            'slug' => $this->generateUniqueSlug($validated['title'], $ad),
            'description' => $validated['description'],
            'price' => $validated['price'] ?? null,
            'city' => $validated['city'],
            'area' => $validated['area'] ?? null,
            'condition' => $validated['condition'],
            'contact_phone' => $validated['contact_phone'] ?? null,
            'whatsapp_number' => $validated['whatsapp_number'] ?? null,
            'status' => $validated['status'] ?? 'pending',
            'approved_at' => null,
            'approved_by' => null,
            'published_at' => null,
        ]);

        $removedImageIds = collect($validated['removed_image_ids'] ?? [])->map(fn ($id) => (int) $id);
        if ($removedImageIds->isNotEmpty()) {
            $imagesToDelete = $ad->images()->whereIn('id', $removedImageIds)->get();

            foreach ($imagesToDelete as $image) {
                Storage::disk('public')->delete($image->path);
                $image->delete();
            }
        }

        $nextSortOrder = (int) $ad->images()->max('sort_order') + 1;
        foreach ($request->file('images', []) as $index => $file) {
            $path = $file->store('ads', 'public');

            $ad->images()->create([
                'path' => $path,
                'sort_order' => $nextSortOrder + $index,
                'is_primary' => false,
            ]);
        }

        if (! $ad->images()->where('is_primary', true)->exists()) {
            $firstImage = $ad->images()->orderBy('sort_order')->first();
            $firstImage?->update(['is_primary' => true]);
        }

        return to_route('ads.show', $ad)->with('success', 'تم تحديث الإعلان وإرساله للمراجعة.');
    }

    public function destroy(Ad $ad): RedirectResponse
    {
        $this->authorize('delete', $ad);

        foreach ($ad->images as $image) {
            Storage::disk('public')->delete($image->path);
        }

        $ad->delete();

        return to_route('ads.my')->with('success', 'تم حذف الإعلان بنجاح.');
    }

    private function generateUniqueSlug(string $title, ?Ad $exceptAd = null): string
    {
        $baseSlug = Str::slug($title);
        $slug = $baseSlug !== '' ? $baseSlug : Str::random(8);
        $counter = 1;

        while (Ad::query()
            ->where('slug', $slug)
            ->when($exceptAd, fn ($query) => $query->whereKeyNot($exceptAd->id))
            ->exists()) {
            $slug = ($baseSlug !== '' ? $baseSlug : 'ad')."-{$counter}";
            $counter++;
        }

        return $slug;
    }
}
