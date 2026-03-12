<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\FootballMatchController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegistrationController;

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:api');
});

Route::get('/matches/count', [FootballMatchController::class, 'count']);
Route::get('/matches', [FootballMatchController::class, 'index']);
Route::get('/matches/{footballMatch}', [FootballMatchController::class, 'show']);
Route::get('/matches/{footballMatch}/comments', [CommentController::class, 'index']);

Route::middleware('auth:api')->group(function () {
    Route::post('/matches', [FootballMatchController::class, 'store']);
    Route::put('/matches/{footballMatch}', [FootballMatchController::class, 'update']);
    Route::delete('/matches/{footballMatch}', [FootballMatchController::class, 'destroy']);

    Route::post('/matches/{footballMatch}/comments', [CommentController::class, 'store']);

    Route::post('/matches/{footballMatch}/register', [RegistrationController::class, 'register']);
    Route::delete('/matches/{footballMatch}/register', [RegistrationController::class, 'unregister']);
    Route::get('/matches/{footballMatch}/players', [RegistrationController::class, 'players']);
    Route::get('/user/matches', [RegistrationController::class, 'userMatches']);
});
