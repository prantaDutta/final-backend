<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Verification;
use Illuminate\Http\Request;

class UserController extends Controller
{
    // checks for unique Email
    public function uniqueEmail(Request $request) {
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return abort(422);
        }
        return response('OK', 200);
    }

    // checks for unique Email excluding id
    public function uniqueEmailExcludingId(Request $request) {
        $user = User::where('email', $request->email)->first();
        if (!$user || $user->id === $request->id) {
            return abort(422);
        }
        return response('OK', 200);
    }

    // verify user
    public function verifyUser(Request $request) {
        $values = $request->get('values');
        // saving user data
        $user = User::find($values['id']);
        $user->name = $values['name'];
        $user->email = $values['email'];
        $user->verified = 'pending';
        $user->save();
        // saving verification Data
        $verification = new Verification();
        $verification->user_id = $values['id'];
        $verification->address = $values['address'];
        $verification->borrower_type = $user->role === 'lender' ? null : $values['borrowerType'];
        $verification->date_of_birth = ($values['dateOfBirth']);
        $verification->gender = $values['gender'];
        $verification->mobile_no = $values['mobileNo'];
        $verification->zila = $values['zila'];
        $verification->division = $values['division'];
        $verification->zip_code = $values['zip_code'];
        $verification->verification_photos = json_encode($values['verificationPhotos']);

        return $user->verification()->save($verification);
    }
}
