<?php

namespace App\Http\Controllers;

use App\Http\Resources\LoanResource;
use App\Models\Loan;
use App\Models\User;
use http\Env\Response;
use Illuminate\Http\Request;

class LoanController extends Controller
{
    public function newLoan(Request $request)
    {
        $values = $request->get('values');
        $id = $request->get('id');

        # find the user first
        $user = User::find($id);

        # add the loan data to the user relation
        $amount = $values['amount'];
        $interest_rate = $values['interestRate'];
        $interest = $amount * ($interest_rate / 100);
        $company_fees = $amount * 0.02;
        $user->loans()->create([
            'loan_amount' => $amount,
            'loan_mode' => 'processing',
            'loan_duration' => $values['loanDuration'],
            'interest_rate' => $interest_rate,
            'amount_with_interest' => $amount + $interest,
            'company_fees' => $company_fees,
            'amount_with_interest_and_company_fees' => $amount + $interest + $company_fees,
            'monthly_installment' => $values['monthlyInstallment'],
            'monthly_installment_with_company_fees' => $values['modifiedMonthlyInstallment']
        ]);

        return response('OK', 200);
    }

    // get all Loans
    public function getLoans(Request $request, $loanType)
    {
        $user = User::find($request->user()->id);
        if ($loanType === 'all') {
            return response()->json([
                'loans' => LoanResource::collection($user->loans)
            ]);
        }

        $loans = $user->loans->where('loan_mode',$loanType);
        return response()->json([
            'loans' => LoanResource::collection($loans)
        ]);
    }
}
