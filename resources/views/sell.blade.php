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
            <section >
                <div class="main-content columns is-mobile is-centered" style="margin-bottom:40px;">
                    <div class="column is-half is-narrow">

                        <div class="panel-body">
                            @if (session('status'))
                                <div class="alert alert-success" style="margin-bottom:25px;">
                                    {!! session('status') !!}
                                </div>
                            @endif

                            <h2 class="title is-4 has-text-centered">{{isset($list) ? 'Your Sale Listing' : 'Your New Sale Listing'}}</h2>

                            <div id="messageResponse" class="has-text-centered"></div>

                            <form name="listForm" method="POST" action="{{ route('addsell') }}" enctype="multipart/form-data">
                                {{ csrf_field() }}
                                <input type="hidden" id="long" name="long" value="{{isset($list) ? $list->long : ''}}" />
                                <input type="hidden" id="lat" name="lat" value="{{isset($list) ? $list->lat : ''}}" />

                                <div class="column margin_col" style="margin-left:0px !important;margin-right:0px !important;">
                                    <div id="page1">

                                        <div class="fields content">
                                            <div class="columns">
                                                <div class="column" style="padding-left:0px;">
                                                    <label class="label">Type of Property: <span class="req" style="display:inline;">*</span></label>
                                                    <div class="select">
                                                        <select name="type" id="ptype" class="req-f">
                                                            <option selected disabled value="0">Please select type</option>
                                                            @foreach($types as $type)
                                                                @if ($type->filter_data_id != 40 && $type->filter_data_id != 692)
                                                                    @if (\Illuminate\Support\Facades\Input::old('type') == $type->filter_data_id)
                                                                        <option value="{{ $type->filter_data_id }}" selected>{{$type->value}}</option>
                                                                    @elseif(isset($list) && $list->type_id == $type->filter_data_id)
                                                                        <option value="{{$type->filter_data_id}}" selected>{{$type->value}}</option>
                                                                    @else
                                                                        <option value="{{$type->filter_data_id}}">{{$type->value}}</option>

                                                                    @endif
                                                                @endif
                                                            @endforeach
                                                        </select>
                                                        @if ($errors->has('type'))
                                                        <span class="help-block">
                                                            <strong>{{ $errors->first('type') }}</strong>
                                                        </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="fields content" style="margin-top:1.8rem">
                                            <div class="columns">
                                                <div class="column" style="padding-left:0px;margin-right:1.5rem;">
                                                    <label class="label">Property Address: <span class="req">*</span></label>
                                                    <div class="select">
                                                        <select name="boro" id="boro" class="req-f">
                                                            @foreach($districts as $district)
                                                                @if (\Illuminate\Support\Facades\Input::old('boro') == $district->filter_data_id)
                                                                    <option value="{{ $district->filter_data_id }}" selected>{{$district->value}}</option>
                                                                @elseif(isset($list) && $list->boro == $district->filter_data_id)
                                                                    <option value="{{$district->filter_data_id}}" selected>{{$district->value}}</option>
                                                                @else
                                                                    <option value="{{$district->filter_data_id}}">{{$district->value}}</option>
                                                                @endif
                                                            @endforeach
                                                        </select>
                                                        @if ($errors->has('district'))
                                                        <span class="help-block">
                                                            <strong>{{ $errors->first('district') }}</strong>
                                                        </span>
                                                        @endif
                                                        <input class="hidden" name="user_id" value="{{ Auth::user()->id }}">
                                                        <input class="hidden" name="model" value="@if(isset($list)) 1
                                                        @elseif(old('model')) {{old('model')}}
                                                        @else 0 @endif">
                                                        @if(isset($list))
                                                            <input type="hidden" name="id" value="{{$list->id}}">
                                                        @elseif(old('id')) 
                                                            <input type="hidden" name="id" value="{{old('id')}}">
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="column" style="padding-left:0px;position:relative;">
                                                    <div id="list-google-map"></div>
                                                    <label class="label" for="">Neighborhood: <span class="req">*</span></label>
                                                    <div class="select">
                                                        <select name="district" id="district" class="req-f">
                                                            <option data-parent="0" value="">Please select neighborhood</option>
                                                            @foreach($districts as $district)

                                                                @if($district->value == 'Manhattan' || $district->value == 'Staten Island')
                                                                    @foreach($district->subdistritcs as $subdistricts)
                                                                        @foreach($subdistricts as $subdistrict)
                                                                            @if (\Illuminate\Support\Facades\Input::old('district') == $subdistrict->filter_data_id)
                                                                                @if (in_array($subdistrict->value, array('Beekman', 'Kips Bay', 'Murray Hill', 'Sutton Place', 'Turtle Bay', "Hell's Kitchen", 'Hudson Yards')))
                                                                                    <option value="{{$subdistrict->filter_data_id}}" data-parent="{{$district->filter_data_id}}" selected>&nbsp;&nbsp;&nbsp;&nbsp;- {{$subdistrict->value}}</option>
                                                                                @else
                                                                                    <option value="{{$subdistrict->filter_data_id}}" data-parent="{{$district->filter_data_id}}" selected>{{$subdistrict->value}}</option>
                                                                                @endif
                                                                            @elseif(isset($list) && $list->district_id == $subdistrict->filter_data_id)
                                                                                @if (in_array($subdistrict->value, array('Beekman', 'Kips Bay', 'Murray Hill', 'Sutton Place', 'Turtle Bay', "Hell's Kitchen", 'Hudson Yards')))
                                                                                    <option value="{{$subdistrict->filter_data_id}}" data-parent="{{$district->filter_data_id}}" selected>&nbsp;&nbsp;&nbsp;&nbsp;- {{$subdistrict->value}}</option>
                                                                                @else
                                                                                    <option value="{{$subdistrict->filter_data_id}}" data-parent="{{$district->filter_data_id}}" selected>{{$subdistrict->value}}</option>
                                                                                @endif
                                                                            @else
                                                                                @if($subdistrict->value == "ALL DOWNTOWN" || $subdistrict->value == "ALL MIDTOWN" ||
                                                                                    $subdistrict->value == "Midtown East" || $subdistrict->value == "Midtown West" ||
                                                                                    $subdistrict->value == "ALL UPPER EAST SIDE" || $subdistrict->value == "ALL UPPER WEST SIDE" || $subdistrict->value == "ALL UPPER MANHATTAN" ||
                                                                                    $subdistrict->value == "East Shore" || $subdistrict->value == "Mid-Island" || $subdistrict->value == "South Shore" || $subdistrict->value == "West Shore")
                                                                                    {{--<optgroup label="{{$subdistrict->value}}"></optgroup>--}}
                                                                                    <option value="{{$subdistrict->value}}" data-parent="{{$district->filter_data_id}}" disabled style="color:#000;font-weight:bold;background-color:#ccc;"><b>{{$subdistrict->value}}</b></option>
                                                                                @elseif (in_array($subdistrict->value, array('Beekman', 'Kips Bay', 'Murray Hill', 'Sutton Place', 'Turtle Bay', "Hell's Kitchen", 'Hudson Yards')))
                                                                                    <option value="{{$subdistrict->filter_data_id}}" data-parent="{{$district->filter_data_id}}">&nbsp;&nbsp;&nbsp;&nbsp;- {{$subdistrict->value}}</option>
                                                                                @else
                                                                                    <option value="{{$subdistrict->filter_data_id}}" data-parent="{{$district->filter_data_id}}">{{$subdistrict->value}}</option>
                                                                                @endif
                                                                            @endif
                                                                        @endforeach
                                                                    @endforeach
                                                                @else
                                                                    @foreach($district->subdistritcs as $subdistrict)
                                                                        @if (\Illuminate\Support\Facades\Input::old('district') == $subdistrict->filter_data_id)
                                                                            <option value="{{ $subdistrict->filter_data_id }}" data-parent="{{$district->filter_data_id}}" selected>{{$subdistrict->value}}</option>
                                                                        @elseif(isset($list) && $list->district_id == $subdistrict->filter_data_id)
                                                                            <option value="{{$subdistrict->filter_data_id}}"  data-parent="{{$district->filter_data_id}}" selected>{{$subdistrict->value}}</option>
                                                                        @else
                                                                            <option value="{{$subdistrict->filter_data_id}}" data-parent="{{$district->filter_data_id}}">{{$subdistrict->value}}</option>
                                                                        @endif
                                                                    @endforeach
                                                                @endif

                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    @if ($errors->has('district'))
                                                        <span class="help-block">
                                                                <strong>{{ $errors->first('district') }}</strong>
                                                            </span>
                                                    @endif
                                                </div>
                                            </div>



                                            <div class="columns is-variable form-content">
                                                <div class="column">
                                                    <label class="label" for="" style="position:relative;">Street Address: <span class="req">*</span><span class="clear-select">[x]</span></label>
                                                    <input id="street_address" placeholder="" type="hidden" class="input req-f" value="{{isset($list) ? $list->address : old('street_address')}}" name="street_address"  minlength="3" />


                                                    <select id="address" class="input" name="address">
                                                        @if(isset($list))
                                                            <option value="{{$list->building_id}}" selected="selected">{{$list->address}}</option>
                                                        @else
                                                            <option selected="selected"></option>
                                                        @endif
                                                    </select>
                                                    @if ($errors->has('street_address'))
                                                        <span class="help-block">
                                                            <strong>{{ $errors->first('street_address') }}</strong>
                                                        </span>
                                                    @endif
                                                </div>
                                                <div class="column">
                                                    <div>
                                                        <label class="label" for="">City: <span class="req">*</span><span class="clear">[X]</span></label>
                                                        <input placeholder="" type="text" class="input req-f" id="city" name="city" value="{{isset($list) ? $list->city : old('city')}}" />
                                                    </div>
                                                        @if ($errors->has('city'))
                                                        <span class="help-block">
                                                            <strong>{{ $errors->first('city') }}</strong>
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="columns is-variable form-content">
                                                <div class="column">
                                                    <div>
                                                        <label class="label" for="">Apartment: <span class="may-req t6 t7">*</span><span class="clear">[X]</span></label>
                                                        <input placeholder="" type="text" class="input may-req-f t6 t7" value="{{isset($list) ? $list->apartment : old('apartment')}}"  name="apartment" max-length="20" />
                                                    </div>
                                                    @if ($errors->has('apartment'))
                                                        <span class="help-block">
                                                            <strong>{{ $errors->first('apartment') }}</strong>
                                                        </span>
                                                    @endif
                                                </div>
                                                <div class="column">
                                                    <div>
                                                        <label class="label" for="">State: <span class="clear">[X]</span></label>
                                                        <input placeholder="" type="text" class="input" name="ny" value="@if(isset($list)){{$list->state}}@elseif(old('ny')){{old('ny')}}@else NY @endif" maxlength="5" readonly="readonly" />
                                                    </div>
                                                        @if ($errors->has('ny'))
                                                            <span class="help-block">
                                                                <strong>{{ $errors->first('ny') }}</strong>
                                                            </span>
                                                        @endif
                                                </div>
                                                <div class="column">
                                                    <div>
                                                        <label class="label" for="">ZIP: <span class="req">*</span><span class="clear">[X]</span></label>
                                                        <input placeholder="" type="text" class="input req-f" name="zip" value="{{isset($list) ? $list->zip : old('zip')}}" maxlength="5" />
                                                    </div>
                                                        @if ($errors->has('zip'))
                                                            <span class="help-block">
                                                                <strong>{{ $errors->first('zip') }}</strong>
                                                            </span>
                                                        @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="fields content" style="margin-top:3.5rem;">
                                            <label class="label is-medium">Detail Information:</label>                                            

                                            <div class="columns is-variable form-content">
                                                <div class="column">
                                                    <div style="margin-bottom:1rem">
                                                        <label class="label" for="">Status: <span class="req">*</span></label>
                                                        <div class="select">
                                                            <select name="status" id="status" class="req-f">                                                                
                                                                <option value="1" @if (\Illuminate\Support\Facades\Input::old('status') == '1' || isset($list) && $list->status == '1') selected @endif>Available</option>
                                                                <option value="-1" @if (\Illuminate\Support\Facades\Input::old('status') == '-1' || isset($list) && $list->status == '-1') selected @endif>Off Market</option>
                                                                <option value="-2" @if (\Illuminate\Support\Facades\Input::old('status') == '-2' || isset($list) && $list->status == '-2') selected @endif>In Contract</option>
                                                                <option value="-3" @if (\Illuminate\Support\Facades\Input::old('status') == '-3' || isset($list) && $list->status == '-3') selected @endif>Sold</option>
                                                            </select>
                                                            <input type="hidden" name="old_status" value="{{isset($list) ? $list->status : ''}}" />
                                                        </div>
                                                    </div>
                                                    @if ($errors->has('status'))
                                                        <span class="help-block">
                                                        <strong>{{ $errors->first('status') }}</strong>
                                                    </span>
                                                    @endif

                                                    <div>
                                                        <label class="label" for="">Bed: <span class="may-req t6 t7">*</span><span class="clear">[X]</span></label>
                                                        <input placeholder="" type="text" class="input may-req-f t6 t7" name="bed" value="{{isset($list) ? $list->beds : old('bed')}}" maxlength="5" />
                                                    </div>
                                                    @if ($errors->has('bed'))
                                                        <span class="help-block">
                                                            <strong>{{ $errors->first('bed') }}</strong>
                                                        </span>
                                                    @endif
                                                    <div style="margin-top:1rem">
                                                         <label class="label" for="">Size (ft<sup>2</sup>): <span class="may-req t6 t8 t9">*</span><span class="clear">[X]</span></label>
                                                         <input placeholder="" type="text" class="input may-req-f t6 t8 t9" name="size" value="{{isset($list) ? $list->sq_feet : old('size')}}" maxlength="6" />
                                                     </div>
                                                    @if ($errors->has('size'))
                                                        <span class="help-block">
                                                            <strong>{{ $errors->first('size') }}</strong>
                                                        </span>
                                                    @endif
                                                    <div style="margin-top:1rem">
                                                         <label class="label" for="">Total Units: <span class="may-req t8 t9">*</span><span class="clear">[X]</span></label>
                                                         <input placeholder="" type="text" class="input may-req-f t8 t9" name="unit" value="{{isset($list) ? $list->units : old('unit')}}" maxlength="5" />
                                                     </div>
                                                    @if ($errors->has('unit'))
                                                        <span class="help-block">
                                                            <strong>{{ $errors->first('unit') }}</strong>
                                                        </span>
                                                    @endif
                                                </div>

                                                <div class="column">
                                                    <div style="margin-bottom:1rem">
                                                        <label class="label" for="">Condition: <span class="req">*</span></label>
                                                        <div class="select">
                                                            <select name="condition" id="condition" class="req-f">                                                                
                                                                <option value="Good" @if (\Illuminate\Support\Facades\Input::old('condition') == 'Good' || isset($list) && $list->condition == 'Good') selected @endif>Good</option>
                                                                <option value="Excellent" @if (\Illuminate\Support\Facades\Input::old('condition') == 'Excellent' || isset($list) && $list->condition == 'Excellent') selected @endif>Excellent</option>
                                                                <option value="Fair" @if (\Illuminate\Support\Facades\Input::old('condition') == 'Fair' || isset($list) && $list->condition == 'Fair') selected @endif>Fair</option>
                                                                <option value="Need Work" @if (\Illuminate\Support\Facades\Input::old('condition') == 'Need Work' || isset($list) && $list->condition == 'Need Work') selected @endif>Need Work</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    @if ($errors->has('condition'))
                                                        <span class="help-block">
                                                        <strong>{{ $errors->first('condition') }}</strong>
                                                    </span>
                                                    @endif

                                                    <div>
                                                        <label class="label" for="">Bath: <span class="may-req t6 t7">*</span><span class="clear">[X]</span></label>
                                                        <input placeholder="" type="text" class="input may-req-f t6 t7" name="bath" value="{{isset($list) ? $list->baths : old('bath')}}" maxlength="5" />
                                                    </div>
                                                    @if ($errors->has('bath'))
                                                        <span class="help-block">
                                                        <strong>{{ $errors->first('bath') }}</strong>
                                                    </span>
                                                    @endif

                                                    <div style="margin-top:1rem">
                                                        <label class="label" for="">Total Room: <span class="may-req t6 t7">*</span><span class="clear">[X]</span></label>
                                                        <input placeholder="" type="text" class="input may-req-f t6 t7" name="room" value="{{isset($list) ? $list->room : old('room')}}" maxlength="6" />
                                                    </div>
                                                    @if ($errors->has('room'))
                                                        <span class="help-block">
                                                        <strong>{{ $errors->first('room') }}</strong>
                                                    </span>
                                                    @endif

                                                    <div style="margin-top:1rem">
                                                        <label class="label" for="">Furnished:</label>
                                                        <div class="select">
                                                            <select name="furnished" id="furnished">                                                                
                                                                <option value="0" @if (\Illuminate\Support\Facades\Input::old('furnished') == '0' || isset($list) && $list->furnished == '0') selected @endif>No</option>
                                                                <option value="1" @if (\Illuminate\Support\Facades\Input::old('furnished') == '1' || isset($list) && $list->furnished == '1') selected @endif>Yes</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    @if ($errors->has('furnished'))
                                                        <span class="help-block">
                                                        <strong>{{ $errors->first('furnished') }}</strong>
                                                    </span>
                                                    @endif
                                                    
                                                </div>
                                            </div>

                                            <label class="label desc" for="">Description: <span class="req">*</span><span class="clear-text">[X]</span></label>
                                            <textarea id="editor" name="description" class="textarea req-f" rows="5" minlength="5" > {{isset($list) ? $list->unit_description : \Illuminate\Support\Facades\Input::old('description')}}</textarea>
                                            @if ($errors->has('description'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('description') }}</strong>
                                            </span>
                                            @endif
                                            <div>
                                                <label class="label form-content" for="">Web of agent about the property: <span class="clear">[X]</span></label>
                                                <input placeholder="" type="text" class="input" name="web" value="{{isset($list) ? $list->web : old('web')}}" />
                                            </div>
                                            @if ($errors->has('web'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('web') }}</strong>
                                            </span>
                                            @endif

                                            <div>
                                                <label class="label form-content" for="">Price: <span class="req">*</span><span class="clear">[X]</span></label>
                                                <input placeholder="" type="text" class="input form-input-width req-f" name="price" value="{{isset($list) ? $list->price : old('price')}}" maxlength="13" />
                                                <input type="hidden" name="last_price" value="{{isset($list) ? (Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $list->user_updated_at)->format('Y-m-d') == Carbon\Carbon::now()->format('Y-m-d') ? $list->last_price : $list->price) : old('last_price')}}" />
                                            </div>
                                            @if ($errors->has('price'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('price') }}</strong>
                                                </span>
                                            @endif

                                            <div class="fields content" style="margin-top:3.5rem;">
                                            <label class="label is-medium form-content" for="">Monthly Charge:</label>
                                            <div class="columns is-variable form-content">
                                                <div class="column">
                                                    <div>
                                                        <label class="label" for="">Monthly Tax: <span class="may-req t6 t8 t9">*</span><span class="clear">[X]</span></label>
                                                        <input placeholder="" type="text" class="input may-req-f t6 t8 t9" name="tax" value="{{isset($list) ? $list->monthly_taxes : old('tax')}}" maxlength="8" />
                                                    </div>
                                                    @if ($errors->has('tax'))
                                                        <span class="help-block">
                                                            <strong>{{ $errors->first('tax') }}</strong>
                                                        </span>
                                                    @endif

                                                    {{--<div style="margin-top:1rem;">
                                                        <label class="label" for="">Common Charge: <span class="may-req t6">*</span><span class="clear">[X]</span></label>
                                                        <input placeholder="" type="text" class="input may-req-f t6" name="charge" value="{{isset($list) ? $list->common_charges : old('charge')}}" maxlength="8" />
                                                    </div>--}}
                                                    @if ($errors->has('charge'))
                                                        <span class="help-block">
                                                            <strong>{{ $errors->first('charge') }}</strong>
                                                        </span>
                                                    @endif
                                                </div>
                                                <div class="column">
                                                    <div>
                                                        <label class="label" for=""><span id="maint_7">Monthly Maintenance: (Co-Op)</span><span id="maint_6">Monthly Common Charge:</span> <span class="may-req t6 t7">*</span><span class="clear">[X]</span></label>
                                                        <input placeholder="" type="text" class="input may-req-f t6 t7" name="maintenance" value="{{isset($list) ? $list->maintenance : old('maintenance')}}" maxlength="8" />
                                                    </div>
                                                    @if ($errors->has('maintenance'))
                                                    <span class="help-block">
                                                        <strong>{{ $errors->first('maintenance') }}</strong>
                                                    </span>
                                                    @endif
                                                </div>
                                            </div>                                            

                                            @if (0)
                                            <div>
                                                <label class="label" for="">Total Commission: <span class="clear">[X]</span></label>
                                                <input placeholder="" type="text" class="input form-input-width" name="commission" value="{{isset($list) ? $list->commission : old('commission')}}" maxlength="8" />                                                
                                            </div>   
                                            @if ($errors->has('commission'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('commission') }}</strong>
                                            </span>
                                            @endif
                                            @endif

                                            </div>

                                            <label class="label form-content" for="">Allow broker to contact you?</label>
                                            <label><input placeholder="" type="radio" class="radio" name="broker" value="1" checked/>Yes</label>
                                            <label><input placeholder="" type="radio" class="radio" name="broker" value="0"
                                                          @if(isset($list) && $list->broker == "0")checked
                                                          @elseif(old('broker','broker')=="0") checked @endif/>No</label>
                                        </div>

                                        <div class="fix checkline">
                                            <label class="label">Apartment Features: </label>
                                            <ul class="filters_list">
                                                @foreach ( $filters as $k=>$filter)
                                                    @if($filter->parent_id == 6)
                                                        <li class="">
                                                            <label class="" for="{{$filter->filter_data_id}}">
                                                                <input name="filters[]" class="checkbox" type="checkbox" id="{{$filter->filter_data_id}}" value="{{$filter->filter_data_id}}"
                                                                       @if(isset($list) && is_array($list->amenities) && in_array($filter->filter_data_id, $list->amenities))  checked
                                                                       @elseif(is_array(old('filters')) && in_array($filter->filter_data_id, old('filters')))
                                                                       checked
                                                                        @endif
                                                                >
                                                                {{$filter->value}}</label>
                                                        </li>
                                                    @endif
                                                @endforeach
                                            </ul>
                                        </div>
                                        @if ($errors->has('features'))
                                            <span class="help-block">
                                            <strong>{{ $errors->first('features') }}</strong>
                                        </span>
                                        @endif

                                        <div class="fix checkline" style="margin-top:1.6rem;">
                                            <label class="label">Building Amenities: </label>
                                            <ul class="filters_list">
                                                @foreach ( $filters as $k=>$filter)
                                                    @if($filter->parent_id == 0)
                                                        <li class="">
                                                            <label class="" for="{{$filter->filter_data_id}}">
                                                                <input name="filters[]" class="checkbox" type="checkbox" id="{{$filter->filter_data_id}}" value="{{$filter->filter_data_id}}"
                                                                       @if(isset($list) && is_array($list->amenities) && in_array($filter->filter_data_id, $list->amenities))  checked
                                                                       @elseif(is_array(old('filters')) && in_array($filter->filter_data_id, old('filters')))
                                                                       checked
                                                                        @endif
                                                                >
                                                                {{$filter->value}}</label>
                                                        </li>
                                                    @endif
                                                @endforeach
                                            </ul>
                                        </div>
                                        @if ($errors->has('filters'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('filters') }}</strong>
                                        </span>
                                        @endif

                                        <div class="fields content">
                                            <div style="margin-top:1.5rem;width:48%;">
                                                <label class="label form-content" for="">Year Built: <span class="clear">[X]</span></label>
                                                <input placeholder="" type="text" class="input form-input-width" name="year_built" value="{{isset($list) ? $list->year_built : old('year_built')}}" maxlength="4" />
                                            </div>
                                            @if ($errors->has('year_built'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('year_built') }}</strong>
                                            </span>
                                            @endif

                                            @if(isset($list->OpenHouse) && !$list->OpenHouse->isEmpty())
                                                @foreach($list->OpenHouse as $key=>$v)
                                                    @if(Carbon\Carbon::now()->format('Y-m-d') > $v->date)
                                                        @php unset($list->OpenHouse[$key]) @endphp
                                                    @endif
                                                @endforeach
                                                @foreach($list->OpenHouse as $key=>$item)
                                                    <div id="openHouse-{{$key}}" class="block">
                                                        <div class="form-content columns is-variable">
                                                            <div class="column" style="padding-bottom:0px;">
                                                                <label class="label form-content" for="">Open House:</label>
                                                                <input type="date" name="openHouse[{{$key}}][date]" class="input" value="{{$item->date}}">
                                                                <input type="hidden" name="openHouse[{{$key}}][openID]" class="input" value="{{$item->id}}">
                                                            </div>
                                                            <div class="column" style="padding-bottom:0px;">
                                                                <label class="label form-content" for="">Starts:</label>
                                                                <div class="select">
                                                                    <select name="openHouse[{{$key}}][start]">
                                                                        @foreach($openHours as $value)
                                                                            <option value="{{$value->hour}}" {{Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $item->start_time)->format('H:i') == $value->hour ? 'selected' : ''}}>{{$value->title}}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="column" style="padding-bottom:0px;">
                                                                <label class="label form-content" for="">Ends:</label>
                                                                <div class="select">
                                                                    <select name="openHouse[{{$key}}][end]">
                                                                        @foreach($openHours as $value)
                                                                            <option value="{{$value->hour}}" {{Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $item->end_time)->format('H:i') == $value->hour ? 'selected' : ''}}>{{$value->title}}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="column" style="padding-bottom:0px;margin-top:2rem;">
                                                                <label class="label form-content" for="" style="white-space: nowrap;">
                                                                    <input type="checkbox" name="openHouse[{{$key}}][appointment]" value="1" {{$item->appointment == 1 ? 'checked' : ''}}>&nbsp;By Appointment</label>
                                                            </div>
                                                            <div class="column listremove" style="padding-bottom:0px;">
                                                                <deleteopenhouse inline-template v-bind:id="'{{$item->id}}'" v-bind:num="'{{$key}}'">
                                                                    <a v-on:click="deleteOpenHouse">Remove</a>
                                                                </deleteopenhouse>
                                                            </div>
                                                        </div>

                                                    </div>
                                                @endforeach
                                                <div class="form-content">
                                                    <a class="more button is-primary mainbgc" style="margin-top:1.5rem;" {{$key>=0 ? "data-key=".$key : ''}} >Add an Open House</a>
                                                </div>
                                            @else

                                                <div class="form-content">
                                                    <a class="more button is-primary mainbgc" style="margin-top:1.5rem;">Add an Open House</a>
                                                </div>

                                            @endif

                                            <div class="form-content columns is-variable">
                                                <div class="column">
                                                    <label class="label " for="">Images: <span class="req">*</span></label>
                                                    <input type="file" name="image[]" class="select is-primary req-f" id="image" value="{{ old('image') }}" accept="image/*" multiple >
                                                    @if ($errors->has('image'))
                                                        <div style="clear:both;"></div>
                                                        <span class="help-block">
                                                           <strong>{{ $errors->first('image') }}</strong>
                                                        </span>
                                                    @elseif (session('img_err'))
                                                        <div style="clear:both;"></div>
                                                        <span class="help-block">
                                                           <strong>{{ session('img_err') }}</strong>
                                                        </span>
                                                    @endif
                                                    @if(isset($list) && isset($list->path_for_images))
                                                        @foreach($list->path_for_images as $k=>$item)
                                                            @if($item !== '')
                                                                <div class="images-wrapper" id="images-{{$k}}" style="margin-top:1rem;padding-left:0rem;position:relative;">
                                                                    @if($list->amazon_id == 0)
                                                                        <img src="{{ env('S3_IMG_PATH') }}{{$item}}" />
                                                                    @else
                                                                        <img src="{{ env('S3_IMG_PATH_1') }}{{$item}}" />
                                                                    @endif
                                                                    <div class="top-right" style="top:0px;">
                                                                        <deletesellimages inline-template v-bind:list="'{{$list->id}}'" v-bind:num="'{{$k}}'" :names="'images'" v-bind:img_name="'{{explode('/', $item)[count(explode('/', $item))-1]}}'">
                                                                            <button type="button" v-on:click="deleteImage"><i class="fa fa-trash" aria-hidden="true"></i></button>
                                                                        </deletesellimages>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                    @endif
                                                </div>
                                                <div class="column">

                                                    <label class="label" for="">Floor Plan: </label>
                                                    <input type="file" name="plans[]" class="select is-primary" id="plans" value="{{ old('plans') }}" accept="image/*" multiple>
                                                    @if ($errors->has('plans'))
                                                    <div style="clear:both;"></div>
                                                    <span class="help-block">
                                                        <strong>{{ $errors->first('plans') }}</strong>
                                                    </span>
                                                    @elseif (session('plan_err'))
                                                        <div style="clear:both;"></div>
                                                        <span class="help-block">
                                                           <strong>{{ session('plan_err') }}</strong>
                                                        </span>
                                                    @endif
                                                    @if(isset($list) && isset($list->path_for_floorplans))
                                                        @foreach($list->path_for_floorplans as $k=>$item)
                                                            @if($item !== '')
                                                                <div class="plans-wrapper" id="plans-{{$k}}" style="margin-top:1rem;padding-left:0rem;position:relative;">
                                                                    @if($list->amazon_id == 0)
                                                                        <img src="{{ env('S3_IMG_PATH') }}{{$item}}" />
                                                                    @else
                                                                        <img src="{{ env('S3_IMG_PATH_1') }}{{$item}}" />
                                                                    @endif
                                                                    <div class="top-right" style="top:0px;">
                                                                        <deletesellimages inline-template v-bind:list="'{{$list->id}}'" v-bind:num="'{{$k}}'" :names="'plans'" v-bind:img_name="'{{explode('/', $item)[count(explode('/', $item))-1]}}'">
                                                                            <button type="button" v-on:click="deleteImage"><i class="fa fa-trash" aria-hidden="true"></i></button>
                                                                        </deletesellimages>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                    @endif
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                    <div id="page2" style="display:none">
                                        <div class="fields content">

                                            <label class="label is-medium" for="">Submit Document: </label>

                                            <div class="columns is-variable">
                                                <div class="column" style="margin-top:1rem;position:relative;">

                                                    <label class="label" for="">Agent:</label>
                                                    @if(Auth::user()->isAgent())
                                                    <div style="margin-bottom:10px;">Please type in your name. If your name appears on our agents list or if you have an agent url, you don't need to submit your real estate licence.</div>
                                                    <label class="label" for="">Your Name:</label>
                                                    <div class="control has-icons-left ">
                                                        <input class="input" type="text" id="input_name" name="name" value="{{ old('name') }}" placeholder="Name" autofocus onkeyup="searchAgent(this.value);">
                                                        <ul id="agent_dropdown" class="dropdown-content dropdown-menu" style="margin-top:0px;">
                                                        </ul>
                                                        <span class="icon is-small is-left">
                                                        <i class="fa fa-user"></i>
                                                        </span>
                                                    </div>
                                                    @endif
                                                    <input type="hidden" id="name_on_list" name="name_on_list" value="0" />
                                                    <label class="label ex_agree" for="" style="margin-top:1rem;">Real Estate Licence: @if(Auth::user()->isAgent())<span style="color:red;">*</span>@endif</label>
                                                    <input type="file" name="agreement[]" class="ex_agree" value="{{ old('agreement') }}" id="agreement" accept="image/jpeg,image/gif,image/png,.bmp,.svg,application/pdf" multiple>
                                                    @if ($errors->has('agreement'))
                                                    <div style="clear:both;"></div>
                                                    <span class="help-block">
                                                        <strong>{{ $errors->first('agreement') }}</strong>
                                                    </span>
                                                    @elseif (session('agreement_err'))
                                                    <div style="clear:both;"></div>
                                                    <span class="help-block">
                                                        <strong>{{ session('agreement_err') }}</strong>
                                                    </span>
                                                    @endif
                                                    @if(isset($list) && isset($list->path_for_ex_agreement))
                                                        @foreach($list->path_for_ex_agreement as $k=>$item)
                                                            @if($item !== '')
                                                                <div class="agreement-wrapper ex_agree" id="agreement-{{$k}}" style="margin-top:1rem;padding-left:0rem;position:relative;">
                                                                    @if (preg_match('/\.pdf/', $item))
                                                                        <img src="/images/pdf.png" style="width:150px;" onclick="window.open('{{ env('S3_IMG_PATH_1') }}{{$item}}');" />
                                                                    @elseif($list->amazon_id == 0)
                                                                        <img src="{{ env('S3_IMG_PATH') }}{{$item}}" onclick="window.open('{{ env('S3_IMG_PATH') }}{{$item}}');" />
                                                                    @else
                                                                        <img src="{{ env('S3_IMG_PATH_1') }}{{$item}}" onclick="window.open('{{ env('S3_IMG_PATH_1') }}{{$item}}');" />
                                                                    @endif
                                                                    <div class="top-right" style="top:0px;">
                                                                        <deletesellimages inline-template v-bind:list="'{{$list->id}}'" v-bind:num="'{{$k}}'" :names="'agreement'" v-bind:img_name="'{{explode('/', $item)[count(explode('/', $item))-1]}}'">
                                                                            <button type="button" v-on:click="deleteImage"><i class="fa fa-trash" aria-hidden="true"></i></button>
                                                                        </deletesellimages>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                    @endif

                                                    {{--<label class="label form-content" for="" style="margin-top:1rem;">Copy License: </label>
                                                    <input type="file" name="copylicense[]" class="" value="{{ old('copylicense') }}" id="copylicense" accept="image/jpeg,image/gif,image/png,.bmp,.svg,application/pdf" multiple>
                                                    @if ($errors->has('copylicense'))
                                                    <span class="help-block">
                                                        <strong>{{ $errors->first('copylicense') }}</strong>
                                                    </span>
                                                    @endif                                                    
                                                    @if(isset($list) && isset($list->path_for_copy_licence))
                                                        @foreach($list->path_for_copy_licence as $k=>$item)
                                                            @if($item !== '')
                                                                <div class="licence-wrapper" id="licence-{{$k}}" style="margin-top:1rem;padding-left:0rem;position:relative;">
                                                                    @if($list->amazon_id == 0)
                                                                        <img src="{{ env('S3_IMG_PATH') }}{{$item}}" />
                                                                    @else
                                                                        <img src="{{ env('S3_IMG_PATH_1') }}{{$item}}" />
                                                                    @endif
                                                                    <div class="top-right" style="top:0px;">
                                                                        <deletesellimages inline-template v-bind:list="'{{$list->id}}'" v-bind:num="'{{$k}}'" :names="'licence'" v-bind:img_name="'{{explode('/', $item)[count(explode('/', $item))-1]}}'">
                                                                            <button type="button" v-on:click="deleteImage"><i class="fa fa-trash" aria-hidden="true"></i></button>
                                                                        </deletesellimages>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                    @endif
                                                    --}}
                                                </div>
                                                <div class="column" style="margin-top:1rem;position:relative;">
                                                    <label class="label" for="">
                                                        Owner: 
                                                        <br><span style="font-size:14px;font-weight:normal;">(Choose one of the followings:)</span>
                                                    </label>
                                                    <label class="label" for="" style="margin-top:1rem;">Deed: @if(Auth::user()->isOwner())<span style="color:red;">*</span>@endif</label>
                                                    <input type="file" name="deed[]" class="" value="{{ old('deed') }}" id="deed" accept="image/jpeg,image/gif,image/png,.bmp,.svg,application/pdf" multiple>
                                                    @if ($errors->has('deed'))
                                                    <div style="clear:both;"></div>
                                                    <span class="help-block">
                                                    <strong>{{ $errors->first('deed') }}</strong>
                                                    </span>
                                                    @elseif (session('deed_err'))
                                                    <div style="clear:both;"></div>
                                                    <span class="help-block">
                                                        <strong>{{ session('deed_err') }}</strong>
                                                    </span>
                                                    @endif
                                                    @if(isset($list) && isset($list->path_for_deed))
                                                        @foreach($list->path_for_deed as $k=>$item)
                                                            @if($item !== '')
                                                                <div class="deed-wrapper" id="deed-{{$k}}" style="margin-top:1rem;padding-left:0rem;position:relative;">
                                                                    @if (preg_match('/\.pdf/', $item))
                                                                        <img src="/images/pdf.png" style="width:150px;" onclick="window.open('{{ env('S3_IMG_PATH_1') }}{{$item}}');" />
                                                                    @elseif($list->amazon_id == 0)
                                                                        <img src="{{ env('S3_IMG_PATH') }}{{$item}}" onclick="window.open('{{ env('S3_IMG_PATH') }}{{$item}}');" />
                                                                    @else
                                                                        <img src="{{ env('S3_IMG_PATH_1') }}{{$item}}" onclick="window.open('{{ env('S3_IMG_PATH_1') }}{{$item}}');" />
                                                                    @endif
                                                                    <div class="top-right" style="top:0px;">
                                                                        <deletesellimages inline-template v-bind:list="'{{$list->id}}'" v-bind:num="'{{$k}}'" :names="'deed'" v-bind:img_name="'{{explode('/', $item)[count(explode('/', $item))-1]}}'">
                                                                            <button type="button" v-on:click="deleteImage"><i class="fa fa-trash" aria-hidden="true"></i></button>
                                                                        </deletesellimages>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                    @endif

                                                    <div style="font-weight:bold;margin-top:20px;">OR</div>

                                                    <label class="label form-content" for="" style="margin-top:1rem;">Utility Bill: @if(Auth::user()->isOwner())<span style="color:red;">*</span>@endif</label>
                                                    <input type="file" name="utilitybill[]" class="" value="{{ old('utilitybill') }}" id="utilitybill" accept="image/jpeg,image/gif,image/png,.bmp,.svg,application/pdf" multiple>
                                                    @if ($errors->has('utilitybill'))
                                                    <div style="clear:both;"></div>
                                                    <span class="help-block">
                                                    <strong>{{ $errors->first('utilitybill') }}</strong>
                                                    </span>
                                                    @elseif (session('utilitybill_err'))
                                                    <div style="clear:both;"></div>
                                                    <span class="help-block">
                                                        <strong>{{ session('utilitybill_err') }}</strong>
                                                    </span>
                                                    @endif
                                                    @if(isset($list) && isset($list->path_for_un_bill))
                                                        @foreach($list->path_for_un_bill as $k=>$item)
                                                            @if($item !== '')
                                                                <div class="bill-wrapper" id="bill-{{$k}}" style="margin-top:1rem;padding-left:0rem;position:relative;">
                                                                    @if (preg_match('/\.pdf/', $item))
                                                                        <img src="/images/pdf.png" style="width:150px;" onclick="window.open('{{ env('S3_IMG_PATH_1') }}{{$item}}');" />
                                                                    @elseif($list->amazon_id == 0)
                                                                        <img src="{{ env('S3_IMG_PATH') }}{{$item}}" onclick="window.open('{{ env('S3_IMG_PATH') }}{{$item}}');" />
                                                                    @else
                                                                        <img src="{{ env('S3_IMG_PATH_1') }}{{$item}}"onclick="window.open('{{ env('S3_IMG_PATH_1') }}{{$item}}');"  />
                                                                    @endif
                                                                    <div class="top-right" style="top:0px;">
                                                                        <deletesellimages inline-template v-bind:list="'{{$list->id}}'" v-bind:num="'{{$k}}'" :names="'bill'" v-bind:img_name="'{{explode('/', $item)[count(explode('/', $item))-1]}}'">
                                                                            <button type="button" v-on:click="deleteImage"><i class="fa fa-trash" aria-hidden="true"></i></button>
                                                                        </deletesellimages>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                    @endif

                                                    <div style="font-weight:bold;margin-top:20px;">OR</div>

                                                    <label class="label" for="" style="margin-top:1rem;">Photo ID: @if(Auth::user()->isOwner())<span style="color:red;">*</span>@endif</label>
                                                    <input type="file" name="photoid[]" class="select is-primary" value="{{ old('photoid') }}" id="photoid" accept="image/jpeg,image/gif,image/png,.bmp,.svg,application/pdf" multiple>
                                                    @if ($errors->has('photoid'))
                                                    <div style="clear:both;"></div>
                                                    <span class="help-block">
                                                        <strong>{{ $errors->first('photoid') }}</strong>
                                                    </span>
                                                    @elseif (session('photoid_err'))
                                                    <div style="clear:both;"></div>
                                                    <span class="help-block">
                                                        <strong>{{ session('photoid_err') }}</strong>
                                                    </span>
                                                    @endif
                                                    @if(isset($list) && isset($list->path_for_photo_id))
                                                        @foreach($list->path_for_photo_id as $k=>$item)
                                                            @if($item !== '')
                                                                <div class="photoid-wrapper" id="photoid-{{$k}}" style="margin-top:1rem;padding-left:0rem;position:relative;">
                                                                    @if (preg_match('/\.pdf/', $item))
                                                                        <img src="/images/pdf.png" style="width:150px;" onclick="window.open('{{ env('S3_IMG_PATH_1') }}{{$item}}');" />
                                                                    @elseif($list->amazon_id == 0)
                                                                        <img src="{{ env('S3_IMG_PATH') }}rental-listing-images/{{$item}}" onclick="window.open('{{ env('S3_IMG_PATH') }}{{$item}}');" />
                                                                    @else
                                                                        <img src="{{ env('S3_IMG_PATH_1') }}{{$item}}" onclick="window.open('{{ env('S3_IMG_PATH_1') }}{{$item}}');" />
                                                                    @endif
                                                                    <div class="top-right" style="top:0px;">
                                                                        <deletesellimages inline-template v-bind:list="'{{$list->id}}'" v-bind:num="'{{$k}}'" :names="'photoid'" v-bind:img_name="'{{explode('/', $item)[count(explode('/', $item))-1]}}'">
                                                                            <button type="button" v-on:click="deleteImage"><i class="fa fa-trash" aria-hidden="true"></i></button>
                                                                        </deletesellimages>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                    @endif
                                                </div>
                                            </div>

                                            <label class="label form-content" for="">Do you want to feature it?</label>
                                            <label><input placeholder="" type="radio" class="radio" name="feature" value="1" checked/>Yes</label>
                                            <label><input placeholder="" type="radio" class="radio" name="feature" value="0"
                                                          {{--@if(isset($list) && $list->feature == 1)checked--}}
                                                          @if(isset($list))checked
                                                          @elseif(old('feature','feature')=="0") checked @endif/>No</label>

                                            @if(Auth::user()->isAdmin())
                                            <label class="label form-content" for="">Do you approve it?</label>
                                            <label><input placeholder="" type="radio" class="radio" name="is_verified" value="0" @if($list->is_verified==0) checked @endif />Pending</label>
                                            <label><input placeholder="" type="radio" class="radio" name="is_verified" value="1" @if($list->is_verified==1) checked @endif />Approve</label>
                                            <label><input placeholder="" type="radio" class="radio" name="is_verified" value="-1" @if($list->is_verified==-1) checked @endif />Decline</label>
                                            <input type="hidden" name="old_verified" value="{{$list->is_verified}}" />
                                            @endif

                                            @if(!isset($list) || isset($list) && !$list->is_verified)
                                            <div style="font-size:13px;margin-top:30px;">
                                                *Notes:
                                                <ul style="padding-left:15px;">
                                                <li style="list-style-type:disc;">Please give us 24 - 48 hours to verify your data. You can still activate your listing in the mean time.</li>
                                                </ul>
                                            </div>
                                            @endif
                                        </div>
                                        @if(Auth::user()->isAgent() || Auth::user()->isMan() || Auth::user()->isOwner() || Auth::user()->isAdmin())
                                        <button id="save" type="submit" class="button is-primary mainbgc" style="margin-top:0.8rem" onclick="if(validForm2()) { document.listForm.submit(); } else return false;">
                                            SAVE
                                        </button>
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="column">
                                    <input type="hidden" name="active" value="{{isset($list) ? $list->active : old('active')}}" />
                                     
                                    @if(Auth::user()->isAgent() || Auth::user()->isMan() || Auth::user()->isOwner() || Auth::user()->isAdmin())
                                    <a id="next" class="button is-link mainbgc" onclick="nextPage()">NEXT PAGE</a>
                                    <a id="previous" class="button is-link mainbgc" onclick="previousPage()" style="display: none">BACK TO PREVIOUS PAGE</a>
                                    
                                        @if (isset($list))
                                    <br><br><br>
                                    <button id="save2" type="submit" class="button is-primary mainbgc" style="margin-top:0px;" onclick="if(validForm1()) { document.listForm.submit(); } else return false;"> 
                                        SAVE
                                    </button>  

                                            @if(!isset($list) || isset($list) && !$list->is_verified)
                                    <div id="page1_note" style="font-size:13px;margin-top:30px;">
                                        *Notes:
                                        <ul style="padding-left:15px;">
                                        <li style="list-style-type:disc;">Please give us 24 - 48 hours to verify your data. You can still activate your listing in the mean time.</li>
                                        </ul>
                                    </div>
                                            @endif
                                        @endif
                                    @endif
                                </div>
                                

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
        function searchAgent(name) {
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
                            $('#agent_dropdown').append('<li class="dropdown-item" style="cursor:pointer" onclick="$(\'.ex_agree\').hide();$(\'#name_on_list\').val(1);$(\'#input_name\').val(\''+aname+'\');$(\'#agent_dropdown\').hide();">'+value+'</li>');
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


        var editor = CKEDITOR.replace( 'editor' );

        $('.clear').on("click", function () {
            $(this).closest('div').find('.input').val('');
        });

        $('.clear-text').on("click", function () {
            CKEDITOR.instances.editor.setData( '' );
            $('#address').val('');
        });

        $('.clear-select').on("click", function () {
            $('#address').val(1).trigger('change.select2');     
            $('#street_address').val('');
        });

        $('#district option').hide();

        var id = $('#boro').val();

        $('[data-parent='+id+']').show();

        $('#boro').on('change', function(e) {

            $('#district option').hide();
            $('#district optgroup').hide();
            $('#district').prop('selectedIndex',0);
            e.preventDefault();
            var parentId = $(this).val();

            $('[data-parent='+parentId+']').show();

        });

        var districtId;
        $('#district').on('change', function(e) {
            districtId = $(this).val();
        });


        var address = $('#address');        
        
        address.select2({
            tags: true,
            selectOnBlur: true,
            ajax: {
                url: "/autocomplete/search",
                dataType: 'json',
                delay: 250,
                type: 'GET',
                data: function (params) {
                    //console.log(params);
                    return {
                        searchquery: params.term,
                        districtID: districtId,
                    };
                },
                processResults: function (data) {
                    var arr = [];
                    $.each(data, function (index, value) {
                        arr.push({
                            id: value.building_id,
                            text: value.building_address,
                            city: value.building_city,
                            zip: value.building_zip,
                            year: value.building_build_year,
                            type: value.building_type,
                            district: value.building_district_id,
                            amenities: value.building_amenities
                        });
                    });
                    return {
                        results: arr
                    };
                },
                cache: true
            },
            escapeMarkup: function (markup) { return markup; },
            minimumInputLength: 1
        });

        address.on('select2:select', function (e) {
            $('.clear-select').css('display', 'inline-block');

            var data = e.params.data;

            $('#street_address').val(data.text);

            /*$('input[name=city]').val(data.city);
            $('input[name=zip]').val(data.zip);
            $('input[name=year_built]').val(data.year);
            if (data.type != undefined && data.type != 40) { //40 is building
                $('select[name=type]').val(data.type);
                $('#ptype').change();
            }
            if (data.district != undefined)
                $('select[name=district]').val(data.district);*/

            if(data.amenities){
                var amenities = data.amenities;

                for (var i = 0; i < amenities.length; i++) {

                    $('#'+amenities[i]).attr('checked', 'checked');
                }
            }

            var tid = $('#district').find(':selected').attr('data-parent');

            if (tid && tid != 0)
                $('#boro').val(tid);
        });

        $('.select2').click(function(){
            //alert('3');
        });

        var key = '{{isset($key) ? $key : 'undefined'}}';

        if (key === 'undefined' || key === null) {
            var i = 0;
        }else{
            i=parseInt(key)+1;
        }

        $('.more').click(function() {
            $(this).before('<div id="openHouse-'+i+'" class="block">' +
                '                     <div class="form-content columns is-variable">' +
                '                         <div class="column" style="padding-bottom:0px;">' +
                '                             <label class="label form-content" for="">Open House:</label>\n' +
                '                                 <input type="date" name="openHouse['+i+'][date]" class="input" value="">' +
                '                         </div>' +
                '                         <div class="column" style="padding-bottom:0px;">' +
                '                             <label class="label form-content" for="">Starts:</label>\n' +
                '                                 <div class="select">' +
                '                                     <select name="openHouse['+i+'][start]">' +
                '                                          @foreach($openHours as $value)' +
                '                                              <option value="{{$value->hour}}">{{$value->title}}</option>' +
                '                                          @endforeach' +
                '                                     </select>' +
                '                                 </div>' +
                '                         </div>' +
                '                         <div class="column" style="padding-bottom:0px;">' +
                '                             <label class="label form-content" for="">Ends:</label>\n' +
                '                                 <div class="select">' +
                '                                     <select name="openHouse['+i+'][end]">' +
                '                                         @foreach($openHours as $value)' +
                '                                            <option value="{{$value->hour}}">{{$value->title}}</option>' +
                '                                         @endforeach' +
                '                                     </select>' +
                '                                 </div>' +
                '                         </div>' +
                '                         <div class="column" style="padding-bottom:0px;margin-top:2rem;">' +
                '                             <label class="label form-content" for="" style="white-space: nowrap;">' +
                '                                    <input type="checkbox" name="openHouse['+i+'][appointment]" value="1">&nbsp;By Appointment</label>' +
                '                         </div>' +
                '                         <div class="column listremove" style="padding-bottom:0px;">' +
                '                             <a onclick="removeOpenHouse('+i+')">Remove</a>' +
                '                         </div>' +
                '                         </div>' +
                '                         </div>');
            i++;
        });

        $('#ptype').on('change', function(e) {
            var ptype = $(this).val();    
            
            if (ptype==6) {
                $('#maint_6').show();
                $('#maint_7').hide();
            }
            else {
                $('#maint_7').show();
                $('#maint_6').hide();
            }


            
            $('.req').css('display', 'inline-block');
            $('.may-req').hide();
            $('.may-req.t'+ptype).css('display', 'inline-block');

            $('.red-f').removeClass('red-f');
        });

        $('.req-f, .may-req-f, [aria-labelledby="select2-address-container"]').on('click', function(e) {
            $(this).removeClass('red-f');
        });      

        /*setTimeout(function(){
        $("iframe").load(function(){
            $(this).contents().on("mousedown, mouseup, click", function(){
                alert("Click detected inside iframe.");
            });
        }); }, 3000);*/

        /*$(".cke_wysiwyg_frame").contents().find(".cke_editable").on('click', function(e) {            
            $('#cke_editor').removeClass('red-f');
        });*/      
       
        $('.label.desc').on('mouseover click', function(e) {
            $('#cke_editor').removeClass('red-f');
        });

        function validForm1() {
            $('.red-f').removeClass('red-f');

            var err = false;
            var first = '';
            var ptype = $('#ptype').val();

            if (!ptype) {                
                $('html,body').unbind().animate({scrollTop: $('#ptype').offset().top-100},'slow');
                $('#ptype').addClass('red-f');
                return false;
            }

            $('.req-f, .may-req-f.t'+ptype).each(function(index) {                
                var fname = $(this).attr('name');

                if (fname == 'image[]' && $('.images-wrapper').length > 0) {                    
                    return true;  //return true; = continue;  return false; = break;
                }
                else if (fname == 'description') {
                    var fval = $(".cke_wysiwyg_frame").contents().find(".cke_editable").text();
                }
                else
                    var fval = $(this).val();

                //alert(fname+'==='+fval);

                if (!fval) {                    
                    if (fname == 'street_address') { 
                        $('[aria-labelledby="select2-address-container"]').addClass('red-f');
                    }
                    else if (fname == 'description') {
                        $('#cke_editor').addClass('red-f');
                    }
                    else
                        $(this).addClass('red-f');
                    if (!first) {
                        first = fname;
                    }
                    err = true;
                }
            });
            
            if (err) {                
                $('html,body').unbind().animate({scrollTop: $('[name="'+first+'"]').offset().top-100},'slow');
                return false;
            }
            else    
                return true;
        }

        function validForm2() {
            @if (isset($list) && $list->amazon_id === 0)
            return true;
            {{--@elseif(Auth::user()->isAgent() || Auth::user()->isMan())--}}
            @elseif(Auth::user()->isAgent())
            if($('#name_on_list').val() == 1 || $('#agreement').val() || $('.agreement-wrapper').length > 0)
                return true;
            else {
                swal('Agents must provide Agreement in order to submit listing.');
                return false;
            }
            @elseif(Auth::user()->isOwner())
            if(($('#deed').val() || $('.deed-wrapper').length > 0) || ($('#utilitybill').val() || $('.bill-wrapper').length > 0) || ($('#photoid').val() || $('.photoid-wrapper').length > 0))
                return true;
            else {
                swal('Owners must provide Deed or Utility Bill or Photo ID in order to submit listing.');
                return false;
            }
            @else
            //Admin
            var is_verified = $("input[name='is_verified']:checked").val();
            if (is_verified != $("input[name='old_verified']").val()) {
                if (is_verified == 1) 
                    alert('An approval email will be sent to User ID: {{$list->user_id}} and the listing will be activated.');
                if (is_verified == -1) 
                    alert('A decline email will be sent to User ID: {{$list->user_id}}');
            }
            return true;
            @endif            
        }

        function removeOpenHouse(id){
            $("#openHouse-"+id).remove();
        }

        function nextPage(){
            if(validForm1()) {
                $("#next").hide();
                $("#page1").hide();
                $("#page2").show();
                $("#previous").show();
                $('#save2').hide();
                if ($('#page1_note').length)
                    $('#page1_note').hide();
            }
        }

        function previousPage(){
            $("#page2").hide();
            $("#previous").hide();
            $("#next").show();
            $("#page1").show();
            $('#save2').show();
            if ($('#page1_note').length)
                $('#page1_note').show();
        }

        $(document).ready(function() {
            var ptype = $('#ptype').val();
            if (ptype && ptype != 0) {
                $('#ptype').change();
            }

            var element = document.getElementById('list-google-map');

            mapboxgl.accessToken = 'pk.eyJ1Ijoic3RvcGUiLCJhIjoiY2ppb2x2a3JrMDlrODNwdG5sNzVndjg0ayJ9.2NzNFhDvEoWZDbWshb7Phg';

            var map = new mapboxgl.Map({
                container: element,
                style: 'mapbox://styles/mapbox/streets-v9',
                //center: [40.783060, -73.971249],
                zoom: 13,
                scrollZoom: false,
                minZoom: 8,
                maxZoom: 16
            });

            map.on('load', function() {
                map.addSource('border', {
                    "type": "geojson",
                    "data": {
                        "type": "Feature",
                        "properties": {},
                        "geometry": {
                            "type": "MultiPolygon",
                            "coordinates": []
                        }
                    }
                });

                map.addLayer({
                    "id": "polygon",
                    "source": "border",
                    "type": "line",
                    "layout": {
                        "line-join": "round",
                        "line-cap": "round"
                    },
                    "paint": {
                        "line-color": "#007cbf",
                        "line-width": 2
                    }
                }); 
            });

            var nav = new mapboxgl.NavigationControl();
            map.addControl(nav, 'top-right');   

            var marker = new mapboxgl.Marker().setLngLat([0, 0]).addTo(map);

            if ($('#district option:selected').val()) {
                $('#list-google-map').show();

                var neighbor = $.trim($('#district option:selected').text().replace('- ', ''));  
                var street = $('#street_address').val();  
                var boro = $('#boro option:selected').text();       
                
                map.on('load', function () {
                    var geocoder = new google.maps.Geocoder();          

                    geocoder.geocode({'address': street+', '+boro}, function(results, status) {
                        if (status === 'OK') {
                            //console.log(results[0].geometry.location);
                            marker.setLngLat([results[0].geometry.location.lng(), results[0].geometry.location.lat()]);
                            map.setCenter([results[0].geometry.location.lng(), results[0].geometry.location.lat()]);
                        }
                    });                    

                    var fname = neighbor.toLowerCase().replace(new RegExp(' ', 'g'), '_').replace(new RegExp('/', 'g'), '_').replace(new RegExp("'", 'g'), '_');

                    if (fname == 'hunters_point')
                        fname = 'long_island_city'; 
                    else if (fname == 'beekman')
                        fname = 'turtle_bay'; 

                    axios.get('/mapbox/polygon', { params: { file: fname } }).then(function (response) {
                        if (response.data.success) {
                            map.getSource('border').setData(response.data.geometry);
                        }
                    });
                });
            }
            else    
                $('#list-google-map').hide();

            $('#district').change(function() {
                $('#list-google-map').show();

                var boro = $('#boro option:selected').text();
                var neighbor = $.trim($('#district option:selected').text().replace('- ', ''));                  
                
                if (neighbor == 'Beekman')
                    neighbor = 'Turtle Bay';

                var geocoder = new google.maps.Geocoder();

                geocoder.geocode({'address': neighbor+', '+boro}, function(results, status) {
                    if (status === 'OK') {
                        //console.log(results[0].geometry.location);
                        //marker.setLngLat([results[0].geometry.location.lng(), results[0].geometry.location.lat()]);
                        map.setCenter([results[0].geometry.location.lng(), results[0].geometry.location.lat()]);
                    }
                });

                var fname = neighbor.toLowerCase().replace(new RegExp(' ', 'g'), '_').replace(new RegExp('/', 'g'), '_').replace(new RegExp("'", 'g'), '_');

                if (fname == 'hunters_point')
                    fname = 'long_island_city'; 
                else if (fname == 'beekman')
                    fname = 'turtle_bay'; 

                axios.get('/mapbox/polygon', { params: { file: fname } }).then(function (response) {
                    if (response.data.success) {
                        map.getSource('border').setData(response.data.geometry);
                    }
                    else
                        map.getSource('border').setData({"type": "Point","coordinates": [0,0]});
                });
            });

            address.on('select2:select', function (e) {
                $('#list-google-map').show();

                var street = $('#street_address').val();
                var boro = $('#boro option:selected').text();                 

                var geocoder = new google.maps.Geocoder();

                geocoder.geocode({'address': street+', '+boro}, function(results, status) {
                    if (status === 'OK') {
                        //console.log(results[0].geometry.location);
                        marker.setLngLat([results[0].geometry.location.lng(), results[0].geometry.location.lat()]);
                        map.setCenter([results[0].geometry.location.lng(), results[0].geometry.location.lat()]);

                        $('#long').val(results[0].geometry.location.lng());
                        $('#lat').val(results[0].geometry.location.lat());
                    }
                });
            });
        });

    </script>
@endsection