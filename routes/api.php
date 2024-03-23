<?php

use App\Http\Controllers\CostController;
use App\Http\Controllers\CostTypeController;
use App\Http\Controllers\LoginController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
Route::post('login', LoginController::class)->middleware('guest:sanctum');
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout']);
    Route::get('/user', [LoginController::class, 'getUser']);
    Route::get('/cost-types', [CostTypeController::class, 'index']);
    Route::get('/cost-types/{costType}', [CostTypeController::class, 'show']);
    Route::post('/cost-types', [CostTypeController::class, 'store']);
    Route::put('/cost-types/{costType}', [CostTypeController::class, 'update']);
    Route::delete('/cost-types/{costType}', [CostTypeController::class, 'destroy']);

    Route::get('/costs', [CostController::class, 'index']);
    Route::post('/costs', [CostController::class, 'store']);
    Route::get('/costs/{id}', [CostController::class, 'show']);
    Route::put('/costs/{id}', [CostController::class, 'update']);
    Route::delete('/costs/{id}', [CostController::class, 'destroy']);
});
Route::post('register', [LoginController::class, 'register'])->middleware('guest:sanctum');


