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
    Route::get('/users/{verified}', [AdminController::class, 'getUsers']);

    // get single verification requests
    Route::get('/user/{id}', [AdminController::class, 'getSingleUser']);

    // make an account verified
    Route::get('/user/{ifVerified}/{id}', [AdminController::class, 'makeAccountVerified']);

    // get all Loan Requests
    Route::get('/loans/{requestType}', [AdminController::class, 'getAllLoans']);

    // Fetching Admin Dashboard Data
//    Route::get('/recent-data',[AdminController::class,'recentData']);

    // Fetching Alternate Dashboard Data
    Route::get('/dashboard-data',[AdminController::class,'dashboardData']);

    // Getting All Transactions
    Route::get('/transactions/{type}/{status}',[AdminController::class,'getTransactions']);

    // Get Single Transaction Requests
    Route::get('/transaction/{id}',[AdminController::class,'getSingleTransactions']);

    // Transaction Successful Or Failed
    Route::get('/transaction/{type}/{id}',[AdminController::class, 'markTransaction']);
});
