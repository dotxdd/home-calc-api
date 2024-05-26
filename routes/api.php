<?php

use App\Http\Controllers\CostController;
use App\Http\Controllers\CostStatsController;
use App\Http\Controllers\CostTypeController;
use App\Http\Controllers\CostTypeLimitController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\PredictionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
Route::post('login', LoginController::class)->middleware('guest:sanctum');
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout']);
    Route::get('/user', [LoginController::class, 'getUser']);
    Route::patch('/user/config', [LoginController::class, 'updateFirstConfig']);
    Route::get('/cost-types', [CostTypeController::class, 'index']);
    Route::get('/cost-types/{costType}', [CostTypeController::class, 'show']);
    Route::post('/cost-types', [CostTypeController::class, 'store']);
    Route::put('/cost-types/{costType}', [CostTypeController::class, 'update']);
    Route::delete('/cost-types/{costType}', [CostTypeController::class, 'destroy']);
    Route::get('/cost-types-without-pagination', [CostTypeController::class, 'indexWithoutPagination']);


    Route::get('/costs', [CostController::class, 'index']);
    Route::post('/costs', [CostController::class, 'store']);
    Route::get('/costs/{id}', [CostController::class, 'show']);
    Route::put('/costs/{id}', [CostController::class, 'update']);
    Route::delete('/costs/{id}', [CostController::class, 'destroy']);

    Route::get('/cost-types-limits', [CostTypeLimitController::class, 'index']);
    Route::get('/cost-types-limits/{id}', [CostTypeLimitController::class, 'show']);
    Route::post('/cost-types-limits', [CostTypeLimitController::class, 'store']);
    Route::put('/cost-types-limits/{id}', [CostTypeLimitController::class, 'update']);
    Route::delete('/cost-types-limits/{id}', [CostTypeLimitController::class, 'destroy']);

    //stats
    Route::get('/daily/costs/stats', [CostStatsController::class, 'getDailyCosts']);
    Route::get('/monthly/costs/stats', [CostStatsController::class, 'getMonthlyCosts']);
    Route::get('/quarterly/costs/stats', [CostStatsController::class, 'getQuarterlyCosts']);
    Route::get('/yearly/costs/stats', [CostStatsController::class, 'getYearlyCosts']);

    Route::get('/predictions', [PredictionController::class, 'getPredictions']);


});
Route::post('register', [LoginController::class, 'register'])->middleware('guest:sanctum');


