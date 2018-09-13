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

    <div>
        <div>
            @include('partial.header')  
            <section>

                <div class="main-content columns is-mobile is-centered name_label_content" style="margin-bottom:100px;overflow:visible;">
                    <div class="content">                       
                            
                    <div id="page1" class="p_listings2" style="text-align:left;">
                        @if (session('status'))
                            <div class="alert alert-success" style="text-align:left;margin-bottom:25px;">
                                {!! session('status') !!}
                            </div>
                        @endif

                        <h4><b>Owners' Mailing List</b></h4>

                        <div>We have the most updated list of owners' names and addresses data from all condo buildings in NYC that have more than 20 units.</div>

                        @if (!Auth::user()->premium && (!isset($_COOKIE['free_mail']) || $_COOKIE['free_mail'] < env('FREE_MAIL')))
                        <div style="margin-top:20px;">You can download the first 200 records for free.</div>
                        @endif
                        
                        <h4 class="block" style="margin-top:40px;"><b>Select Building/Property</b></h4>

                        <form action="{{route('generateList')}}" method="post" id="generateForm" name="generateForm" enctype="multipart/form-data">
                                {{ csrf_field() }}
                        <div id="inputBlock" class="block">
                            @for($i=1; $i<=20; $i++)
                            <div class="builds columns" style="@if($i > 1) display:none; @endif">
                                <div class="column" style="padding-left:0px;">
                                    <label class="label" for="">{{$i}}. Street: </label>
                                    <select id="address-{{$i}}" class="input address" name="build[{{$i}}][address]" style="width:230px;max-width:230px;" autocomplete=off>
                                        <option></option>
                                        @foreach($buildings as $building)
                                            <option value="{{$building->bbl}}">{{$building->address}}</option>
                                        @endforeach
                                    </select>
                                </div>                                
                                <div class="column" style="padding-left:0px;">
                                    <label class="label" for="">City: </label>
                                    <input placeholder="" type="text" class="city input" value=""  name="build[{{$i}}][city]" style="max-width:230px;" />
                                </div>
                                <div class="column" style="padding-left:0px;">
                                    <label class="label" for="">State: </label>
                                    <input placeholder="" type="text" class="state input" value=""  name="build[{{$i}}][state]" style="max-width:230px;" />
                                </div>
                                <div class="column" style="padding-left:0px;">
                                    <label class="label" for="">Zip: </label>
                                    <input iplaceholder="" type="text" class="zip input" value=""  name="build[{{$i}}][zip]" style="max-width:230px;" />
                                </div>
                                <input type="hidden" name="build[{{$i}}][bbl]" value="" />
                                <input type="hidden" name="build[{{$i}}][units]" value="" />
                             </div>
                            @endfor

                            <a id="more" class="button is-primary mainbgc" onclick="addMore()">Add More</a><br><br>
                            <a id="generate" class="button is-primary mainbgc" onclick="submitForm();">Generate CSV File</a><br><Br>
                            <span style="font-size:13px;">*Please open the CSV (Comma Delimited) file with Excel</span>
                        </div>
                        </form>
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
    var buildings = {!! $buildings !!};

    @for($i=1; $i<=20; $i++)
    var address = $('#address-{{$i}}');

    address.select2({
        selectOnBlur: true,
    });

    address.on('change', function(e) {
        var bbl = $(this).val(); 

        for (var key in buildings) {
            if (buildings.hasOwnProperty(key)) {
                if(buildings[key]['bbl'] == bbl){ 
                    $('input[name="build[{{$i}}][city]"]').val(buildings[key]['city']);
                    $('input[name="build[{{$i}}][state]"]').val(buildings[key]['state']);
                    $('input[name="build[{{$i}}][zip]"]').val(buildings[key]['zipcode']);
                    $('input[name="build[{{$i}}][bbl]"]').val(buildings[key]['bbl']);
                    $('input[name="build[{{$i}}][units]"]').val(buildings[key]['units']);
                    break;
                }                
            }
        }
    });
    @endfor

    submitForm = function() {
        document.generateForm.submit();
    }

    addMore = function() {
        var max = true;
        $('.builds').each(function(ind){
            if($(this).is(":hidden")) {
                $(this).show();
                max = false;
                return false;
            }
        });

        if (max)
            swal('Maximum 20 buildings each time.');
    }
</script>
@endsection