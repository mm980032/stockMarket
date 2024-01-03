<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\Schedule\Services\ScheduleService;

/*
    |--------------------------------------------------------------------------
    | API Routes
    |--------------------------------------------------------------------------
    |
    | Here is where you can register API routes for your application. These
    | routes are loaded by the RouteServiceProvider within a group which
    | is assigned the "api" middleware group. Enjoy building your API!
    |
*/

Route::middleware(['auth:sanctum'])->prefix('v1')->name('api.')->group(function () {
    Route::get('schedule', fn (Request $request) => $request->user())->name('schedule');
});

// 測試排程
Route::get('/test/pusher', [ScheduleService::class, 'ownBuy']);
