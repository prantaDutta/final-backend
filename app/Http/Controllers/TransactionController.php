<?php

namespace App\Http\Controllers;

use App\Http\Resources\TransactionResource;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function getAllDeposits($id)
    {
        $user = User::find($id);
        $deposits = $user->transactions()
            ->where('user_id', $id)
            ->where('transaction_type','deposit');

        if ($deposits) {
            return response()->json([
                'user' => new UserResource($user),
                'transactions' => TransactionResource::collection($user->transactions)
            ], 200);
        }

        return response()->json([],200);
    }
}
