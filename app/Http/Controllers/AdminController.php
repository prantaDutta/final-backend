<?php

namespace App\Http\Controllers;

use App\Http\Resources\InstallmentResource;
use App\Http\Resources\LoanResource;
use App\Http\Resources\TransactionDetailResource;
use App\Http\Resources\TransactionResource;
use App\Http\Resources\UserResource;
use App\Http\Resources\VerificationResource;
use App\Models\Administration;
use App\Models\Installment;
use App\Models\Loan;
use App\Models\Transaction;
use App\Models\User;
use App\Notifications\AccountVerificationFailed;
use App\Notifications\AccountVerificationSuccessful;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    // getting all the verification requests and Users
    public function getUsers($verified): JsonResponse
    {
        // $verified can be all, verified, pending and unverified
        if ($verified === 'all') {
            $user = User::all();
        } else {
            $user = User::where('verified', $verified)->get();
        }
        if ($user) {
            return response()->json([
                'users' => UserResource::collection($user)
            ]);
        }
        return response()->json(['users' => []]);
    }

    // getting single user
    /**
     * @param $id
     * @return JsonResponse
     * @throws \JsonException
     */
    public function getSingleUser($id): JsonResponse
    {
        $user = User::findOrFail($id);

        $verification_photos = [];
        $bankStatements = '{}';

        // Separating the verification photos from the verification data
        if ($user->verification !== null) {
            $verification_photos = json_decode($user->verification->verification_photos, false, 512, JSON_THROW_ON_ERROR);

            // exploding the bankStatements into an array
            $explode_bankAccountStatements = explode('#', $verification_photos->bankAccountStatements);

            // last element is an empty string, so we had to delete it
            array_pop($explode_bankAccountStatements);

            // generating a string to decode to an object
            $bankStatements = '{';
            foreach ($explode_bankAccountStatements as $key => $explode_bankAccountStatement) {
                $bankStatements .= '"bankStatement ' . $key + 1 . '": "' . $explode_bankAccountStatement . '"';
                // we should not include the trailing comma for the last element
                if ($key !== count($explode_bankAccountStatements) - 1) {
                    $bankStatements .= ',';
                }
            }
            $bankStatements .= '}';

            // deleting bankAccountStatements from the verification photos
            // as we are calculating it differently
            unset($verification_photos->bankAccountStatements);
        }

        $loans = $user->loans;
        $installments = $user->installments;
        $transactions = $user->transactions;

        $user_data = '{ "total loans": "' . count($loans) . '", "total installments": "' . count($installments) . '", "total transactions": "' . count($transactions) . '"}';

        return response()->json([
            'user' => new UserResource($user),
            'verification' => $user->verification !== null ? new VerificationResource($user->verification) : null,
            'verificationPhotos' => $verification_photos,
            'bankStatements' => json_decode($bankStatements, false, 512, JSON_THROW_ON_ERROR),
            'user_data' => json_decode($user_data, false, 512, JSON_THROW_ON_ERROR),
        ]);
    }

    // making an account verified
    public function makeAccountVerified($ifVerified, $id): JsonResponse
    {
        $user = User::findOrFail($id);
        if ($ifVerified === 'verified') {
            $user->update([
                'verified' => 'verified',
            ]);
            $user->notify(new AccountVerificationSuccessful());
            return response()->json(["OK"]);
        }

        if ($ifVerified === 'unverified') {
            $user->update([
                'verified' => 'unverified',
            ]);
            $user->notify(new AccountVerificationFailed());
            return response()->json(["OK"]);
        }

        return response()->json([
            'error' => 'Something Went Wrong',
        ], 500);
    }

    // get loans, installments and transactions count for one user
    public function getThingsForOneUser($type, $id): JsonResponse
    {
        $user = User::findOrFail($id);

        if ($type === 'loans') {
            return response()->json([
                'loans' => LoanResource::collection($user->loans),
                'name' => $user->name,
            ]);
        }

        if ($type === 'transactions') {
            return response()->json([
                'transactions' => TransactionResource::collection($user->transactions),
                'name' => $user->name,
            ]);
        }

        if ($type === 'user-installments') {
            return response()->json([
                'installments' => InstallmentResource::collection($user->installments),
                'name' => $user->name,
            ]);
        }

        return response()->json(["ERROR"], 500);
    }

    // get all loans
    public function getAllLoans($requestType): JsonResponse
    {
        if ($requestType === 'all') {
            $loans = Loan::with('users')
                ->latest()->get();
        } else {
            $loans = Loan::where('loan_mode', $requestType)
                ->with('users')->latest()->get();
        }
        return response()->json([
            'loans' => LoanResource::collection($loans)
        ]);
    }

    # Getting one single loan details
    /**
     * @throws \JsonException
     */
    public function getSingleLoan($id) : JsonResponse
    {
        $loan = Loan::findOrFail($id);

        $the_borrower = $loan->users()
            ->where('role', 'borrower')->get();

        $the_lenders = $loan->users()
            ->where('role', 'lender')->get();

        $installments = $loan->installments;

        $installment_data = '{ "total installments": "' . count($installments) . '"}';

        [$lender_data, $lender_ids] = $this->getUserDetailsWithPivotAmount($the_lenders);

        [$borrower_data, $borrower_id] = $this->getUserDetailsWithPivotAmount($the_borrower);

        return response()->json([
            'loan' => new LoanResource($loan),
            'theBorrower' => $borrower_data,
            'theLenders' => $lender_data,
            'borrowerId' => $borrower_id,
            'lenderIds' => $lender_ids,
            'totalInstallments' => json_decode($installment_data),
        ]);
    }

    // Get All Lenders and borrowers with pivot amount
    protected function getUserDetailsWithPivotAmount($users): array
    {
        $user_data = '{';
        $user_ids = [];
        foreach ($users as $key => $user) {
            $user_ids[] = $user->id;
            $user_data .= '"' . $user->name . '": "' . $user->pivot->amount . '"';
            if ($key !== count($users) - 1) {
                $user_data .= ',';
            }
        }

        $user_data .= '}';

        $user_json_data = json_decode($user_data, false, 512, JSON_THROW_ON_ERROR);

        return array($user_json_data, $user_ids);
    }

    // get all loan installments
    public function getLoanInstallments($id): JsonResponse
    {
        $loan = Loan::findOrFail($id);

        return response()->json([
            'installments' => InstallmentResource::collection($loan->installments),
            'id' => $loan->id,
        ]);
    }

    // Get Dashboard data
    public function dashboardData(): JsonResponse
    {
        $verification_requests = User::where('verified', 'pending')->count();
        $loan_requests = Loan::where('loan_mode', 'processing')->count();
        $withdrawal_requests = Transaction::where('transaction_type', 'withdraw')
            ->where('status', 'pending')->count();

        return response()->json([
            'verifications' => $verification_requests,
            'loans' => $loan_requests,
            'withdrawals' => $withdrawal_requests
        ]);
    }

   // Get all, pending etc. Transactions
    public function getTransactions($type, $status): JsonResponse
    {
        if ($type === 'all' && $status === 'all') {
            $requests = Transaction::all();
        } else if ($type === 'all' && $status !== 'all') {
            $requests = Transaction::where('status', $status)->latest()->get();
        } else if ($type !== 'all' && $status === 'all') {
            $requests = Transaction::where('transaction_type', $type)
                ->latest()->get();
        } else {
            $requests = Transaction::where('transaction_type', $type)
                ->where('status', $status)->latest()->get();
        }

        return response()->json([
            'requests' => TransactionResource::collection($requests)
        ]);
    }

    // Get Single Withdrawal Request
    public function getSingleTransactions($id): JsonResponse
    {
        $transaction = Transaction::find($id);
        return response()->json([
            'transaction' => new TransactionResource($transaction),
            'transactionDetail' => new TransactionDetailResource($transaction->transaction_detail),
            'user' => new UserResource($transaction->user),
        ]);
    }

    // marking withdrawal request as Completed or Failed
    public function markTransaction($type, $id): bool
    {
        $transaction = Transaction::find($id);
        return $transaction->update([
            'status' => $type
        ]);
    }

    // Get All Installments
    public function getAllInstallments($status): JsonResponse
    {
        if ($status !== 'all') {
            $installments = Installment::where('status', $status)->get();
        } else {
            $installments = Installment::all();
        }

        return response()->json([
            'installments' => InstallmentResource::collection($installments),
        ]);
    }

    // Get Penalty Data
    public function getPenaltyData(Request $request): JsonResponse
    {
        $user = $request->user();
        return response()->json([
            'penaltyData' => $user->administration->penalty_data,
        ]);
    }

    // update penalty data
    public function updatePenaltyData(Request $request): JsonResponse
    {
        $user = $request->user();
        if ($user->role !== 'admin') {
            return response()->json(["ERROR"], 500);
        }

        $penalty_data = $request->get('penaltyData');

        $administration = Administration::first();

        if ($administration === null) {
            return response()->json(["ERROR"], 500);
        }

        $administration->update([
            'penalty_data' => $penalty_data,
        ]);

        return response()->json(['OK']);
    }

    // get default interest rate
    public function getInterestRate(Request $request): JsonResponse
    {
        $user = $request->user();
        return response()->json([
            'interestRate' => $user->administration->default_interest_rate,
        ]);
    }

    // update interest rate
    public function updateInterestRate(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->role !== 'admin') {
            return response()->json(["ERROR"], 500);
        }

        $interest_rate = $request->get('interestRate');

        $administration = Administration::first();

        if ($administration === null) {
            return response()->json(["ERROR"], 500);
        }

        $administration->update([
            'default_interest_rate' => $interest_rate,
        ]);

        return response()->json(['OK']);
    }
}
