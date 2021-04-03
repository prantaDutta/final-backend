<?php

namespace App\Http\Controllers;

use App\Http\Resources\InstallmentResource;
use App\Http\Resources\LoanResource;
use App\Models\Installment;
use App\Models\User;
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
        ]);
    }

    # Pay Installment
    public function payInstallment(Request $request)
    {
        $amount = $request->get('amount');
        $id = $request->get('id');
        $user = $request->user();

        $installment = Installment::findOrFail($id);

        $total_amount = $installment->total_amount;

        if ($user->balance >= $total_amount) {
            DB::table('users')
                ->where('id', $user->id)
                ->decrement('balance', $total_amount);

            $installment->update([
                'status' => 'paid',
            ]);

            return response()->json([
                "OK"
            ]);
        }
        return response()->json(["ERROR"], 500);
    }
}
