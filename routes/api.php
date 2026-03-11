<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\FootballMatchController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:api');
});

Route::get('/matches/count', [FootballMatchController::class, 'count']);
Route::get('/matches', [FootballMatchController::class, 'index']);
Route::get('/matches/{footballMatch}', [FootballMatchController::class, 'show']);

Route::middleware('auth:api')->group(function () {
    Route::post('/matches', [FootballMatchController::class, 'store']);
    Route::put('/matches/{footballMatch}', [FootballMatchController::class, 'update']);
    Route::delete('/matches/{footballMatch}', [FootballMatchController::class, 'destroy']);
});
