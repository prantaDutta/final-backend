<?php

namespace App\Http\Controllers;

use App\Http\Resources\LoanPreferenceResource;
use App\Http\Resources\LoanResource;
use App\Http\Resources\NotificationResource;
use App\Http\Resources\TransactionResource;
use App\Http\Resources\UserResource;
use App\Http\Resources\VerificationResource;
use App\Models\User;
use App\Models\Util;
use App\Notifications\EmailVerified;
use App\Notifications\MobileNoVerified;
use App\Notifications\SendMobileOTP;
use App\Notifications\VerifyEmail;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Notification;
use Redirect;

class UserController extends Controller
{
    // redirecting to login
    public function login()
    {
        $url = config('app.frontEndUrl');
        return Redirect::to($url . '/login');
    }

    // checks for unique Email
    public function uniqueEmail(Request $request)
    {
        $user = User::where('email', $request->get('email'))->first();
        if (!$user) {
            return abort(422);
        }
        return response('OK', 200);
    }

    // Sending Email
//    public function sendEmail(Request $request)
//    {
//
//        $actions = new EmailActionButton('Verify', '/api/user/verify-email');
//        $utility = new UtilController();
//        $utility->sendAnEmail(
//            $request->get('email'),
//            "Verify Your Email",
//            "Please Click the button to verify your email",
//            "Verify Email",
//            $actions
//        );
//
//        return response()->json(["OK"], 200);
//    }

    // get current user's mobile no
    public function getMobileNo(Request $request)
    {
        return $request->user()->verification->mobile_no;
    }

    // Sending Email
    public function sendVerifyEmail(Request $request)
    {
        $email = $request->get('email');
        $user = $request->user();
        $uniq_id = uniqid('', true);
        $otp = mt_rand(100000, 999999);
        $user->util()->update([
            'email_verify_token' => $uniq_id,
            'email_verify_otp' => $otp
        ]);
        Notification::route('mail', [$email => $user->name])
            ->notify(new VerifyEmail($user->name, $email, $otp, $uniq_id));
        return response()->json(["OK"], 200);
    }

    // Verifying the email
    public function verifyEmail($email, $token)
    {
        $util = Util::where('email_verify_token', $token)->first();
        $user = User::find($util->user_id);
        $user->email = $email;
        $user->save();
        if ($util && ($util->updated_at->diffInMinutes() <= 15)) {
            $user = User::find($util->user_id);
            $user->notify(new EmailVerified());
            $url = config('app.frontEndUrl');
            return Redirect::to($url);
        }
        return abort(404);
    }

    //verify email with otp
    public function verifyEmailOtp(Request $request)
    {
        $otp = $request->get('otp');
        $email = $request->get('email');
        $util = Util::where('email_verify_otp', $otp)->first();
        $user = User::find($util->user_id);
        $user->email = $email;
        $user->save();
//        if ($util && ($util->updated_at->diffInMinutes() <= 15)) {
        if ($util) {
            $user = User::find($util->user_id);
            $user->email_verified_at = Carbon::now();
            $user->save();
            $user->notify(new EmailVerified());
            return response()->json(["Ok"], 200);
        }
        return abort(404);
    }

    // Sending OTP to mobile no
    public function sendMobileOTP(Request $request)
    {
        $mobile_no = $request->get('mobileNo');
        $user = $request->user();

        $user->mobile_no = 880 . $mobile_no;
        $user->save();

        try {
            // uncomment this lines to send messages
//            $util = new UtilController();
//            $util->sendSMS('880' . $mobile_no, 'Your OTP is ' . $otp);
            $user->notify(new SendMobileOTP());
            return response()->json(["OK"], 200);
        } catch (Exception $e) {
            return $e;
        }
    }

    // Verifying Mobile No
    public function verifyMobileNo(Request $request)
    {
        $otp = $request->get('otp');
        $util = Util::where('mobile_no_verify_otp', $otp)->first();
        $user = User::find($util->user_id);
        $user->mobile_no_verified_at = Carbon::now();
        $user->save();
        if ($util && ($util->updated_at->diffInMinutes() <= 15)) {
            $user->notify(new MobileNoVerified());
            return response()->json(["Ok"], 200);
        }
        return abort(404);
    }

    // Is Contact Verified
    public function isContactVerified(Request $request)
    {
        $user = $request->user();
        $email_verified = false;
        if ($user->email_verified_at !== null) {
            $email_verified = true;
        }
        $mobile_no_verified = false;
        if ($user->mobile_no_verified_at !== null) {
            $mobile_no_verified = true;
        }
        return response()->json([
            'email' => $email_verified,
            'mobileNo' => $mobile_no_verified,
        ], 200);
    }

    // checks for unique Email excluding id
    public function uniqueEmailExcludingId(Request $request)
    {
        $user = User::where('email', $request->get('email'))->first();
        if ($user && $user->id !== $request->get('id')) {
            return response('ERROR', 422);
        }
        return response('OK', 200);
    }

    // getting the current user with verification data
    public function userWithVerificationData(Request $request)
    {
        $user = $request->user();
        return response()->json([
            'user' => new UserResource($user),
            'verification' => new VerificationResource($user->verification)
        ]);
    }

    // Fetching recent data
    public function recentData(Request $request)
    {
        // Get First Two Rows
        $loans = $request->user()->loans->skip(0)->take(2);
        $transactions = $request->user()->transactions->skip(0)->take(2);
        return response()->json([
            'loans' => LoanResource::collection($loans),
            'transactions' => TransactionResource::collection($transactions)
        ]);
    }

    // fetching Alternate Dashboard Data
    public function dashboardData(Request $request)
    {
        $loans = $request->user()->loans;
        $transactions = $request->user()->transactions;

        $ongoingLoans = $loans->where('loan_mode', 'ongoing')->count();
        $processingLoans = $loans->where('loan_mode', 'processing')->count();
        $finishedLoans = $loans->where('loan_mode', 'finished')->count();

        $deposits = $transactions->where('transaction_type', 'deposit')->count();
        $withdrawals = $transactions->where('transaction_type', 'withdraw')->count();
        return response()->json([
            'ongoing' => $ongoingLoans,
            'processing' => $processingLoans,
            'finished' => $finishedLoans,
            'deposits' => $deposits,
            'withdrawals' => $withdrawals
        ]);
    }

    // update Personal Settings
    public function updatePersonalSettings(Request $request, $info)
    {
        // Finding authenticated user
        $user = User::find($request->user()->id);

        // if address field is modified
        if ($info === 'address') {
            // Validating Address Fields
            $request->validate([
                'address' => 'required|string',
                'division' => 'required|string',
                'zila' => 'required|string',
                'zipCode' => 'required|numeric'
            ]);

            // updating the database
            $user->verification->update([
                'address' => $request->get('address'),
                'division' => $request->get('division'),
                'zila' => $request->get('zila'),
                'zip_code' => $request->get('zipCode')
            ]);

            return response()->json(["OK"], 200);
        }

        // if mobile field is modified
        if ($info === 'mobile') {
            // Validating Mobile No
            $request->validate([
                'mobileNo' => 'required|numeric|digits:10',
            ]);

            // updating the database
            $user->verification->update([
                'mobile_no' => $request->get('mobileNo'),
            ]);

            return response()->json(["OK"], 200);
        }

        // if email field is modified
        if ($info === 'email') {
            // Validating Email
            $request->validate([
                'email' => [
                    'required',
                    Rule::unique('users')->ignore($user->id),
                ],
            ]);

            // updating the database
            $user->update([
                'email' => $request->get('email'),
            ]);

            return response()->json(["OK"], 200);
        }
        return abort(422);
    }

    // update account Settings
    public function updateAccountSettings(Request $request, $info)
    {
        $user = User::find($request->user()->id);
        if ($info === 'language') {
            $user->update([
                'language' => $request->get('language'),
            ]);
            return response()->json(["OK"], 200);
        }
        if ($info === 'close') {
            try {
                $user->delete();
                return response()->json(["OK"], 200);
            } catch (Exception $e) {
                return response()->json(["Something Went Wrong"], 500);
            }
        }
        if ($info === 'password') {
            $request->validate([
                'currentPassword' => 'required|string|min:8',
                'newPassword' => 'required|string|min:8',
                'password_confirmation' => 'required|confirmed'
            ]);

            if (Hash::check($request->get('currentPassword'), $user->password)) {
                $user->update([
                    'password' => Hash::make($request->get('newPassword'))
                ]);

                return response()->json(["Ok"], 200);
            }
        }

        return abort(422);
    }

    // Get dashboard Notifications
    public function getNotifications(Request $request)
    {
        $user = User::find($request->user()->id);
        $notifications = $user->notifications()
            ->orderBy('updated_at')
            ->skip(0)->take(3)->get();
        return response()->json([
            'notifications' => NotificationResource::collection($notifications),
            'count' => $user->unreadNotifications()->count()
        ]);
    }

    // Get All Notifications
    public function getAllNotifications(Request $request)
    {
        $user = User::find($request->user()->id);
        $notifications = $user->notifications()
            ->orderBy('read_at')
            ->get();
        return response()->json([
            'notifications' => NotificationResource::collection($notifications),
        ]);
    }

    // Delete Notification
    public function deleteNotification(Request $request, $id)
    {
        return $request->user()->notifications()->where('id', $id)->delete();
    }

//    # Get Loan Preferences
//    public function getLoanPreferences(Request $request)
//    {
//        $user = $request->user();
////        return $user->loan_preference;
//        return response()->json([
//            'preferences' => new LoanPreferenceResource($user->loan_preference)
//        ], 200);
//    }
//
    # Saving Loan Preferences
    public function saveLoanPreferences(Request $request)
    {
        $distributed_amounts = implode( ', ', $request->get('distributedArray'));
        $user = $request->user();
        $user->loan_preference()->update([
            'distributed_amounts' => $distributed_amounts,
        ]);
        return response()->json(["OK"], 200);
    }
}
