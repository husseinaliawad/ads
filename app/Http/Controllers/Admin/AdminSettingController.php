<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AdminSettingController extends Controller
{
    public function index(): Response
    {
        $settings = Setting::query()
            ->whereIn('key', [
                'site_name',
                'site_logo',
                'primary_color',
                'secondary_color',
                'featured_ads_limit',
            ])
            ->pluck('value', 'key');

        return Inertia::render('Admin/Settings/Index', [
            'settings' => [
                'site_name' => $settings['site_name'] ?? 'سوق الإعلانات',
                'site_logo' => $settings['site_logo'] ?? '',
                'primary_color' => $settings['primary_color'] ?? '#2563EB',
                'secondary_color' => $settings['secondary_color'] ?? '#10B981',
                'featured_ads_limit' => (int) ($settings['featured_ads_limit'] ?? 8),
            ],
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'site_name' => ['required', 'string', 'max:120'],
            'site_logo' => ['nullable', 'string', 'max:255'],
            'primary_color' => ['required', 'string', 'max:20'],
            'secondary_color' => ['required', 'string', 'max:20'],
            'featured_ads_limit' => ['required', 'integer', 'min:1', 'max:30'],
        ]);

        foreach ($validated as $key => $value) {
            Setting::setValue($key, $value);
        }

        return back()->with('success', 'تم حفظ الإعدادات بنجاح.');
    }
}

