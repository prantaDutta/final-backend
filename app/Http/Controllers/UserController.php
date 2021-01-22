<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
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
}
