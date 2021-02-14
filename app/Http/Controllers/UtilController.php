<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

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
        Mail::send('mail', array('data' => $data), function($message) use($email, $subject){
            $message->to($email, 'Grayscale')->subject
            ($subject);
        });
        return response()->json(["OK"], 200);
    }
}
