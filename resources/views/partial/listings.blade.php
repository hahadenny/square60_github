

@php 
    if (Auth::user()->isAgent()) {
        $agent_infos = Auth::user()->agentInfos();
        $agent_url = '/agent/'.str_replace(array(' ', '/', '#', '?'), '_', $agent_infos->full_name);
    }
@endphp

@if($sellListing['countFeature'] != 0)
    <h4><b>{{$sellListing['countFeature']}} Featured Sales:</b></h4>
    <hr>
    @foreach($sellListing['feature'] as $item)
        <div class="columns box-listing">            
            <div class="column" style="padding-left:0px;">
                @if ($item->is_verified == -1)
                <div style="margin-bottom:10px; color:red; font-weight:bold">List Not Approved.</div>
                @elseif ($item->is_verified == 0)
                <div style="margin-bottom:10px; color:#3273dc; font-weight:bold">List Pending.</div>
                @elseif (!$item->active)
                <div style="margin-bottom:10px; color:red; font-weight:bold">List not activated yet. Please click the "Non-Active" button to activate it.</div>
                @else
                    @if($item->estate_type == 1 && $item->status == -1)
                    <div style="margin-bottom:10px; color:rgb(192, 74, 74); font-weight:bold">OFF MARKET</div>
                    @elseif($item->estate_type == 1 && $item->status == -2)
                    <div style="margin-bottom:10px; color:rgb(192, 74, 74); font-weight:bold">IN CONTRACT</div>
                    @elseif($item->estate_type == 1 && $item->status == -3)
                    <div style="margin-bottom:10px; color:rgb(192, 74, 74); font-weight:bold">SOLD</div>
                    @endif
                @endif
                <div class="columns box-listing">
                    <div class="column" style="padding-left:0px;padding-bottom:0px;padding-top:0px;">
                        @if ($item->img)
                            <img src="@if(env('TEST_IMG'))/images/display3.jpg @else{{ env('S3_IMG_PATH_1') }}{{$item->img}}@endif" alt="" style="width:240px;" />
                        @else
                            <img class="is-background" src="/images/default_image_coming.jpg" alt="" style="width:240px;" />
                        @endif
                    </div>
                    <div class="column" style="padding-top:0px;padding-left:0px;">
                        <a @if($item->active == 1) 
                                @if (!$item->unit)
                                href="/show/{{str_replace(' ','_',$item->name)}}/_/{{str_replace(' ','_',$item->city)}}"
                                @else
                                href="/show/{{str_replace(' ','_',$item->name)}}/{{str_replace('#', '', str_replace(array(' ', '/'), '_', $item->unit))}}/{{str_replace(' ','_',$item->city)}}" 
                                @endif 
                            @else href="javascript:void(0);" onclick="swal('You have not activated this listing yet.');" @endif><b>{{$item->full_address}} {{$item->unit}}</b></a><br>
                        {{$item->neighborhood}}, {{$item->unit_type}}<br>
                        ${{number_format($item->price,0,'.',',')}}<br>
                        {{$item->beds}} beds &middot; {{$item->baths}} baths<br>
                        {{$item->sq_feet}} ft<sup>2</sup><br>
                        Listed by <span style="color: #2366d1; cursor:pointer;" @if(isset($agent_url))onclick="window.location='{{$agent_url}}';"@endif>@if(isset($agent_infos->full_name)){{$agent_infos->full_name}}@else{{Auth::user()->name}}@endif</span>            
                    </div>
                    <div class="column" style="padding-top:0px;padding-left:0px;padding-right:0px;"> 
                        <div style="float:left;">
                            <form method="POST" action="{{ route('submitsell') }}" enctype="multipart/form-data">
                                {{ csrf_field() }}
                                <input type="submit" class="button {{$item->active == 1 ? "is-warning mainbgc" : "is-danger"}}" name="submit" value="{{$item->active == 1 ? "Active" : "Non-Active"}}" style="display:table-cell;color:#fff;width:83px;" @if($item->is_verified == -1) onclick="swal('Sorry, your Listing is not approved.');return false;" @elseif(!$item->is_verified) onclick="swal('Your Listing is not approved yet. Please give us 24 hours to verify your data.');return false;" @endif>
                                <input type="hidden" name="id" value="{{$item->id}}">
                            </form>
                            <form method="POST" action="{{ route('editsell') }}" enctype="multipart/form-data">
                                {{ csrf_field() }}
                                <input type="submit" class="button is-info mainbgc" name="submit" value="Edit" style="display:table-cell;margin-top:0.5rem;width:83px;">
                                <input type="hidden" name="id" value="{{$item->id}}">
                            </form>
                            <form method="POST" action="{{ route('deletesell') }}" enctype="multipart/form-data">
                                {{ csrf_field() }}
                                <input type="submit" class="button is-primary mainbgc" name="submit" onclick="return confirm('Are you sure you want to delete this item?');" value="Delete" style="display:table-cell;margin-top:0.5rem;width:83px;">
                                <input type="hidden" name="id" value="{{$item->id}}">
                            </form>
                        </div>
                        <div style="float:left;margin-left:10px;">
                            <a href="{{route('feature', ['id' => $item->id])}}" class="button is-success" style="margin-top:0px;background-color:#1fb314;width:83px;">
                                Feature
                            </a><br>
                            <a href="{{route('premium', ['id' => $item->id])}}" class="button is-warning" style="margin-top:0.5rem;width:83px;color:#fff;background-color:#e99b19;">
                                Premium
                            </a>
                        </div>
                    </div>
                </div>

                <div style="clear:both;"></div>
                <div style="font-size:13px;margin-top:20px;">
                Listing ID:
                {{$item->id}}
                <br><br>
                Feature Ends:<br>
                @foreach($item->features as $k => $value)
                    {{Carbon\Carbon::parse($value->ends_at)->format('Y-m-d')}}
                    @if($value->renew)
                    <div style="margin-top:10px;">Auto Renew On:<br>{{Carbon\Carbon::parse($value->ends_at)->format('Y-m-d')}}</div>
                    @endif
                    @break;
                @endforeach        

                @if (count($item->premiums))
                    <br>
                    Premium Ends:<br>
                    @foreach($item->premiums as $k => $value)
                        {{Carbon\Carbon::parse($value->ends_at)->format('Y-m-d')}}
                        @if($value->renew)
                        <div style="margin-top:10px;">Auto Renew On:<br>{{Carbon\Carbon::parse($value->ends_at)->format('Y-m-d')}}</div>
                        @endif
                        @break;
                    @endforeach      
                @endif  
                </div> 

                @if(isset($item->openHouse) && !empty($item->openHouse))
                    @foreach($item->openHouse as $key=>$v)
                        @if(Carbon\Carbon::now()->format('Y-m-d') > $v->date)
                            @php unset($item->openHouse[$key]) @endphp
                        @endif
                    @endforeach
                    @if(count($item->openHouse))
                    <div style="padding-bottom:10px;margin-top:20px;"><b>Open House:</b></div>
                    @endif
                    @foreach($item->openHouse as $value)
                        <div>{{Carbon\Carbon::parse($value->date)->format('D M j')}} &nbsp;
                            @if($value->appointment)
                                <b>By Appointment</b>
                            @else
                                {{Carbon\Carbon::parse($value->start_time)->format('g:i A')}} -
                                {{Carbon\Carbon::parse($value->end_time)->format('g:i A')}}
                            @endif
                        </div>
                    @endforeach
                @endif

                <form method="POST" action="{{ route('addsell') }}" enctype="multipart/form-data" style="margin-bottom:3rem;">
                    {{ csrf_field() }}
                    <div class="form-content openhouse" style="max-width:550px;">
                        <a class="more button is-primary" list-id="{{$item->id}}" style="margin-top:0.5rem;background-color:#e77366;">Add an Open House</a><br>
                        <input id="save-openhouse-{{$item->id}}" type="submit" class="button is-info mainbgc" value="Save" style="margin-top:1rem;display:none;">
                    </div>                
                    <input type="hidden" name="id" value="{{$item->id}}">
                    <input type="hidden" name="openhouseonly" value="1">                    
                </form>
            </div>
        </div>
    @endforeach
    <hr>
@endif
<br>


@if($rentalListing['countFeature'] != 0)
    <h4><b>{{$rentalListing['countFeature']}} Featured Rentals:</b></h4>
    <hr>
    @foreach ( $rentalListing['feature'] as $item)

        <div class="columns box-listing">            
            <div class="column" style="padding-left:0px;">        
                @if ($item->is_verified == -1)
                <div style="margin-bottom:10px; color:red; font-weight:bold">List Not Approved.</div>
                @elseif ($item->is_verified == 0)
                <div style="margin-bottom:10px; color:#3273dc; font-weight:bold">List Pending.</div>
                @elseif (!$item->active)
                <div style="margin-bottom:10px; color:red; font-weight:bold">List not activated yet. Please click the "Non-Active" button to activate it.</div>
                @else
                    @if($item->estate_type == 2 && $item->status == -1)
                    <div style="margin-bottom:10px; color:rgb(192, 74, 74); font-weight:bold">OFF MARKET</div>
                    @elseif($item->estate_type == 2 && $item->status == -2)
                    <div style="margin-bottom:10px; color:rgb(192, 74, 74); font-weight:bold">PENDING</div>
                    @elseif($item->estate_type == 2 && $item->status == -3)
                    <div style="margin-bottom:10px; color:rgb(192, 74, 74); font-weight:bold">RENTED</div>
                    @endif
                @endif        
                <div class="columns box-listing">
                    <div class="column" style="padding-left:0px;padding-bottom:0px;padding-top:0px;">
                        @if ($item->img)
                            <img src="@if(env('TEST_IMG'))/images/display3.jpg @else{{ env('S3_IMG_PATH_1') }}{{$item->img}}@endif" alt="" style="width:240px;">
                        @else
                            <img class="is-background" src="/images/default_image_coming.jpg" alt="" style="width:240px" />
                        @endif
                    </div>
                    <div class="column" style="padding-top:0px;padding-left:0px;">
                        <a @if($item->active == 1) 
                                @if (!$item->unit)
                                href="/show/{{str_replace(' ','_',$item->name)}}/_/{{str_replace(' ','_',$item->city)}}"
                                @else
                                href="/show/{{str_replace(' ','_',$item->name)}}/{{str_replace('#', '', str_replace(array(' ', '/'), '_', $item->unit))}}/{{str_replace(' ','_',$item->city)}}" 
                                @endif 
                            @else href="javascript:void(0);" onclick="swal('You have not activated this listing yet.');" @endif><b>{{$item->full_address}} {{$item->unit}}</b></a><br>
                        {{$item->neighborhood}}, {{$item->unit_type}}<br>
                        ${{number_format($item->price,0,'.',',')}}<br>
                        {{$item->beds}} beds &middot; {{$item->baths}} baths<br>
                        {{$item->sq_feet}} ft<sup>2</sup><br>
                        Listed by <span style="color: #2366d1;cursor:pointer;" @if(isset($agent_url))onclick="window.location='{{$agent_url}}';"@endif>@if(isset($agent_infos->full_name)){{$agent_infos->full_name}}@else{{Auth::user()->name}}@endif</span>
                    </div>
                    <div class="column" style="padding-top:0px;padding-left:0px;padding-right:0px;">
                        <div style="float:left;">
                            <form method="POST" action="{{ route('submitrental') }}" enctype="multipart/form-data">
                                {{ csrf_field() }}
                                <input type="submit" class="button {{$item->active == 1 ? "is-warning mainbgc" : "is-danger"}}" name="submit" value="{{$item->active == 1 ? "Active" : "Non-Active"}}" style="display:table-cell;color:#fff;width:83px;" @if($item->is_verified == -1) onclick="swal('Sorry, your Listing is not approved.');return false;" @elseif(!$item->is_verified) onclick="swal('Your Listing is not approved yet. Please give us 24 hours to verify your data.');return false;" @endif>
                                <input type="hidden" name="id" value="{{$item->id}}">
                            </form>
                            <form method="POST" action="{{ route('editrental') }}" enctype="multipart/form-data">
                                {{ csrf_field() }}
                                <input type="submit" class="button is-info mainbgc" name="submit" value="Edit" style=display:table-cell;margin-top:0.5rem;width:83px;">
                                <input type="hidden" name="id" value="{{$item->id}}">
                            </form>
                            <form method="POST" action="{{ route('deleterental') }}" enctype="multipart/form-data">
                                {{ csrf_field() }}
                                <input type="submit" class="button is-primary mainbgc" name="submit" onclick="return confirm('Are you sure you want to delete this item?');" value="Delete" style="display:table-cell;margin-top:0.5rem;width:83px;">
                                <input type="hidden" name="id" value="{{$item->id}}">
                            </form>
                        </div>
                        <div style="float:left;margin-left:10px;">
                            <a href="{{route('feature', ['id' => $item->id])}}" class="button is-primary" style="margin-top:0rem;background-color:#1fb314;width:83px;">
                                Feature
                            </a><br>
                            <a href="{{route('premium', ['id' => $item->id])}}" class="button is-warning" style="margin-top:0.5rem;width:83px;color:#fff;background-color:#e99b19;">
                                Premium
                            </a>
                        </div>
                    </div>
                </div>

                <div style="clear:both;"></div>
                <div style="font-size:13px;margin-top:20px;">
                Listing ID:
                {{$item->id}}
                <br><br>
                Feature Ends:<br>
                @foreach($item->features as $k => $value)
                    {{Carbon\Carbon::parse($value->ends_at)->format('Y-m-d')}}
                    @if($value->renew)
                    <div style="margin-top:10px;">Auto Renew On:<br>{{Carbon\Carbon::parse($value->ends_at)->format('Y-m-d')}}</div>
                    @endif
                    @break;
                @endforeach 

                @if (count($item->premiums))
                    <br>
                    Premium Ends:<br>
                    @foreach($item->premiums as $k => $value)
                        {{Carbon\Carbon::parse($value->ends_at)->format('Y-m-d')}}
                        @if($value->renew)
                        <div style="margin-top:10px;">Auto Renew On:<br>{{Carbon\Carbon::parse($value->ends_at)->format('Y-m-d')}}</div>
                        @endif
                        @break;
                    @endforeach      
                @endif  
                </div>

                @if(isset($item->openHouse) && !empty($item->openHouse))
                    @foreach($item->openHouse as $key=>$v)
                        @if(Carbon\Carbon::now()->format('Y-m-d') > $v->date)
                            @php unset($item->openHouse[$key]) @endphp
                        @endif
                    @endforeach
                    @if(count($item->openHouse))
                    <div style="padding-bottom:10px;margin-top:20px;"><b>Open House:</b></div>
                    @endif
                    @foreach($item->openHouse as $value)
                        <div>{{Carbon\Carbon::parse($value->date)->format('D M j')}} &nbsp;
                            @if($value->appointment)
                                <b>By Appointment</b>
                            @else
                                {{Carbon\Carbon::parse($value->start_time)->format('g:i A')}} -
                                {{Carbon\Carbon::parse($value->end_time)->format('g:i A')}}
                            @endif
                        </div>
                    @endforeach
                @endif

                <form method="POST" action="{{ route('addrental') }}" enctype="multipart/form-data" style="margin-bottom:3rem;">
                    {{ csrf_field() }}
                    <div class="form-content openhouse" style="max-width:550px;">
                        <a class="more button is-primary" list-id="{{$item->id}}" style="margin-top:0.5rem;background-color:#e77366;">Add an Open House</a><br>
                        <input id="save-openhouse-{{$item->id}}" type="submit" class="button is-info mainbgc" value="Save" style="margin-top:1rem;display:none;">
                    </div>                
                    <input type="hidden" name="id" value="{{$item->id}}">
                    <input type="hidden" name="openhouseonly" value="1">
                    
                </form>
            </div>
        </div>

    @endforeach
    <hr>
@endif


@if($sellListing['count'] != 0 || $rentalListing['count'] != 0)

    @if($sellListing['count'] != 0)
        <h4><b>{{$sellListing['count']}} Non-Featured Sales:</b></h4>
        <hr>
        @foreach($sellListing['data'] as $item)
            <div class="columns box-listing">                
                <div class="column" style="padding-left:0px;">
                    @if ($item->is_verified == -1)
                    <div style="margin-bottom:10px; color:red; font-weight:bold">List Not Approved.</div>
                    @elseif ($item->is_verified == 0)
                    <div style="margin-bottom:10px; color:#3273dc; font-weight:bold">List Pending.</div>
                    @elseif (!$item->active)
                    <div style="margin-bottom:10px; color:red; font-weight:bold">List not activated yet. Please click the "Non-Active" button to activate it.</div>
                    @else
                        @if($item->estate_type == 1 && $item->status == -1)
                        <div style="margin-bottom:10px; color:rgb(192, 74, 74); font-weight:bold">OFF MARKET</div>
                        @elseif($item->estate_type == 1 && $item->status == -2)
                        <div style="margin-bottom:10px; color:rgb(192, 74, 74); font-weight:bold">IN CONTRACT</div>
                        @elseif($item->estate_type == 1 && $item->status == -3)
                        <div style="margin-bottom:10px; color:rgb(192, 74, 74); font-weight:bold">SOLD</div>
                        @endif
                    @endif
                    <div class="columns box-listing">
                        <div class="column" style="padding-left:0px;padding-bottom:0px;padding-top:0px;">
                            @if ($item->img)
                                <img src="@if(env('TEST_IMG'))/images/display3.jpg @else{{ env('S3_IMG_PATH_1') }}{{$item->img}}@endif" alt="" style="width:240px;">
                            @else
                                <img class="is-background" src="/images/default_image_coming.jpg" alt="" style="width:240px;" />
                            @endif
                        </div>
                        <div class="column" style="padding-top:0px;padding-left:0px;padding-right:0px;">
                            <a @if($item->active == 1) 
                                    @if (!$item->unit)
                                    href="/show/{{str_replace(' ','_',$item->name)}}/_/{{str_replace(' ','_',$item->city)}}"
                                    @else
                                    href="/show/{{str_replace(' ','_',$item->name)}}/{{str_replace('#', '', str_replace(array(' ', '/'), '_', $item->unit))}}/{{str_replace(' ','_',$item->city)}}" 
                                    @endif                                 
                                @else href="javascript:void(0);" onclick="swal('You have not activated this listing yet.');" @endif><b>{{$item->full_address}} {{$item->unit}}</b></a><br>
                            {{$item->neighborhood}}, {{$item->unit_type}}<br>
                            ${{number_format($item->price,0,'.',',')}}<br>
                            {{$item->beds}} beds &middot; {{$item->baths}} baths<br>
                            {{$item->sq_feet}} ft<sup>2</sup><br>
                            Listed by <span style="color: #2366d1;cursor:pointer;" @if(isset($agent_url))onclick="window.location='{{$agent_url}}';"@endif>@if(isset($agent_infos->full_name)){{$agent_infos->full_name}}@else{{Auth::user()->name}}@endif</span>
                        </div>
                        <div class="column" style="padding-top:0px;padding-left:0px;padding-right:0px;">
                            <div style="float:left;">
                                <form method="POST" action="{{ route('submitsell') }}" enctype="multipart/form-data">
                                    {{ csrf_field() }}
                                    <input type="submit" class="button {{$item->active == 1 ? "is-warning mainbgc" : "is-danger"}}" name="submit" value="{{$item->active == 1 ? "Active" : "Non-Active"}}" style="display:table-cell;color:#fff;width:83px;" @if($item->is_verified == -1) onclick="swal('Sorry, your Listing is not approved.');return false;" @elseif(!$item->is_verified) onclick="swal('Your Listing is not approved yet. Please give us 24 hours to verify your data.');return false;" @endif>
                                    <input type="hidden" name="id" value="{{$item->id}}">
                                </form>
                                <form method="POST" action="{{ route('editsell') }}" enctype="multipart/form-data">
                                    {{ csrf_field() }}
                                    <input type="submit" class="button is-info mainbgc" name="submit" value="Edit" style="display:table-cell;margin-top:0.5rem;width:83px;">
                                    <input type="hidden" name="id" value="{{$item->id}}">
                                </form>
                                <form method="POST" action="{{ route('deletesell') }}" enctype="multipart/form-data">
                                    {{ csrf_field() }}
                                    <input type="submit" class="button is-primary mainbgc" name="submit" onclick="return confirm('Are you sure you want to delete this item?');" value="Delete" style="display:table-cell;margin-top:0.5rem;width:83px;">
                                    <input type="hidden" name="id" value="{{$item->id}}">
                                </form>
                            </div>
                            <div style="float:left;margin-left:10px;">
                                <a href="{{route('feature', ['id' => $item->id])}}" class="button is-primary" style="margin-top:0rem;background-color:#1fb314;width:83px;">
                                    Feature
                                </a><br>
                                <a href="{{route('premium', ['id' => $item->id])}}" class="button is-warning" style="margin-top:0.5rem;width:83px;color:#fff;background-color:#e99b19;">
                                    Premium
                                </a>
                            </div>
                        </div>
                    </div>

                    <div style="clear:both;"></div>
                    <div style="font-size:13px;margin-top:20px;">
                    Listing ID:
                    {{$item->id}}
                    <br>
                    @if (count($item->premiums))
                        <br>
                        Premium Ends:<br>
                        @foreach($item->premiums as $k => $value)
                            {{Carbon\Carbon::parse($value->ends_at)->format('Y-m-d')}}
                            @if($value->renew)
                            <div style="margin-top:10px;">Auto Renew On:<br>{{Carbon\Carbon::parse($value->ends_at)->format('Y-m-d')}}</div>
                            @endif
                            @break;
                        @endforeach      
                    @endif      
                    </div>  

                    @if(isset($item->openHouse) && !empty($item->openHouse))
                        @foreach($item->openHouse as $key=>$v)
                            @if(Carbon\Carbon::now()->format('Y-m-d') > $v->date)
                                @php unset($item->openHouse[$key]) @endphp
                            @endif
                        @endforeach
                        @if(count($item->openHouse))
                        <div style="padding-bottom:10px;margin-top:20px;"><b>Open House:</b></div>
                        @endif
                        @foreach($item->openHouse as $value)
                            <div>{{Carbon\Carbon::parse($value->date)->format('D M j')}} &nbsp;
                                @if($value->appointment)
                                    <b>By Appointment</b>
                                @else
                                    {{Carbon\Carbon::parse($value->start_time)->format('g:i A')}} -
                                    {{Carbon\Carbon::parse($value->end_time)->format('g:i A')}}
                                @endif
                            </div>
                        @endforeach
                    @endif

                    <form method="POST" action="{{ route('addsell') }}" enctype="multipart/form-data" style="margin-bottom:3rem;">
                        {{ csrf_field() }}
                        <div class="form-content openhouse" style="max-width:550px;">
                            <a class="more button is-primary" list-id="{{$item->id}}" style="margin-top:0.5rem;background-color:#e77366;">Add an Open House</a><br>
                            <input id="save-openhouse-{{$item->id}}" type="submit" class="button is-info mainbgc" value="Save" style="margin-top:1rem;display:none;">
                        </div>                
                        <input type="hidden" name="id" value="{{$item->id}}">
                        <input type="hidden" name="openhouseonly" value="1">                        
                    </form>
                </div>
            </div>
        @endforeach
    @endif
    <br>

    @if($rentalListing['count'] != 0)
        <h4><b>{{$rentalListing['count']}} Non-Featured Rentals:</b></h4>
        <hr>
        @foreach($rentalListing['data'] as $item)
            <div class="columns box-listing">                
                <div class="column" style="padding-left:0px;">
                    @if ($item->is_verified == -1)
                    <div style="margin-bottom:10px; color:red; font-weight:bold">List Not Approved.</div>
                    @elseif ($item->is_verified == 0)
                    <div style="margin-bottom:10px; color:#3273dc; font-weight:bold">List Pending.</div>
                    @elseif (!$item->active)
                    <div style="margin-bottom:10px; color:red; font-weight:bold">List not activated yet. Please click the "Non-Active" button to activate it.</div>
                    @else
                        @if($item->estate_type == 2 && $item->status == -1)
                        <div style="margin-bottom:10px; color:rgb(192, 74, 74); font-weight:bold">OFF MARKET</div>
                        @elseif($item->estate_type == 2 && $item->status == -2)
                        <div style="margin-bottom:10px; color:rgb(192, 74, 74); font-weight:bold">PENDING</div>
                        @elseif($item->estate_type == 2 && $item->status == -3)
                        <div style="margin-bottom:10px; color:rgb(192, 74, 74); font-weight:bold">RENTED</div>
                        @endif    
                    @endif
                    <div class="columns box-listing">
                        <div class="column" style="padding-left:0px;padding-bottom:0px; padding-top:0px;">
                            @if ($item->img)
                                <img src="@if(env('TEST_IMG'))/images/display3.jpg @else{{ env('S3_IMG_PATH_1') }}{{$item->img}}@endif" alt="" style="width:240px;">
                            @else
                                <img class="is-background" src="/images/default_image_coming.jpg" alt="" style="width:240px;" />
                            @endif
                        </div>
                        <div class="column" style="padding-top:0px;padding-left:0px;">
                            <a @if($item->active == 1) 
                                    @if (!$item->unit)
                                    href="/show/{{str_replace(' ','_',$item->name)}}/_/{{str_replace(' ','_',$item->city)}}"
                                    @else
                                    href="/show/{{str_replace(' ','_',$item->name)}}/{{str_replace('#', '', str_replace(array(' ', '/'), '_', $item->unit))}}/{{str_replace(' ','_',$item->city)}}" 
                                    @endif                                 
                                @else href="javascript:void(0);" onclick="swal('You have not activated this listing yet.');" @endif><b>{{$item->full_address}} {{$item->unit}}</b></a><br>
                            {{$item->neighborhood}}, {{$item->unit_type}}<br>
                            ${{number_format($item->price,0,'.',',')}}<br>
                            {{$item->beds}} beds &middot; {{$item->baths}} baths<br>
                            {{$item->sq_feet}} ft<sup>2</sup><br>
                            Listed by <span style="color: #2366d1;cursor:pointer;" @if(isset($agent_url))onclick="window.location='{{$agent_url}}';"@endif>@if(isset($agent_infos->full_name)){{$agent_infos->full_name}}@else{{Auth::user()->name}}@endif</span>         
                        </div>
                        <div class="column" style="padding-top:0px;padding-left:0px;padding-right:0px;">
                            <div style="float:left;">
                                <form method="POST" action="{{ route('submitrental') }}" enctype="multipart/form-data">
                                    {{ csrf_field() }}
                                    <input type="submit" class="button {{$item->active == 1 ? "is-warning mainbgc" : "is-danger"}}" name="submit" value="{{$item->active == 1 ? "Active" : "Non-Active"}}" style="display:table-cell;color:#fff;width:83px;" @if($item->is_verified == -1) onclick="swal('Sorry, your Listing is not approved.');return false;" @elseif(!$item->is_verified) onclick="swal('Your Listing is not approved yet. Please give us 24 hours to verify your data.');return false;" @endif>
                                    <input type="hidden" name="id" value="{{$item->id}}">
                                </form>
                                <form method="POST" action="{{ route('editrental') }}" enctype="multipart/form-data">
                                    {{ csrf_field() }}
                                    <input type="submit" class="button is-info mainbgc" name="submit" value="Edit" style="display:table-cell;margin-top:0.5rem;width:83px;">
                                    <input type="hidden" name="id" value="{{$item->id}}">
                                </form>
                                <form method="POST" action="{{ route('deleterental') }}" enctype="multipart/form-data">
                                    {{ csrf_field() }}
                                    <input type="submit" class="button is-primary mainbgc" name="submit" onclick="return confirm('Are you sure you want to delete this item?');" value="Delete" style="display:table-cell;margin-top:0.5rem;width:83px;">
                                    <input type="hidden" name="id" value="{{$item->id}}">
                                </form>
                            </div>
                            <div style="float:left;margin-left:10px;">
                                <a href="{{route('feature', ['id' => $item->id])}}" class="button is-primary" style="margin-top:0rem;background-color:#1fb314;width:83px;">
                                    Feature
                                </a><br>
                                <a href="{{route('premium', ['id' => $item->id])}}" class="button is-warning" style="margin-top:0.5rem;width:83px;color:#fff;background-color:#e99b19;">
                                    Premium
                                </a>
                            </div>
                        </div>
                    </div>

                    <div style="clear:both;"></div>
                    <div style="font-size:13px;margin-top:20px;">
                    Listing ID:
                    {{$item->id}}
                    <br>
                    @if (count($item->premiums))
                        <br>
                        Premium Ends:<br>
                        @foreach($item->premiums as $k => $value)
                            {{Carbon\Carbon::parse($value->ends_at)->format('Y-m-d')}}
                            @if($value->renew)
                            <div style="margin-top:10px;">Auto Renew On:<br>{{Carbon\Carbon::parse($value->ends_at)->format('Y-m-d')}}</div>
                            @endif
                            @break;
                        @endforeach      
                    @endif       
                    </div>  

                    @if(isset($item->openHouse) && !empty($item->openHouse))
                        @foreach($item->openHouse as $key=>$v)
                            @if(Carbon\Carbon::now()->format('Y-m-d') > $v->date)
                                @php unset($item->openHouse[$key]) @endphp
                            @endif
                        @endforeach
                        @if(count($item->openHouse))
                        <div style="padding-bottom:10px;margin-top:20px;"><b>Open House:</b></div>
                        @endif
                        @foreach($item->openHouse as $value)
                            <div>{{Carbon\Carbon::parse($value->date)->format('D M j')}} &nbsp;
                                @if($value->appointment)
                                    <b>By Appointment</b>
                                @else
                                    {{Carbon\Carbon::parse($value->start_time)->format('g:i A')}} -
                                    {{Carbon\Carbon::parse($value->end_time)->format('g:i A')}}
                                @endif
                            </div>
                        @endforeach
                    @endif

                    <form method="POST" action="{{ route('addrental') }}" enctype="multipart/form-data" style="margin-bottom:3rem;">
                        {{ csrf_field() }}
                        <div class="form-content openhouse" style="max-width:550px;">
                            <a class="more button is-primary" list-id="{{$item->id}}" style="margin-top:0.5rem;background-color:#e77366;">Add an Open House</a><br>
                            <input id="save-openhouse-{{$item->id}}" type="submit" class="button is-info mainbgc" value="Save" style="margin-top:1rem;display:none;">
                        </div>                
                        <input type="hidden" name="id" value="{{$item->id}}">
                        <input type="hidden" name="openhouseonly" value="1">                        
                    </form>
                </div>
            </div>
        @endforeach

    @endif
@endif

@section('additional_scripts')
<script>
var i = 0;    
$('.more').click(function() {
    var list_id = $(this).attr('list-id');
    $('#save-openhouse-'+list_id).show();
    $(this).before('<div id="openHouse-'+list_id+'-'+i+'" class="block openhouse-block-'+list_id+'">' +
        '                     <div class="form-content columns is-variable">' +
        '                         <div class="column" style="padding-bottom:0px;">' +
        '                             <label class="label form-content" for="">Open House:</label>\n' +
        '                                 <input type="date" name="openHouse['+i+'][date]" class="input" value="" style="max-width:150px;">' +
        '                         </div>' +
        '                         <div class="column">' +
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
        '                         <div class="column" style="margin-top:2rem;padding-bottom:0px;">' +
        '                             <label class="label form-content" for="" style="white-space: nowrap;">' +
        '                                    <input type="checkbox" name="openHouse['+i+'][appointment]" value="1">&nbsp;By Appointment</label>' +
        '                         </div>' +
        '                         <div class="column listremove" style="padding-bottom:0px;">' +
        '                             <a onclick="removeOpenHouse('+list_id+', '+i+')">Remove</a>' +
        '                         </div>' +
        '                         </div>' +
        '                         </div>');
    i++;
});

function removeOpenHouse(list_id, id){
    $("#openHouse-"+list_id+'-'+id).remove();
    if (!$('.openhouse-block-'+list_id).length)
        $('#save-openhouse-'+list_id).hide();
}
</script>
@endsection