<?php

use App\Http\Controllers\AdController;
use App\Http\Controllers\Admin\AdminAdController;
use App\Http\Controllers\Admin\AdminCategoryController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminReportController;
use App\Http\Controllers\Admin\AdminSettingController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/ads', [AdController::class, 'index'])->name('ads.index');

Route::middleware(['auth', 'verified'])->group(function (): void {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/ads/my', [AdController::class, 'myAds'])->name('ads.my');
    Route::get('/ads/create', [AdController::class, 'create'])->name('ads.create');
    Route::post('/ads', [AdController::class, 'store'])->name('ads.store');
    Route::get('/ads/{ad}/edit', [AdController::class, 'edit'])->name('ads.edit');
    Route::put('/ads/{ad}', [AdController::class, 'update'])->name('ads.update');
    Route::delete('/ads/{ad}', [AdController::class, 'destroy'])->name('ads.destroy');

    Route::get('/favorites', [FavoriteController::class, 'index'])->name('favorites.index');
    Route::post('/favorites/{ad}', [FavoriteController::class, 'store'])->name('favorites.store');
    Route::delete('/favorites/{ad}', [FavoriteController::class, 'destroy'])->name('favorites.destroy');

    Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
    Route::post('/messages', [MessageController::class, 'store'])->name('messages.store');

    Route::post('/reports', [ReportController::class, 'store'])->name('reports.store');
});

Route::get('/ads/{ad}', [AdController::class, 'show'])->name('ads.show');

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'verified', 'can:access-admin'])
    ->group(function (): void {
        Route::get('/', AdminDashboardController::class)->name('dashboard');

        Route::get('/ads', [AdminAdController::class, 'index'])->name('ads.index');
        Route::patch('/ads/{ad}', [AdminAdController::class, 'update'])->name('ads.update');
        Route::delete('/ads/{ad}', [AdminAdController::class, 'destroy'])->name('ads.destroy');

        Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
        Route::patch('/users/{user}', [AdminUserController::class, 'update'])->name('users.update');

        Route::resource('categories', AdminCategoryController::class)->only(['index', 'store', 'update', 'destroy']);

        Route::get('/reports', [AdminReportController::class, 'index'])->name('reports.index');
        Route::patch('/reports/{report}', [AdminReportController::class, 'update'])->name('reports.update');

        Route::get('/settings', [AdminSettingController::class, 'index'])->name('settings.index');
        Route::patch('/settings', [AdminSettingController::class, 'update'])->name('settings.update');
    });

require __DIR__.'/auth.php';

