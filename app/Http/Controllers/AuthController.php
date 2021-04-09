<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    // register the user
    public function register(Request $request): UserResource|JsonResponse
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

        User::create([
            'email' => $request->get('email'),
            'name' => $request->get('name'),
            'role' => $request->get('role'),
            'verified' => 'unverified',
            'balance' => 0.00,
            'password' => bcrypt($request->get('password'))
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return new UserResource($request->user());
        }

        return response()->json([
            'error' => 'Something went Wrong'
        ], 422);
    }

    // logging the user
    public function login(Request $request): UserResource|JsonResponse
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
    public function logout(Request $request): Response|Application|ResponseFactory
    {
        $request->user()->unreadNotifications->markAsRead();
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return response('OK');
    }
}
