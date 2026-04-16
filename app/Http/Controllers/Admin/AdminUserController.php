<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class AdminUserController extends Controller
{
    public function index(Request $request): Response
    {
        $filters = $request->only(['search', 'role', 'is_active']);

        $users = User::query()
            ->when($filters['search'] ?? null, function ($query, $search): void {
                $query->where(function ($inner) use ($search): void {
                    $inner
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->when($filters['role'] ?? null, fn ($query, $role) => $query->where('role', $role))
            ->when(isset($filters['is_active']) && $filters['is_active'] !== '', fn ($query) => $query->where('is_active', (bool) $filters['is_active']))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('Admin/Users/Index', [
            'users' => $users,
            'filters' => $filters,
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:25'],
            'city' => ['nullable', 'string', 'max:120'],
            'role' => ['required', Rule::in(['user', 'admin'])],
            'is_active' => ['required', 'boolean'],
        ]);

        if ($request->user()->id === $user->id && ! (bool) $validated['is_active']) {
            return back()->with('error', 'لا يمكنك تعطيل حسابك من لوحة الإدارة.');
        }

        $user->update([
            'name' => $validated['name'] ?? $user->name,
            'phone' => $validated['phone'] ?? $user->phone,
            'city' => $validated['city'] ?? $user->city,
            'role' => $validated['role'],
            'is_active' => (bool) $validated['is_active'],
        ]);

        return back()->with('success', 'تم تحديث بيانات المستخدم.');
    }
}

