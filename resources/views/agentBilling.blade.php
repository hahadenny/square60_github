@extends('layouts.app')

@section('header')
    {{--<header>
        @include('layouts.header')
    </header>--}}
@endsection

@section('content')
    <div class="mobile-menu" id="mobile-menu">
        @include('layouts.header2_1')        
    </div>

    <div id="app">
        <div>
            @include('partial.header')  
            <section>

                <div class="main-content columns is-mobile is-centered" style="margin-bottom:40px;">
                    <div class="content p_listings" style="text-align:center;">                       

                            <div class="columns">
                                <div class="column">
                                    <div style="text-align:left;margin:0 auto;width:300px;">
                                        <form action="{{route('managePaym')}}" method="post" id="payment-form" name="payment-form">
                                            {{ csrf_field() }}
                                            <input type="hidden" id="action" name="action" value="" />
                                            <table cellpadding=0 cellspacing=0 border=0 width="100%">
                                                @if (session('status'))
                                                <tr>
                                                    <td colspan=3 style="border:none;padding-left:0px;padding-right:0px;">                                                
                                                        <div class="alert alert-success">
                                                            {!! session('status') !!}
                                                        </div>                                                
                                                    </td>
                                                </tr>
                                                <tr><td style="border:none;padding-left:0px;"></td></tr>
                                                @endif                                        
                                                <tr>
                                                    <td colspan=3 style="border:none;padding-left:0px;"><h4>{{Auth::user()->name}}'s Payment Method</h4></td>
                                                </tr>
                                                <tr>
                                                    <td colspan=3 style="border:none;padding-left:0px;">Default Payment Method:</td>
                                                </tr>
                                                <tr>
                                                    <td style="border:none;padding-left:0px;width:200px;">
                                                        <select name="cus_id" style="padding:5px;margin-top:0px;margin-bottom:0px;min-width:150px;" onchange="updatePaym();">
                                                        @if(count($user_payments))
                                                            @foreach ($user_payments as $payment) 
                                                            <option class="cus_id" value="{{$payment->cus_id}}" @if($payment->in_use == 1) selected @endif>{{$payment->card_brand}} ending - {{$payment->last4}}</option>
                                                            @endforeach
                                                        @else
                                                            <option class="cus_id" vlaue="">N/A</option>
                                                        @endif
                                                        </select>
                                                    </td>
                                                    @if(count($user_payments))
                                                    <td style="vertical-align:middle;border:none;padding-left:0px;width:60px;">
                                                        <a href="javascript:void(0)" style="text-decoration:underline;color:#1c5599;" onclick="deletePaym();">Delete</a>
                                                    </td>
                                                    @endif
                                                    <td style="vertical-align:middle;border:none;padding-left:0px;"> 
                                                        <a href="javascript:void(0)" style="text-decoration:underline;color:#1c5599;" onclick="$('.card-paym').show();">Add @if(!count($user_payments)) Card @endif</a>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="card-paym" colspan=3 style="border:none;padding-left:0px;display:none;">
                                                        <div id="card-element" class="cell example example3" style="width:90vw;max-width:300px;margin-top:20px;">
                                                            <div class="fieldset">
                                                                <div style="margin-bottom:6px;">Card Holder Name:</div>
                                                                <input id="example3-name" name="name" data-tid="elements_examples.form.name_label" class="field" type="text" placeholder="Name On Card" required>    
                                                                <div style="margin-bottom:6px;">Credit/Debit Card:</div>
                                                                <div id="example3-card-number" class="field empty"></div>
                                                                <div style="float:left;width:100%;">
                                                                    <div style="float:left; width:51%;">Expiry Date:</div>
                                                                    <div style="float:left; width:49%;">CVC:</div>
                                                                </div>
                                                                <div id="example3-card-expiry" class="field empty half-width"></div>
                                                                <div id="example3-card-cvc" class="field empty half-width"></div>      
                                                                <div style="margin-bottom:6px;">Billing Address:</div>
                                                                <input id="example3-address" name="address" data-tid="elements_examples.form.name_label" class="field" type="text" placeholder="Address" required>
                                                                <div style="float:left;width:100%;">
                                                                    <div style="float:left; width:51%;">City:</div>
                                                                    <div style="float:left; width:49%;">State:</div>
                                                                </div>
                                                                <input id="example3-city" name="city" style="height:40px;margin-top:6px;" data-tid="elements_examples.form.name_label" class="field half-width" type="text" placeholder="City" @if(!count($user_payments)) required @endif>
                                                                <input id="example3-state" name="state" data-tid="elements_examples.form.name_label" class="field half-width" type="text" placeholder="State"required>
                                                                <div style="float:left;width:100%;">
                                                                    <div style="float:left; width:51%;margin-bottom:6px;">Zip Code:</div>
                                                                </div>
                                                                <div id="example3-card-zip" class="field empty half-width"></div> 
                                                                {{--<input id="example3-zip" data-tid="elements_examples.form.postal_code_placeholder" class="field empty third-width" placeholder="Zip Code" required>--}}                                   
                                                            </div>
                                                            <div class="error" role="alert">
                                                                <span class="message"></span>
                                                            </div>
                                                            <div class="success">                                           
                                                            <h3 class="title" data-tid="elements_examples.success.title"></h3>
                                                            <p class="message"></p>
                                                            <a class="reset" href="#"></a>
                                                            </div>
                                                        </div>

                                                        {{--<div id="card-element" class="stripe_paym" style="margin-top:20px;">
                                                        </div>--}}

                                                        <div id="card-errors" role="alert" style="margin-top:10px;color:red"></div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="card-paym" colspan=3 style="border:none;padding-left:0px;display:none;">
                                                        <button class="button is-primary mainbgc">Save</button>
                                                    </td>
                                                </tr>
                                            </table>
                                        </form>
                                    </div>
                                </div>
                            </div>
                    </div>

                </div>

            </section>
        </div>
    </div>
@endsection

@section('additional_scripts')
<script src="https://js.stripe.com/v3/"></script>
<script>
    function deletePaym() {
        if ($('.cus_id').length <= 1) {
            swal('You need to have at least 1 payment method.');
        } 
        else {
            $('#action').val('delete');
            document.payment-form.submit();
        }
    }

    function updatePaym() {
        $('#action').val('update');
        document.payment-form.submit();
    }

    // Create a Stripe client.
    var stripe = Stripe('{{env('STRIPE_KEY')}}');

    var elements = stripe.elements();

    var elementStyles = {
            base: {
                color: '#282828',
                fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
                fontSmoothing: 'antialiased',
                fontSize: '16px',

                ':focus': {
                    color: '#424770',
                },

                '::placeholder': {
                    color: '#ccc',
                },

                ':focus::placeholder': {
                    color: '#CFD7DF',
                },
            },
            invalid: {
                color: '#fff',
                ':focus': {
                    color: '#FA755A',
                },
                '::placeholder': {
                    color: '#fff',
                },
            },
        };

        var elementClasses = {
            focus: 'focus',
            empty: 'empty',
            invalid: 'invalid',
        };

        var cardNumber = elements.create('cardNumber', {
            style: elementStyles,
            classes: elementClasses,
            placeholder: 'Card Number'
        });
        cardNumber.mount('#example3-card-number');

        var cardExpiry = elements.create('cardExpiry', {
            style: elementStyles,
            classes: elementClasses,
        });
        cardExpiry.mount('#example3-card-expiry');

        var cardCvc = elements.create('cardCvc', {
            style: elementStyles,
            classes: elementClasses,
        });
        cardCvc.mount('#example3-card-cvc');

        postalCode = elements.create('postalCode', {
            style: elementStyles,
            classes: elementClasses,
            placeholder: 'Zip Code'
        });
        postalCode.mount('#example3-card-zip');

        cardNumber.addEventListener('change', function(event) {
            var displayError = document.getElementById('card-errors');
            if (event.error) {
                displayError.textContent = event.error.message;
            } else {
                displayError.textContent = '';
            }
        });

        cardExpiry.addEventListener('change', function(event) {
            var displayError = document.getElementById('card-errors');
            if (event.error) {
                displayError.textContent = event.error.message;
            } else {
                displayError.textContent = '';
            }
        });

        cardCvc.addEventListener('change', function(event) {
            var displayError = document.getElementById('card-errors');
            if (event.error) {
                displayError.textContent = event.error.message;
            } else {
                displayError.textContent = '';
            }
        });

        postalCode.addEventListener('change', function(event) {
            var displayError = document.getElementById('card-errors');
            if (event.error) {
                displayError.textContent = event.error.message;
            } else {
                displayError.textContent = '';
            }
        });

    /*var style = {
        base: {
            color: '#32325d',
            lineHeight: '18px',
            fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
            fontSmoothing: 'antialiased',
            fontSize: '16px',
            '::placeholder': {
                color: '#aab7c4'
            }
        },
        invalid: {
            color: '#fa755a',
            iconColor: '#fa755a'
        }
    };

    // Create an instance of the card Element.
    var card = elements.create('card', {style: style});

    // Add an instance of the card Element into the `card-element` <div>.
    card.mount('#card-element');

    // Handle real-time validation errors from the card Element.
    card.addEventListener('change', function(event) {
        var displayError = document.getElementById('card-errors');
        if (event.error) {
            displayError.textContent = event.error.message;
        } else {
            displayError.textContent = '';
        }
    });*/

    // Handle form submission.
    var form = document.getElementById('payment-form');
    form.addEventListener('submit', function(event) {
        $('#action').val('add');

        event.preventDefault();

        stripe.createToken(cardNumber).then(function(result) {
            if (result.error) {
                // Inform the user if there was an error.
                var errorElement = document.getElementById('card-errors');
                errorElement.textContent = result.error.message;
            } else {
                // Send the token to your server.
                stripeTokenHandler(result.token);
            }
        });
    });


    function stripeTokenHandler(token) {
        var form = document.getElementById('payment-form');
        var hiddenInput = document.createElement('input');
        hiddenInput.setAttribute('type', 'hidden');
        hiddenInput.setAttribute('name', 'stripeToken');
        hiddenInput.setAttribute('value', token.id);
        form.appendChild(hiddenInput);

        // Submit the form
        form.submit();
    }
</script>
@endsection

@section('footer')
    {{--@include('layouts.footer')--}}
    @include('layouts.footerMain2')
@endsection