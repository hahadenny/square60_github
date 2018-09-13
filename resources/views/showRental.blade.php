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

<div id="app">
  <div>
    @include('partial.header')  
    <section>
        <div class="main-content columns is-mobile is-centered" style="margin-bottom:40px;">
            <div class="content">
                @if (session('status'))
                    <div class="alert alert-success">
                        {{ session('status') }}
                    </div>
                @endif
                <h4>Total: <b>{{$dataListing->total()}}</b></h4>
                @if($dataListing->total() != 0)
                <table>
                    <tr>
                        <th class="has-text-centered">
                            Listing ID
                        </th>
                        <th class="has-text-centered">
                            Status
                        </th>
                        <th class="has-text-centered">
                            Verify
                        </th>
                        <th class="has-text-centered">
                            Name
                        </th>
                        <th class="has-text-centered">
                            Address
                        </th>
                        <th class="has-text-centered">
                            Type
                        </th>
                        <th class="has-text-centered">

                        </th>
                        <th class="has-text-centered">

                        </th>
                        <th class="has-text-centered">

                        </th>
                    </tr>
                    @foreach($dataListing as $item)
                        <tr>
                            <td class="has-text-centered">
                                {{$item->id}}
                            </td>
                            <td class="has-text-centered {{$item->active == 1 ? "has-text-success" : "has-text-danger"}}">
                                {{$item->active == 1 ? "Active" : "Non-Active"}}
                            </td>
                            <td class="has-text-centered">
                                @if($item->is_verified == 1)
                                    Apporved
                                @elseif($item->is_verified == 0)
                                    Pending
                                @elseif($item->is_verified == -1)
                                    Declined
                                @endif
                            </td>
                            <td class="has-text-centered">
                                {{$item->name}}
                            </td>
                            <td class="has-text-centered">
                                {{$item->address}}
                            </td>
                            <td class="has-text-centered">
                                {{$item->unit_type}}
                            </td>

                            <td class="has-text-centered">
                                <form method="POST" action="{{ route('submitrental') }}" enctype="multipart/form-data">
                                    {{ csrf_field() }}
                                    <input type="submit" class="button {{$item->active == 1 ? "is-warning" : "is-success"}}" name="submit" value="{{$item->active == 1 ? "Active" : "Non-Active"}}">
                                    <input type="hidden" name="id" value="{{$item->id}}">
                                </form>
                            </td>
                            <td class="has-text-centered">
                                <form method="POST" action="{{ route('editrental') }}" enctype="multipart/form-data">
                                    {{ csrf_field() }}
                                    <input type="submit" class="button is-info" name="submit" value="edit">
                                    <input type="hidden" name="id" value="{{$item->id}}">
                                </form>
                            </td>
                            <td class="has-text-centered">
                                <form method="POST" action="{{ route('deleterental') }}" enctype="multipart/form-data">
                                    {{ csrf_field() }}
                                    <input type="submit" class="button is-danger" name="submit" onclick="return confirm('Are you sure you want to delete this item?');" value="delete">
                                    <input type="hidden" name="id" value="{{$item->id}}">
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </table>
                {{$dataListing->links()}}
                @endif
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