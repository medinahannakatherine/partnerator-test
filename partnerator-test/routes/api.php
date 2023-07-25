<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Tymon\JWTAuth\Http\Middleware\Authenticate;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\OmsController;
use App\Http\Controllers\PostmanDataController;
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
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);


Route::middleware('jwt.verify')->group(function() {
    Route::post('/emrates',[OmsController::class,'getRates']);
    Route::post('/emdates',[OmsController::class,'getDates']);
    Route::post('/check-estimates',[OmsController::class,'getRatesDates']);
});


