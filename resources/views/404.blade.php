
@extends('layouts.app2')

@section('header')
@endsection

@section('content')
<div class="mobile-menu" id="mobile-menu">
    @include('layouts.header2_1')        
</div>
<div class="wrapper" style="min-height:50vh">
    <div class="content" style="text-align:center;">
        @include('partial.header')  
        

<h1 style="padding-top:30px;">404 - Page not found</h1>


    </div>
</div>
@endsection

@section('footer')
	@include('layouts.footerMain2')
@endsection  