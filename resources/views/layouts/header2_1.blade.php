<ul>
    @if (Auth::guest())
    <li>
        <a href="{{ route('login') }}">Sign In</a>
    </li>
    <li>
        <a href="{{ route('register') }}">Sign Up</a>
    </li>
    <li>
        <a href="#" class="showModal modal-button">Submit Listings</a>
    </li>
    <li style="display:none;">
        <a href="{{ route('advert') }}">Advertisements</a>
    </li>
    @else
        @if(Auth::user()->isAgent())
        <div class="navbar-item has-dropdown is-hoverable extra_owner">

            @if (Auth::user()->premium)
                @if(Auth::user()->isAgentVerified() == -1)
                <a href="javascript:void(0)" onclick="swal('Sorry, your agent account is not approved.');" class="navbar-link owner-link">
                    Owners' Mailing List 
                </a>
                @elseif(Auth::user()->isAgentVerified())
                <a href="{{route('ownerMail')}}" class="navbar-link owner-link">
                    Owners' Mailing List 
                </a>
                @else
                <a href="javascript:void(0)" onclick="swal('You agent account is not approved yet. Please give us 24 hours to verify your data.');" class="navbar-link owner-link">
                    Owners' Mailing List 
                </a>
                @endif
            @else 
            <a href="{{route('ownerMail')}}" class="navbar-link owner-link">
                Owners' Mailing List 
            </a>
            @endif
        </div>

        <div class="navbar-item has-dropdown is-hoverable">

            <a class="navbar-link">
                @if(Auth::user()->premium==3)
                <img  src="/images/diamond.png" style="vertical-align:middle;width:30px;margin-right:7px;" /> 
                @elseif(Auth::user()->premium==2)
                <img  src="/images/gold.png" style="vertical-align:bottom;width:30px;margin-right:6px;" /> 
                @elseif(Auth::user()->premium==1)
                <img  src="/images/silver.png" style="vertical-align:middle;width:30px;margin-right:6px;" /> 
                @endif
                Agent Account
            </a>

            <div class="navbar-dropdown">
                <a href="{{ route('profile') }}" class="navbar-item modal-button">
                    My Profile
                </a>
                <a href="{{ route('home') }}" class="navbar-item modal-button">
                    Settings
                </a>
                <hr class="navbar-divider">
                <a href="{{ route('logout') }}" class="navbar-item modal-button">
                    Logout
                </a>
            </div>
        </div>
        
        <div class="navbar-item has-dropdown is-hoverable">
            <a class="navbar-link">
                Marketing
            </a>

            <div class="navbar-dropdown">
                <a href="{{ route('upgrade') }}" class="navbar-item modal-button">
                    Upgrade Your Membership
                </a>
                <a href="{{ route('listing') }}" class="navbar-item modal-button">
                    Feature Listings
                </a>
                <a href="{{ route('listing') }}" class="navbar-item modal-button">
                    Premium Listings
                </a>
                @if(Auth::user()->isAgentVerified() == -1)
                <a href="javascript:void(0)" onclick="swal('Sorry, your agent account is not approved.');" class="navbar-item modal-button">
                    Your Name Featured on Building 
                </a>
                @elseif (Auth::user()->isAgentVerified())
                <a href="{{route('nameLabelBilling')}}" class="navbar-item modal-button">
                    Your Name Featured on Building
                </a>
                @else
                <a href="javascript:void(0)" onclick="swal('You agent account is not approved yet. Please give us 24 hours to verify your data.');" class="navbar-item modal-button">
                    Your Name Featured on Building 
                </a>
                @endif
                @if (Auth::user()->premium)
                    @if(Auth::user()->isAgentVerified() == -1)
                    <a href="javascript:void(0)" onclick="swal('Sorry, your agent account is not approved.');" class="navbar-item modal-button">
                        Owners' Mailing List 
                    </a>
                    @elseif(Auth::user()->isAgentVerified())
                    <a href="{{route('ownerMail')}}" class="navbar-item modal-button">
                        Owners' Mailing List 
                    </a>
                    @else
                    <a href="javascript:void(0)" onclick="swal('You agent account is not approved yet. Please give us 24 hours to verify your data.');" class="navbar-item modal-button">
                        Owners' Mailing List 
                    </a>
                    @endif
                @else 
                <a href="{{route('ownerMail')}}" class="navbar-item modal-button">
                    Owners' Mailing List 
                </a>
                @endif
            </div>
        </div>

        <div class="navbar-item has-dropdown is-hoverable">
            <a class="navbar-link">
                Tools
            </a>

            <div class="navbar-dropdown">
                <a href="{{ route('listing') }}" class="navbar-item modal-button">
                    My Listings
                </a>                
                <a href="{{route('openHouse')}}" class="navbar-item modal-button">
                    Open Houses
                </a>
                @if(Auth::user()->isAgentVerified() == -1)
                <a href="javascript:void(0)" onclick="swal('Sorry, your agent account is not approved.');" class="navbar-item modal-button">
                    Your Name Featured on Building
                </a>
                @elseif (Auth::user()->isAgentVerified())
                <a href="{{route('nameLabelBilling')}}" class="navbar-item modal-button">
                    Your Name Featured on Building
                </a>
                @else
                <a href="javascript:void(0)" onclick="swal('You agent account is not approved yet. Please give us 24 hours to verify your data.');" class="navbar-item modal-button">
                    Your Name Featured on Building
                </a>
                @endif
                <a href="{{route('agentBilling')}}" class="navbar-item modal-button">
                    Payment Method
                </a>
                <a href="{{ route('billing') }}" class="navbar-item modal-button">
                    Billing History
                </a>
                <a href="{{route('searchListing')}}" class="navbar-item modal-button">
                    Saved Items
                </a>
                <a href="{{ route('listing') }}" class="navbar-item modal-button">
                    Submit Listings
                </a>
                @if (Auth::user()->premium)
                    @if(Auth::user()->isAgentVerified() == -1)
                    <a href="javascript:void(0)" onclick="swal('Sorry, your agent account is not approved.');" class="navbar-item modal-button">
                        Owners' Mailing List 
                    </a>
                    @elseif(Auth::user()->isAgentVerified())
                    <a href="{{route('ownerMail')}}" class="navbar-item modal-button">
                        Owners' Mailing List 
                    </a>
                    @else
                    <a href="javascript:void(0)" onclick="swal('You agent account is not approved yet. Please give us 24 hours to verify your data.');" class="navbar-item modal-button">
                        Owners' Mailing List 
                    </a>
                    @endif
                @else 
                <a href="{{route('ownerMail')}}" class="navbar-item modal-button">
                    Owners' Mailing List 
                </a>
                @endif
            </div>
        </div>
        @elseif(Auth::user()->isOwner())
                <div class="navbar-item has-dropdown is-hoverable">
                    <a class="navbar-link">
                        @if(Auth::user()->premium==3)
                        <img  src="/images/diamond.png" style="vertical-align:middle;width:30px;margin-right:7px;" /> 
                        @elseif(Auth::user()->premium==2)
                        <img  src="/images/gold.png" style="vertical-align:bottom;width:30px;margin-right:6px;" /> 
                        @elseif(Auth::user()->premium==1)
                        <img  src="/images/silver.png" style="vertical-align:middle;width:30px;margin-right:6px;" /> 
                        @endif
                        Owner Account
                    </a>

                    <div class="navbar-dropdown">
                        <a href="{{ route('profileOwner') }}" class="navbar-item modal-button">
                            My Profile
                        </a>
                        <a href="{{ route('home') }}" class="navbar-item modal-button">
                            Settings
                        </a>
                        <hr class="navbar-divider">
                        <a href="{{ route('logout') }}" class="navbar-item modal-button">
                            Logout
                        </a>
                    </div>
                </div>
                <div class="navbar-item has-dropdown is-hoverable">
                    <a class="navbar-link">
                        Tool
                    </a>

                    <div class="navbar-dropdown">
                        <a href="{{ route('listing') }}" class="navbar-item modal-button">
                            My Listings
                        </a>
                        <a href="{{ route('upgrade') }}" class="navbar-item modal-button">
                            Upgrade Your Membership
                        </a>
                        <a href="{{ route('listing') }}" class="navbar-item modal-button">
                            Feature Listings
                        </a>
                        <a href="{{ route('listing') }}" class="navbar-item modal-button">
                            Premium Listings
                        </a>
                        <a href="{{route('agentBilling')}}" class="navbar-item modal-button">
                            Payment Method
                        </a>
                        <a href="{{ route('billing') }}" class="navbar-item modal-button">
                            Billing History
                        </a>
                        <a href="{{route('openHouse')}}" class="navbar-item modal-button">
                            Open Houses
                        </a>
                        <a href="{{route('searchListing')}}" class="navbar-item modal-button">
                            Saved Items
                        </a>
                        <a href="{{ route('listing') }}" class="navbar-item modal-button">
                            Submit Listings
                        </a>
                    </div>
                </div>
        @elseif(Auth::user()->isMan())
                <div class="navbar-item has-dropdown is-hoverable">
                    <a class="navbar-link">
                        @if(Auth::user()->premium==3)
                        <img  src="/images/diamond.png" style="vertical-align:middle;width:30px;margin-right:7px;" /> 
                        @elseif(Auth::user()->premium==2)
                        <img  src="/images/gold.png" style="vertical-align:bottom;width:30px;margin-right:6px;" /> 
                        @elseif(Auth::user()->premium==1)
                        <img  src="/images/silver.png" style="vertical-align:middle;width:30px;margin-right:6px;" /> 
                        @endif
                        Management Account
                    </a>

                    <div class="navbar-dropdown">
                        <a href="{{ route('profileMan') }}" class="navbar-item modal-button">
                            My Profile
                        </a>
                        <a href="{{ route('home') }}" class="navbar-item modal-button">
                            Settings
                        </a>
                        <hr class="navbar-divider">
                        <a href="{{ route('logout') }}" class="navbar-item modal-button">
                            Logout
                        </a>
                    </div>
                </div>
                <div class="navbar-item has-dropdown is-hoverable">
                    <a class="navbar-link">
                        Tools
                    </a>

                    <div class="navbar-dropdown">
                        <a href="{{ route('listing') }}" class="navbar-item modal-button">
                            My Listings
                        </a>
                        <a href="{{ route('upgrade') }}" class="navbar-item modal-button">
                            Upgrade Your Membership
                        </a>
                        <a href="{{ route('listing') }}" class="navbar-item modal-button">
                            Feature Listings
                        </a>
                        <a href="{{ route('listing') }}" class="navbar-item modal-button">
                            Premium Listings
                        </a>
                        <a href="{{route('agentBilling')}}" class="navbar-item modal-button">
                            Payment Method
                        </a>
                        <a href="{{ route('billing') }}" class="navbar-item modal-button">
                            Billing History
                        </a>
                        <a href="{{route('openHouse')}}" class="navbar-item modal-button">
                            Open Houses
                        </a>
                        <a href="{{route('searchListing')}}" class="navbar-item modal-button">
                            Saved Items
                        </a>
                        <a href="{{ route('listing') }}" class="navbar-item modal-button">
                            Submit Listings
                        </a>
                    </div>
                </div>
        @elseif(Auth::user()->isUser())
            <div class="navbar-item has-dropdown is-hoverable">
                <a class="navbar-link">
                    User Account
                </a>
                <div class="navbar-dropdown">
                    <a href="{{ route('home') }}" class="navbar-item modal-button">
                        Settings
                    </a>
                    <hr class="navbar-divider">
                    <a href="{{ route('logout') }}" class="navbar-item modal-button">
                        Logout
                    </a>
                </div>
            </div>
            <div class="navbar-item has-dropdown is-hoverable">
                <a class="navbar-link">
                    Tools
                </a>

                <div class="navbar-dropdown">
                    <a href="{{route('searchListing')}}" class="navbar-item modal-button">
                        Saved Items
                    </a>
                    <a id="showModalSubmit" class="navbar-item modal-button" style="cursor:pointer;" onclick="$('#modalSubmit').addClass('is-active');">
                        Submit Listings
                    </a>
                </div>
            </div>
        @endif
        @if (Auth::user()->isAdmin())
            <div class="navbar-item has-dropdown is-hoverable">
                <a class="navbar-link">
                    Admin Tools
                </a>

                <div class="navbar-dropdown">
                    <a href="{{ route('buildings') }}" class="navbar-item modal-button">
                        Buildings
                    </a>
                    <a href="{{ route('addBuilding') }}" class="navbar-item modal-button">
                        New Building
                    </a>
                    <a href="{{ route('allSell') }}" class="navbar-item modal-button">
                        Sale Listing
                    </a>
                    <a href="{{ route('allRental') }}" class="navbar-item modal-button">
                        Rental Listing
                    </a>        
                    <a href="{{ route('agentsList') }}" class="navbar-item modal-button">
                        Agents List
                    </a>            
                    <a href="{{ route('userList') }}" class="navbar-item modal-button">
                        Users List
                    </a>        
                    <a href="{{ route('articlesList') }}" class="navbar-item modal-button">
                        Article List
                    </a>            
                    <a href="{{ route('newArticle') }}" class="navbar-item modal-button">
                        New Article
                    </a>                    
                    <a href="{{ route('logout') }}" class="navbar-item modal-button">
                        Logout
                    </a>
                </div>
            </div>            
        @endif
@endif
</ul>