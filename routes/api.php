<?php

use App\Http\Controllers\Api\AdApiController;
use App\Http\Controllers\Api\MessageApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/ads', [AdApiController::class, 'index'])->name('api.ads.index');
Route::get('/ads/{ad}', [AdApiController::class, 'show'])->name('api.ads.show');

Route::middleware('auth:sanctum')->group(function (): void {
    Route::get('/me', fn (Request $request) => $request->user())->name('api.me');
    Route::get('/messages', [MessageApiController::class, 'index'])->name('api.messages.index');
    Route::post('/messages', [MessageApiController::class, 'store'])->name('api.messages.store');
});

