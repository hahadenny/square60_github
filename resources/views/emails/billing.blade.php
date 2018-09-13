<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
</head>
<body>

<table class="wrapper" width="100%" cellpadding="0" cellspacing="0">
    <tr>
        <td align="center">
            <table class="content" align="center" width="100%" cellpadding="0" cellspacing="0">
                <tr>
                    <td class="header" align="center" style="background-color: #fff; padding: 1rem; padding-left:0px; padding-bottom:15px;">
                        <a href="{{$domain}}">
                            <img src="{{$domain}}images/logo2.png" alt="Square60" width="150px">
                        </a>
                    </td>
                </tr>

                <!-- Email Body -->
                <tr>
                    <td align=center class="body" width="100%" cellpadding="0" cellspacing="0">
                        <table class="inner-body" align="center" width="100%" cellpadding="0" cellspacing="0" style="max-width:570px;">
                            <!-- Body content -->
                            <tr>
                                <td class="content-cell">
                                    <h2 align="left" style="font-size:16px;padding-bottom:10px;">{{$subject}}</h2>
                                    @if(isset($data->listing_id))
                                    <p style="font-family: Arial; box-sizing: border-box; color: #000; line-height: 1.5em; margin-top: 0; text-align: left;">
                                        <table width="100%" cellpadding="0" cellspacing="0" style="font-size:16px;">
                                            <tr>
                                                <td width="110px">Listing ID:</td>
                                                <td>{{$data->listing_id}}</td>
                                            </tr>
                                        </table>
                                    </p>
                                    @elseif(isset($data->building_id))
                                    <p style="font-family: Arial; box-sizing: border-box; color: #000; line-height: 1.5em; margin-top: 0; text-align: left;">
                                        <table width="100%" cellpadding="0" cellspacing="0" style="font-size:16px;">
                                            <tr>
                                                <td width="110px">Building ID:</td>
                                                <td>{{$data->building_id}}</td>
                                            </tr>
                                        </table>
                                    </p>
                                    @endif
                                    @if(isset($data->listing_address))
                                    <p style="font-family: Arial; box-sizing: border-box; color: #000; line-height: 1.5em; margin-top: 0; text-align: left;">
                                        <table width="100%" cellpadding="0" cellspacing="0" style="font-size:16px;">
                                            <tr>
                                                <td width="110px" valign="top">Listing:</td>
                                                <td><a href="{{$domain}}{{$data->listing_url}}">{{$data->listing_address}}</a></td>
                                            </tr>
                                        </table>
                                    </p>
                                    @elseif(isset($data->building_address))
                                    <p style="font-family: Arial; box-sizing: border-box; color: #000; line-height: 1.5em; margin-top: 0; text-align: left;">
                                        <table width="100%" cellpadding="0" cellspacing="0" style="font-size:16px;">
                                            <tr>
                                                <td width="110px" valign="top">Building:</td>
                                                <td><a href="{{$domain}}{{$data->building_url}}">{{$data->building_address}}</a></td>
                                            </tr>
                                        </table>
                                    </p>
                                    @endif
                                    @if(isset($data->period))
                                    <p style="font-family: Arial; box-sizing: border-box; color: #000; line-height: 1.5em; margin-top: 0; text-align: left;">
                                        <table width="100%" cellpadding="0" cellspacing="0" style="font-size:16px;">
                                            <tr>
                                                <td width="110px">Period:</td>
                                                <td>
                                                    @if($data->period == '1w')
                                                        1 Week
                                                    @elseif($data->period == '2w')
                                                        2 Weeks
                                                    @elseif($data->period == '4w')
                                                        4 Weeks
                                                    @elseif($data->period == '6w')
                                                        6 Weeks
                                                    @elseif($data->period == '1m')
                                                        1 Month
                                                    @elseif($data->period == '2m')
                                                        2 Months
                                                    @elseif($data->period == '3m')
                                                        3 Months
                                                    @elseif($data->period == '6m')
                                                        6 Months
                                                    @elseif($data->period == '1y')
                                                        1 Year
                                                    @elseif($data->period == '2y')
                                                        2 Years
                                                    @elseif($data->period == '3y')
                                                        3 Years
                                                    @endif
                                                </td>
                                            </tr>
                                        </table>                                    
                                    </p>
                                    @endif
                                    @if(isset($data->renew) && $data->renew == 1)
                                    <p style="font-family: Arial; box-sizing: border-box; color: #000; line-height: 1.5em; margin-top: 0; text-align: left;">
                                        <table width="100%" cellpadding="0" cellspacing="0" style="font-size:16px;">
                                            <tr>
                                                <td width="110px">Auto Renew:</td>
                                                <td>Yes</td>
                                            </tr>
                                        </table>
                                    </p>
                                    @endif
                                    @if(isset($data->amount))
                                    <p style="font-family: Arial; box-sizing: border-box; color: #000; line-height: 1.5em; margin-top: 0; text-align: left;">
                                    <table width="100%" cellpadding="0" cellspacing="0" style="font-size:16px;">
                                            <tr>
                                                <td width="110px">Charged:</td>
                                                <td><b>${{$data->amount}}</b></td>
                                            </tr>
                                        </table>
                                    </p>
                                    @endif
                                    @if(isset($data->ends_at))
                                    <p style="font-family: Arial; box-sizing: border-box; color: #000; line-height: 1.5em; margin-top: 0; text-align: left;">
                                    <table width="100%" cellpadding="0" cellspacing="0" style="font-size:16px;">
                                            <tr>
                                                <td width="110px">End Date:</td>
                                                <td>{{Carbon\Carbon::parse($data->ends_at)->format('Y-m-d')}}</td>
                                            </tr>
                                        </table>
                                    </p>
                                    @endif
                                </td>
                            </tr>

                        </table>
                    </td>
                </tr>

                <tr>
                    <td style="padding-top:30px;"><br>
                        <table class="footer" align="center" width="100%" cellpadding="0" cellspacing="0" style="background-color: whitesmoke">
                            <tr>
                                <td class="content-cell" align="center" style="padding: 1rem;font-size: 13px;font-family: Arial;">

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