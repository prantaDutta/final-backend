<?php

use App\Http\Controllers\LoanController;
use App\Http\Controllers\SslCommerzPaymentController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VerificationController;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


// Authenticated Routes
Route::group(['middleware' => ['auth:sanctum']], static function () {

    // get current user
    Route::get('/', static function (Request $request) {
        return new UserResource($request->user());
    });

    // Checking Unique Email Excluding Id
    Route::post('/unique-email-excluding-id', [UserController::class, 'uniqueEmailExcludingId']);

    // getting the user with verification
    Route::get('/user-with-verification', [UserController::class, 'userWithVerificationData']);

    // verify an user
    Route::post('/verify', [VerificationController::class, 'verifyUser']);

    // borrower asking for a new loan
    Route::post('/new-loan', [LoanController::class, 'newLoan']);

    // get all Loans
    Route::post('/all-loans', [LoanController::class, 'getAllLoans']);

    // Deposit Routes
    Route::get('/deposit', [SslCommerzPaymentController::class, 'exampleEasyCheckout']);

    // Get All Deposit Transactions
    Route::get('/get-all-deposits/{id}', [TransactionController::class, 'getAllDeposits']);

    // Get All Withdrawal Transactions
    Route::get('/get-all-withdrawals/{id}', [TransactionController::class, 'getAllWithdrawals']);

    // Withdraw
    Route::post('/withdraw', [TransactionController::class, 'withdraw']);
});
