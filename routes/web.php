<?php

use App\Http\Controllers\SslCommerzPaymentController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', static function () {
    $url = config('app.frontEndUrl');
    return Redirect::to($url);
});

Route::get('/login', [UserController::class, 'login'])->name('login');

// SSLCOMMERZ Start
//Route::get('/example1', [SslCommerzPaymentController::class, 'exampleEasyCheckout']);
//Route::get('/example2', [SslCommerzPaymentController::class, 'exampleHostedCheckout']);

//Route::post('/pay', [SslCommerzPaymentController::class, 'index']);
Route::post('/pay-via-ajax', [SslCommerzPaymentController::class, 'payViaAjax']);
Route::post('/withdraw-via-ajax', [SslCommerzPaymentController::class, 'withdrawViaAjax']);

Route::post('/success', [SslCommerzPaymentController::class, 'success']);
Route::post('/fail', [SslCommerzPaymentController::class, 'fail']);
Route::post('/cancel', [SslCommerzPaymentController::class, 'cancel']);

Route::post('/ipn', [SslCommerzPaymentController::class, 'ipn']);
//SSLCOMMERZ END
