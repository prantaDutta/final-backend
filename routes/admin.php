<?php

use App\Http\Controllers\AdminController;
use App\Http\Resources\UserResource;
use App\Http\Resources\VerificationResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


// Authenticated Routes
Route::group(['middleware' => ['auth:sanctum']], static function () {

    // get current admin
    Route::get('/', static function (Request $request) {
        return response()->json([
            'user' => new UserResource($request->user()),
            'verification' => $request->user()->verification
                ? new VerificationResource($request->user()->verification)
                : null
        ]);
    });

    // get all verification requests
    Route::get('/users/{verified}', [AdminController::class, 'getUsers']);

    // get single user requests
    Route::get('/user/{id}', [AdminController::class, 'getSingleUser']);

    // get all loan installments from an id
    Route::get('/user/loan-installments/{id}', [AdminController::class, 'getLoanInstallments']);

    // get all loans, transactions, installments for a user
    Route::get('/user/{type}/{id}', [AdminController::class, 'getThingsForOneUser']);

    // make an account verified
    Route::get('/verification-check/{ifVerified}/{id}', [AdminController::class, 'makeAccountVerified']);

    // get all Loan Requests
    Route::get('/loans/{requestType}', [AdminController::class, 'getAllLoans']);

    // Fetching Alternate Dashboard Data
    Route::get('/dashboard-data', [AdminController::class, 'dashboardData']);

    // Getting All Transactions
    Route::get('/transactions/{type}/{status}', [AdminController::class, 'getTransactions']);

    // Get Single Transaction Requests
    Route::get('/transaction/{id}', [AdminController::class, 'getSingleTransactions']);

    // Transaction Successful Or Failed
//    Route::get('/transaction/{type}/{id}', [AdminController::class, 'markTransaction']);

    // Get Single Loan Details
    Route::get('/get-single-loan/{id}', [AdminController::class, 'getSingleLoan']);

    // Get All Installments
    Route::get('/installments/{status}', [AdminController::class, 'getAllInstallments']);

    // Get Penalty Data
    Route::get('/get-penalty-data', [AdminController::class, 'getPenaltyData']);

    // Update Penalty Data
    Route::post('/update-penalty-data', [AdminController::class, 'updatePenaltyData']);

    // get default interest rate
    Route::get('/get-interest-rate', [AdminController::class, 'getInterestRate']);

    // update default interest rate
    Route::post('/update-interest-rate', [AdminController::class, 'updateInterestRate']);

    // get lender by amount
//    Route::get('/get-lender-by-amount/{amount}', [AdminController::class, 'getLenderByAmount']);
});
