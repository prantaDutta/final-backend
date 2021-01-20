<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Authenticated Routes
Route::group(['middleware' => ['auth:sanctum']], static function () {
    // get current user
    Route::get('/user', static function(Request $request) {
        return $request->user();
    });
    // verify an user
    Route::post('/verify', [UserController::class, 'verifyUser']);
});

//Route::middleware('auth:sanctum')->get('/user', static function (Request $request) {
//    return $request->user();
//});

Route::group(['middleware' => ['web']], static function () {
    // Auth Routes
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Checking Unique Email
    Route::post('/unique-email', [UserController::class, 'uniqueEmail']);

    // Checking Unique Email Excluding Id
    Route::post('/unique-email-excluding-id', [UserController::class, 'uniqueEmailExcludingId']);
});
