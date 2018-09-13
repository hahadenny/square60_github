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
                    <td class="header" align="center" style="background-color: #fff; padding: 1rem">
                        <a href="{{$domain}}">
                            <img src="{{$domain}}images/logo2.png" alt="Square60" width="150">
                        </a>
                    </td>
                </tr>

                <!-- Email Body -->
                <tr>
                    <td class="body" width="100%" cellpadding="0" cellspacing="0">
                        <table class="inner-body" align="center" width="100%" cellpadding="0" cellspacing="0" style="max-width:570px;">
                            <!-- Body content -->
                            <tr>
                                <td class="content-cell">
                                    <h2 align="center" style="font-size:16px">You received a message from:</h2><br>
                                    @if(isset($data['advertLink']))
                                    <p style="font-family: Arial; box-sizing: border-box; color: #000; font-size: 16px; line-height: 1.5em; margin-top: 0; text-align: left;">Listing: <a href="{{$data['advertLink']}}">{{$data['advertLink']}}</a></p>
                                    @endif
                                    @if(isset($data['name']) && $data['name'])
                                    <p style="font-family: Arial; box-sizing: border-box; color: #000; font-size: 16px; line-height: 1.5em; margin-top: 0; text-align: left;">Name: {{$data['name']}}</p>
                                    @endif
                                    <p style="font-family: Arial; box-sizing: border-box; color: #000; font-size: 16px; line-height: 1.5em; margin-top: 0; text-align: left;">Email: {{$data['useremail']}}</p>
                                    @if(isset($data['phone']))
                                    <p style="font-family: Arial; box-sizing: border-box; color: #000; font-size: 16px; line-height: 1.5em; margin-top: 0; text-align: left;">Phone: {{$data['phone']}}</p>
                                    @endif
                                    <p style="font-family: Arial; box-sizing: border-box; color: #000; font-size: 16px; line-height: 1.5em; margin-top: 0; text-align: left;">Message: {{$data['message']}}</p>
                                </td>
                            </tr>

                        </table>
                    </td>
                </tr>

                @if(!isset($agent_user_id))
                @elseif($agent_user_id == 0)
                <tr>
                    <td class="body" width="100%" cellpadding="0" cellspacing="0">
                        <table class="inner-body" align="center" width="100%" cellpadding="0" cellspacing="0" style="max-width:570px;">
                            <tr>
                                <td><br><br>
                                    <div style="margin-bottom:10px;font-size:13px;">We're forwarding you clients who are interested in your listings. For more details, please contact us via info@square60.com.</div>
                                    <div style="margin-bottom:10px;font-size:13px;">If you do not wish to recieve these emails, you can <a href="{{$domain}}unsubscribe/?s={{urlencode($encrypted)}}">unsubscribe from this email</a>.</div>
                                </td>
                            </tr>   
                        </table>
                    </td>
                </tr>
                @else
                <tr>
                    <td class="body" width="100%" cellpadding="0" cellspacing="0">
                        <table class="inner-body" align="center" width="100%" cellpadding="0" cellspacing="0" style="max-width:570px;">
                            <tr>
                                <td><br><br>
                                    <div style="margin-bottom:10px;font-size:13px;">To unsubscribe: please <a href="{{$domain}}login">login</a> and go to Settings. Select "No" to stop recieving interested clients emails.</div>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                @endif

                <tr>
                    <td><br>
                        <table class="footer" align="center" width="100%" cellpadding="0" cellspacing="0" style="background-color: whitesmoke">           
                            <tr>
                                <td class="content-cell" align="center" valign="middle" style="padding: 1rem;font-size: 13px;font-family: Arial;">
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