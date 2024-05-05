<?php

use App\Http\Controllers\KeywordsController;
use App\Http\Controllers\LoginController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
Route::post('login', LoginController::class)->middleware('guest:sanctum');
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout']);
    Route::get('/user', [LoginController::class, 'getUser']);
    Route::patch('/user/config', [LoginController::class, 'updateApiKey']);
    Route::patch('/user/password', [LoginController::class, 'changePassword']);

    // Routes for CategoryController

    Route::get('/keywords', [KeywordsController::class, 'index']);
    Route::post('/keywords', [KeywordsController::class, 'store']);
    Route::get('/keywords/{id}', [KeywordsController::class, 'show']);
    Route::put('/keywords/{id}', [KeywordsController::class, 'update']);
    Route::delete('/keywords/{id}', [KeywordsController::class, 'destroy']);


    Route::get('/favourites', [\App\Http\Controllers\FavouritesController::class, 'index']);
    Route::post('/favourites', [\App\Http\Controllers\FavouritesController::class, 'store']);
    Route::get('/favourites/{id}', [\App\Http\Controllers\FavouritesController::class, 'show']);
    Route::put('/favourites/{id}', [\App\Http\Controllers\FavouritesController::class, 'update']);
    Route::delete('/favourites/{id}', [\App\Http\Controllers\FavouritesController::class, 'destroy']);
    Route::post('/favourites/idea', [\App\Http\Controllers\IdeaController::class, 'addFav']);

    Route::post('/categories', [CategoryController::class, 'store']);

    Route::put('/categories/{id}', [CategoryController::class, 'update']);
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);
    Route::post('/generate-text',  [\App\Http\Controllers\IdeaController::class, 'generateText']);


});
Route::post('register', [LoginController::class, 'register'])->middleware('guest:sanctum');

Route::get('/categories', [CategoryController::class, 'index'])->middleware('guest:sanctum');
Route::get('/categories/{id}', [CategoryController::class, 'show'])->middleware('guest:sanctum');


