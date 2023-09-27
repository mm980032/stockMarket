<?php

use App\Http\Controllers\StockController;
use App\Services\StockMarketService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// LineBot通知範例
// Route::get('/line-bot/push',[StockMarketService::class, 'notifyStockMarketInfo']);

// Route::get('/info',[StockMarketService::class, 'compareOpeningAndLowest']);
// Route::get('/get/focuse',[StockMarketService::class, 'getFocuseInfo']);

// 基礎股票資訊(intit)
Route::get('/buildBaseStock',[StockController::class, 'buildBaseStock']);

Route::post('/focus',[StockController::class, 'createFocusStock']);
