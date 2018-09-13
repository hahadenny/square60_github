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
                    <h4>Total: <b>{{$usersList->total()}}</b></h4>
                    @if($usersList->total() != 0)
                    <table>
                        <tr>
                            <th class="has-text-centered">
                                User ID
                            </th>
                            <th class="has-text-centered">
                                Name
                            </th>
                            <th class="has-text-centered">
                                Email
                            </th>
                            <th class="has-text-centered">
                                Phone
                            </th>
                            <th class="has-text-centered">

                            </th>
                            <th class="has-text-centered">

                            </th>
                        </tr>
                        @foreach($usersList as $item)
                        <tr>
                            <td class="has-text-centered">
                                {{$item->id}}
                            </td>
                            <td class="has-text-centered">
                                {{$item->name}}
                            </td>
                            <td class="has-text-centered">
                                {{$item->email}}
                            </td>
                            <td class="has-text-centered">
                                {{$item->phone}}
                            </td>

                            <td class="has-text-centered">
                                <form method="POST" action="{{ route('editUser') }}" enctype="multipart/form-data">
                                    {{ csrf_field() }}
                                    <input type="submit" class="button is-info" name="submit" value="edit">
                                    <input type="hidden" name="id" value="{{$item->id}}">
                                </form>
                            </td>
                            <td class="has-text-centered">
                                <form method="POST" action="{{ route('deleteUser') }}" enctype="multipart/form-data">
                                    {{ csrf_field() }}
                                    <input type="submit" class="button is-danger" name="submit" onclick="return confirm('Are you sure you want to delete this item?');" value="delete">
                                    <input type="hidden" name="id" value="{{$item->id}}">
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </table>
                    {{$usersList->links()}}
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