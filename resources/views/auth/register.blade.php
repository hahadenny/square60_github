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
<div class="main-content columns is-mobile is-centered" style="margin-bottom:40px;">
    
    <div class="column is-half is-narrow">

        <div class="hero is-small" style="margin-top:0px;">
            <div class="hero-body">
                <div class="box">
                    <div class="is-size-3 has-text-centered">Sign Up</div>
                    <hr/>

                    <form  name="registerForm" method="POST" action="{{ route('register') }}" enctype="multipart/form-data" onsubmit="return false;">
                        {{ csrf_field() }}

                        <div class="field">
                            <label class="label">Type</label>
                            <div class="control">
                                <div class="buttons has-addons">
                                    <label class="button is-success is-selected" style="margin-bottom:10px;">
                                        <input type="radio" name="type" value="1" @if(old('type','type')== "1") checked @endif  checked />
                                        User
                                    </label>
                                    <label class="button">
                                        <input type="radio" name="type" value="2" @if(old('type','type') == "2") checked @endif>
                                        Owner
                                    </label>
                                    <label class="button">
                                        <input type="radio" name="type" value="3" @if(old('type','type') == "3") checked @endif>
                                        Agent
                                    </label>
                                    <label class="button">
                                        <input type="radio" name="type" value="5" @if(old('type','type') == "5") checked @endif>
                                        Management
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div id="agentMsg" style="color:red;padding-bottom:10px;display:none;"><i>*Please use your professional email to register.</i></div>

                        <div id="agentID" class="field" style="display:none;">
                            <label class="label">Copy of Your License <span style="color:red;display:inline;">*</span></label>
                            <div style="color:red;padding-bottom:10px;"><i>*You don't need to upload your license if you name or email is on our agents list.</i></div>
                            <div class="control has-icons-left ">
                                    <div id="images-1" class="image-wrapper" style="display:none;width: 50%; max-width:200px; height:auto; padding:0px;">
                                        <img id="blah" src="#" alt="" />
                                    </div>
                                    <input id="agent_license" type="file" name="license" class="select is-primary" value="{{ old('license') }}" id="license" accept="image/*,application/pdf" onchange="readURL(this);">
                            </div>

                            @if ($errors->has('license'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('license') }}</strong>
                                </span>
                            @endif
                        </div>

                        <div id="agentLink" class="field" style="display:none;">
                            <label class="label">OR<div style="margin-top:10px;">Real Estate Agent Profile Link <span style="color:red;display:inline;">*</span></div></label>
                            <div class="control has-icons-left ">
                                <input id="agent_web_site" class="input" type="text" placeholder="Web Site" name="web_site" value="{{old('web_site')}}">
                                <span class="icon is-small is-left">
                                  <i class="fa fa-globe"></i>
                                </span>
                            </div>

                            @if ($errors->has('web_site'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('web_site') }}</strong>
                                </span>
                            @endif
                        </div>

                        <div class="field">
                            <label class="label">Name</label>
                            
                            <div class="control has-icons-left ">
                                <input class="input" type="text" id="input_name" name="name" value="{{ old('name') }}" placeholder="Name" autofocus onkeyup="searchAgent(this.value);">
                                <ul id="agent_dropdown" class="dropdown-content dropdown-menu">
                                    <li class="dropdown-item"><a href="#">abc</a></li>
                                </ul>
                                <span class="icon is-small is-left">
                                  <i class="fa fa-user"></i>
                                </span>
                            </div>

                            <div id="name_list" style="color:green;display:none;">Your name is on our agents list!</div>

                            @if ($errors->has('name'))
                                <span class="help-block">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                            @endif
                        </div>

                        <div class="field">
                            <label class="label">Telephone</label>
                            <div class="control has-icons-left ">
                                <input class="input" type="text" placeholder="Telephone" name="phone" value="{{ old('phone') }}" >
                                <span class="icon is-small is-left">
                                  <i class="fa fa-phone"></i>
                                </span>
                            </div>

                            @if ($errors->has('phone'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('phone') }}</strong>
                                </span>
                            @endif
                        </div>

                        <div class="field">
                            <label class="label">Email</label>
                            <div class="control has-icons-left ">
                                <input class="input" type="email" placeholder="Email" id="input_email" name="email" value="{{ old('email') }}" required>
                                <span class="icon is-small is-left">
                                  <i class="fa fa-envelope"></i>
                                </span>
                            </div>

                            <div id="email_list" style="color:green;display:none;margin-top:5px;">Congratulation! We already have your profile. Please sign up and check your existing listings.</div>

                            @if ($errors->has('email'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('email') }}</strong>
                                </span>
                            @endif
                        </div>                       

                        <div id="emailConfirm" class="field" style="display: none">
                            <label class="label">Confirm Email</label>
                            <div class="control has-icons-left ">
                                <input class="input" id="email-confirm" type="email" name="email_confirmation"   placeholder="Confirm email" value="{{ old('email_confirmation') }}">
                                <span class="icon is-small is-left">
                                  <i class="fa fa-envelope"></i>
                                </span>
                            </div>

                            @if ($errors->has('email_confirmation'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('email_confirmation') }}</strong>
                                </span>
                            @endif
                        </div>

                        <div class="field">
                            <label class="label">Password</label>
                            <div class="control">
                                <input class="input" id="password" type="password" name="password" required>
                            </div>
                            @if ($errors->has('password'))
                                <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                            @endif
                        </div>

                        <div class="field">
                            <label class="label">Confirm Password</label>
                            <div class="control">
                                <input class="input" id="password-confirm" type="password" name="password_confirmation" required>
                            </div>
                        </div>

                        <button type="submit" class="button is-primary mainbgc" onclick="validateForm();">
                            Register
                        </button>
                        <input type="hidden" id="is_agent" name="is_agent" value="0" />
                        <input type="hidden" id="is_email_agent" name="is_email_agent" value="0" />
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
    </div>
</div>
@endsection

@section('footer')
    {{--@include('layouts.footer')--}}
    @include('layouts.footerMain2')
@endsection


@section('additional_scripts')
    <script>
        function searchAgent(name) {
            var type = $("input[name='type']:checked").val();
            if (type != 3)
                return false;

            name = $.trim(name);
            if (name.length > 2) {
                var token = '{{Session::token()}}';

                $.post("/api/checkagent",
                {
                    name: name,
                    _token: token
                },
                function(data, status){ 
                    //alert("Data: " + data + "\nStatus: " + status);
                    $('#agent_dropdown').html('');
                    var agents = $.parseJSON(data);
                    if (agents.length) { 
                        $.each(agents, function(key, value) {
                            adata = value.split(' | ');
                            aname = adata[0].replace("'", " ");
                            $('#agent_dropdown').append('<li class="dropdown-item" style="cursor:pointer" onclick="$(\'#input_name\').val(\''+aname+'\');$(\'#agent_dropdown\').hide();">'+value+'</li>');
                        });
                        $('#agent_dropdown').show();
                    }
                    else {
                        $('#agent_dropdown').hide();
                    }
                }).fail(function(response) {
                    //alert('Error: ' + response.responseText);
                });
            }
            else {
                $('#agent_dropdown').html('');
                $('#agent_dropdown').hide();
            }
        }

        $('#input_name').keyup(function(){
            var token = '{{Session::token()}}';
            var name = $(this).val();
            var type = $("input[name='type']:checked").val();
            //alert(type);
            if (type == 3 && name.length > 5) {                
                $.post("/api/checkname",
                {
                    name: name,
                    _token: token
                },
                function(data, status){ 
                    //alert("Data: " + data + "\nStatus: " + status);
                    if (data == 'T') {
                        $('#name_list').show();
                        $('#is_agent').val(1);
                    }
                    else {
                        $('#name_list').hide();
                        $('#is_agent').val(0);
                    }
                }).fail(function(response) {
                    //alert('Error: ' + response.responseText);
                });
            }
        });

        $('#input_email').keyup(function(){
            var token = '{{Session::token()}}';
            var email = $.trim($(this).val());
            var type = $("input[name='type']:checked").val();
            //alert(type);
            if (email.length > 10) {                
                $.post("/api/checkemail",
                {
                    email: email,
                    _token: token
                },
                function(data, status){ 
                    //alert("Data: " + data + "\nStatus: " + status);
                    if (data == 'T') {
                        $('#email_list').show();
                        $('#is_email_agent').val(1);
                    }
                    else {
                        $('#email_list').hide();
                        $('#is_email_agent').val(0);
                    }
                }).fail(function(response) {
                    //alert('Error: ' + response.responseText);
                });
            }
        });

        var oldType = '{{old("type")}}';

        if(oldType == 2 || oldType == 5){
            $('label.is-success').removeClass('is-success is-selected');
            $(':radio:checked').closest('label').addClass('is-success is-selected');
            $('#emailConfirm').show();
        }else if(oldType == 3){
            $('label.is-success').removeClass('is-success is-selected');
            $(':radio:checked').closest('label').addClass('is-success is-selected');
            $('#emailConfirm').show();
            $('#agentMsg').show();
            //$('#agentID').show();
            //$('#agentLink').show();
        }else if (oldType == 1){
            $('#emailConfirm').hide();
        }

        $("input[name=type]:radio").change(function () {

            $(':radio:checked').not(this).prop('checked',false)
            $('label.is-success').removeClass('is-success is-selected'); //Reset selection

            $(this).closest('label').addClass('is-success is-selected');   //Add class to list item

            $('#agentMsg').hide();
            $('#agentID').hide();
            $('#agentLink').hide();

            var value = $(this).val();
            if(value == 3) {
                $('#agentMsg').show();
                //$('#agentID').show();
                //$('#agentLink').show();
                $('#emailConfirm').show();
            }
            if(value == 2 || value == 5){
                $('#emailConfirm').show();
            }else if (value == 1){
                $('#emailConfirm').hide();
            }

        });

        function readURL(input) {
            $("#images-1").show();
            if (input.files && input.files[0]) {

                var reader = new FileReader();
                reader.onload = function (e) {
                    $('#blah')
                        .attr('src', e.target.result)
                        .width('100%').height('auto');
                };

                reader.readAsDataURL(input.files[0]);
            }
        }

        function validateForm() {
            if($("input[name=type]:radio:checked").val() == 3 && $('#is_agent').val() == 0 && $('#is_email_agent').val() == 0) {
                {{--if (!$('#agent_license').val() && !$('#agent_web_site').val()) {
                    swal('All agents must provide either an Agent License or a Real Estate Agent Profile Link.');
                    return false;
                }
                else--}}
                    document.registerForm.submit();
            }
            else
                document.registerForm.submit();
        }
    </script>
@endsection
