<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use App\Notifications\VerifyEmail;
use App\Notifications\WelcomeMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    // register the user
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
            'role' => [
                'required',
                Rule::in(['lender', 'borrower']),
            ],
        ]);

        $user = User::create([
            'email' => $request->email,
            'name' => $request->name,
            'role' => $request->role,
            'verified' => 'unverified',
            'balance' => 0.00,
            'password' => Hash::make($request->password)
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $uniq_id = uniqid('', true);
            $otp = mt_rand(100000, 999999);
            $user->util()->create([
                'email_verify_token' => $uniq_id,
                'email_verify_otp' => $otp
            ]);
            $user->notify(new WelcomeMessage());
            $user->notify(new VerifyEmail($user->name, $user->email, $otp, $uniq_id));
            return new UserResource($request->user());
        }

        return response()->json([
            'error' => 'Something went Wrong'
        ], 422);
    }

    // logging the user
    public function login(Request $request)
    {
//        dd($request);
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6'
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            return new UserResource($request->user());
        }

        return response()->json([
            'email' => 'The provided credentials do not match our records.',
        ], 422);
    }

    // logging user out
    public function logout(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return response('OK', 200);
    }
}
