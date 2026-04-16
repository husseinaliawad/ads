<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Illuminate\Http\Request;
use Inertia\Middleware;
use Tightenco\Ziggy\Ziggy;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $siteName = Setting::getValue('site_name', config('app.name'));
        $primaryColor = Setting::getValue('primary_color', '#2563EB');
        $secondaryColor = Setting::getValue('secondary_color', '#10B981');

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user(),
            ],
            'app' => [
                'name' => $siteName,
                'locale' => app()->getLocale(),
                'rtl' => true,
                'colors' => [
                    'primary' => $primaryColor,
                    'secondary' => $secondaryColor,
                ],
            ],
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
            ],
            'ziggy' => fn () => [
                ...(new Ziggy)->toArray(),
                'location' => $request->url(),
            ],
        ];
    }
}
