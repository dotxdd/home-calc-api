<?php

use App\Http\Controllers\LoginController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/test', function () {
    return 'test1';
});
Route::prefix('api')->group(function () {
    Route::post('/register', [UserController::class, 'register']);
    Route::post('/login', 'App\Http\Controllers\LoginController@login');
    Route::get('/test', 'App\Http\Controllers\UserController@getTest');
    Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth');


    Route::get('/user', 'App\Http\Controllers\UserController@getUserDetails')->middleware('auth:sanctum');
});
