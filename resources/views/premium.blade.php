@extends('layouts.app')

@section('header')

@endsection

@section('content')
    <div class="mobile-menu" id="mobile-menu">
        @include('layouts.header2_1')        
    </div>
    <div id="app">
        <div>
            
            @include('partial.header')  

            <section style="width:80%;max-width:300px; margin:0 auto;">

                <div class="main-content columns is-mobile is-centered" style="margin-bottom:40px;">
                    <div class="content">
                        @if (session('status'))
                            <div class="alert alert-success" style="margin-bottom:20px;color:red;text-align:center;">
                                {!! session('status') !!}
                            </div>
                        @endif

                            <form action="{{route('premiuming')}}" method="post" id="payment-form" style="width:100%;max-width:550px;margin:0 auto;padding:0 10px;">
                                {{ csrf_field() }}

                                    <h4>Premium Listing ID {{request()->id}}:</h4>
                                    @if(Auth::user()->premium)
                                    <div style="color:red;margin-bottom:15px;">
                                        <i>You will have @if(Auth::user()->premium==1) {{(1-env('SILV_PREM_DISC'))*100}}% @elseif(Auth::user()->premium==2) {{(1-env('GOLD_PREM_DISC'))*100}}% @elseif(Auth::user()->premium==3) {{(1-env('DIAM_PREM_DISC'))*100}}% @endif discount!</i>
                                    </div>
                                    @endif
                                    <div class="container" style="padding-left:0px;">
                                        <label class="checkbox">
                                            <input id="week" name="period" required="required" type="radio" class="price" value="1w">
                                            $<span class="1w">{{env('PREM_1W')}}</span> one week.
                                        </label>
                                    </div>
                                    <div class="container" style="padding-left:0px;">
                                        <label class="checkbox">
                                            <input id="twoWeeks" name="period" required="required" type="radio" class="price" value="2w">
                                            $<span class="2w">{{env('PREM_2W')}}</span> two weeks.
                                        </label>
                                    </div>
                                    <div class="container" style="padding-left:0px;">
                                        <label class="checkbox">
                                            <input id="fourWeeks" name="period" required="required" type="radio" class="price" value="4w">
                                            $<span class="4w">{{env('PREM_4W')}}</span> four weeks.
                                        </label>
                                    </div>
                                    {{--
                                    <div class="container" style="padding-left:0px;">
                                        <label class="checkbox">
                                            <input id="sixWeeks" name="period" required="required" type="radio" class="price" value="6w">
                                            $<span class="6w">250</span> six weeks.
                                        </label>
                                    </div>
                                    <div class="container" style="padding-left:0px;">
                                        <label class="checkbox">
                                            <input id="year" name="period" required="required" type="radio" class="price" value="2m">
                                            $<span class="2m">300</span> two months.
                                        </label>
                                    </div>
                                    --}}
                                    <div class="container" style="margin-top:10px;padding-left:0px;">
                                        Auto Renew&nbsp;<label class="checkbox">
                                            <input type="checkbox"  name="recuring" value="1">
                                            Yes.
                                            <input type="hidden" name="id" value="{{request()->id}}">
                                        </label>
                                    </div>
                                    <div style="font-size:13px;margin-top:10px;">*Note: Your can unsubscribe at any time.</div>
                                    <hr>

                                <div>

                                    <div class="form-row">

                                        @if (count($user_payments))
                                        <label for="card-element">
                                            Credit/Debit Card:
                                        </label>                                        
                                        <div style="margin-top:10px;">
                                            <input type="radio" name="paym" value="prev" checked onclick="changePaym('prev')" /> Previous Payment
                                        </div>
                                        
                                        <div id="user-payments">                                            
                                            <select name="cus_id" style="padding:7px;margin-top:10px;margin-left:25px;margin-bottom:10px;">
                                                @foreach ($user_payments as $payment) 
                                                <option value="{{$payment->cus_id}}" @if($payment->in_use == 1) selected @endif>{{$payment->card_brand}} ending - {{$payment->last4}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        @endif

                                        <div style="margin-top:10px;margin-bottom:20px;@if (!count($user_payments)) display:none; @endif">
                                            <input type="radio" name="paym" value="new" onclick="changePaym('new')" @if (!count($user_payments)) checked @endif /> New Payment
                                        </div>

                                        <div id="card-element" class="cell example example3" style="width:90vw;max-width:300px;@if (count($user_payments)) display:none; @endif">
                                            <div class="fieldset">
                                                <div style="margin-bottom:6px;">Card Holder Name:</div>
                                                <input id="example3-name" name="name" data-tid="elements_examples.form.name_label" class="field" type="text" placeholder="Name On Card" @if(!count($user_payments)) required @endif>
                                                <div style="margin-bottom:6px;">Credit/Debit Card:</div>
                                                <div id="example3-card-number" class="field empty"></div>
                                                <div style="float:left;width:100%;">
                                                    <div style="float:left; width:51%;">Expiry Date:</div>
                                                    <div style="float:left; width:49%;">CVC:</div>
                                                </div>
                                                <div id="example3-card-expiry" class="field empty half-width"></div>
                                                <div id="example3-card-cvc" class="field empty half-width"></div>      
                                                <div style="margin-bottom:6px;">Billing Address:</div>
                                                <input id="example3-address" name="address" data-tid="elements_examples.form.name_label" class="field" type="text" placeholder="Address" @if(!count($user_payments)) required @endif>
                                                <div style="float:left;width:100%;">
                                                    <div style="float:left; width:51%;">City:</div>
                                                    <div style="float:left; width:49%;">State:</div>
                                                </div>
                                                <input id="example3-city" name="city" style="height:40px;margin-top:6px;" data-tid="elements_examples.form.name_label" class="field half-width" type="text" placeholder="City" @if(!count($user_payments)) required @endif>
                                                <input id="example3-state" name="state" data-tid="elements_examples.form.name_label" class="field half-width" type="text" placeholder="State" @if(!count($user_payments)) required @endif>
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

                                        {{--<div id="card-element" class="stripe_paym" style="margin-top:10px;@if (count($user_payments)) display:none; @endif">
                                        </div>--}}

                                        <div id="card-errors" role="alert" style="margin-top:10px;color:red"></div>
                                    </div>
                                    <div class="container" style="margin-top:30px;padding-left:0px;">
                                        <h5>Total: <span id="total"></span></h5>
                                        <button class="button is-primary mainbgc" style="margin-top:20px;">Check Out</button>
                                    </div>


                                </div>

                                <br><a id="previous" class="button is-link" onclick="previousPage()" style="display: none">BACK</a>


                            </form>

                    </div>

                </div>

            </section>
        </div>
    </div>
@endsection

@section('footer')
    {{--@include('layouts.footer')--}}
    @include('layouts.footerMain2')
@endsection

@section('additional_scripts')
    <script src="https://js.stripe.com/v3/"></script>
    <script>

        function changePaym(type) {
            if (type == 'new') {
                $('#card-element').show();
                $('#user-payments').hide();
                $('#example3-name, #example3-address, #example3-city, #example3-state').attr('required', 'true');
            }
            else if (type == 'prev') {
                $('#card-element').hide();
                $('#user-payments').show();
                $('#example3-name, #example3-address, #example3-city, #example3-state').removeAttr('required');
            }
        }

        function nextPage(){
            $("#next").hide();
            $("#page1").hide();
            $("#page2").show();
            $("#previous").show();
        }

        function previousPage(){
            $("#page2").hide();
            $("#previous").hide();
            $("#next").show();
            $("#page1").show();
        }

        /*$('.price').change(function() {
            if($(this).is(":checked")) {

                var total = $(this).val();
                $('#total').html('$'+total);
            }else{
                $('#total').html('');
                $(this).not(this).prop('checked', false);
            }
        });*/

        $('input.price').on('change', function() {
            $('input.price').not(this).prop('checked', false);
            if($(this).is(":checked")) {
                var total_span = $(this).val();
                var total = $('.'+total_span).html();
                
                @if(Auth::user()->premium)
                var total_int = parseFloat(total);
                var dis_total = total_int * @if(Auth::user()->premium==1) {{env('SILV_PREM_DISC')}} @elseif(Auth::user()->premium==2) {{env('GOLD_PREM_DISC')}} @elseif(Auth::user()->premium==3) {{env('DIAM_PREM_DISC')}} @endif;
                total = dis_total.toFixed(2);
                $('#total').html('$'+total+' <i style="color:red">(discounted)</i>');                
                @else
                $('#total').html('$'+total);
                @endif

            }else{
                $('#total').html('');
            }
        });


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
            if ($("input[name='paym']:checked").val() == 'prev') {
                form.submit();
                return false;
            }

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