<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Library\LoanDistribution\GenerateLenderDataArray;
use App\Library\LoanDistribution\LenderData;
use App\Library\LoanDistribution\TestDistributor;
use App\Models\Loan;
use App\Models\User;
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
    Route::get('/testing-distributing/{amount}', static function ($amount) {
        $loan_distributor = new TestDistributor($amount, uniqid('', true));
        return $loan_distributor->distribute();
    });

    // This is temporary too
    Route::get('/lender-data/{amount}', static function (int $amount) {
        $temp = new GenerateLenderDataArray();
        return $temp->generate($amount);
    });

    Route::get('/random-user', static function () {
        $lender_ids = [1, 2, 3];
        do {
            $user = User::has('transactions')
                ->inRandomOrder()
                ->where('role', 'lender')
                ->whereNotIn('id', $lender_ids)
                ->whereHas('util', function ($q) {
//                    $q->where('loan_limit', '<=', 5);
                    $q->whereRaw('loan_limit= (select min(`loan_limit`) from utils)');
                })
                ->where('verified', 'verified')
                ->first();

            if ($user === null) {
                return response("Error", 500);
            }

        } while ($user->balance < $user->loan_preference->maximum_distributed_amount);

        return $user->util;
    });

    Route::get('/check-amount', static function () {
        $loan = Loan::findOrFail(3027);

        $the_borrower = $loan->users()
            ->where('role', 'borrower')->first();

        $the_lenders = $loan->users()
            ->where('role', 'lender')->get();

        $lender_data = [];
        foreach ($the_lenders as $lender) {
            $lender_data[] = [
                'name' => $lender->name,
                'id' => $lender->id,
                'amount' => $lender->pivot->amount,
            ];
        }

        return $lender_data;

        return [
            $the_borrower, $the_lenders
        ];
    });

});
