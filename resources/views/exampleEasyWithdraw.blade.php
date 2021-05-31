<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="SSLCommerz">
    <title>Example - Easy Withdraw (Popup) | SSLCommerz</title>

    <!-- Bootstrap core CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
          integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

    <style>
        .bd-placeholder-img {
            font-size: 1.125rem;
            text-anchor: middle;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }

        @media (min-width: 768px) {
            .bd-placeholder-img-lg {
                font-size: 3.5rem;
            }
        }
    </style>
</head>
<body class="bg-light">
<div class="container">
    <div class="py-5 text-center">
        <h2>Withdraw Now with SSLCommerz</h2>
        <p class="lead">We are using SSLCommerz as a payment gateway where our users can withdraw money at real time. This is an example template from SSLCommerz Payment gateway.</p>
    </div>
    <div class="row justify-center">
        {{--        <div class="col-md-4 order-md-2 mb-4">--}}
        {{--            <h4 class="d-flex justify-content-between align-items-center mb-3">--}}
        {{--                <span class="text-muted">Your cart</span>--}}
        {{--                <span class="badge badge-secondary badge-pill">3</span>--}}
        {{--            </h4>--}}
        {{--            <ul class="list-group mb-3">--}}
        {{--                <li class="list-group-item d-flex justify-content-between lh-condensed">--}}
        {{--                    <div>--}}
        {{--                        <h6 class="my-0">Product name</h6>--}}
        {{--                        <small class="text-muted">Brief description</small>--}}
        {{--                    </div>--}}
        {{--                    <span class="text-muted">1000</span>--}}
        {{--                </li>--}}
        {{--                <li class="list-group-item d-flex justify-content-between lh-condensed">--}}
        {{--                    <div>--}}
        {{--                        <h6 class="my-0">Second product</h6>--}}
        {{--                        <small class="text-muted">Brief description</small>--}}
        {{--                    </div>--}}
        {{--                    <span class="text-muted">50</span>--}}
        {{--                </li>--}}
        {{--                <li class="list-group-item d-flex justify-content-between lh-condensed">--}}
        {{--                    <div>--}}
        {{--                        <h6 class="my-0">Third item</h6>--}}
        {{--                        <small class="text-muted">Brief description</small>--}}
        {{--                    </div>--}}
        {{--                    <span class="text-muted">150</span>--}}
        {{--                </li>--}}
        {{--                <li class="list-group-item d-flex justify-content-between">--}}
        {{--                    <span>Total (BDT)</span>--}}
        {{--                    <strong>1200 TK</strong>--}}
        {{--                </li>--}}
        {{--            </ul>--}}
        {{--        </div>--}}
        <div class="col-md-8 order-md-1 offset-md-2">
            <h4 class="mb-3">Withdrawal Details</h4>
            <form method="POST" class="needs-validation" novalidate>

                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label for="total_amount">Amount</label>
                        <input
                            value="{{ app('request')->input('amount') }}"
                            type="text" name="amount" id="total_amount"
                            placeholder="Enter The Amount You want to Deposit"
                            class="form-control" required/>
                        <div class="invalid-feedback">
                            Please Enter a Amount
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label for="firstName">Full name</label>
                        <label for="customer_name"></label>
                        <input type="text" name="customer_name" class="form-control"
                               id="customer_name" placeholder=""
                               value="{{ $user->name ?: "" }}" required>
                        <div class="invalid-feedback">
                            Valid customer name is required.
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="mobile">Mobile</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">+880</span>
                        </div>
                        <input type="text" name="customer_mobile" class="form-control" id="mobile" placeholder="Mobile"
                               value="{{ $user ? substr((string)$user->mobile_no, 3) : null }}" required>
                        <div class="invalid-feedback" style="width: 100%;">
                            Your Mobile number is required.
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="email">Email <span class="text-muted">(Optional)</span></label>
                    <input type="email" name="customer_email" class="form-control" id="email"
                           placeholder="you@example.com" value="{{ $user->email ?: null  }}" required>
                    <div class="invalid-feedback">
                        Please enter a valid email address for withdrawal
                    </div>
                </div>

                <div class="mb-3">
                    <label for="address">Address</label>
                    <input type="text" class="form-control" id="address" placeholder="Enter Your Address here"
                           value="{{ $user->verification->address ?? null  }}" required>
                    <div class="invalid-feedback">
                        Please enter your shipping address.
                    </div>
                </div>

                {{--                <div class="mb-3">--}}
                {{--                    <label for="address2">Address 2 <span class="text-muted">(Optional)</span></label>--}}
                {{--                    <input type="text" class="form-control" id="address2" placeholder="Apartment or suite">--}}
                {{--                </div>--}}

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="division">Division</label>
                        <select class="custom-select d-block w-100" id="division" required>
                            <option value="">Choose One...</option>
                            @if ($user->verification)
                                <option selected
                                        value="{{ $user->verification->division }}">{{ ucfirst($user->verification->division) }}</option>
                            @endif
                            @foreach(json_decode($divisions, true) as $division)
                                @if($user->verification && ucfirst($user->verification->division) == $division['name'])
                                    @continue
                                @endif
                                <option value="{{ lcfirst($division['name']) }}">{{ $division['name']  }}</option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback">
                            Please provide a valid Division.
                        </div>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="zila">Zila</label>
                        <select class="custom-select d-block w-100" id="zila" required>
                            <option value="">Choose One...</option>
                            @if ($user->verification)
                                <option selected
                                        value="{{ $user->verification->zila }}">{{ ucfirst($user->verification->zila)  }}</option>
                            @endif
                            @foreach(json_decode($zilas, true) as $zila)
                                @if($user->verification && ucfirst($user->verification->zila) === $zila['name'])
                                    @continue
                                @endif
                                <option value="{{ lcfirst($zila['name']) }}">{{ $zila['name'] }}</option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback">
                            Please provide a valid Zila.
                        </div>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="zip">Zip</label>
                        <input type="text" class="form-control"
                               value="{{$user->verification ? $user->verification->zip_code : null}}" id="zip"
                               placeholder="e.g 4000" required>
                        <div class="invalid-feedback">
                            Zip code required.
                        </div>
                    </div>
                </div>
                <hr class="mb-4">
                {{--                <div class="custom-control custom-checkbox">--}}
                {{--                    <input type="checkbox" class="custom-control-input" id="same-address">--}}
                {{--                    <input type="hidden" value="1200" name="amount" id="total_amount" required/>--}}
                {{--                    <label class="custom-control-label" for="same-address">Shipping address is the same as my billing--}}
                {{--                        address</label>--}}
                {{--                </div>--}}
{{--                <div class="custom-control custom-checkbox">--}}
{{--                    <input type="checkbox" class="custom-control-input" id="save-info">--}}
{{--                    <label class="custom-control-label" for="save-info">Save this information for next time</label>--}}
{{--                </div>--}}
                <hr class="mb-4">
                <button class="btn btn-primary btn-lg btn-block" id="sslczPayBtn"
                        token="if you have any token validation"
                        postdata="your javascript arrays or objects which requires in backend"
                        order="If you already have the transaction generated for current order"
                        endpoint="{{ url('/withdraw-via-ajax') }}"> Withdraw Now
                </button>
            </form>
        </div>
    </div>

    <footer class="my-5 pt-5 text-muted text-center text-small">
        <p class="mb-1">&copy; 2021 Grayscale. All Rights Reserved.</p>
        <ul class="list-inline">
            <li class="list-inline-item"><a href="#">Privacy</a></li>
            <li class="list-inline-item"><a href="#">Terms</a></li>
            <li class="list-inline-item"><a href="#">Support</a></li>
        </ul>
    </footer>
</div>
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
        integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo"
        crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"
        integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1"
        crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"
        integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM"
        crossorigin="anonymous"></script>


<!-- If you want to use the popup integration, -->
<script>
    let obj = {};

    let amount = $('#total_amount');

    obj.cus_name = $('#customer_name').val();
    obj.cus_phone = $('#mobile').val();
    obj.cus_email = $('#email').val();
    obj.cus_addr1 = $('#address').val();
    obj.amount = amount.val();

    $('input').change(function () {
        obj.cus_name = $('#customer_name').val();
        obj.cus_phone = $('#mobile').val();
        obj.cus_email = $('#email').val();
        obj.cus_addr1 = $('#address').val();
        obj.amount = amount.val();
    });

    $('#sslczPayBtn').prop('postdata', obj);

    (function (window, document) {
        var loader = function () {
            var script = document.createElement("script"), tag = document.getElementsByTagName("script")[0];
            // script.src = "https://seamless-epay.sslcommerz.com/embed.min.js?" + Math.random().toString(36).substring(7); // USE THIS FOR LIVE
            script.src = "https://sandbox.sslcommerz.com/embed.min.js?" + Math.random().toString(36).substring(7); // USE THIS FOR SANDBOX
            tag.parentNode.insertBefore(script, tag);
        };

        window.addEventListener ? window.addEventListener("load", loader, false) : window.attachEvent("onload", loader);
    })(window, document);
</script>
</body>
</html>
