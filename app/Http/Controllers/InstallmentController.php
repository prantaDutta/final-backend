<?php

namespace App\Http\Controllers;

use App\Http\Resources\InstallmentResource;
use App\Http\Resources\LoanResource;
use App\Http\Resources\UserResource;
use App\Models\Installment;
use App\Models\User;
use Carbon\Carbon;
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
            'installments' => InstallmentResource::collection($installments),
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
    public function payInstallment(Request $request) : JsonResponse
    {
        $amount = (int) $request->get('amount');
        $id = $request->get('id');
//        $user = $request->user();
        $user = User::findOrFail(3);

        $user_balance = $user->balance;
        // checking whether the borrower has enough balance to pay
        if ($user_balance < $amount) {
            return response()->json([
                "error" => "You don't have enough balance",
            ], 500);
        }
        // installment is the current installment
        $installment = Installment::findOrFail($id);

        $loan = $installment->loan;

        $due_installments = $installment::where('status', 'due')
            ->where('loan_id', $loan->id)
            ->get();

        $flag = false;
        $installment_due_date = Carbon::parse($installment->due_date);
        foreach ($due_installments as $due_installment) {
            $due_installment_due_date = Carbon::parse($due_installment->due_date);

            if ($due_installment_due_date->lt($installment_due_date)) {
                $flag = true;
                break;
            }
        }

        if ($flag === true) {
            return response()->json([
                "error" => "Please Pay Previous Installment First"
            ], 422);
        }

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
        foreach ($current_loan->lender_data as $lender_datum) {
            // finding the lender installment row
            $lender_installment = Installment::where('loan_id', $current_loan->id)
                ->where('installment_no', $installment->installment_no)
                ->whereHas('user', function ($q) use ($lender_datum) {
                    $q->where('id', $lender_datum['lender_id']);
                })
                ->first();

            if ($lender_installment === null) {
                return response()->json([
                    "error" => "Can't Find Lender Installments",
                    "description" => "It looks like there are no lenders associated with this installment. Are you sure you created this loan?",
                ], 500);
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
            "OK",
        ]);
    }
}
