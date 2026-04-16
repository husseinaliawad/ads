<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReportRequest;
use App\Models\Ad;
use App\Models\Report;
use Illuminate\Http\RedirectResponse;

class ReportController extends Controller
{
    public function store(StoreReportRequest $request): RedirectResponse
    {
        $this->authorize('create', Report::class);

        $validated = $request->validated();
        $ad = Ad::query()->findOrFail((int) $validated['ad_id']);

        if ($ad->user_id === $request->user()->id) {
            return back()->with('error', 'لا يمكنك تقديم بلاغ على إعلانك.');
        }

        Report::query()->create([
            'ad_id' => $ad->id,
            'user_id' => $request->user()->id,
            'reason' => $validated['reason'],
            'details' => $validated['details'] ?? null,
            'status' => 'pending',
        ]);

        return back()->with('success', 'تم إرسال البلاغ إلى الإدارة بنجاح.');
    }
}

