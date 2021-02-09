<?php

namespace App\Http\Controllers;

use App\Http\Resources\LoanResource;
use App\Http\Resources\TransactionResource;
use App\Http\Resources\UserResource;
use App\Http\Resources\VerificationResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Redirect;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // redirecting to login
    public function login()
    {
        $url = config('app.frontEndUrl');
        return Redirect::to($url . '/login');
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

        $ongoingLoans = $loans->where('loan_mode', 'ongoing')->count();
        $processingLoans = $loans->where('loan_mode', 'processing')->count();
        $finishedLoans = $loans->where('loan_mode', 'finished')->count();

        $deposits = $transactions->where('transaction_type', 'deposit')->count();
        $withdrawals = $transactions->where('transaction_type', 'withdraw')->count();
        return response()->json([
            'ongoing' => $ongoingLoans,
            'processing' => $processingLoans,
            'finished' => $finishedLoans,
            'deposits' => $deposits,
            'withdrawals' => $withdrawals
        ]);
    }

    // update Personal Settings
    public function updatePersonalSettings(Request $request, $info)
    {
        // Finding authenticated user
        $user = User::find($request->user()->id);

        // if address field is modified
        if ($info === 'address') {
            // Validating Address Fields
            $request->validate([
                'address' => 'required|string',
                'division' => 'required|string',
                'zila' => 'required|string',
                'zipCode' => 'required|numeric'
            ]);

            // updating the database
            $user->verification->update([
                'address' => $request->get('address'),
                'division' => $request->get('division'),
                'zila' => $request->get('zila'),
                'zip_code' => $request->get('zipCode')
            ]);

            return response()->json(["OK"],200);
        }

        // if mobile field is modified
        if ($info === 'mobile') {
            // Validating Mobile No
            $request->validate([
                'mobileNo' => 'required|numeric|digits:13',
            ]);

            // updating the database
            $user->verification->update([
                'mobile_no' => $request->get('mobileNo'),
            ]);

            return response()->json(["OK"],200);
        }

        // if email field is modified
        if ($info === 'email') {
            // Validating Email
            $request->validate([
                'email' => [
                    'required',
                    Rule::unique('users')->ignore($user->id),
                ],
            ]);

            // updating the database
            $user->update([
                'email' => $request->get('email'),
            ]);

            return response()->json(["OK"],200);
        }
        return abort(422);
    }

    // update account Settings
    public function updateAccountSettings(Request $request, $info)
    {
        $user = User::find($request->user()->id);
        if ($info === 'language') {
            $user->update([
                'language' => $request->get('language'),
            ]);
            return response()->json(["OK"],200);
        }
        if ($info === 'close') {
            try {
                $user->delete();
                return response()->json(["OK"],200);
            } catch (\Exception $e) {
                return response()->json(["Something Went Wrong"],500);
            }
        }
        if ($info === 'password') {
            $request->validate([
                'currentPassword' => 'required|string|min:8',
                'newPassword' => 'required|string|min:8',
                'password_confirmation' => 'required|confirmed'
            ]);

            if (Hash::check($request->get('currentPassword'), $user->password)) {
                $user->update([
                    'password' => Hash::make($request->get('newPassword'))
                ]);

                return response()->json(["Ok"], 200);
            }
        }

        return abort(422);
    }
}
