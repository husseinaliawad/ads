<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class AdminReportController extends Controller
{
    public function index(Request $request): Response
    {
        $filters = $request->only(['status']);

        $reports = Report::query()
            ->with(['ad:id,title,slug', 'user:id,name', 'reviewer:id,name'])
            ->when($filters['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('Admin/Reports/Index', [
            'reports' => $reports,
            'filters' => $filters,
            'statuses' => [
                ['value' => 'pending', 'label' => 'جديد'],
                ['value' => 'reviewed', 'label' => 'قيد المتابعة'],
                ['value' => 'resolved', 'label' => 'تم الحل'],
                ['value' => 'dismissed', 'label' => 'مرفوض'],
            ],
        ]);
    }

    public function update(Request $request, Report $report): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['pending', 'reviewed', 'resolved', 'dismissed'])],
        ]);

        $report->update([
            'status' => $validated['status'],
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
        ]);

        return back()->with('success', 'تم تحديث حالة البلاغ.');
    }
}

