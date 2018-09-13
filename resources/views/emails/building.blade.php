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
                            <img src="{{$domain}}images/logo2.png" alt="Square60" width="150px">
                        </a>
                    </td>
                </tr>

                <!-- Email Body -->
                <tr><td style="color:#1c5599;font-size:17px;border-bottom:solid 4px #000;padding-bottom:10px;font-weight:bold;"><?=str_replace(' (', ':<br>(', $subject)?></td></tr>
                <tr>
                    <td>
                    <table align="left" width="100%" cellpadding="0" cellspacing="0" style="margin-top:30px; font-family:arial;">
                        <!-- Body content -->
                        <tr>
                            <td valign=top style="padding-left:3%;padding-bottom:12px;">       
                                <a href="{{$domain}}building/{{str_replace(' ','_',$result->building_name)}}/{{str_replace(' ','_',$result->building_city)}}" style="border:none;"> 
                            @if ($result->img)
                                <img style="width: 90%; max-width:300px;" src="{{$result->img}}" alt="">
                            @else
                                <img style="width: 90%; max-width:300px;" src="{{$domain}}}images/default_image_coming.jpg" alt="">
                            @endif            
                                </a>           
                            </td>
                        </tr>
                        <tr>
                            <td style="padding-left:3%;" valign="top" style="color:#000;">
                                <div><a href="{{$domain}}building/{{str_replace(array(' ', '/', '#', '?'),'_',$result->building_name)}}/{{str_replace(' ','_',$result->building_city)}}" style="color:#1c5599;text-decoration:none;font-size:18px;">{{html_entity_decode(str_replace(' ','&zwnj; ',$result->building_name))}}</a></div>
                                <div style="margin-top:10px;font-size:18px;">{{$result->building_address}}, {{$result->building_city}} {{$result->building_state}}, {{$result->building_zip}}</div>
                                <div style="margin-top:9px; font-size:13px;">@if(isset($result->filterType)){{$result->filterType->value}}  in @endif{{$result->neighborhood}}</div>
                                <div style="margin-top:9px;font-size:13px;">Total Units: {{$result->building_units}}</div>
                                <div style="margin-top:9px;font-size:13px;">Floors: {{$result->building_stories}}</div>
                                <div style="margin-top:9px;font-size:13px;">Year Built: {{$result->building_build_year}}@if ($result->building_build_year < 1945) (prewar) @endif</div>
                                @if(isset($result->filterType))
                                <div style="margin-top:9px;font-size:13px;">Building Type: {{$result->filterType->value}}</div>
                                @endif
                                <div style="margin-top:9px;font-size:13px;">Description:</div>
                                <div style="margin-top:9px;font-size:13px;">{!! $result->building_description !!}</div>
                                <div style="margin-top:9px;font-size:13px;">Amenities/Features:</div>
                                <div style="margin-top:9px;font-size:13px;">
                                    <ul>
                                    @foreach (explode(',', $result->building_amenities[0]) as $b_amenities)
                                        @if($b_amenities)
                                    <li>{{$b_amenities}}</li>
                                        @endif
                                    @endforeach
                                    </ul>
                                </div>
                                {{--<div style="margin-top:9px;font-size:13px;">Listed By:</div>
                                @foreach ($agents as $agent)
                                <div style="margin-top:9px;font-size:13px;"> 
                                {{$agent->name}}<br>
                                @if (isset($agent->phone) && $agent->phone)
                                {{$agent->phone}}<br>
                                @endif  
                                @if($result->path_to_logo)
                                @if (isset($agent->web_site) && $agent->web_site) 
                                <a href="{{$agent->web_site}}">{{$agent->web_site}}</a>
                                @endif
                                <img src="{{$result->path_to_logo}}" />
                                @elseif(isset($agent->company) && $agent->company)
                                {{ucwords($agent->company)}}<br>
                                @endif
                                </div>
                                @endforeach
                                --}}
                            </td>
                        </tr>
                        <tr><td height="30px"></td></tr>
                    </table>
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