<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;


Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/register-checker', [AuthController::class, 'registerChecker']);

Route::middleware('auth:api')->group(function () {
    Route::get('/auth/user', [AuthController::class, 'user']);
});

Route::get('/test', function () {
    return 'OK API';
});

// Route::middleware('auth:api')->group(function () {
//     Route::get('/me', [AuthController::class, 'me']);
// });
