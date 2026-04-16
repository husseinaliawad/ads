<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class AdminCategoryController extends Controller
{
    public function index(): Response
    {
        $categories = Category::query()
            ->with('parent:id,name')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(20);

        $parentOptions = Category::query()
            ->whereNull('parent_id')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['id', 'name']);

        return Inertia::render('Admin/Categories/Index', [
            'categories' => $categories,
            'parentOptions' => $parentOptions,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'icon' => ['nullable', 'string', 'max:80'],
            'description' => ['nullable', 'string', 'max:500'],
            'parent_id' => ['nullable', 'exists:categories,id'],
            'is_active' => ['required', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        Category::query()->create([
            'name' => $validated['name'],
            'slug' => $this->uniqueSlug($validated['name']),
            'icon' => $validated['icon'] ?? null,
            'description' => $validated['description'] ?? null,
            'parent_id' => $validated['parent_id'] ?? null,
            'is_active' => (bool) $validated['is_active'],
            'sort_order' => $validated['sort_order'] ?? 0,
        ]);

        return back()->with('success', 'تم إنشاء التصنيف.');
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'icon' => ['nullable', 'string', 'max:80'],
            'description' => ['nullable', 'string', 'max:500'],
            'parent_id' => ['nullable', 'exists:categories,id', Rule::notIn([$category->id])],
            'is_active' => ['required', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $category->update([
            'name' => $validated['name'],
            'slug' => $this->uniqueSlug($validated['name'], $category),
            'icon' => $validated['icon'] ?? null,
            'description' => $validated['description'] ?? null,
            'parent_id' => $validated['parent_id'] ?? null,
            'is_active' => (bool) $validated['is_active'],
            'sort_order' => $validated['sort_order'] ?? 0,
        ]);

        return back()->with('success', 'تم تحديث التصنيف.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        if ($category->ads()->exists()) {
            return back()->with('error', 'لا يمكن حذف التصنيف لأنه مرتبط بإعلانات.');
        }

        $category->delete();

        return back()->with('success', 'تم حذف التصنيف.');
    }

    private function uniqueSlug(string $name, ?Category $except = null): string
    {
        $base = Str::slug($name);
        $slug = $base !== '' ? $base : 'category';
        $counter = 1;

        while (Category::query()
            ->where('slug', $slug)
            ->when($except, fn ($query) => $query->whereKeyNot($except->id))
            ->exists()) {
            $slug = ($base !== '' ? $base : 'category').'-'.$counter;
            $counter++;
        }

        return $slug;
    }
}
