<?php

namespace App\Http\Controllers;

use App\Http\Resources\TransactionResource;
use App\Http\Resources\UserResource;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    // get all deposits
    public function getAllDeposits(Request $request)
    {
        $id = $request->user()->id;
        $user = User::find($id);
        $deposits = $user->transactions()
            ->where('user_id', $id)
            ->where('transaction_type','deposit')->get();

        if ($deposits) {
            return response()->json([
                'user' => new UserResource($user),
                'transactions' => TransactionResource::collection($deposits)
            ], 200);
        }

        return response()->json([],200);
    }

    // get all withdrawals
    public function getAllWithdrawals(Request $request)
    {
        $id = $request->user()->id;
        $user = User::find($id);
        $withdrawals = $user->transactions()
            ->where('user_id', $id)
            ->where('transaction_type','withdraw')
            ->get();

        if ($withdrawals) {
            return response()->json([
                'user' => new UserResource($user),
                'transactions' => TransactionResource::collection($withdrawals)
            ], 200);
        }

        return response()->json([],200);
    }

    // Withdraw Money
    public function withdraw(Request $request)
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
            'phone' => '880' . $user->verification->mobile_no,
            'amount' => $values['amount'],
            'status' => 'Pending',
            'address' => $user->verification->address,
            'transaction_id' => $trxID,
            'transaction_type' => 'withdraw',
            'currency' => "BDT"
        ]);

        // Saving the data to the transaction Details table
        $current_transaction = Transaction::where('transaction_id',$trxID)->first();
        $current_transaction->transaction_detail()->updateOrCreate([
            'card_type' => strtoupper($values['method'])."-".ucfirst($values['method']),
//            'card_no' => "N/A",
            'bank_tran_id' => uniqid("",true),
//            'error' => "N/A",
            'card_issuer' => ucfirst($values['method']) . " Mobile Banking",
            'card_brand' => "MOBILE BANKING",
            'risk_level' => "0",
            'risk_title' => "Safe"
        ]);

        return response()->json(["OK"],200);
    }
}
