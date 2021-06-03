<?php

namespace App\Http\Controllers;

use App\Http\Resources\TransactionDetailResource;
use App\Http\Resources\TransactionResource;
use App\Http\Resources\UserResource;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    // get all deposits
    public function getAllDeposits(Request $request): JsonResponse
    {
        $id = $request->user()->id;
        $user = User::find($id);
        $deposits = $user->transactions()
            ->where('user_id', $id)
            ->where('transaction_type', 'deposit')
            ->orderByDesc('created_at')->get();

        if ($deposits) {
            return response()->json([
                'user' => new UserResource($user),
                'transactions' => TransactionResource::collection($deposits)
            ], 200);
        }

        return response()->json([], 200);
    }

    // get all withdrawals
    public function getAllWithdrawals(Request $request): JsonResponse
    {
        $id = $request->user()->id;
        $user = User::find($id);
        $withdrawals = $user->transactions()
            ->where('user_id', $id)
            ->where('transaction_type', 'withdraw')
            ->get();

        if ($withdrawals) {
            return response()->json([
                'user' => new UserResource($user),
                'transactions' => TransactionResource::collection($withdrawals)
            ], 200);
        }

        return response()->json([], 200);
    }

    // Check before withdrawal
    public function checkBeforeWithdrawal(Request $request, $amount): JsonResponse
    {
        $user = $request->user();
        if ((int)$user->balance < (int)$amount) {
            return response()->json([
                "error" => "You don't have that much"
            ], 422);
        }

        if ($user->role === 'borrower') {
            $installment = $user->installments->where('status', 'due')->first();
            if ($installment !== null) {
                return response()->json([
                    "error" => "Please Pay the due installments first"
                ], 422);
            }
        }

        return response()->json(["OK"]);
    }

    // Withdraw Money
    public function withdraw(Request $request): JsonResponse
    {
        $values = $request->get('values');
        $id = $request->get('id');
        $trxID = $values['trxID'];

        # find the user first
        $user = User::find($id);

        # Deduct the money from the balance
        $user->update([
            'balance' => $user->balance - $values['amount']
        ]);

        // withdraw money
        $user->transactions()->updateOrCreate([
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->mobile_no,
            'amount' => $values['amount'],
            'status' => 'Pending',
            'address' => $user->verification->address,
            'transaction_id' => $trxID,
            'transaction_type' => 'withdraw',
            'currency' => "BDT"
        ]);

        // Saving the data to the transaction Details table
        $current_transaction = Transaction::where('transaction_id', $trxID)
            ->first();

        if ($current_transaction === null) {
            return response()->json([
                "Error" => "Transaction Not Found"
            ], 500);
        }

        $current_transaction->transaction_detail()->updateOrCreate([
            'card_type' => strtoupper($values['method']) . "-" . ucfirst($values['method']),
//            'card_no' => "N/A",
            'bank_tran_id' => uniqid("", true),
//            'error' => "N/A",
            'card_issuer' => ucfirst($values['method']) . " Mobile Banking",
            'card_brand' => "MOBILE BANKING",
            'risk_level' => "0",
            'risk_title' => "Safe"
        ]);

        return response()->json(["OK"]);
    }

    // get single deposit
    public function getSingleTransaction(Request $request, $type, $id) : JsonResponse
    {
        if ($type === 'deposit' || $type === 'withdraw') {
            $transaction = Transaction::where('id', $id)
                ->where('transaction_type', $type)
                ->first();

            $user = $request->user();

            $authorized_user = $transaction->user()->findOrFail($user->id);

            if ($authorized_user === null) {
                return response()->json(["UNAUTHORIZED"], 419);
            }

            return response()->json([
                'transaction' => new TransactionResource($transaction),
                'details' => new TransactionDetailResource($transaction->transaction_detail)
            ]);
        }

        return response()->json([
            "ERROR"
        ], 500);
    }
}
