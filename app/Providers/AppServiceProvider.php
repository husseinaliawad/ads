<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);

        app()->setLocale(config('app.locale'));
        Date::setLocale(config('app.locale'));

        Gate::define('access-admin', fn (User $user): bool => $user->isAdmin());
    }
}
