<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Nexmo\Client;
use Nexmo\Client\Credentials\Basic;

class UtilController extends Controller
{
    // get help
    public function getHelp(Request $request): Exception | JsonResponse
    {
        $values = $request->get('values');

        $name = $values['name'];
        $email = $values['email'];
        $msg = $values['message'];

        try {
            $this->sendAnEmail($name, $email, 'Help', $msg, 'Help Email');
            return response()->json(["OK"]);
        } catch (Exception $exception) {
            return $exception;
        }
    }

    // Sending an Email
    public function sendAnEmail($name, $email, $title, $message, $subject, $action = null): JsonResponse
    {
        $data = [
            'name' => $name,
            'title' => $title,
            'message' => $message,
            'action' => $action,
        ];
        Mail::send('mail', array('data' => $data), function ($message) use ($email, $subject) {
            $message->to($email, 'Grayscale')
                ->subject($subject);
        });
        return response()->json(["OK"]);
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

    public function generateAUniqueTrxId() : string
    {
        $length = config('app.trx_id_length');
        $the_string = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        do {
            $trx_id = substr(str_shuffle($the_string),1,$length);
            $trx_id_from_database = Transaction::where('transaction_id', $trx_id)->first();
        } while($trx_id_from_database !== null);

        return $trx_id;
    }
}
