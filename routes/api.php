<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\LiveController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('api')->group(function () {
    //登录
    Route::post('/login', [LoginController::class, 'login'])->name('login.login');
    //发送验证码
    Route::post('/sendCode', [LoginController::class, 'sendCode'])->name('login.sendCode');
    //发送sms验证码
    Route::get('/sendSms', [LoginController::class, 'sendSms'])->name('login.sendSms');
    //广播数据数据推送
    Route::get('/push', [LiveController::class, 'push'])->name('live.push');
    //广播数据数据查询
    Route::get('/outs', [LiveController::class, 'outs'])->name('live.outs');
});
