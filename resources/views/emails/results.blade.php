<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
</head>
<body>
<table width="100%" cellpadding="0" cellspacing="0">
    <tr>
        <td>
            <table align="center" width="100%" cellpadding="0" cellspacing="0">
                <tr>
                    <td class="header" align="center" style="background-color: #fff; padding: 1rem; padding-bottom:30px;">
                        <a href="{{$domain}}">
                            <img src="{{$domain}}images/logo2.png" alt="Square60" width="150">
                        </a>
                    </td>
                </tr>

                <!-- Email Body -->
                <tr><td style="color:#1c5599;font-size:17px;border-bottom:solid 4px #000;padding-bottom:10px;font-weight:bold;"><?=str_replace(' (', ':<br>(', $subject)?></td></tr>
                <tr>
                    <td>
                    <table align="left" width="100%" cellpadding="0" cellspacing="0" style="margin-top:30px; font-family:arial;">
                        <!-- Body content -->
                        @foreach ($results as $result)
                            <tr>
                                <td valign=top style="padding-left:3%;padding-bottom:12px;">   
                                    <div style="color:rgb(192, 74, 74);font-weight:800;font-size:18px;margin-bottom:7px;">
                                        @if($result->estate_type == 1 && $result->status == -1)
                                        OFF MARKET
                                        @elseif($result->estate_type == 1 && $result->status == -2)
                                        IN CONTRACT
                                        @elseif($result->estate_type == 1 && $result->status == -3)
                                        SOLD
                                        @elseif($result->estate_type == 2 && $result->status == -1)
                                        OFF MARKET
                                        @elseif($result->estate_type == 2 && $result->status == -2)
                                        PENDING
                                        @elseif($result->estate_type == 2 && $result->status == -3)
                                        RENTED
                                        @elseif($result->furnished)
                                        FURNISHED
                                        @endif
                                    </div>

                                    @if(!$result->unit)
                                    <a href="{{$domain}}show/{{str_replace(' ','_',$result->name)}}/_/{{str_replace(' ','_',$result->city)}}" style="border:none;"> 
                                    @else
                                    <a href="{{$domain}}show/{{str_replace(' ','_',$result->name)}}/{{str_replace('#', '', str_replace(array(' ', '/'), '_', $result->unit))}}/{{str_replace(' ','_',$result->city)}}" style="border:none;"> 
                                    @endif
                                @if ($result->img)
                                    <img style="width: 90%; max-width:300px;" src="{{$result->img}}" alt="">
                                @else
                                    <img style="width: 90%; max-width:300px;" src="{{$domain}}}/images/default_image_coming.jpg" alt="">
                                @endif            
                                    </a>           
                                </td>
                            </tr>
                            <tr>
                                <td style="padding-left:3%;" valign="top" style="color:#000;">
                                    <div>
                                        @if(!$result->unit)
                                        <a href="{{$domain}}show/{{str_replace(' ','_',$result->name)}}/_/{{str_replace(' ','_',$result->city)}}" style="color:#1c5599;text-decoration:none;font-size:18px;">
                                        @else
                                        <a href="{{$domain}}show/{{str_replace(' ','_',$result->name)}}/{{str_replace('#', '', str_replace(array(' ', '/'), '_', $result->unit))}}/{{str_replace(' ','_',$result->city)}}" style="color:#1c5599;text-decoration:none;font-size:18px;">
                                        @endif
                                            {{$result->full_address}} {{$result->unit}}
                                        </a>
                                    </div>
                                    @if($lastmail && $result->first_activated_at > $lastmail)<div style="margin-top:9px; font-size:13px;color:orange;">(NEW)</div>@endif
                                    <div style="margin-top:10px;"><span style="font-size:18px">${{$result->price}}</span></div>
                                    @if($lastmail && $result->last_price && $result->price > $result->last_price)<div style="margin-top:9px; font-size:13px; color:red;">(Price raised from ${{$result->last_price}})</div>@endif
                                    @if($lastmail && $result->last_price && $result->price < $result->last_price)<div style="margin-top:9px; font-size:13px; color:green;">(Price reduced from ${{$result->last_price}})</div>@endif
                                    <div style="margin-top:9px; font-size:13px;">{{$result->beds}} beds &nbsp;&bull;&nbsp; {{$result->baths}} baths @if($result->sq_feet)&nbsp;&bull;&nbsp; {{$result->sq_feet}} ft<sup>2</sup>@endif</div>
                                    <div style="margin-top:9px; font-size:13px;">{{$result->unit_type}} in {{$result->neighborhood}}</div>
                                    <div style="margin-top:12px;">
                                    @if(isset($result->openHouse) && !empty($result->openHouse))
                                        @foreach($result->openHouse as $key=>$item)
                                            @if(Carbon\Carbon::now() > $item->end_time)
                                                @php unset($result->openHouse[$key]) @endphp
                                        @endif
                                        @endforeach
                                        @if(count($result->openHouse))
                                        <b style="font-size:12px;">OPEN HOUSE:<br></b>
                                        @endif
                                        @foreach($result->openHouse as $value)
                                            <div style="font-size:12px;margin-top:7px;"><b>{{strtoupper(Carbon\Carbon::parse($value->date)->format('D M j'))}}
                                                @if($value->appointment)
                                                    BY APPOINTMENT
                                                @else
                                                    {{Carbon\Carbon::parse($value->start_time)->format('g:iA')}} -
                                                    {{Carbon\Carbon::parse($value->end_time)->format('g:iA')}}
                                                @endif
                                                </b>
                                            </div>
                                        @endforeach
                                    @endif
                                    </div>
                                </td>
                            </tr>
                            <tr><td height="30px"></td></tr>
                        @endforeach

                        @if($search_id)
                        <tr><td height="20px"></td></tr>
                        <tr><td colspan=2 align=center><a href="{{$domain}}search/{{$search_id}}" style="font-size:18px;text-decoration:none;color:#1c5599;">More Listing >></a></td></tr>
                        <tr><td height="50px"></td></tr>
                        @endif
                    </table>
                    </td>
                </tr>

                <tr>
                    <td>
                    <div style="margin-bottom:10px;font-size:13px;">To unsubscribe: please <a href="{{$domain}}login">login</a> and go to settings. Select "No" for recieving notification email question.</div>
                    </td>
                </tr>

                <tr>
                    <td>
                        <table class="footer" align="center" width="100%" cellpadding="0" cellspacing="0" style="background-color:whitesmoke;font-family:arial;font-size:13px;">             
                            <tr>
                                <td class="content-cell" align="center" style="padding: 1rem">                                    
                                    <strong>Square60</strong> &copy; All copyright reserved. {{--<a href="http://jgthms.com">Jeremy Thomas</a>. The source code is licensed
                                    <a href="http://opensource.org/licenses/mit-license.php">MIT</a>. The website content is licensed <a href="http://creativecommons.org/licenses/by-nc-sa/4.0/">CC BY NC SA 4.0</a>.--}}
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>