<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Http\Resources\VerificationResource;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    // redirecting to login
    public function login()
    {
        $url = config('app.frontEndUrl');
        return \Redirect::to($url . '/login');
    }

    // checks for unique Email
    public function uniqueEmail(Request $request)
    {
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return abort(422);
        }
        return response('OK', 200);
    }

    // checks for unique Email excluding id
    public function uniqueEmailExcludingId(Request $request)
    {
        $user = User::where('email', $request->email)->first();
        if (!$user || $user->id === $request->id) {
            return abort(422);
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
}
