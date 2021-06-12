<?php

namespace App\Http\Controllers;

use App\Http\Resources\LoanPreferenceResource;
use App\Http\Resources\NotificationResource;
use App\Http\Resources\UserResource;
use App\Http\Resources\VerificationResource;
use App\Models\Installment;
use App\Models\User;
use App\Models\Util;
use App\Notifications\EmailVerifiedNotification;
use App\Notifications\ForgotPasswordNotification;
use App\Notifications\MobileNoVerifiedNotification;
use App\Notifications\PasswordChangedNotification;
use App\Notifications\SentMobileOTPNotification;
use App\Notifications\SentVerifyEmailOTPNotification;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Redirect;

/**
 * Class UserController
 * @package App\Http\Controllers
 */
class UserController extends Controller
{
    /**
     * redirecting to login
     * @return RedirectResponse
     */
    public function login(): RedirectResponse
    {
        $url = config('app.frontEndUrl');
        return Redirect::to($url . '/login');
    }

    public function forgotPassword(Request $request): JsonResponse
    {
        $email = $request->get('email');

        try {
            $user = User::where('email', $email)->first();
            $uniq_id = uniqid('', true);
            $user->util()->update([
                'email_verify_token' => $uniq_id,
            ]);
            $user->notify(new ForgotPasswordNotification($user->name, $user->email, $uniq_id));
            return response()->json(["OK"]);
        } catch (Exception $exception) {
            return response()->json([
                "error" => "Email Not Found"
            ], 422);
        }
    }

    public function verifyForgotPassword(Request $request): JsonResponse
    {
        $password = $request->get('password');
        $email = $request->get('email');
        $token = $request->get('token');

        $user = User::where('email', $email)->first();
        $util = Util::where('email_verify_token', $token)->first();
        if ($util && ($util->updated_at->diffInMinutes() <= 15)) {
            $user->update([
                'password' => bcrypt($password)
            ]);

            $user->notify(new PasswordChangedNotification());
            return response()->json(["OK"]);
        }

        return $this->error(422, "Something Went Wrong");
    }

    /**
     * This function returns an error as JSON Response
     * I used this function through out this controller
     * @param int $code
     * @param string $msg
     * @return JsonResponse
     */
    protected function error($code = 500, $msg = "ERROR"): JsonResponse
    {
        return response()->json(["ERROR" => $msg], $code);
    }

    /**
     * checks for unique Email
     * @param Request $request
     * @return Application|ResponseFactory|JsonResponse|Response
     */
    public function uniqueEmail(Request $request): Response|JsonResponse|Application|ResponseFactory
    {
        $user = User::where('email', $request->get('email'))->first();
        if (!$user) {
            return $this->error(422, "Wrong Credentials");
        }
        return response('OK');
    }

    /**
     * Sends Verification Email with otp
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function sendVerifyEmail(Request $request): JsonResponse
    {
        $email = $request->get('email');
        $user = $request->user();
        $uniq_id = uniqid('', true);
        $otp = random_int(100000, 999999);
        $user->util()->update([
            'email_verify_token' => $uniq_id,
            'email_verify_otp' => $otp
        ]);

//        Notification::route('mail', [$email => $user->name])
        $user->notify(new SentVerifyEmailOTPNotification($user->name, $email, $otp, $uniq_id));
        return response()->json(["OK"]);
    }

    /**
     * Verifies Email through the link And Notifies The User
     * @param $email
     * @param $token
     * @return RedirectResponse|JsonResponse
     */
    public function verifyEmail($email, $token): RedirectResponse|JsonResponse
    {
        $util = Util::where('email_verify_token', $token)->first();

        if ($util === null) {
            return $this->error();
        }

        $user = User::find($util->user_id);
        $user->email = $email;
        $user->save();
        if ($util && ($util->updated_at->diffInMinutes() <= 15)) {
            $user = User::find($util->user_id);
            $user->notify(new EmailVerifiedNotification());
            $url = config('app.frontEndUrl');
            return Redirect::to($url);
        }
        return $this->error(422, "Email Not Verified");
    }

    /**
     * Verifies Email through otp and notifies the user
     * @param Request $request
     * @return JsonResponse
     */
    public function verifyEmailOtp(Request $request): JsonResponse
    {
        $otp = $request->get('otp');
        $email = $request->get('email');
        $util = Util::where('email_verify_otp', $otp)->first();

        if ($util === null) {
            return $this->error();
        }

        $user = User::find($util->user_id);
        $user->email = $email;
        $user->save();
        if ($util && ($util->updated_at->diffInMinutes() <= 15)) {
            $user = User::find($util->user_id);
            $user->email_verified_at = Carbon::now();
            $user->save();
            $user->notify(new EmailVerifiedNotification());
            return response()->json(["Ok"]);
        }
        return $this->error();
    }

    /**
     * Send an sms to the user
     * @param Request $request
     * @return Exception|JsonResponse
     * @throws Exception
     */
    public function sendMobileOTP(Request $request): JsonResponse|Exception
    {
        $mobile_no = $request->get('mobileNo');
        $user = $request->user();

        $otp = random_int(100000, 999999);
        $user->util()->updateOrCreate(
            [
                'user_id' => $user->id,
            ],
            [
                'mobile_no_verify_otp' => $otp,
            ]
        );

        $user->update([
            'mobile_no' => 880 . $mobile_no,
        ]);

        try {
            // uncomment this lines to send messages
//            $util = new UtilController();
//            $util->sendSMS('880' . $mobile_no, 'Your OTP is ' . $otp);
            $user->notify(new SentMobileOTPNotification($otp));
            return response()->json(["OK"]);
        } catch (Exception) {
            return $this->error(500, "Error Sending SMS");
        }
    }

    /**
     * Verify Mobile No
     * @param Request $request
     * @return JsonResponse
     */
    public function verifyMobileNo(Request $request): JsonResponse
    {
        $otp = $request->get('otp');
        $util = Util::where('mobile_no_verify_otp', $otp)->first();

        if ($util === null) {
            return $this->error();
        }

        $user = User::find($util->user_id);
        $user->mobile_no_verified_at = Carbon::now();
        $user->save();
        if ($util && ($util->updated_at->diffInMinutes() <= 15)) {
            $user->notify(new MobileNoVerifiedNotification());
            return response()->json(["Ok"]);
        }
        return $this->error(404, "Otp Didn't Match");
    }

    /**
     * checking whether email and mobile no are verified
     * @param Request $request
     * @return JsonResponse
     */
    public function isContactVerified(Request $request): JsonResponse
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
        ]);
    }

    /**
     * Checks unique email without current user id
     * @param Request $request
     * @return Application|ResponseFactory|Response
     */
    public function uniqueEmailExcludingId(Request $request): Response|Application|ResponseFactory
    {
        $user = User::where('email', $request->get('email'))->first();
        if ($user && $user->id !== $request->get('id')) {
            return response('ERROR', 422);
        }
        return response('OK');
    }

    // Fetching recent data

    /**
     * Self explanatory
     * @param Request $request
     * @return JsonResponse
     */
    public function userWithVerificationData(Request $request): JsonResponse
    {
        $user = $request->user();
        return response()->json([
            'user' => new UserResource($user),
            'verification' => new VerificationResource($user->verification)
        ]);
    }

    /**
     * fetches the dashboard data
     * @param Request $request
     * @return JsonResponse
     */
    public function dashboardData(Request $request): JsonResponse
    {
        $user = $request->user();
        $loans = $user->loans;
        $transactions = $user->transactions;
        $installments = Installment::where('user_id', $user->id)
            ->where('status', 'due')
            ->get();

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
            'withdrawals' => $withdrawals,
            'dueInstallments' => count($installments),
        ]);
    }

    public function getDueBalance(Request $request): JsonResponse
    {
        $user = $request->user();
        $installments= $user->installments->where('status', 'due');
        $due_amount = 0.00;
        foreach ($installments as $installment) {
            $due_amount += $installment->total_amount;
        }
        return response()->json([
            'amount' => $due_amount,
        ]);
    }

    /**
     * update personal settings
     * @param Request $request
     * @param $info
     * @return JsonResponse
     */
    public function updatePersonalSettings(Request $request, $info): JsonResponse
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

            return response()->json(["OK"]);
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

            return response()->json(["OK"]);
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

            return response()->json(["OK"]);
        }
        return $this->error(422);
    }


    /**
     * update account settings
     * @param Request $request
     * @param $info
     * @return JsonResponse
     */
    public function updateAccountSettings(Request $request, $info): JsonResponse
    {
        $user = User::find($request->user()->id);
        if ($info === 'language') {
            $user->update([
                'language' => $request->get('language'),
            ]);
            return response()->json(["OK"]);
        }
        if ($info === 'close') {
            try {
                if ($user->loans->where('loan_mode', 'ongoing')) {
                    return response()->json([
                        "ERROR"
                    ], 422);
                }
                if ((int)$user->balance > 0) {
                    return response()->json([
                        "ERROR"
                    ], 422);
                }
                $user->delete();
                return response()->json(["OK"]);
            } catch (Exception) {
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

                return response()->json(["Ok"]);
            }
        }

        return $this->error(422);
    }


    /**
     * Get the first 3 Notifications
     * @param Request $request
     * @return JsonResponse
     */
    public function getNotifications(Request $request): JsonResponse
    {
        $user = User::findOrFail($request->user()->id);

        $notifications = $user->notifications()
            ->orderBy('read_at')
            ->orderBy('created_at', 'desc')
            ->skip(0)->take(3)
            ->get();

//        $notifications = $user->unreadNotifications()
//            ->orderBy('updated_at')
//            ->skip(0)->take(3)->get();

        return response()->json([
            'notifications' => NotificationResource::collection($notifications),
            'count' => $user->unreadNotifications()->count(),
        ]);
    }

    /**
     * get all notifications
     * @param Request $request
     * @return JsonResponse
     */
    public function getAllNotifications(Request $request): JsonResponse
    {
        $user = User::find($request->user()->id);
        $notifications = $user->notifications()
            ->orderBy('read_at')
            ->get();
        return response()->json([
            'notifications' => NotificationResource::collection($notifications),
        ]);
    }

    /**
     *  Mark First Three Notifications as Notified
     * @param Request $request
     * @return JsonResponse
     */
    public function markThreeAsNotified(Request $request): JsonResponse
    {
        $ids = $request->get('notificationIds');
        $user = User::findOrFail($request->user()->id);

        foreach ($ids as $id) {
            $user->notifications->where('id', $id)->markAsRead();
        }

        return response()->json(["OK"]);
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

    /**
     * Deletes a notification
     * @param Request $request
     * @param $id
     * @return mixed
     */
    public function deleteNotification(Request $request, $id): mixed
    {
        return $request->user()->notifications()->where('id', $id)->delete();
    }

    /**
     * Saving Loan Preferences
     * @param Request $request
     * @return JsonResponse
     */
    public function saveLoanPreferences(Request $request): JsonResponse
    {
        $user = $request->user();
        $amount = $request->get('maximumDistributedAmount');
        $autoPayments = $request->get('autoPayments');

        if ($amount !== null) {
            $user->loan_preference()->update([
                'maximum_distributed_amount' => $amount,
            ]);
            return response()->json(["OK"]);
        }

        if ($autoPayments !== null) {
            $user->loan_preference()->update([
                'auto_payments' => $autoPayments,
            ]);
            return response()->json(["OK"]);
        }

        return response()->json(["ERROR"], 500);
    }

    /**
     * Get Loan Preferences
     * @param Request $request
     * @return JsonResponse
     */
    public function getLoanPreferences(Request $request): JsonResponse
    {
        $user = $request->user();
        return response()->json([
            'loanPreference' => new LoanPreferenceResource($user->loan_preference),
        ]);
    }

    // get default interest rate
    public function getDefaultInterestRate(): JsonResponse
    {
        $admin = User::where('role', 'admin')->first();
        if ($admin === null) {
            return response()->json(["Error"], 500);
        }
        return response()->json([
            'interestRate' => $admin->administration->default_interest_rate,
        ]);
    }
}
