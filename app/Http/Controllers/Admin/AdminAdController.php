<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ad;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class AdminAdController extends Controller
{
    public function index(Request $request): Response
    {
        $filters = $request->only(['search', 'status', 'category']);

        $ads = Ad::query()
            ->with(['user:id,name', 'category:id,name', 'primaryImage'])
            ->when($filters['search'] ?? null, function ($query, $search): void {
                $query->where(function ($inner) use ($search): void {
                    $inner
                        ->where('title', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->when($filters['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
            ->when($filters['category'] ?? null, fn ($query, $categoryId) => $query->where('category_id', $categoryId))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $categories = Category::query()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['id', 'name']);

        return Inertia::render('Admin/Ads/Index', [
            'ads' => $ads,
            'filters' => $filters,
            'categories' => $categories,
            'statuses' => [
                ['value' => 'pending', 'label' => 'قيد المراجعة'],
                ['value' => 'approved', 'label' => 'مقبول'],
                ['value' => 'rejected', 'label' => 'مرفوض'],
                ['value' => 'sold', 'label' => 'مباع'],
                ['value' => 'draft', 'label' => 'مسودة'],
            ],
        ]);
    }

    public function update(Request $request, Ad $ad): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['pending', 'approved', 'rejected', 'sold', 'draft'])],
            'is_featured' => ['nullable', 'boolean'],
        ]);

        $payload = [
            'status' => $validated['status'],
            'is_featured' => (bool) ($validated['is_featured'] ?? false),
        ];

        if ($validated['status'] === 'approved') {
            $payload['approved_by'] = $request->user()->id;
            $payload['approved_at'] = now();
            $payload['published_at'] = $ad->published_at ?? now();
        } elseif ($validated['status'] === 'rejected') {
            $payload['approved_by'] = $request->user()->id;
            $payload['approved_at'] = now();
            $payload['published_at'] = null;
        }

        if ((bool) ($validated['is_featured'] ?? false)) {
            $payload['featured_until'] = now()->addDays(7);
        } else {
            $payload['featured_until'] = null;
        }

        $ad->update($payload);

        return back()->with('success', 'تم تحديث حالة الإعلان.');
    }

    public function destroy(Ad $ad): RedirectResponse
    {
        foreach ($ad->images as $image) {
            Storage::disk('public')->delete($image->path);
        }

        $ad->delete();

        return back()->with('success', 'تم حذف الإعلان.');
    }
}

