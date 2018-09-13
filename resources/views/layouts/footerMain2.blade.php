<div class="footer">
    <footer class="main-footer main-footer-bg" >
        <div class="container">            
        @php if ($_SERVER['REQUEST_URI'] != '/') { @endphp
            {{--
            <div style="width:100%; text-align:center;margin-bottom:35px;">                
                <a href="#">
                    <i class="fa fa-facebook-official" aria-hidden="true"></i>
                </a>
                &nbsp;&nbsp;&nbsp;
                <a href="#">
                    <i class="fa fa-twitter" aria-hidden="true"></i>
                </a>
                &nbsp;&nbsp;&nbsp;
                <a href="#">
                    <i class="fa fa-instagram" aria-hidden="true"></i>
                </a>                
            </div>
            --}}
            @php } @endphp
            {{--<div class="logo-wr">
                <!-- !!! warning-->
                <!-- if main page -->
                <span>
                    <img src="/images/logo_only.png" alt=""> Square60
                </span>
                <!-- else -->
                <!-- <a href="#">
                    <img src="images/logo-footer.svg" alt="">
                </a> -->
            </div>--}}
            <nav class="footer-nav">
                <ul>
                    <li style="display:flex;">
                        {{--<div style="margin: 0 auto;display:flex;">
                            <img src="/images/logo_only.png" alt="" width="30px" height="31px" style="width:30px;height:31px;">
                            <div style="padding-top:3px;padding-left:10px;font-weight:600;">Square60.com</div>
                        </div>--}}
                        <img src="/images/logo2.png" alt="" width="120px" height="24px" style="width:120px;height:24px;margin:0 auto;">
                    </li>                    
                    <li>
                        <a href="/privacy">Privacy</a>
                    </li>
                    <li>
                        <a href="/terms">Terms & Conditions</a>
                    </li>
                    <li>
                        <a href="javascript:void(0);" onclick="if($('#contact_info').is(':visible')) $('#contact_info').hide(); else $('#contact_info').show();">Contact Us</a>
                        <div id="contact_info" style="display:none;padding-top:10px;">Email: <a href="mailto:info@square60.com" style="color:#4577b9;">info@square60.com</a></div>
                    </li>
                </ul>
                <ul style="margin-top:20px;">
                    {{--<li>
                        <a href="https://www.google.com/maps/d/edit?amp%3Busp=sharing&mid=15EY08Kd-1Ml045u8PJWcrC8rp9oee5pO&ll=40.70479599991072%2C-73.97769799999998&z=10" target="_blank">Google Map All Neighborhood</a>
                    </li>--}}                 
                    <li>
                        <a href="/agentslist">List of All Agents</a>
                    </li>   
                    <li>
                        <div style="width:270px;"></div>
                    </li> 
                    <li>
                        <a href="#"></a>
                    </li>                  
                </ul>
            </nav>
            <p>
            @if(isset($seo) && isset($seo['desc']) && $seo['desc'])
            <h1 style="font-size:13px;color:#707070;font-weight:normal;">{{$seo['desc']}}</h1>
            @elseif(in_array(Route::current()->getName(), array('main', 'salessearch', 'rentalssearch')))
            <h1 style="font-size:13px;color:#707070;font-weight:normal;">Square60 is an authentic real estate search engine for single family, multi-family houses, apartments, co-op, condo sales and rentals in all five boroughs of New York City: Manhattan, Brooklyn, Queens, Bronx and Staten Island, with the most updated prices, open houses and information.</h1>
            @endif
            </p>
            <p>Please note: Square60 is not responsible for the content of third party advertisements displayed from time to time
                on Square60's website. Such advertisements remain the sole responsibility of the advertiser and Square60 does
                not endorse, nor can it comment on, any products which may appear.</p>
            <p>&#169; 2017 Square60, all rights reserved. Part of the Square60 group of websites <a href="/terms" style="color:inherit;text-decoration:underline;">Terms and Conditions</a> | <a href="/privacy" style="color:inherit;text-decoration:underline;">Privacy Policy</a>                
            </p>
        </div>
    </footer>
</div>