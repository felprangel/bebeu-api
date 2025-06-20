<?php

use App\Http\Controllers\UsersController;
use App\Http\Controllers\WaterIntakeController;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Support\Facades\Route;

Route::post('/register', [UsersController::class, 'createUser']);
Route::post('/login', [UsersController::class, 'login']);

Route::middleware(Authenticate::class . ':sanctum')->group(function () {
    Route::post('/logout', [UsersController::class, 'logout']);
    Route::patch('/me/goal', [UsersController::class, 'changeWaterGoal']);
    Route::get('/me/goal', [UsersController::class, 'getWaterGoal']);
    Route::get('/me/intake', [UsersController::class, 'getWaterIntake']);
    Route::post('/me/intake', [WaterIntakeController::class, 'create']);
});
