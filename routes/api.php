<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\InvitationRedemptionController;
use App\Http\Controllers\TicketValidationController;
use App\Http\Controllers\AdminController;

// AUTH
Route::post('/auth/register-checker', [AuthController::class, 'registerChecker']);
Route::post('/auth/login', [AuthController::class, 'login']);

Route::middleware('auth:api')->post('/auth/logout', [AuthController::class, 'logout']);

// PUBLIC ENDPOINTS
Route::middleware('throttle:invitation_lookup')->get('/invitations/{hash}', [InvitationController::class, 'show']);
Route::middleware('throttle:redeem')->post('/redeem', [InvitationRedemptionController::class, 'store']);

// CHECKER
Route::middleware(['auth:api', 'role:checker'])->group(function () {
    Route::get('/checker/test', fn() => 'OK CHECKER');

    Route::post('/tickets/{code}/validate', [TicketValidationController::class, 'validateTicket']);
});

// ADMIN
Route::middleware(['auth:api', 'role:admin'])->group(function () {
    Route::get('/admin/test', fn() => 'OK ADMIN');

    Route::get('/admin/events/{event}/tickets-used', [AdminController::class, 'ticketsUsed']);
    Route::get('/admin/redemptions', [AdminController::class, 'redemptions']);
});
