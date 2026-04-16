<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="rtl">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="منصة إعلانات مبوبة عربية حديثة لبيع وشراء كل ما تحتاجه بسهولة وأمان.">

        <title inertia>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800&family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @routes
        @php
            $hasViteAssets = app()->environment('testing')
                || file_exists(public_path('build/manifest.json'))
                || file_exists(public_path('hot'));
        @endphp
        @if ($hasViteAssets)
            @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
        @endif
        @inertiaHead
    </head>
    <body class="font-sans antialiased bg-slate-50 text-slate-900">
        @unless ($hasViteAssets)
            <div style="max-width: 760px; margin: 32px auto; padding: 16px; border: 1px solid #F59E0B; background: #FFFBEB; color: #92400E; border-radius: 12px; font-family: Cairo, sans-serif;">
                <strong>تنبيه:</strong> ملفات الواجهة غير مبنية بعد (<code>public/build/manifest.json</code> غير موجود).<br>
                شغّل <code>npm install</code> ثم <code>npm run build</code> أو <code>npm run dev</code>.
            </div>
        @endunless
        @inertia
    </body>
</html>
