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
                <div class="breadcrumbs">
                    <div class="container">
                        <ul>
                            <li>
                                <a href="javascript:void(0);" onclick="if(!document.referrer) window.location.href = '/'; else window.history.go(-1); return false;">Previous Page</a>
                            </li>
                            <li>
                                <span>Agent Page</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="main-content columns is-mobile is-centered" style="margin-bottom:40px;">
                    <div class="column is-9 is-centered agentpage" style="padding:0px;">

                        <div class="panel-body is-clearfix listing">
                            @if (session('status'))
                                <div class="alert alert-success">
                                    {{ session('status') }}
                                </div>
                            @endif

                            <div class="column is-8-desktop is-pulled-left" style="padding-right: 20px">
                                <article class="media">
                                    <figure class="media-left">
                                        <div style="margin: 0px 20px 10px 0px;">
                                            @if (isset($agent->img))
                                                <p class="image is-128x128 agent-image" style="overflow: hidden;border-radius: 50%;background-image: url(@if(env('TEST_IMG'))/images/agent_sample.jpg @elseif (isset($agent->img) && $agent->img){{$agent->img}}@else /images/default_agent.jpg @endif);background-size:cover;background-position:top center;">
                                                    {{--<img src="{{$agent->img}}">--}}
                                                </p>
                                            @endif
                                        </div>
                                    </figure>
                                    <div class="media-content">
                                        <div class="content">
                                            <strong>{{$agent->full_name}}</strong>
                                            <br />
                                            @if ($agent->path_to_logo)
                                                @if ($agent->web_site)
                                                    <a href="{{$agent->web_site}}" target="_blank"><img src="@if(env('TEST_IMG'))/images/listed-meta.png @else{{$agent->path_to_logo}}@endif" alt="" style="max-width:150px;max-height:50px;margin-top:10px;"></a>
                                                @else
                                                    <img src="@if(env('TEST_IMG'))/images/listed-meta.png @else{{$agent->path_to_logo}}@endif" alt="" class="profile_logo" style="max-height:50px;margin-top:10px;">
                                                @endif
                                            @elseif(isset($agent->company))
                                                <div style="margin-top:5px;">
                                                @if ($agent->web_site)
                                                    <a href="{{$agent->web_site}}" target="_blank">{{ucwords($agent->company)}}</a>
                                                @else
                                                    {{ucwords($agent->company)}}
                                                @endif
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </article>
                                <div class="bio" style="margin-top:10px;@if(!isset($agent->user) || !$agent->user->premium) display:none; @endif">
                                    {!! strip_tags($agent->description) !!}
                                </div>

                                <div class="sale-listing" style="margin-top: 20px;">
                                    @if(empty($estates['sales']) && empty($estates['rentals']) || !empty($estates['sales']))
                                        <h3 class="has-text-weight-bold">
                                            <span id="sale_1" class="select_list" onclick="showSales('cur_sales');">{{count($estates['sales'])}}
                                                {{ count($estates['sales']) == 1 ? "Sale" : "Sales" }}
                                            </span>
                                        @if (isset($agent->user) && $agent->user->premium)
                                        | 
                                            <span id="sale_2" class="unselect_list" onclick="showSales('past_sales');">{{count($estates['past_sales'])}}                                            
                                                {{ count($estates['past_sales']) == 1 ? "Past Sale" : "Past Sales" }}
                                            </span>
                                        @endif
                                        </h3>
                                        <hr class="large" style="margin-top:10px;margin-bottom:15px;" />

                                        @foreach ($estates['sales'] as $k => $result)
                                            @php if($result->status != 1 && (!isset($agent->user) || !$agent->user->premium)) continue; @endphp
                                            <div class="agent_lists cur_sales" @if($k==0)style="padding-top:0px;"@endif>
                                                <div class="columns box-listing">
                                                    <div class="column is-4" style="padding-left:0px;">

                                                        @if ($result->img)
                                                            <img src="@if(env('TEST_IMG'))/images/display3.jpg @else{{$result->img}}@endif" alt="" style="width:229px;">
                                                        @else
                                                            <img src="/images/default_image_coming.jpg" alt="" style="width:229px;">
                                                        @endif

                                                    </div>

                                                    <div class="column" style="padding-left:0px;">
                                                        @if($result->estate_type == 1 && $result->status == -1)
                                                        <div style="margin-bottom:8px; color:rgb(192, 74, 74); font-weight:bold">OFF MARKET</div>
                                                        @elseif($result->estate_type == 1 && $result->status == -2)
                                                        <div style="margin-bottom:8px; color:rgb(192, 74, 74); font-weight:bold">IN CONTRACT</div>
                                                        @elseif($result->estate_type == 1 && $result->status == -3)
                                                        <div style="margin-bottom:8px; color:rgb(192, 74, 74); font-weight:bold">SOLD</div>
                                                        @endif

                                                        <div class="level" style="margin-bottom:10px;">
                                                            {{--<a href="/show/{{str_replace(' ','_',$result->name)}}/{{$result->id}}">--}}
                                                            {{--<a style="padding-right:10px;" href="/show/{{str_replace(' ','_',$result->name)}}/@if(!$result->unit)_ @else{{str_replace('#', '', str_replace(array(' ', '/'), '_', $result->unit))}}@endif/{{str_replace(' ','_',$result->city)}}/{{$result->id}}">--}}
                                                            <a style="padding-right:10px;" href="/show/{{str_replace(' ','_',$result->name)}}/@if(!$result->unit)_ @else{{str_replace('#', '', str_replace(array(' ', '/'), '_', $result->unit))}}@endif/{{str_replace(' ','_',$result->city)}}">
                                                                <h4 class="is-6 main-color a"><b>{{$result->full_address}} {{$result->unit}}</b> &nbsp;<div style="margin-top:5px;">{{ucwords($result->neighborhood)}}</div></h4>
                                                            </a>
                                                            <span class="is-danger title is-6" style="white-space: nowrap;">{{$result->unit_type}}<div style="margin-top:10px;">{{$result->estate_type == 2 && $result->fees == 0 ? 'No Fee' : ''}}</div></span>
                                                        </div>

                                                        <div class="content">
                                                            <span id="price-toline" class="title is-6" style="margin-bottom:0px;">$ {{$result->price}}</span>
                                                            @if ($result->estate_type==2 && $result->monthly_cost)<span>$ {{$result->monthly_cost}}/monthly </span>@endif

                                                            <div id="listing-ads" class="">
                                                                <ul class="level-left is-mobile" id="listing-ad-ul-type" style="max-width:100%;margin-top:8px;">
                                                                    <li id="listing-ad-bed">{{$result->beds}} beds | {{$result->baths}} baths @if($result->sq_feet) | {{$result->sq_feet}} ft<sup>2</sup> @endif</li>
                                                                    {{--<li id="listing-ad-bath">{{$result->baths}} baths|</li>
                                                                    <li id="listing-ad-ft">{{$result->sq_feet}} ft<sup>2</sup></li>--}}
                                                                </ul>
                                                            </div>

                                                            {{--
                                                            <div class="listing-ad-type" style="margin-top:8px;">
                                                                @if ($result->agent_company)
                                                                    {{ucwords($result->agent_company)}}
                                                                @endif
                                                                <br>
                                                                @if ($result->path_to_logo)
                                                                    <span class="company_logo"><img style="max-width:150px;max-height:50px;" src="{{$result->path_to_logo}}"></span>
                                                                @endif
                                                            </div>
                                                            --}}

                                                            @if(isset($result->OpenHouse) && !$result->OpenHouse->isEmpty())
                                                                @foreach($result->OpenHouse as $key=>$item)
                                                                    @if(Carbon\Carbon::now()->format('Y-m-d') > $item->date)
                                                                        @php unset($result->OpenHouse[$key]) @endphp
                                                                    @endif
                                                                @endforeach
                                                                @if(count($result->OpenHouse))
                                                                <div style="padding-bottom:5px;padding-top:10px;">Open House:</div>
                                                                @endif     
                                                                <div style="float:left;">                                                                                                                   
                                                                @foreach($result->OpenHouse as $key=>$item)
                                                                    <div class="listing-ad-open-house is-pulled-left" style="padding-left:0px;width:100%;">
                                                                    <span class="button2" style="border:none;padding:0px;"><b>{{Carbon\Carbon::parse($item->date)->format('D M j')}}</b>&nbsp;
                                                                @if($item->appointment)
                                                                    <b>by appointment</b>
                                                                @else
                                                                    {{Carbon\Carbon::parse($item->start_time)->format('g:i A')}} -
                                                                    {{Carbon\Carbon::parse($item->end_time)->format('g:i A')}}
                                                                    @endif</span>
                                                                    </div>
                                                                @endforeach
                                                                </div>
                                                                <div style="clear:both;"></div>
                                                            @endif

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <hr />
                                        @endforeach

                                        @foreach ($estates['past_sales'] as $k => $result)
                                            @php if($result->status != 1 && (!isset($agent->user) || !$agent->user->premium)) continue; @endphp
                                            <div class="agent_lists past_sales" @if($k==0)style="padding-top:0px;"@endif>
                                                <div class="columns box-listing">
                                                    <div class="column is-4" style="padding-left:0px;">

                                                        @if ($result->img)
                                                            <img src="@if(env('TEST_IMG'))/images/display3.jpg @else{{$result->img}}@endif" alt="" style="width:229px;">
                                                        @else
                                                            <img src="/images/default_image_coming.jpg" alt="" style="width:229px;">
                                                        @endif

                                                    </div>

                                                    <div class="column" style="padding-left:0px;">
                                                        @if($result->estate_type == 1 && $result->status == -1)
                                                        <div style="margin-bottom:8px; color:rgb(192, 74, 74); font-weight:bold">OFF MARKET</div>
                                                        @elseif($result->estate_type == 1 && $result->status == -2)
                                                        <div style="margin-bottom:8px; color:rgb(192, 74, 74); font-weight:bold">IN CONTRACT</div>
                                                        @elseif($result->estate_type == 1 && $result->status == -3)
                                                        <div style="margin-bottom:8px; color:rgb(192, 74, 74); font-weight:bold">SOLD</div>
                                                        @endif

                                                        <div class="level" style="margin-bottom:10px;">
                                                            {{--<a href="/show/{{str_replace(' ','_',$result->name)}}/{{$result->id}}">--}}
                                                            {{--<a style="padding-right:10px;" href="/show/{{str_replace(' ','_',$result->name)}}/@if(!$result->unit)_ @else{{str_replace('#', '', str_replace(array(' ', '/'), '_', $result->unit))}}@endif/{{str_replace(' ','_',$result->city)}}/{{$result->id}}">--}}
                                                            <a style="padding-right:10px;" href="/show/{{str_replace(' ','_',$result->name)}}/@if(!$result->unit)_ @else{{str_replace('#', '', str_replace(array(' ', '/'), '_', $result->unit))}}@endif/{{str_replace(' ','_',$result->city)}}">   
                                                                <h4 class="is-6 main-color a"><b>{{$result->full_address}} {{$result->unit}}</b> &nbsp;<div style="margin-top:5px;">{{ucwords($result->neighborhood)}}</div></h4>
                                                            </a>
                                                            <span class="is-danger title is-6" style="white-space: nowrap;">{{$result->unit_type}}<div style="margin-top:10px;">{{$result->estate_type == 2 && $result->fees == 0 ? 'No Fee' : ''}}</div></span>
                                                        </div>

                                                        <div class="content">
                                                            <span id="price-toline" class="title is-6" style="margin-bottom:0px;">$ {{$result->price}}</span>
                                                            @if ($result->estate_type==2 && $result->monthly_cost)<span>$ {{$result->monthly_cost}}/monthly </span>@endif

                                                            <div id="listing-ads" class="">
                                                                <ul class="level-left is-mobile" id="listing-ad-ul-type" style="max-width:100%;margin-top:8px;">
                                                                    <li id="listing-ad-bed">{{$result->beds}} beds | {{$result->baths}} baths @if($result->sq_feet) | {{$result->sq_feet}} ft<sup>2</sup> @endif</li>
                                                                    {{--<li id="listing-ad-bath">{{$result->baths}} baths|</li>
                                                                    <li id="listing-ad-ft">{{$result->sq_feet}} ft<sup>2</sup></li>--}}
                                                                </ul>
                                                            </div>

                                                            {{--
                                                            <div class="listing-ad-type" style="margin-top:8px;">
                                                                @if ($result->agent_company)
                                                                    {{ucwords($result->agent_company)}}
                                                                @endif
                                                                <br>
                                                                @if ($result->path_to_logo)
                                                                    <span class="company_logo"><img style="max-width:150px;max-height:50px;" src="{{$result->path_to_logo}}"></span>
                                                                @endif
                                                            </div>
                                                            --}}

                                                            @if(isset($result->OpenHouse) && !$result->OpenHouse->isEmpty())
                                                                @foreach($result->OpenHouse as $key=>$item)
                                                                    @if(Carbon\Carbon::now()->format('Y-m-d') > $item->date)
                                                                        @php unset($result->OpenHouse[$key]) @endphp
                                                                    @endif
                                                                @endforeach
                                                                @if(count($result->OpenHouse))
                                                                <div style="padding-bottom:5px;padding-top:10px;">Open House:</div>
                                                                @endif
                                                                <div style="float:left;"> 
                                                                @foreach($result->OpenHouse as $key=>$item)
                                                                    <div class="listing-ad-open-house is-pulled-left" style="padding-left:0px;width:100%;">
                                                                    <span class="button2" style="border:none;padding:0px;"><b>{{Carbon\Carbon::parse($item->date)->format('D M j')}}</b>&nbsp;
                                                                @if($item->appointment)
                                                                    <b>by appointment</b>
                                                                @else
                                                                    {{Carbon\Carbon::parse($item->start_time)->format('g:i A')}} -
                                                                    {{Carbon\Carbon::parse($item->end_time)->format('g:i A')}}
                                                                    @endif</span>
                                                                    </div>
                                                                @endforeach
                                                                </div>
                                                                <div style="clear:both;"></div>
                                                            @endif

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <hr />
                                        @endforeach
                                    @endif
                                </div>

                                <div style="clear:both;"></div>

                                <div class="rental-listing" style="margin-top:50px;">
                                    @if(empty($estates['sales']) && empty($estates['rentals']) || !empty($estates['rentals']))
                                        <div style="margin-top:30px;">
                                            <h3 class="has-text-weight-bold">
                                                <span id="rental_1" class="select_list" onclick="showRentals('cur_rentals');">{{count($estates['rentals'])}}
                                                    {{ count($estates['rentals']) == 1 ? "Rental" : "Rentals" }}
                                                </span>
                                            @if (isset($agent->user) && $agent->user->premium)
                                            | 
                                                <span id="rental_2" class="unselect_list" onclick="showRentals('past_rentals');">{{count($estates['past_rentals'])}}                                            
                                                    {{ count($estates['past_rentals']) == 1 ? "Past Rental" : "Past Rentals" }}
                                                </span>
                                            @endif
                                            </h3>                                            
                                        </div>
                                        <hr class="large" style="margin-top:10px;margin-bottom:15px;" />

                                        @foreach ($estates['rentals'] as $k => $result)
                                            @php if($result->status != 1 && (!isset($agent->user) || !$agent->user->premium)) continue; @endphp
                                            <div class="agent_lists cur_rentals" @if($k==0)style="padding-top:0px;"@endif>
                                                <div class="columns box-listing">
                                                    <div class="column is-4" style="padding-left:0px;">

                                                        @if ($result->img)
                                                            <img src="@if(env('TEST_IMG'))/images/display3.jpg @else{{$result->img}}@endif" alt="" style="width:229px;">
                                                        @else
                                                            <img src="/images/default_image_coming.jpg" alt="" style="width:229px;">                                                                
                                                        @endif

                                                    </div>

                                                    <div class="column" style="padding-left:0px;">
                                                        @if($result->estate_type == 2 && $result->status == -1)
                                                        <div style="margin-bottom:8px; color:rgb(192, 74, 74); font-weight:bold">OFF MARKET</div>
                                                        @elseif($result->estate_type == 2 && $result->status == -2)
                                                        <div style="margin-bottom:8px; color:rgb(192, 74, 74); font-weight:bold">PENDING</div>
                                                        @elseif($result->estate_type == 2 && $result->status == -3)
                                                        <div style="margin-bottom:8px; color:rgb(192, 74, 74); font-weight:bold">RENTED</div>
                                                        @endif  

                                                        <div class="level" style="margin-bottom:10px;">
                                                            {{--<a style="padding-right:10px;" href="/show/{{str_replace(' ','_',$result->name)}}/{{$result->id}}">--}}
                                                            {{--<a style="padding-right:10px;" href="/show/{{str_replace(' ','_',$result->name)}}/@if(!$result->unit)_ @else{{str_replace('#', '', str_replace(array(' ', '/'), '_', $result->unit))}}@endif/{{str_replace(' ','_',$result->city)}}/{{$result->id}}">--}}
                                                            <a style="padding-right:10px;" href="/show/{{str_replace(' ','_',$result->name)}}/@if(!$result->unit)_ @else{{str_replace('#', '', str_replace(array(' ', '/'), '_', $result->unit))}}@endif/{{str_replace(' ','_',$result->city)}}">
                                                                <h4 class="is-6 main-color a"><b>{{$result->full_address}} {{$result->unit}}</b> &nbsp;<div style="margin-top:5px;">{{ucwords($result->neighborhood)}}</div></h4>
                                                            </a>
                                                            <span class="is-danger title is-6" style="white-space: nowrap;">{{$result->unit_type}}<div style="margin-top:8px;">{{$result->estate_type == 2 && $result->fees == 0 ? 'No Fee' : ''}}</div></span>
                                                        </div>

                                                        <div class="content">
                                                            <div id="price-toline" class="title is-6" style="margin-bottom:0px;">$ {{$result->price}}</div>
                                                            @if ($result->estate_type==2 && $result->monthly_cost)<span>$ {{$result->monthly_cost}}/monthly </span>@endif

                                                            <div id="listing-ads" class="">
                                                                <ul class="level-left is-mobile" id="listing-ad-ul-type" style="max-width:100%; margin-top:8px;">
                                                                    <li id="listing-ad-bed">{{$result->beds}} beds | {{$result->baths}} baths @if($result->sq_feet) | {{$result->sq_feet}} ft<sup>2</sup> @endif</li>
                                                                    {{--<li id="listing-ad-bath">{{$result->baths}} baths|</li>
                                                                    <li id="listing-ad-ft">{{$result->sq_feet}} ft<sup>2</sup></li>--}}
                                                                </ul>
                                                            </div>

                                                            {{--
                                                            <div class="listing-ad-type" style="margin-top:8px;">
                                                                @if ($result->agent_company)
                                                                    {{ucwords($result->agent_company)}}
                                                                @endif
                                                                <br>
                                                                @if ($result->path_to_logo)
                                                                    <span class="company_logo"><img style="max-width:150px;max-height:50px;" src="{{$result->path_to_logo}}"></span>
                                                                @endif
                                                            </div>
                                                            --}}

                                                            @if(isset($result->OpenHouse) && !$result->OpenHouse->isEmpty())
                                                                @foreach($result->OpenHouse as $key=>$item)
                                                                    @if(Carbon\Carbon::now()->format('Y-m-d') > $item->date)
                                                                        @php unset($result->OpenHouse[$key]) @endphp
                                                                    @endif
                                                                @endforeach
                                                                @if(count($result->OpenHouse))
                                                                <div style="padding-bottom:5px;padding-top:10px;">Open House:</div>
                                                                @endif
                                                                <div style="float:left;"> 
                                                                @foreach($result->OpenHouse as $key=>$item)
                                                                    <div class="listing-ad-open-house is-pulled-left" style="padding-left:0px;width:100%;">
                                                                    <span class="button2" style="border:none;padding:0px;"><b>{{Carbon\Carbon::parse($item->date)->format('D M j')}}</b>&nbsp;
                                                                @if($item->appointment)
                                                                    <b>by appointment</b>
                                                                @else
                                                                    {{Carbon\Carbon::parse($item->start_time)->format('g:i A')}} -
                                                                    {{Carbon\Carbon::parse($item->end_time)->format('g:i A')}}
                                                                    @endif</span>
                                                                    </div>
                                                                @endforeach
                                                                </div>
                                                                <div style="clear:both;"></div>
                                                            @endif

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <hr />
                                        @endforeach

                                        @foreach ($estates['past_rentals'] as $k => $result)
                                            @php if($result->status != 1 && (!isset($agent->user) || !$agent->user->premium)) continue; @endphp
                                            <div class="agent_lists past_rentals" @if($k==0)style="padding-top:0px;"@endif>
                                                <div class="columns box-listing">
                                                    <div class="column is-4" style="padding-left:0px;">

                                                        @if ($result->img)
                                                            <img src="@if(env('TEST_IMG'))/images/display3.jpg @else{{$result->img}}@endif" alt="" style="width:229px;">
                                                        @else
                                                            <img src="/images/default_image_coming.jpg" alt="" style="width:229px;">                                                                
                                                        @endif

                                                    </div>

                                                    <div class="column" style="padding-left:0px;">
                                                        @if($result->estate_type == 2 && $result->status == -1)
                                                        <div style="margin-bottom:8px; color:rgb(192, 74, 74); font-weight:bold">OFF MARKET</div>
                                                        @elseif($result->estate_type == 2 && $result->status == -2)
                                                        <div style="margin-bottom:8px; color:rgb(192, 74, 74); font-weight:bold">PENDING</div>
                                                        @elseif($result->estate_type == 2 && $result->status == -3)
                                                        <div style="margin-bottom:8px; color:rgb(192, 74, 74); font-weight:bold">RENTED</div>
                                                        @endif  

                                                        <div class="level" style="margin-bottom:10px;">
                                                            {{--<a style="padding-right:10px;" href="/show/{{str_replace(' ','_',$result->name)}}/{{$result->id}}">--}}
                                                            {{--<a style="padding-right:10px;" href="/show/{{str_replace(' ','_',$result->name)}}/@if(!$result->unit)_ @else{{str_replace('#', '', str_replace(array(' ', '/'), '_', $result->unit))}}@endif/{{str_replace(' ','_',$result->city)}}/{{$result->id}}">--}}
                                                            <a style="padding-right:10px;" href="/show/{{str_replace(' ','_',$result->name)}}/@if(!$result->unit)_ @else{{str_replace('#', '', str_replace(array(' ', '/'), '_', $result->unit))}}@endif/{{str_replace(' ','_',$result->city)}}">
                                                                <h4 class="is-6 main-color a"><b>{{$result->full_address}} {{$result->unit}}</b> &nbsp;<div style="margin-top:5px;">{{ucwords($result->neighborhood)}}</div></h4>
                                                            </a>
                                                            <span class="is-danger title is-6" style="white-space: nowrap;">{{$result->unit_type}}<div style="margin-top:8px;">{{$result->estate_type == 2 && $result->fees == 0 ? 'No Fee' : ''}}</div></span>
                                                        </div>

                                                        <div class="content">
                                                            <div id="price-toline" class="title is-6" style="margin-bottom:0px;">$ {{$result->price}}</div>
                                                            @if ($result->estate_type==2 && $result->monthly_cost)<span>$ {{$result->monthly_cost}}/monthly </span>@endif

                                                            <div id="listing-ads" class="">
                                                                <ul class="level-left is-mobile" id="listing-ad-ul-type" style="max-width:100%; margin-top:8px;">
                                                                    <li id="listing-ad-bed">{{$result->beds}} beds | {{$result->baths}} baths @if($result->sq_feet) | {{$result->sq_feet}} ft<sup>2</sup> @endif</li>
                                                                    {{--<li id="listing-ad-bath">{{$result->baths}} baths|</li>
                                                                    <li id="listing-ad-ft">{{$result->sq_feet}} ft<sup>2</sup></li>--}}
                                                                </ul>
                                                            </div>

                                                            {{--
                                                            <div class="listing-ad-type" style="margin-top:8px;">
                                                                @if ($result->agent_company)
                                                                    {{ucwords($result->agent_company)}}
                                                                @endif
                                                                <br>
                                                                @if ($result->path_to_logo)
                                                                    <span class="company_logo"><img style="max-width:150px;max-height:50px;" src="{{$result->path_to_logo}}"></span>
                                                                @endif
                                                            </div>
                                                            --}}

                                                            @if(isset($result->OpenHouse) && !$result->OpenHouse->isEmpty())
                                                                @foreach($result->OpenHouse as $key=>$item)
                                                                    @if(Carbon\Carbon::now()->format('Y-m-d') > $item->date)
                                                                        @php unset($result->OpenHouse[$key]) @endphp
                                                                    @endif
                                                                @endforeach
                                                                @if(count($result->OpenHouse))
                                                                <div style="padding-bottom:5px;padding-top:10px;">Open House:</div>
                                                                @endif
                                                                <div style="float:left;"> 
                                                                @foreach($result->OpenHouse as $key=>$item)
                                                                    <div class="listing-ad-open-house is-pulled-left" style="padding-left:0px;width:100%;">
                                                                    <span class="button2" style="border:none;padding:0px;"><b>{{Carbon\Carbon::parse($item->date)->format('D M j')}}</b>&nbsp;
                                                                @if($item->appointment)
                                                                    <b>by appointment</b>
                                                                @else
                                                                    {{Carbon\Carbon::parse($item->start_time)->format('g:i A')}} -
                                                                    {{Carbon\Carbon::parse($item->end_time)->format('g:i A')}}
                                                                    @endif</span>
                                                                    </div>
                                                                @endforeach
                                                                </div>
                                                                <div style="clear:both;"></div>
                                                            @endif

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <hr />
                                        @endforeach
                                    @endif
                                </div>

                            </div>

                            <div class="sidebar column is-4-desktop is-pulled-left contact-box hidden-print" style="border:none;">

                                <div class="sidebar-ttl" style="font-size:15px;">
                                    <h3 class="has-text-weight-bold">Contact {{$agent->full_name}}</h3>
                                    @if ($agent->path_to_logo)
                                        @if ($agent->web_site)
                                            <a href="{{$agent->web_site}}" target="_blank"><img src="@if(env('TEST_IMG'))/images/listed-meta.png @else{{$agent->path_to_logo}}@endif" alt="" style="max-width:150px;max-height:50px;margin-top:10px;margin-bottom:8px;"></a>
                                        @else
                                            <img src="@if(env('TEST_IMG'))/images/listed-meta.png @else{{$agent->path_to_logo}}@endif" alt="" style="max-width:150px;max-height:50px;margin-top:10px;margin-bottom:8px;">
                                        @endif
                                    @elseif(isset($agent->company))
                                        <p style="padding:10px 0px;padding-top:7px;">
                                            @if ($agent->web_site)
                                                <a href="{{$agent->web_site}}" target="_blank">{{ucwords($agent->company)}}</a>
                                            @else
                                                {{ucwords($agent->company)}}
                                            @endif
                                        </p>
                                    @endif
                                    @if (isset($agent->office_phone) && trim($agent->office_phone))
                                        <p style="margin-top:0px;">
                                            <span>Call:</span>
                                            <span style="cursor:pointer;color:#205aa2;" onclick="$(this).hide();$('#phone_1').show();">[click show phone #]</span>
                                            <span id="phone_1" style="display:none;">{{$agent->office_phone}}</span>
                                        </p>
                                    @endif
                                </div>
                                <div class="contact-form">
                                    <send inline-template v-bind:type="'regular'" v-bind:agentemail="'{{$agent->email}}'">
                                        <div>                                
                                            <div class="input-wr">
                                                <label for="inp2">phone number:</label>
                                                <input v-model="phone" name="phone" type="text" id="inp2" placeholder="Write here…" onkeypress="return isNumberKey(event)" required>
                                            </div>
                                            <div class="input-wr">
                                                <label for="inp1">e-mail address:</label>
                                                <input v-model="useremail" name="email" type="text" id="inp1" placeholder="Write here…" required>
                                            </div>
                                            <div class="input-wr">
                                                <label for="inp3">Add Note:</label>
                                                <textarea v-model="message" name="message" id="inp3" placeholder="Write here…" required></textarea>
                                            </div>
                                            <button id="sendButton" type="button" v-on:click="setPost" class="button btn"><div style="width:100%;text-align:center;">Send Message</div></button>
                                            <div id="messageResponse" style="padding-top:10px;"></div>
                                        </div>
                                    </send>
                                </div>

                                <div>                                    

                                    {{--
                                    <form id="contact_agent" method="post" action="/contact-agent">
                                        <send inline-template v-bind:type="'regular'" v-bind:agentemail="'{{$agent->email}}'">
                                            <div>
                                                <div class="control has-icons-left field">
                                                    <input v-model="phone" type="tel" name="phone" class="input" value="" placeholder="Phone Number" />
                                                    <span class="icon is-small is-left">
                                                    <i class="fa fa-phone"></i>
                                                </span>
                                                </div>
                                                <div class="control has-icons-left field">
                                                    <input v-model="useremail" class="input" type="email" placeholder="Email" name="email" value="" required />
                                                    <span class="icon is-small is-left">
                                                    <i class="fa fa-envelope"></i>
                                                </span>
                                                </div>
                                                <div class="control has-icons-left field">
                                                <textarea v-model="message" name="message" rows="5" class="textarea" placeholder="Add note...">
                                                </textarea>
                                                </div>

                                                <input name="agent_id" value="{{$agent->id}}" type="hidden" />
                                                <input v-model="agentemail" name="agent_email" value="{{$agent->email}}" type="hidden" />
                                                {{csrf_field()}}
                                                <button type="button" v-on:click="setPost" class="button is-info" style="background-color:#3e65a9;">Send Message</button>
                                            </div>
                                        </send>
                                    </form>      
                                    <div id="messageResponse" class="has-text-centered" style="margin-bottom:20px;"></div>
                                    --}}

                                </div>

                                {{--
                                <div>
                                    <a href="#" onclick="print(); return false;"><i class="fa fa-print"></i>&nbsp;Print</a>
                                </div>
                                <div style="margin-top:5px;">
                                    <a href="#"><i class="fa fa-facebook"></i>&nbsp;Share on Facebook</a>
                                </div>
                                --}}

                            </div>

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
        function showSales(sales) {
            if (sales == 'past_sales') {
                $('.past_sales').show();
                $('.cur_sales').hide();
            }
            else {
                $('.cur_sales').show();
                $('.past_sales').hide();
            }

            $("#sale_1, #sale_2").toggleClass('select_list unselect_list');
        }

        function showRentals(rentals) {
            if (rentals == 'past_rentals') {
                $('.past_rentals').show();
                $('.cur_rentals').hide();
            }
            else {
                $('.cur_rentals').show();
                $('.past_rentals').hide();
            }

            $("#rental_1, #rental_2").toggleClass('select_list unselect_list');
        }

        function isNumberKey(evt){
            var charCode = (evt.which) ? evt.which : event.keyCode
            if (charCode > 31 && (charCode < 48 || charCode > 57))
                return false;
            return true;
        }
    </script>
@endsection