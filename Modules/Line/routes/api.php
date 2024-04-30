<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\Line\app\Http\Controllers\LineLoginController;

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
    Route::get('line', fn (Request $request) => $request->user())->name('line');
});

route::prefix('/line')->group(function () {
    Route::get('/callback/token', [LineLoginController::class,'callBackLineToken']);
    // 訊息推送
    Route::get('/OA/push', [LineLoginController::class,'pushMessage']);
    // 驗證 accessToken
    Route::get('accessToken/verify', [LineLoginController::class,'accessTokenVerify']);
    // 驗證 idToken
    Route::get('/verify', [LineLoginController::class,'verify']);
    // 撤銷 accessToken
    Route::get('/revoke', [LineLoginController::class,'revoke']);
    // 用戶資訊
    Route::get('/userInfo', [LineLoginController::class,'getLineUserInfo']);
    // 官方關係
    route::get('/friendship/status', [LineLoginController::class,'checkFriendshipStatus']);
    // 取消應用程式授權
    Route::get('/remove/user-authorize', [LineLoginController::class,'removeUserAuthorize']);
});



