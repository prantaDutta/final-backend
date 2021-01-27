<?php

namespace App\Http\Controllers;

use App\Http\Resources\LoanResource;
use App\Http\Resources\UserResource;
use App\Http\Resources\VerificationResource;
use App\Models\Loan;
use App\Models\User;

class AdminController extends Controller
{
    // getting all the verification requests
    public function getAllVerificationRequests()
    {
        $user = User::where('verified', 'pending')->get();
        if ($user) {
            return response()->json([
                'users' => UserResource::collection($user)
            ], 200);
        }
        return response()->json(['users' => []], 200);
    }

    // getting single verification requests
    public function getSingleVerificationRequests($id)
    {
        $user = User::find($id);
        return response()->json([
            'pendingUser' => new UserResource($user),
            'verification' => new VerificationResource($user->verification)
        ]);
    }

    // making an account verified
    public function makeAccountVerified($id)
    {
        $user = User::findOrFail($id);
        $user->verified = 'yes';
        $user->save();

        return response()->json('OK', 200);
    }

    // get all loans
    public function getAllLoanRequests($requestType)
    {
        if ($requestType === 'all') {
            $loans = Loan::with('users')->get();
        } else {
            $loans = Loan::where('loan_mode',$requestType)->with('users')->get();
        }
        return response()->json([
            'loans' =>  LoanResource::collection($loans)
        ]);
    }
}
