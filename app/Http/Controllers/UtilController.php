<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Nexmo\Client;
use Nexmo\Client\Credentials\Basic;

class UtilController extends Controller
{
    // Sending an Email
    public function sendAnEmail($email, $title, $message, $subject, $action = null)
    {
        # $action will contain name, msg, url
        $user = User::where('email', $email)->first();
        $data = [
            'name' => $user->name,
            'title' => $title,
            'message' => $message,
            'action' => $action,
        ];
        Mail::send('mail', array('data' => $data), function ($message) use ($email, $subject) {
            $message->to($email, 'Grayscale')->subject
            ($subject);
        });
        return response()->json(["OK"], 200);
    }

    // Send an SMS
    public function sendSMS($mobile_no, $msg)
    {
        $basic = new Basic(config('nexmo.api_key'), config('nexmo.api_secret'));
        $client = new Client($basic);

        // As Nexmo doesn't allow other numbers
        return $client->message()->send([
            'to' => '8801779266259',
            'from' => 'Grayscale',
            'text' => 'Grayscale: ' . $msg
        ]);
    }
}
