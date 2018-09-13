
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
        
        
    </div>
</div>
@endsection

@section('footer')
	@include('layouts.footerMain2')
@endsection  