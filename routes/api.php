<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UtilController;
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

    // Verify Forgot Password
    Route::post('/verify-forgot-password', [UserController::class, 'verifyForgotPassword']);

    // Send Help
    Route::post('/get-help', [UtilController::class, 'getHelp']);

    // Forgot password
    Route::post('/forgot-password', [UserController::class, 'forgotPassword']);

    // This is temporary
//    Route::get('/temp', function (){
//        $user = \App\Models\User::find(3);
//        return $user->notify(new \App\Notifications\DepositNotification());
//    });
//    Route::get('/testing-distributing/{amount}', static function ($amount) {
//        $loan_distributor = new TestDistributor($amount, uniqid('', true));
//        return $loan_distributor->distribute();
//    });
//
//    // This is temporary too
//    Route::get('/lender-data/{amount}', static function (int $amount) {
//        $temp = new GenerateLenderDataArray();
//        return $temp->generate($amount);
//    });
//
//    Route::get('/random-trx-id', static function() {
//        $util = new UtilController();
//        return $util->generateAUniqueTrxId();
//    });
//
//    Route::get('/random-user', static function () {
//        $lender_ids = [1, 2, 3];
//        do {
//            $user = User::has('transactions')
//                ->inRandomOrder()
//                ->where('role', 'lender')
//                ->whereNotIn('id', $lender_ids)
//                ->whereHas('util', function ($q) {
////                    $q->where('loan_limit', '<=', 5);
//                    $q->whereRaw('loan_limit= (select min(`loan_limit`) from utils)');
//                })
//                ->where('verified', 'verified')
//                ->first();
//
//            if ($user === null) {
//                return response("Error", 500);
//            }
//
//        } while ($user->balance < $user->loan_preference->maximum_distributed_amount);
//
//        return $user->util;
//    });

//    Route::get('/check-amount', static function () {
//
//    });
//    Route::get('/check-amount/{type}', [\App\Http\Controllers\InstallmentController::class, 'getAllInstallments']);
//    Route::get('/check-installment/{amount}/{id}', [InstallmentController::class, 'payInstallment']);
});
