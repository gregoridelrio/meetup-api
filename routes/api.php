<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\FootballMatchController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;


Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:api');
});

Route::get('/matches', [FootballMatchController::class, 'index']);
Route::get('/matches/{footballMatch}', [FootballMatchController::class, 'show']);
Route::get('/matches/{footballMatch}/comments', [CommentController::class, 'index']);

Route::middleware('auth:api')->group(function () {

    Route::post('/matches', [FootballMatchController::class, 'store']);
    Route::put('/matches/{footballMatch}', [FootballMatchController::class, 'update']);

    Route::middleware('role:admin')->group(function () {
        Route::delete('/matches/{footballMatch}', [FootballMatchController::class, 'destroy']);
    });

    Route::post('/matches/{footballMatch}/players', [RegistrationController::class, 'register']);
    Route::delete('/matches/{footballMatch}/players', [RegistrationController::class, 'unregister']);
    Route::post('/matches/{footballMatch}/comments', [CommentController::class, 'store']);
    Route::get('/matches/{footballMatch}/players', [RegistrationController::class, 'players']);
    Route::get('/users/matches', [RegistrationController::class, 'userMatches']);
    Route::get('/users/stats', [RegistrationController::class, 'userStats']);

    Route::patch('/users', [UserController::class, 'update']);
});
