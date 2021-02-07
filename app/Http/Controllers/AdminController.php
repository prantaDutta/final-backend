<?php

namespace App\Http\Controllers;

use App\Http\Resources\LoanResource;
use App\Http\Resources\TransactionDetailResource;
use App\Http\Resources\TransactionResource;
use App\Http\Resources\UserResource;
use App\Http\Resources\VerificationResource;
use App\Models\Loan;
use App\Models\Transaction;
use App\Models\User;

class AdminController extends Controller
{
    // getting all the verification requests
    public function getUsers($verified)
    {
        if ($verified === 'all') {
            $user = User::all();
        } else {
            $user = User::where('verified', $verified)->get();
        }
        if ($user) {
            return response()->json([
                'users' => UserResource::collection($user)
            ], 200);
        }
        return response()->json(['users' => []], 200);
    }

    // getting single verification requests
    public function getSingleUser($id)
    {
        $user = User::find($id);
        return response()->json([
            'pendingUser' => new UserResource($user),
            'verification' => new VerificationResource($user->verification)
        ]);
    }

    // making an account verified
    public function makeAccountVerified($ifVerified, $id)
    {
        $user = User::findOrFail($id);
        $user->verified = $ifVerified;
        $user->save();

        return response()->json('OK', 200);
    }

    // get all loans
    public function getAllLoans($requestType)
    {
        if ($requestType === 'all') {
            $loans = Loan::with('users')->get();
        } else {
            $loans = Loan::where('loan_mode', $requestType)->with('users')->get();
        }
        return response()->json([
            'loans' => LoanResource::collection($loans)
        ]);
    }

    // fetching recent dashboard data
//    public function recentData()
//    {
//        // Get First Two Rows
//        $verification_requests = User::where('verified', 'pending')->skip(0)->take(2)->get();
//        $loan_requests = Loan::where('loan_mode', 'processing')->skip(0)->take(2)->get();
//        return response()->json([
//            'loanRequests' => LoanResource::collection($loan_requests),
//            'verificationRequests' => UserResource::collection($verification_requests)
//        ]);
//    }

    // fetching Alternate Dashboard Data
    public function dashboardData()
    {
        $verification_requests = User::where('verified', 'pending')->count();
        $loan_requests = Loan::where('loan_mode', 'processing')->count();
        $withdrawal_requests = Transaction::where('transaction_type', 'withdraw')
            ->where('status', 'pending')->count();

        return response()->json([
            'verifications' => $verification_requests,
            'loans' => $loan_requests,
            'withdrawals' => $withdrawal_requests
        ]);
    }

    // Get all Transactions
    public function getTransactions($type, $status)
    {
        if ($type === 'all' && $status === 'all') {
            $requests = Transaction::all();
        } else if ($type === 'all' && $status !== 'all') {
            $requests = Transaction::where('status', $status)->get();
        } else if ($type !== 'all' && $status === 'all') {
            $requests = Transaction::where('transaction_type', $type)->get();
        } else {
            $requests = Transaction::where('transaction_type', $type)
                ->where('status', $status)->get();
        }

        return response()->json([
            'requests' => TransactionResource::collection($requests)
        ]);
    }

    // Get Single Withdrawal Request
    public function getSingleTransactions($id)
    {
        $transaction = Transaction::find($id);
        return response()->json([
            'transaction' => new TransactionResource($transaction),
            'transactionDetail' => new TransactionDetailResource($transaction->transaction_detail)
        ]);
    }

    // marking withdrawal request as Completed or Failed
    public function markTransaction($type, $id)
    {
        return Transaction::find($id)->update([
            'status' => $type
        ]);
    }
}
