<?php

namespace App\Http\Controllers;

use App\Library\SslCommerz\SslCommerzNotification;
use App\Models\Transaction;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Redirect;

class SslCommerzPaymentController extends Controller
{

    public function exampleEasyCheckout(Request $request)
    {
        $user = $request->user();
        $divisions = file_get_contents((public_path() . '/jsons/divisions.json'));
        $zilas = file_get_contents((public_path() . '/jsons/zilas.json'));
        return view('exampleEasyCheckout')
            ->with('user', $user)
            ->with('divisions', $divisions)
            ->with('zilas', $zilas);
    }

    public function exampleHostedCheckout()
    {
        return view('exampleHosted');
    }

//    public function index(Request $request)
//    {
//        # Here you have to receive all the order data to initate the payment.
//        # Let's say, your oder transaction informations are saving in a table called "transactions"
//        # In "transactions" table, order unique identity is "transaction_id". "status" field contain status of the transaction, "amount" is the order amount to be paid and "currency" is for storing Site Currency which will be checked with paid currency.
//
//        $post_data = array();
//        $post_data['total_amount'] = '10'; # You cant not pay less than 10
//        $post_data['currency'] = "BDT";
//        $post_data['tran_id'] = uniqid('', true); // tran_id must be unique
//
//        # CUSTOMER INFORMATION
//        $post_data['cus_name'] = 'Customer Name';
//        $post_data['cus_email'] = 'customer@mail.com';
//        $post_data['cus_add1'] = 'Customer Address';
//        $post_data['cus_add2'] = "";
//        $post_data['cus_city'] = "";
//        $post_data['cus_state'] = "";
//        $post_data['cus_postcode'] = "";
//        $post_data['cus_country'] = "Bangladesh";
//        $post_data['cus_phone'] = '8801XXXXXXXXX';
//        $post_data['cus_fax'] = "";
//
//        # SHIPMENT INFORMATION
//        $post_data['ship_name'] = "Store Test";
//        $post_data['ship_add1'] = "Dhaka";
//        $post_data['ship_add2'] = "Dhaka";
//        $post_data['ship_city'] = "Dhaka";
//        $post_data['ship_state'] = "Dhaka";
//        $post_data['ship_postcode'] = "1000";
//        $post_data['ship_phone'] = "";
//        $post_data['ship_country'] = "Bangladesh";
//
//        $post_data['shipping_method'] = "NO";
//        $post_data['product_name'] = "Computer";
//        $post_data['product_category'] = "Goods";
//        $post_data['product_profile'] = "physical-goods";
//
//        # OPTIONAL PARAMETERS
//        $post_data['value_a'] = "ref001";
//        $post_data['value_b'] = "ref002";
//        $post_data['value_c'] = "ref003";
//        $post_data['value_d'] = "ref004";
//
//        #Before  going to initiate the payment order status need to insert or update as Pending.
//        $update_product = DB::table('transactions')
//            ->where('transaction_id', $post_data['tran_id'])
//            ->updateOrInsert([
//                'name' => $post_data['cus_name'],
//                'email' => $post_data['cus_email'],
//                'phone' => $post_data['cus_phone'],
//                'amount' => $post_data['total_amount'],
//                'status' => 'Pending',
//                'address' => $post_data['cus_add1'],
//                'transaction_id' => $post_data['tran_id'],
//                'currency' => $post_data['currency']
//            ]);
//
//        $sslc = new SslCommerzNotification();
//        # initiate(Transaction Data , false: Redirect to SSLCOMMERZ gateway/ true: Show all the Payement gateway here )
//        $payment_options = $sslc->makePayment($post_data, 'hosted');
//
//        if (!is_array($payment_options)) {
//            print_r($payment_options);
//            $payment_options = array();
//        }
//
//    }

    public function payViaAjax(Request $request)
    {
        # Here you have to receive all the order data to initiate the payment.
        # Lets your oder transaction information are saving in a table called "transactions"
        # In transactions table order uniq identity is "transaction_id","status" field contain status of the transaction, "amount" is the order amount to be paid and "currency" is for storing Site Currency which will be checked with paid currency.
        $request_data = json_decode($request->get('cart_json'), true);

        Validator::make($request_data, [
            'cus_name' => 'required',
            'cus_email' => 'required',
            'cus_addr1' => 'required',
            'cus_phone' => 'required',
            'amount' => 'required'
        ])->validate();

        $post_data = array();
        $post_data['total_amount'] = $request_data['amount']; # You cant not pay less than 10
        $post_data['currency'] = "BDT";
        $post_data['tran_id'] = uniqid('', true); // tran_id must be unique

        # CUSTOMER INFORMATION
        $post_data['cus_name'] = $request_data['cus_name'];
        $post_data['cus_email'] = $request_data['cus_email'];
        $post_data['cus_add1'] = $request_data['cus_addr1'];
        $post_data['cus_add2'] = "";
        $post_data['cus_city'] = "";
        $post_data['cus_state'] = "";
        $post_data['cus_postcode'] = "";
        $post_data['cus_country'] = "Bangladesh";
        $post_data['cus_phone'] = '880' . $request_data['cus_phone'];
        $post_data['cus_fax'] = "";

        # SHIPMENT INFORMATION
        $post_data['ship_name'] = "Store Test";
        $post_data['ship_add1'] = "Dhaka";
        $post_data['ship_add2'] = "Dhaka";
        $post_data['ship_city'] = "Dhaka";
        $post_data['ship_state'] = "Dhaka";
        $post_data['ship_postcode'] = "1000";
        $post_data['ship_phone'] = "";
        $post_data['ship_country'] = "Bangladesh";

        $post_data['shipping_method'] = "NO";
        $post_data['product_name'] = "Computer";
        $post_data['product_category'] = "Goods";
        $post_data['product_profile'] = "physical-goods";

        # OPTIONAL PARAMETERS
        $post_data['value_a'] = "ref001";
        $post_data['value_b'] = "ref002";
        $post_data['value_c'] = "ref003";
        $post_data['value_d'] = "ref004";


        #Before  going to initiate the payment order status need to update as Pending.

        $user = $request->user();
        $user->transactions()->updateOrCreate([
            'name' => $post_data['cus_name'],
            'email' => $post_data['cus_email'],
            'phone' => $post_data['cus_phone'],
            'amount' => $post_data['total_amount'],
            'status' => 'Pending',
            'address' => $post_data['cus_add1'],
            'transaction_id' => $post_data['tran_id'],
            'transaction_type' => 'deposit',
            'currency' => $post_data['currency']
        ]);

        $sslc = new SslCommerzNotification();
        # initiate(Transaction Data , false: Redirect to SSLCOMMERZ gateway/ true: Show all the Payement gateway here )
        $payment_options = $sslc->makePayment($post_data, 'checkout', 'json');

        if (!is_array($payment_options)) {
            print_r($payment_options);
            $payment_options = array();
        }
    }

    public function success(Request $request)
    {
        echo "Transaction is Successful";

        // getting sslcommerz data
        list($tran_id,
            $amount,
            $currency,
            $card_type,
            $card_no,
            $bank_tran_id,
            $error,
            $card_issuer,
            $card_brand,
            $risk_level,
            $risk_title) = $this->getting_data_from_sslcommerz($request);

        $sslc = new SslCommerzNotification();

        #Check order status in order table against the transaction id or order id.

        $user = $request->user();
        $order_details = $user->transactions()
            ->where('transaction_id', $tran_id)
            ->select('transaction_id', 'status', 'currency', 'amount')->first();

        if ($order_details->status === 'Pending') {
            $validation = $sslc->orderValidate($tran_id, $amount, $currency, $request->all());

            if ($validation === TRUE) {
                /*
                That means IPN did not work or IPN URL was not set in your merchant panel. Here you need to update order status
                in order table as Processing or Completed.
                Here you can also sent sms or email for successful transaction to customer
                */

                $user->transactions()
                    ->where('transaction_id', $tran_id)
                    ->update(['status' => 'Completed']);

                $transaction = $user->transactions()
                    ->where('transaction_id', $tran_id)->first();

                $user->update([
                    'balance' => $user->balance + $transaction->amount
                ]);

                // Saving the data to the transaction Details table
                $current_transaction = Transaction::where('transaction_id', $tran_id)->first();
                $current_transaction->transaction_detail()->updateOrCreate([
                    'card_type' => $card_type,
                    'card_no' => $card_no,
                    'bank_tran_id' => $bank_tran_id,
                    'error' => $error,
                    'card_issuer' => $card_issuer,
                    'card_brand' => $card_brand,
                    'risk_level' => $risk_level,
                    'risk_title' => $risk_title
                ]);

                echo "<br >Transaction is successfully Completed. Redirecting, Please Wait";
                sleep(5);
                $url = config('app.frontEndUrl');
                return Redirect::to($url . '/deposits');
            }

            /*
            That means IPN did not work or IPN URL was not set in your merchant panel and Transaction validation failed.
            Here you need to update order status as Failed in order table.
            */

            $user->transactions()
                ->where('transaction_id', $tran_id)
                ->update(['status' => 'Failed']);
            echo "validation Fail";
        } else if ($order_details->status === 'Completed') {
            /*
             That means through IPN Order status already updated. Now you can just show the customer that transaction is Completed. No need to udate database.
             */
            echo "Transaction is already Successful. Redirecting, Please wait.....";
            sleep(5);
            $url = config('app.frontEndUrl');
            return Redirect::to($url . '/deposits');
        } else {
            #That means something wrong happened. You can redirect customer to your product page.
            echo "Invalid Transaction";
        }

    }

    private function getting_data_from_sslcommerz(Request $request)
    {
        # getting all the returned data from sslcommerz
        return [
            $tran_id = $request->input('tran_id'),
            $amount = $request->input('amount'),
            $currency = $request->input('currency'),
            $card_type = $request->input('card_type'),
            $card_no = $request->input('card_no'),
            $bank_tran_id = $request->input('bank_tran_id'),
            $error = $request->input('error'),
            $card_issuer = $request->input('card_issuer'),
            $card_brand = $request->input('card_brand'),
            $risk_level = $request->input('risk_level'),
            $risk_title = $request->input('risk_title'),
        ];
    }

    public function fail(Request $request)
    {
        # getting sslcommerz data
        list($tran_id,
            $amount,
            $currency,
            $card_type,
            $card_no,
            $bank_tran_id,
            $error,
            $card_issuer,
            $card_brand,
            $risk_level,
            $risk_title) = $this->getting_data_from_sslcommerz($request);

        $user = $request->user();
        $order_details = $user->transactions()
            ->where('transaction_id', $tran_id)
            ->select('transaction_id', 'status', 'currency', 'amount')->first();

        if ($order_details->status === 'Pending') {
            $user->transactions()
                ->where('transaction_id', $tran_id)
                ->update(['status' => 'Failed']);

            // Saving the data to the transaction Details table
            $current_transaction = Transaction::where('transaction_id', $tran_id)->first();
            $current_transaction->transaction_detail()->updateOrCreate([
                'card_type' => $card_type,
                'card_no' => $card_no,
                'bank_tran_id' => $bank_tran_id,
                'error' => $error,
                'card_issuer' => $card_issuer,
                'card_brand' => $card_brand,
                'risk_level' => $risk_level,
                'risk_title' => $risk_title
            ]);

            echo "Transaction Failed";
        } else if ($order_details->status === 'Completed') {
            echo "Transaction is already Successful. Redirecting, Please wait.....";
            sleep(2);
            $url = config('app.frontEndUrl');
            return Redirect::to($url . '/deposits');
        } else {
            echo "Transaction is Invalid";
        }

    }

    public function cancel(Request $request)
    {
        list($tran_id,
            $amount,
            $currency,
            $card_type,
            $card_no,
            $bank_tran_id,
            $error,
            $card_issuer,
            $card_brand,
            $risk_level,
            $risk_title) = $this->getting_data_from_sslcommerz($request);

        $user = $request->user();
        $order_details = $user->transactions()
            ->where('transaction_id', $tran_id)
            ->select('transaction_id', 'status', 'currency', 'amount')->first();

        if ($order_details->status === 'Pending') {
            $user->transactions()
                ->where('transaction_id', $tran_id)
                ->update(['status' => 'Canceled']);

            // Saving the data to the transaction Details table
            $current_transaction = Transaction::where('transaction_id', $tran_id)->first();
            $current_transaction->transaction_detail()->updateOrCreate([
                'card_type' => $card_type,
                'card_no' => $card_no,
                'bank_tran_id' => $bank_tran_id,
                'error' => $error,
                'card_issuer' => $card_issuer,
                'card_brand' => $card_brand,
                'risk_level' => $risk_level,
                'risk_title' => $risk_title
            ]);

            echo "Transaction Canceled";
        } else if ($order_details->status === 'Completed') {
            echo "Transaction is already Successful. Redirecting, Please wait.....";
            sleep(2);
            $url = config('app.frontEndUrl');
            return Redirect::to($url . '/deposits');
        } else {
            echo "Transaction is Invalid";
        }


    }

    public function ipn(Request $request)
    {
        #Received all the payment information from the gateway
        if ($request->input('tran_id')) #Check transaction id is posted or not.
        {

            $tran_id = $request->input('tran_id');

            #Check order status in order table against the transaction id or order id.
            $order_details = DB::table('transactions')
                ->where('transaction_id', $tran_id)
                ->select('transaction_id', 'status', 'currency', 'amount')->first();

            if ($order_details->status === 'Pending') {
                $sslc = new SslCommerzNotification();
                $validation = $sslc->orderValidate($tran_id, $order_details->amount, $order_details->currency, $request->all());
                if ($validation === TRUE) {
                    /*
                    That means IPN worked. Here you need to update order status
                    in order table as Processing or Completed.
                    Here you can also sent sms or email for successful transaction to customer
                    */
                    $update_product = DB::table('transactions')
                        ->where('transaction_id', $tran_id)
                        ->update(['status' => 'Completed']);

                    echo "Transaction is successfully Completed";
                } else {
                    /*
                    That means IPN worked, but Transaction validation failed.
                    Here you need to update order status as Failed in order table.
                    */
                    $update_product = DB::table('transactions')
                        ->where('transaction_id', $tran_id)
                        ->update(['status' => 'Failed']);

                    echo "validation Fail";
                }
            } else if ($order_details->status === 'Completed') {
                #That means Order status already updated. No need to update database.
                echo "Transaction is already successfully Completed";
            } else {
                #That means something wrong happened. You can redirect customer to your product page.
                echo "Invalid Transaction";
            }
        } else {
            echo "Invalid Data";
        }
    }
}
