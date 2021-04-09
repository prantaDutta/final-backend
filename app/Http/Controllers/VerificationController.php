<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    // verify user
    public function verifyUser(Request $request)
    {
        $values = $request->get('values');
        // saving user data
        $user = User::where('email', $values['email'])->first();
        if ($user === null) {
            return abort(500);
        }
        $user->verified = 'pending';
        $user->save();

        $user->verification()->updateOrCreate(
            [
                'user_id' => $values['id'],
            ],
            [
                'date_of_birth' => Carbon::parse($values['dateOfBirth'])->format('Y-m-d'),
                'gender' => $values['gender'],
                'address' => $values['address'],
                'borrower_type' => $user->role === 'lender' ? null : $values['borrowerType'],
                'division' => $values['division'],
                'zila' => $values['zila'],
                'zip_code' => $values['zip_code'],
                'verification_photos' => json_encode($values['verificationPhotos'], JSON_THROW_ON_ERROR),
            ]);

        return new UserResource($user);
    }
}
