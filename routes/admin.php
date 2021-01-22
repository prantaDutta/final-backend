<?php

use App\Http\Controllers\AdminController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


// Authenticated Routes
Route::group(['middleware' => ['auth:sanctum']], static function () {

    // get current user
    Route::get('/', static function (Request $request) {
        return $request->user();
    });

    // get all verification requests
    Route::get('/verification-requests', [AdminController::class, 'getAllVerificationRequests']);

    // get single verification requests
    Route::get('/verification-requests/{id}', [AdminController::class, 'getSingleVerificationRequests']);

    // make an account verified
    Route::get('/verification-requests/verified/{id}', [AdminController::class, 'makeAccountVerified']);

    // get all Loan Requests
    Route::get('/loan-requests/{requestType}', [AdminController::class, 'getAllLoanRequests']);
});
