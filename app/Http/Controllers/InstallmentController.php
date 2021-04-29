<?php

namespace App\Http\Controllers;

use App\Http\Resources\InstallmentResource;
use App\Http\Resources\LoanResource;
use App\Http\Resources\UserResource;
use App\Models\Installment;
use App\Models\Loan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InstallmentController extends Controller
{
    # Get all Installments
    public function getAllInstallments(Request $request, $type): JsonResponse
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['Error' => 'UnAuthorised'], 419);
        }

        $installments = $user->installments;

        if ($type !== 'all') {
            $installments = $user->installments()
                ->where('status', $type)->get();
        }

        return response()->json([
            'installments' => InstallmentResource::collection($installments)
        ]);
    }

    # Get Single Installment
    public function getSingleInstallment($id): JsonResponse
    {
        $installment = Installment::findOrFail($id);

        return response()->json([
            'installment' => new InstallmentResource($installment),
            'loan' => new LoanResource($installment->loan),
            'user' => new UserResource($installment->user),
        ]);
    }

    # Pay Installment
    public function payInstallment(Request $request): JsonResponse
    {

        $amount = $request->get('amount');
        $id = $request->get('id');
        $user = $request->user();

        // checking whether the borrower has enough balance to pay
        if ((int)$user->balance >= (int)$amount) {
            $installment = Installment::findOrFail($id);

            // decrementing borrower balance
            DB::table('users')
                ->where('id', $user->id)
                ->decrement('balance', $amount);

            // making the installment paid
            $installment->update([
                'status' => 'paid',
            ]);

            // finding the current loan
            $current_loan = $installment->loan;

            // finding every lender
            foreach ($current_loan->lender_data as $key => $lender_datum) {
                // finding the lender installment row
                $lender_installment = Installment::where('loan_id', $current_loan->id)
                    ->where('installment_no', $installment->installment_no)
                    ->whereHas('user', function ($q) use ($lender_datum) {
                        $q->where('id', $lender_datum['lender_id']);
                    })
                    ->first();

                if ($lender_installment === null) {
                    return response()->json(["ERROR"], 500);
                }

                // incrementing the lender balance
                DB::table('users')
                    ->where('id', $lender_installment->user_id)
                    ->increment('balance', $lender_installment->total_amount);

                // marking the installment as paid
                $lender_installment->update([
                    'status' => 'paid',
                ]);
            }

            return response()->json([
                "OK"
            ]);
        }
        return response()->json(["ERROR"], 500);
    }
}