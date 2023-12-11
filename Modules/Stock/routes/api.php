<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\Stock\app\Http\Controllers\StockController;

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

Route::controller(StockController::class)->prefix('stock')->group(function(){
    // 基礎建置
    Route::get('/build-based', 'buildBasedInformation');
    // 新增關注
    Route::post('/', 'createFocuseStock');
    // 推薦購買(測試用)
    Route::get('/remmo-buy','recommendBuy');
    // 關注自己購買推播(測試用)
    Route::get('/own-buy','ownBuy');
});

