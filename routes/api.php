<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['web']], static function () {
    // Auth Routes
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Checking Unique Email
    Route::post('/unique-email', [UserController::class, 'uniqueEmail']);

    // Sending an Email
    Route::post('/send-email', [UserController::class, 'sendEmail']);

    // Verify Email
    Route::get('/verify-email/{token}', [UserController::class, 'verifyEmail']);
});
