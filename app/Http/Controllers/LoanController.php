<?php

namespace App\Http\Controllers;

use App\Events\NewLoanRequestEvent;
use App\Http\Resources\LoanResource;
use App\Models\Loan;
use App\Models\User;
use App\Notifications\NewLoanRequested;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class LoanController extends Controller
{
    public function newLoan(Request $request): Response|JsonResponse|Application|ResponseFactory
    {
        $values = $request->get('values');
        $id = $request->get('id');

        # find the user first
        $user = User::find($id);

//        if ($user->loans()->count() <= 3) {
//            return response()->json([
//                'error' => 'You Already have three ongoing loans',
//            ], 422);
//        }

        $unique_loan_id = uniqid('', true);

        # add the loan data to the user relation
        $amount = $values['amount'];
        $interest_rate = $values['interestRate'];
        $interest = $amount * ($interest_rate / 100);
        $company_fees = $amount * 0.02;

        Loan::create([
            'loan_amount' => $amount,
            'loan_mode' => 'processing',
            'unique_loan_id' => $unique_loan_id,
            'loan_duration' => $values['loanDuration'],
            'interest_rate' => $interest_rate,
            'amount_with_interest' => $amount + $interest,
            'company_fees' => $company_fees,
            'amount_with_interest_and_company_fees' => $amount + $interest + $company_fees,
            'monthly_installment' => $values['monthlyInstallment'],
            'monthly_installment_with_company_fees' => $values['modifiedMonthlyInstallment']
        ]);

        $user->notify(new NewLoanRequested);

        # This event is not really necessary at least for now
        # But it boosts performance
        event(new NewLoanRequestEvent(
            $user, // accepted as borrower in the event and loan distributor
            $amount,
            $unique_loan_id
        ));

        return response('OK');
    }

    // get all Loans
    public function getLoans(Request $request, $loanType): JsonResponse
    {
        $user = User::find($request->user()->id);
        if ($loanType === 'all') {
            return response()->json([
                'loans' => LoanResource::collection($user->loans)
            ]);
        }

        $loans = $user->loans->where('loan_mode', $loanType);
        return response()->json([
            'loans' => LoanResource::collection($loans)
        ]);
    }
}
