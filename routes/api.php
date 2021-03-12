<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Library\DistributedLoans\DistributedBorrowing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['web']], static function () {
    // Auth Routes
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Checking Unique Email
    Route::post('/unique-email', [UserController::class, 'uniqueEmail']);

    // Sending an Email
    Route::post('/send-verify-email', [UserController::class, 'sendVerifyEmail']);

    // Verify Email
    Route::get('/verify-email/{email}/{token}', [UserController::class, 'verifyEmail']);

    // This is temporary
    Route::get('/testing-distributing/{amount}', static function (Request $request) {
        $distributed_borrowing = new DistributedBorrowing($request->get('amount'));
        return $distributed_borrowing->distribute();
    });
});
