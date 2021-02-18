<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Verification;
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
        $user->name = $values['name'];
        $user->email = $values['email'];
        $user->verified = 'pending';
        $user->save();
        // saving verification Data
        $verification = new Verification();
        $verification->user_id = $values['id'];
        $verification->address = $values['address'];
        $verification->borrower_type = $user->role === 'lender' ? null : $values['borrowerType'];
        $verification->date_of_birth = Carbon::parse($values['dateOfBirth'])->format('Y-m-d');
        $verification->gender = $values['gender'];
        $verification->mobile_no = $values['mobileNo'];
        $verification->zila = $values['zila'];
        $verification->division = $values['division'];
        $verification->zip_code = $values['zip_code'];
        $verification->verification_photos = json_encode($values['verificationPhotos']);

        $user->verification()->save($verification);

        return response('OK', 200);
    }
}
