<?php

namespace App\Http\Controllers;

use App\Http\Resources\LoanResource;
use App\Http\Resources\TransactionResource;
use App\Http\Resources\UserResource;
use App\Http\Resources\VerificationResource;
use App\Models\Loan;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    // redirecting to login
    public function login()
    {
        $url = config('app.frontEndUrl');
        return \Redirect::to($url . '/login');
    }

    // checks for unique Email
    public function uniqueEmail(Request $request)
    {
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return abort(422);
        }
        return response('OK', 200);
    }

    // checks for unique Email excluding id
    public function uniqueEmailExcludingId(Request $request)
    {
        $user = User::where('email', $request->get('email'))->first();
        if (!$user || $user->id !== $request->get('id')) {
            return response('ERROR', 422);
        }
        return response('OK', 200);
    }

    // getting the current user with verification data
    public function userWithVerificationData(Request $request)
    {
        $user = $request->user();
        return response()->json([
            'user' => new UserResource($user),
            'verification' => new VerificationResource($user->verification)
        ]);
    }

    // Fetching recent data
    public function recentData(Request $request)
    {
        // Get First Two Rows
        $loans = $request->user()->loans->skip(0)->take(2);
        $transactions = $request->user()->transactions->skip(0)->take(2);
        return response()->json([
            'loans' => LoanResource::collection($loans),
            'transactions' => TransactionResource::collection($transactions)
        ]);
    }

    // fetching Alternate Dashboard Data
    public function dashboardData(Request $request)
    {
        $loans = $request->user()->loans;
        $transactions = $request->user()->transactions;

        $ongoingLoans = $loans->where('loan_mode','ongoing')->count();
        $processingLoans = $loans->where('loan_mode','processing')->count();
        $finishedLoans = $loans->where('loan_mode','finished')->count();

        $deposits = $transactions->where('transaction_type','deposit')->count();
        $withdrawals = $transactions->where('transaction_type','withdraw')->count();
        return response()->json([
            'ongoing' => $ongoingLoans,
            'processing' => $processingLoans,
            'finished' => $finishedLoans,
            'deposits' => $deposits,
            'withdrawals' => $withdrawals
        ]);
    }
}
