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
                <div class="main-content columns is-mobile" style="margin-bottom:40px;margin-left:0px;margin-right:0px;margin-top:10px;">
                    <div class="content">
                        

                        @if (0 && $alreadyExpert)  {{--old code--}}
                            <div>
                                You are already a premium member!
                            </div>
                        @else
                        <div id="page1" class="upgrade_page">
                            <div class="column is-centered" style="text-align:left;margin-bottom:10px;">
                                <div class="inner" style="max-width:800px;padding-left:0px;padding-right:20px;">
                                    @if (session('status'))
                                        <div class="alert alert-success" style="margin-bottom:20px; text-align:left;">
                                            {!! session('status') !!}
                                        </div>
                                    @endif

                                    @if (Auth::user()->premium)
                                        <div style="margin-bottom:30px;color:red;font-size:18px;">
                                            <b>You are a @if(Auth::user()->premium==1)Silver @elseif(Auth::user()->premium==2)Gold @elseif(Auth::user()->premium==3)Diamond @endif Member!</b>
                                            @if (isset($alreadyExpert->type))
                                            <div style="font-size:13px;margin-top:10px;">
                                                Membership ends: {{Carbon\Carbon::parse($alreadyExpert->ends_at)->format('Y-m-d')}}<br>                                    
                                                @if ($alreadyExpert->renew == 1)
                                                    Auto Renew on: {{Carbon\Carbon::parse($alreadyExpert->ends_at)->format('Y-m-d')}}<br>
                                                @endif
                                            </div>
                                            @endif
                                        </div>
                                    @endif

                                    <h4 style="text-align:left;font-weight:bold;">Upgrade Your Membership To:</h4>
                                    <div style="margin-top:20px;">
                                        <input name="plan" type="radio" checked onclick="$('#1m_row').show();$('#1y_row').hide();"> Monthly &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input name="plan" type="radio" onclick="$('#1y_row').show();$('#1m_row').hide();"> Yearly &nbsp;<span style="color:red">(Save 20%)</span>
                                    </div>

                                    <div class="is-center bill_content">                                        
                                        <table class="upgrade_table" style="margin-top:20px;font-weight:normal;">
                                            <tr>
                                                <th class="has-text-centered" style="min-width:220px;">                                                    
                                                </th>
                                                <th class="has-text-centered" style="color:rgb(144, 144, 141);font-size:18px;">
                                                    Silver
                                                </th>
                                                <th class="has-text-centered" style="color:#a59955;font-size:18px;">
                                                    Gold
                                                </th>
                                                <th class="has-text-centered" style="color:rgb(77, 175, 175);font-size:18px;">
                                                    Diamond
                                                </th>                                                
                                            </tr>
                                            <tr>
                                                <td style="font-weight:bold;">      
                                                    Personal Webpage                                              
                                                </td>
                                                <td class="has-text-centered">
                                                    &#10004;
                                                </td>
                                                <td class="has-text-centered">
                                                    &#10004;
                                                </td>
                                                <td class="has-text-centered">
                                                    &#10004;
                                                </td>                                                
                                            </tr>
                                            <tr>
                                                <td style="font-weight:bold;">      
                                                    Past Sold Record<br>(Detailed Info)                                              
                                                </td>
                                                <td class="has-text-centered">                                                   
                                                </td>
                                                <td class="has-text-centered" style="vertical-align: middle;">
                                                    &#10004;
                                                </td>
                                                <td class="has-text-centered" style="vertical-align: middle;">
                                                    &#10004;
                                                </td>                                                
                                            </tr>
                                            <tr>
                                                <td style="font-weight:bold;">      
                                                    Feature Listing Discount                                            
                                                </td>
                                                <td class="has-text-centered" style="vertical-align: middle;font-size:17px;">
                                                    {{(1-env('SILV_FEAT_DISC'))*100}}%
                                                </td>
                                                <td class="has-text-centered" style="vertical-align: middle;font-size:17px;">
                                                    {{(1-env('GOLD_FEAT_DISC'))*100}}%
                                                </td>
                                                <td class="has-text-centered" style="vertical-align: middle;font-size:17px;">
                                                    {{(1-env('DIAM_FEAT_DISC'))*100}}%
                                                </td>                                                
                                            </tr>
                                            <tr>
                                                <td style="font-weight:bold;">      
                                                    Premium Listing Discount                                            
                                                </td>
                                                <td class="has-text-centered" style="vertical-align: middle;font-size:17px;">
                                                    {{(1-env('SILV_PREM_DISC'))*100}}%
                                                </td>
                                                <td class="has-text-centered" style="vertical-align: middle;font-size:17px;">
                                                    {{(1-env('GOLD_PREM_DISC'))*100}}%
                                                </td>
                                                <td class="has-text-centered" style="vertical-align: middle;font-size:17px;">
                                                    {{(1-env('DIAM_PREM_DISC'))*100}}%
                                                </td>                                                
                                            </tr>
                                            {{--<tr>
                                                <td style="font-weight:bold;">      
                                                    Access to S60Lot.com                                         
                                                </td>
                                                <td class="has-text-centered" style="vertical-align: middle;font-size:17px;">
                                                    Basic
                                                </td>
                                                <td class="has-text-centered" style="vertical-align: middle;font-size:17px;">
                                                    Advanced
                                                </td>
                                                <td class="has-text-centered" style="vertical-align: middle;font-size:17px;">
                                                    Full
                                                </td>                                                
                                            </tr>--}}
                                            <tr>
                                                <td style="font-weight:bold;">      
                                                    Owners' Mailing List **                                     
                                                </td>
                                                <td class="has-text-centered" style="vertical-align: middle;font-size:17px;">
                                                    {{env('SILV_MAIL')}}/month
                                                </td>
                                                <td class="has-text-centered" style="vertical-align: middle;font-size:17px;">
                                                    {{env('GOLD_MAIL')}}/month
                                                </td>
                                                <td class="has-text-centered" style="vertical-align: middle;font-size:17px;">
                                                    {{env('DIAM_MAIL')}}/month
                                                </td>                                                
                                            </tr>
                                            <tr id="1m_row">
                                                <td style="font-weight:bold;vertical-align: middle;">      
                                                    Subscribe 1 Month                                     
                                                </td>
                                                <td class="has-text-centered">                                                    
                                                    <button class="button is-primary" style="display:table-cell;width:100px;font-size:18px;font-weight:bold;" onclick="selectPrice(1, '1m');nextPage();">${{env('SILV_1M')}}</button>
                                                </td>
                                                <td class="has-text-centered">
                                                    <button class="button is-primary" style="display:table-cell;width:100px;font-size:18px;font-weight:bold;" onclick="selectPrice(2, '1m');nextPage();">${{env('GOLD_1M')}}</button>
                                                </td>
                                                <td class="has-text-centered">
                                                    <button class="button is-primary" style="display:table-cell;width:100px;font-size:18px;font-weight:bold;" onclick="selectPrice(3, '1m');nextPage();">${{env('DIAM_1M')}}</button>
                                                </td>                                                
                                            </tr>
                                            {{--<tr>
                                                <td style="font-weight:bold;vertical-align: middle;">      
                                                    Subscribe 3 Months                                    
                                                </td>
                                                <td class="has-text-centered">
                                                    <button class="button is-primary" style="display:table-cell;width:100px;font-size:18px;font-weight:bold;" onclick="selectPrice(1, '3m');nextPage();">${{env('SILV_3M')}}</button>
                                                </td>
                                                <td class="has-text-centered">
                                                    <button class="button is-primary" style="display:table-cell;width:100px;font-size:18px;font-weight:bold;" onclick="selectPrice(2, '3m');nextPage();">${{env('GOLD_3M')}}</button>
                                                </td>
                                                <td class="has-text-centered">
                                                    <button class="button is-primary" style="display:table-cell;width:100px;font-size:18px;font-weight:bold;" onclick="selectPrice(3, '3m');nextPage();">${{env('DIAM_3M')}}</button>
                                                </td>                                                
                                            </tr>--}}
                                            <tr id="1y_row" style="display:none;">
                                                <td style="font-weight:bold;vertical-align: middle;">      
                                                    Subscribe 1 Year                                     
                                                </td>
                                                <td class="has-text-centered">
                                                    <button class="button is-primary" style="display:table-cell;width:100px;font-size:18px;font-weight:bold;" onclick="selectPrice(1, '1y');nextPage();">${{env('SILV_1Y')}}</button>
                                                </td>
                                                <td class="has-text-centered">
                                                    <button class="button is-primary" style="display:table-cell;width:100px;font-size:18px;font-weight:bold;" onclick="selectPrice(2, '1y');nextPage();">${{env('GOLD_1Y')}}</button>
                                                </td>
                                                <td class="has-text-centered">
                                                    <button class="button is-primary" style="display:table-cell;width:100px;font-size:18px;font-weight:bold;" onclick="selectPrice(3, '1y');nextPage();">${{env('DIAM_1Y')}}</button>
                                                </td>                                                
                                            </tr>
                                        </table>
                                    </div>
                                    <div style="margin-top:20px;width:90vw;max-width:570px;"><i>** We have the most updated list of owners' names and addresses data from all condo buildings in NYC that have more than 20 units.</i></div>
                                    
                                    {{--
                                    <h4 style="text-align:left;margin-bottom:30px;margin-top:30px;"><b>Upgrade Your Membership Today!</b></h4>
                                    <h4>These are the Benefits of Becoming a Member:</h4>
                                    <ul style="padding-top:10px;margin-left:15px;">
                                        <li style="list-style-type:disc">Display your Bio and Listing on your own personal webpage.</li>
                                        <li style="list-style-type:disc">You can access the owner's mailing list.</li>
                                        <li style="list-style-type:disc">Users can view your web pages here at Square60.com.</li>
                                        <li style="list-style-type:disc">Have access to Square60Lot.com.</li>
                                        <li style="list-style-type:disc">25% discount on feature listing.</li>
                                    </ul>
                                    <div style="text-align:left;margin-top:50px;">
                                        <h4>Upgrade Today!</h4>
                                        <a class="button is-primary mainbgc" onclick="nextPage()" style="margin-top:10px;">Upgrade</a>
                                    </div>
                                    --}}
                                </div>                                
                            </div>                            
                        </div>

                        <form action="/upgradeForm" method="post" id="payment-form">
                        {{ csrf_field() }}
                        <div id="page2" style="display: none;">
                            <div class="column is-centered">
                            <div class="inner">
                            <h4>Membership Type:</h4>
                            <div class="container" style="padding-left:0px;">
                                <label class="checkbox">
                                    <input id="mtype1" name="type" required="required" type="radio" value="1" onclick="changePrice(1);" checked>
                                    <b style="color:rgb(144, 144, 141);">Silver</b>
                                </label>
                                <label class="checkbox">
                                    <input id="mtype2" name="type" required="required" type="radio" value="2" onclick="changePrice(2);">
                                    <b style="color:rgb(165, 153, 85);">Gold</b>
                                </label>
                                <label class="checkbox">
                                    <input id="mtype3" name="type" required="required" type="radio" value="3" onclick="changePrice(3);">
                                    <b style="color:rgb(77, 175, 175);">Diamond</b>
                                </label>
                            </div>

                            <div class="mtype" id="mtype_1" style="margin-top:20px;">
                                <div class="container" style="padding-left:0px;">
                                    <label class="checkbox">
                                        <input id="1_1m" name="period" required="required" type="radio" class="price" value="1_1m">
                                        $<span class="1_1m">{{env('SILV_1M')}}</span> per month.
                                    </label>
                                </div>
                                {{--<div class="container" style="padding-left:0px;margin-top:10px;">
                                    <label class="checkbox">
                                        <input id="1_3m" name="period" required="required" type="radio" class="price" value="1_3m">
                                        $<span class="1_3m">{{env('SILV_3M')}}</span> per 3 months.
                                    </label>
                                </div>--}}
                                <div class="container" style="padding-left:0px;margin-top:10px;">
                                    <label class="checkbox">
                                        <input id="1_1y" name="period" required="required" type="radio" class="price" value="1_1y">
                                        $<span class="1_1y">{{env('SILV_1Y')}}</span> per year.
                                    </label>
                                </div>
                            </div>

                            <div class="mtype" id="mtype_2" style="margin-top:20px;display:none;">
                                <div class="container" style="padding-left:0px;">
                                    <label class="checkbox">
                                        <input id="2_1m" name="period" required="required" type="radio" class="price" value="2_1m">
                                        $<span class="2_1m">{{env('GOLD_1M')}}</span> per month.
                                    </label>
                                </div>
                                {{--<div class="container" style="padding-left:0px;margin-top:10px;">
                                    <label class="checkbox">
                                        <input id="2_3m" name="period" required="required" type="radio" class="price" value="2_3m">
                                        $<span class="2_3m">{{env('GOLD_3M')}}</span> per 3 months.
                                    </label>
                                </div>--}}
                                <div class="container" style="padding-left:0px;margin-top:10px;">
                                    <label class="checkbox">
                                        <input id="2_1y" name="period" required="required" type="radio" class="price" value="2_1y">
                                        $<span class="2_1y">{{env('GOLD_1Y')}}</span> per year.
                                    </label>
                                </div>
                            </div>

                            <div class="mtype" id="mtype_3" style="margin-top:20px;display:none;">
                                <div class="container" style="padding-left:0px;">
                                    <label class="checkbox">
                                        <input id="3_1m" name="period" required="required" type="radio" class="price" value="3_1m">
                                        $<span class="3_1m">{{env('DIAM_1M')}}</span> per month.
                                    </label>
                                </div>
                                {{--<div class="container" style="padding-left:0px;margin-top:10px;">
                                    <label class="checkbox">
                                        <input id="3_3m" name="period" required="required" type="radio" class="price" value="3_3m">
                                        $<span class="3_3m">{{env('DIAM_3M')}}</span> per 3 months.
                                    </label>
                                </div>--}}
                                <div class="container" style="padding-left:0px;margin-top:10px;">
                                    <label class="checkbox">
                                        <input id="3_1y" name="period" required="required" type="radio" class="price" value="3_1y">
                                        $<span class="3_1y">{{env('DIAM_1Y')}}</span> per year.
                                    </label>
                                </div>
                            </div>

                            <div class="container" style="padding-left:0px;margin-top:10px;">
                                Auto Renew&nbsp;&nbsp;<label class="checkbox">
                                    <input type="checkbox"  name="recuring" value="1">
                                    Yes.
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
                                <div class="container" style="padding-left:0px;margin-top:30px;">
                                    <h5>Total: <span id="total"></span></h5>
                                    <button class="button is-primary" style="margin-top:20px;">Check Out</button>
                                    <br><br>
                                    <a id="previous" class="button is-link mainbgc" onclick="previousPage()" style="display: none;">BACK</a>
                                </div>                                
                            </div>
                            </div>
                            </div>
                        </div>

                        </form>

                    </div>
                    @endif
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
            window.scrollTo(0, 0);
            $('.alert-success').html('');
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

        $('input.price').on('change', function() {
            $('input.price').not(this).prop('checked', false);
                if($(this).is(":checked")) {
                    var total_span = $(this).val();
                    var total = $('.'+total_span).html();
                    $('#total').html('$'+total);
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

        function changePrice(type) {
            $("input[name='period']").prop("checked", false);
            $('.mtype').hide();
            $('#mtype_'+type).show();
        }

        function selectPrice(type, period) {
            $('#mtype'+type).click();
            $('#'+type+'_'+period).click();
            //$('.mtype').hide();
            //$('#mtype_'+type).show();
        }
    </script>
@endsection