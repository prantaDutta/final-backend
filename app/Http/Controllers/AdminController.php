<?php

namespace App\Http\Controllers;

use App\Http\Resources\InstallmentResource;
use App\Http\Resources\LoanResource;
use App\Http\Resources\TransactionDetailResource;
use App\Http\Resources\TransactionResource;
use App\Http\Resources\UserResource;
use App\Http\Resources\VerificationResource;
use App\Models\Administration;
use App\Models\Installment;
use App\Models\Loan;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    // getting all the verification requests
    public function getUsers($verified): JsonResponse
    {
        if ($verified === 'all') {
            $user = User::all();
        } else {
            $user = User::where('verified', $verified)->get();
        }
        if ($user) {
            return response()->json([
                'users' => UserResource::collection($user)
            ]);
        }
        return response()->json(['users' => []], 200);
    }

    // getting single verification requests
    public function getSingleUser($id): JsonResponse
    {
        $user = User::find($id);
        return response()->json([
            'pendingUser' => new UserResource($user),
            'verification' => new VerificationResource($user->verification)
        ]);
    }

    // making an account verified
    public function makeAccountVerified($ifVerified, $id): JsonResponse
    {
        $user = User::findOrFail($id);
        $user->verified = $ifVerified;
        $user->save();

        return response()->json('OK');
    }

    // get all loans
    public function getAllLoans($requestType): JsonResponse
    {
        if ($requestType === 'all') {
            $loans = Loan::with('users')
                ->latest()->get();
        } else {
            $loans = Loan::where('loan_mode', $requestType)
                ->with('users')->latest()->get();
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
    public function dashboardData(): JsonResponse
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
    public function getTransactions($type, $status): JsonResponse
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
    public function getSingleTransactions($id): JsonResponse
    {
        $transaction = Transaction::find($id);
        return response()->json([
            'transaction' => new TransactionResource($transaction),
            'transactionDetail' => new TransactionDetailResource($transaction->transaction_detail)
        ]);
    }

    // marking withdrawal request as Completed or Failed
    public function markTransaction($type, $id): bool
    {
        return Transaction::find($id)->update([
            'status' => $type
        ]);
    }

    # Getting one single loan details
    public function getSingleLoan($id): JsonResponse
    {
        $loan = Loan::findOrFail($id);

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

        return response()->json([
            'loan' => new LoanResource($loan),
            'theBorrower' => [
                'name' => $the_borrower->name,
                'id' => $the_borrower->id,
                'amount' => $the_borrower->pivot->amount,
            ],
            'theLenders' => $lender_data,
        ]);
    }

    // Get All Installments
    public function getAllInstallments($status): JsonResponse
    {
        if ($status !== 'all') {
            $installments = Installment::where('status', $status)->get();
        } else {
            $installments = Installment::all();
        }

        return response()->json([
            'installments' => InstallmentResource::collection($installments),
        ]);
    }

    // Get Penalty Data
    public function getPenaltyData(Request $request)
    {
        $user = $request->user();
        return response()->json([
            'penaltyData' => $user->administration->penalty_data,
        ]);
    }

    // update penalty data
    public function updatePenaltyData(Request $request)
    {
        $user = $request->user();
        if ($user->role !== 'admin') {
            return response()->json(["ERROR"], 500);
        }

        $penalty_data = $request->get('penaltyData');

        $administration = Administration::first();
        $administration->update([
            'penalty_data' => $penalty_data,
        ]);

        return response()->json(['OK']);
    }
}
