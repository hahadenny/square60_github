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
            <div class="column is-half is-narrow">

                <div class="panel-body">
                    @if (session('status'))
                        <div class="alert alert-success" style="margin-bottom:20px;color:green;">
                            {!! session('status') !!}
                        </div>
                    @endif

                        @if (session('error'))
                            <div class="alert alert-error" style="margin-bottom:20px;color:red;">
                                {{ session('error') }}
                            </div>
                        @endif

                    <form method="POST" action="{{ route('edit') }}" >
                        {{ csrf_field() }}
                        <input type="hidden" id="changepwd" name="changepwd" value="0" />
                        <div class="field">
                            <label class="label">Name</label>
                                <div class="control has-icons-left ">
                                    <input class="input" type="text" name="name" value="{{ Auth::user()->name }}" placeholder="Name input" required autofocus>
                                    <input class="input" type="hidden" name="id" value="{{ Auth::user()->id }}">
                                    <span class="icon is-small is-left">
                                        <i class="fa fa-user"></i>
                                    </span>
                                </div>

                            @if ($errors->has('name'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('name') }}</strong>
                                </span>
                            @endif
                        </div>

                        <div class="field">
                            <label class="label">Email</label>
                                <div class="control has-icons-left ">
                                    <input class="input" type="email" placeholder="Email input" name="email" value="{{ Auth::user()->email }}" required>
                                        <span class="icon is-small is-left">
                                            <i class="fa fa-envelope"></i>
                                        </span>
                                </div>

                            @if ($errors->has('email'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('email') }}</strong>
                                </span>
                            @endif
                        </div>

                        <div id="cp" style="color:#4577b9;text-decoration:underline;cursor:pointer;margin-bottom:15px;" onclick="changePwd();">Change password</div>

                        @if(session('pwd'))
                        <div style="color:red; margin-bottom:10px;">*Please change your password first.</div>
                        @endif

                        {{--<div class="field">
                            <label class="label">Phone</label>
                                <div class="control has-icons-left ">
                                    <input class="input" type="text" placeholder="Phone input" name="phone" value="{{ Auth::user()->phone }}" required>
                                    <span class="icon is-small is-left">
                                        <i class="fa fa-phone"></i>
                                    </span>
                                </div>

                            @if ($errors->has('phone'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('phone') }}</strong>
                                </span>
                            @endif
                        </div>--}}

                        <div id="change_pwd" style="display:none;">
                            <div class="field">
                                <label class="label">Current password</label>
                                <div class="control">
                                    <input class="input pwd" id="currentPassword" type="password" name="currentPassword" value="@if(session('pwd')){{session('pwd')}}@endif">
                                </div>
                                @if ($errors->has('currentPassword'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('currentPassword') }}</strong>
                                    </span>
                                @endif
                            </div>

                            <div class="field">
                                <label class="label">New Password</label>
                                    <div class="control">
                                        <input class="input pwd" id="password" type="password" name="password">
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
                                        <input class="input pwd" id="password-confirm" type="password" name="password_confirmation">
                                    </div>
                            </div>
                        </div>

                        <div style="margin-top:30px;margin-bottom:15px;"><label class="label2 form-content" style="font-weight:normal;">Do you want to recieve new/updated listings notification emails from your saved items?</label></div>
                                    <label><input placeholder="" type="radio" class="radio" name="update_email" value="1" @if(Auth::user()->update_email) checked @endif/>Yes</label>
                                    <label><input placeholder="" type="radio" class="radio" name="update_email" value="0" @if(!Auth::user()->update_email) checked @endif/>No</label><br>

                        {{--@if(Auth::user()->isAgent() && !Auth::user()->agentInfos()->forward_email)--}}
                        @if(Auth::user()->isAgent())
                            @php
                                if(Auth::user()->agentInfos()->forward_email)
                                    $forward_email = 1;
                                else
                                    $forward_email = 0;
                            @endphp
                        <div style="margin-top:30px;margin-bottom:15px;"><label class="label2 form-content" style="font-weight:normal;">Do you want to receive emails from clients who are interesting in your listings?</label></div>
                                    <label><input placeholder="" type="radio" class="radio" name="forward_email" value="1" @if($forward_email) checked @endif/>Yes</label>
                                    <label><input placeholder="" type="radio" class="radio" name="forward_email" value="0" @if(!$forward_email) checked @endif/>No</label><br>
                        @endif

                        <button type="submit" class="button is-primary mainbgc" style="margin-top:30px;">
                            Update
                        </button>

                    </form>
                </div>

            </div>
        </div>
    </section>
   </div>
</div>
@endsection

@section('additional_scripts')
<script>
changePwd=function() {
    $('#changepwd').val(1);
    $('#change_pwd').show();
    $('.pwd').attr('required', true);
}

@if ($errors->has('password') || $errors->has('currentPassword'))
    $('#cp').click();
@endif

@if(session('pwd'))
changePwd();
@endif
</script>
@endsection

@section('footer')
    {{--@include('layouts.footer')--}}
    @include('layouts.footerMain2')
@endsection
