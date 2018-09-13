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
    <section class="is-menu-custom" style="padding-left:10px;padding-right:10px;">
        <div class="main-content columns is-mobile is-centered" style="margin-bottom:40px;">
            
            @include('partial.sidemenu')

            <div class="column is-half is-narrow is-custom">

                <div class="panel-body">
                    @if (session('status'))
                        <div class="alert alert-success" style="margin-bottom:40px;">
                            {{ session('status') }}
                        </div>
                    @endif

                    <div id="messageResponse" class="alert alert-success">
                    </div>
                        
                    <h2 class="title is-4 has-text-centered" style="margin-bottom:50px;">@if(isset($agent) && $agent->first_name) {{$agent->first_name}} {{$agent->last_name}} @else Your @endif @if(Auth::user()->premium==1)Silver @elseif(Auth::user()->premium==2)Gold @elseif(Auth::user()->premium==3)Diamond @endif Profile</h2>
                    <form name="updateForm" method="POST" action="{{ route('updateAgent') }}" enctype="multipart/form-data" >
                        {{ csrf_field() }}

                        @if ($agent->license)
                        <div class="field">
                            <label class="label">License:</label>
                            @if (preg_match('/\.pdf/', $agent->license))
                                <img src="/images/pdf.png" style="width:150px;" onclick="window.open('{{ env('S3_IMG_PATH_1') }}{{$agent->license}}');" />
                            @elseif($agent->user_id == 0)
                                <img src="{{ env('S3_IMG_PATH') }}{{$agent->license}}"  style="width:200px;" onclick="window.open('{{ env('S3_IMG_PATH') }}{{$agent->license}}');" />
                            @else
                                <img src="{{ env('S3_IMG_PATH_1') }}{{$agent->license}}"  style="width:200px;"onclick="window.open('{{ env('S3_IMG_PATH_1') }}{{$agent->license}}');" />
                            @endif
                        </div>
                        @endif

                        <div class="field">
                            <label class="label">Full Name</label>
                            <div class="control has-icons-left ">
                                <input class="input" type="text" name="full_name" value="{{isset($agent) ? $agent->full_name : old('full_name')}}" required>
                                <span class="icon is-small is-left"><i class="fa fa-link"></i></span>
                            </div>

                            @if ($errors->has('full_name'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('full_name') }}</strong>
                                </span>
                            @endif
                        </div>

                        <div class="columns is-variable form-content">
                            <div class="column">
                                <div class="field">
                                    <label class="label">First Name</label>
                                    <div class="control has-icons-left ">
                                        <input class="input" type="text" placeholder="First Name" name="firstName" value="{{isset($agent) ? $agent->first_name : old('firstName')}}" required>
                                        <span class="icon is-small is-left"><i class="fa fa-user"></i></span>
                                    </div>

                                    @if ($errors->has('firstName'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('firstName') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="column">
                                <div class="field">
                                    <label class="label">Last Name</label>
                                    <div class="control has-icons-left ">
                                        <input class="input" type="text" name="lastName" value="{{isset($agent) ? $agent->last_name : old('lastName')}}" placeholder="Last Name input" required autofocus>
                                        <input class="input" type="hidden" name="id" value="{{$agent->id}}">
                                        <span class="icon is-small is-left"><i class="fa fa-user"></i></span>
                                    </div>


                                    @if ($errors->has('lastName'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('lastName') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>                            
                        </div>                       

                        <div class="field">
                            <label class="label">Web-site</label>
                            <div class="control has-icons-left ">
                                <input class="input" type="text" placeholder="Web site" name="webLink" value="{{isset($agent) ? $agent->web_site : old('webLink')}}" required>
                                <span class="icon is-small is-left"><i class="fa fa-link"></i></span>
                            </div>

                            @if ($errors->has('webLink'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('webLink') }}</strong>
                                </span>
                            @endif
                        </div>

                        <div class="field">
                            <label class="label">Photo</label>
                            <div class="control has-icons-left ">

                                    @if(isset($agent) && $agent->photo_url != '')
                                        <div class="image-wrapper" id="images-1" style="width: 30%;min-width:200px;">

                                            <p class="image is-128x128 agent-image" style="overflow: hidden;border-radius: 50%;background-image: url({{env('S3_IMG_PATH_1')}}{{$agent->photo_url}});background-size:cover;background-position:top center;">
                                            </p>

                                            <div class="top-right">
                                                <deletephoto inline-template v-bind:list="'{{$agent->id}}'" v-bind:num="1" :names="'images'">
                                                    <button type="button" v-on:click="deleteImage"><i class="fa fa-trash" aria-hidden="true"></i></button>
                                                </deletephoto>
                                            </div>
                                        </div>
                                        <label class="label" style="margin-top:10px;">Update Photo</label>
                                        <input type="file" name="photo" class="select is-primary" value="{{ old('photo') }}" id="photo" accept="image/*" onchange="readURL(this);">
                                    @else
                                    <div class="image-wrapper" id="images-1" style="width: 30%; min-width:200px;">
                                        <img id="blah" src="#" alt="" />
                                        <div class="top-right">
                                           <button id="deleteButton" type="button" style="display: none"><i class="fa fa-trash"></i></button>
                                        </div>
                                    </div>
                                        <input type="file" name="photo" class="select is-primary" value="{{ old('photo') }}" id="photo" onchange="readURL(this);">

                                    @endif
                            </div>

                            @if ($errors->has('photo'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('photo') }}</strong>
                                </span>
                            @endif
                        </div>

                        <div class="field">
                            <label class="label">Email</label>
                            <div class="control has-icons-left ">
                                <input class="input" type="email" placeholder="Email input" name="email" value="{{$agent->user->email}}" required>
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

                        <div class="field">
                            <label class="label">Company</label>
                            <div class="control has-icons-left ">
                                <input class="input" type="text" placeholder="Company" name="company" value="{{isset($agent) ? $agent->company : old('company')}}" required>
                                <span class="icon is-small is-left"><i class="fa fa-building"></i></span>
                            </div>

                            @if ($errors->has('company'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('company') }}</strong>
                                </span>
                            @endif
                        </div>

                        <div class="field">
                            <label class="label">Company Logo</label>
                        @if(isset($agent) && $agent->logo_path != '')
                            <div class="image-wrapper" id="logo-1" style="width: 30%;min-width:250px;">
                                @if($agent->user_id == 0)
                                    <img src="{{ env('S3_IMG_PATH') }}{{$agent->logo_path}}"  style="max-width:150px;max-height:50px;"/>
                                @else
                                    <img src="{{ env('S3_IMG_PATH_1') }}{{$agent->logo_path}}"  style="max-width:150px;max-height:50px;"/>
                                @endif

                                <div class="top-right">
                                    <deletephoto inline-template v-bind:list="'{{$agent->id}}'" v-bind:num="1" :names="'logo'">
                                        <button type="button" v-on:click="deleteImage"><i class="fa fa-trash" aria-hidden="true"></i></button>
                                    </deletephoto>
                                </div>
                            </div>
                            <label class="label">Update Logo</label>
                            <input type="file" name="logo" class="select is-primary" value="{{ old('logo') }}" id="logo" accept="image/*" onchange="readLogo(this);">
                        @else
                            <div class="image-wrapper" id="logo-1">
                                <img id="blahLogo" src="#" alt="" style="max-width:150px;max-height:50px;"/>
                                <div class="top-right">
                                    <button id="deleteLogoButton" type="button" style="display: none"><i class="fa fa-trash"></i></button>
                                </div>
                            </div>
                            <input type="file" name="logo" class="select is-primary" value="{{ old('logo') }}" id="logo" onchange="readLogo(this);">
                        @endif
                        </div>

                        <div class="field">
                            <label class="label">Office Phone<br><span style="font-weight:normal;">(shown on listing)</span></label>
                            <div class="control has-icons-left ">
                                <input class="input" type="text" placeholder="Office Phone" name="officePhone" value="{{isset($agent) ? $agent->office_phone : old('officePhone')}}" required>
                                <span class="icon is-small is-left">
                                        <i class="fa fa-phone"></i>
                                    </span>
                            </div>

                            @if ($errors->has('officePhone'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('officePhone') }}</strong>
                                </span>
                            @endif
                        </div>

                        <div class="field">
                            <label class="label">Cell Phone</label>
                            <div class="control has-icons-left ">
                                <input class="input" type="text" placeholder="Phone input" name="cellPhone" value="{{$agent->user->phone}}" required>
                                <span class="icon is-small is-left">
                                        <i class="fa fa-mobile"></i>
                                    </span>
                            </div>

                            @if ($errors->has('cellPhone'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('cellPhone') }}</strong>
                                </span>
                            @endif
                        </div>

                        <div class="field">
                            <label class="label">Fax</label>
                            <div class="control has-icons-left ">
                                <input class="input" type="text" placeholder="Fax input" name="fax" value="{{isset($agent) ? $agent->fax : old('fax')}}">
                                <span class="icon is-small is-left">
                                        <i class="fa fa-fax"></i>
                                    </span>
                            </div>

                            @if ($errors->has('fax'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('fax') }}</strong>
                                </span>
                            @endif
                        </div>

                        <div class="field">
                            <label class="label" for="">Bio:</label>
                            <textarea id="editor" name="description" class="textarea" rows="5" minlength="5" required>{{isset($agent) ? $agent->description : old('description')}} </textarea>
                            @if ($errors->has('description'))
                                <span class="help-block">
                                                    <strong>{{ $errors->first('description') }}</strong>
                                                </span>
                            @endif
                        </div>

                        @if(Auth::user()->isAdmin())
                        <br>
                        <label class="label form-content" for="">Do you approve it?</label>
                        <label><input placeholder="" type="radio" class="radio" name="is_verified" value="0" @if($agent->is_verified==0) checked @endif />Pending</label>
                        <label><input placeholder="" type="radio" class="radio" name="is_verified" value="1" @if($agent->is_verified==1) checked @endif />Approve</label>
                        <label><input placeholder="" type="radio" class="radio" name="is_verified" value="-1" @if($agent->is_verified==-1) checked @endif />Decline</label>
                        <input type="hidden" name="old_verified" value="{{$agent->is_verified}}" />
                        <br><br><br>
                        @endif

                        <button type="button" class="button is-primary mainbgc" onclick="validForm();">
                            Save
                        </button>

                    </form>

                </div>

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
    <script>
        var editor = CKEDITOR.replace( 'editor' );

        function validForm() {
            var is_verified = $("input[name='is_verified']:checked").val();
            if (is_verified != $("input[name='old_verified']").val()) {
                if (is_verified == 1) 
                    alert('An approval email will be sent to User ID: {{$agent->user_id}} Email: {{$agent->user->email}}');
                if (is_verified == -1) 
                    alert('A decline email will be sent to User ID: {{$agent->user_id}} Email: {{$agent->user->email}}');
            }
            document.updateForm.submit();
        }

        function readURL(input) {
            $("#images-1").show();
            if (input.files && input.files[0]) {

                var reader = new FileReader();
                reader.onload = function (e) {
                    $('#blah')
                        .attr('src', e.target.result)
                        .width('100%')
                        .height(200);
                };

                reader.readAsDataURL(input.files[0]);
            }
            $('#deleteButton').show();
        }

        $("#deleteButton").click(function(){
            $("#images-1").hide();
            $("#photo").val('');
        });
    </script>
@endsection
