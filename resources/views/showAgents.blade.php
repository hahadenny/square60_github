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
                        <h4>Total: <b>{{$agentsList->total()}}</b></h4>
                        @if($agentsList->total() != 0)
                            <table>
                                <tr>
                                    <th class="has-text-centered">
                                        ID
                                    </th>
                                    <th class="has-text-centered">
                                        Agent ID
                                    </th>
                                    <th class="has-text-centered">
                                        User ID
                                    </th>
                                    <th class="has-text-centered">
                                        Verify
                                    </th>
                                    <th class="has-text-centered">
                                        Name
                                    </th>
                                    <th class="has-text-centered">
                                        Company
                                    </th>
                                    <th class="has-text-centered">
                                        Office phone
                                    </th>
                                    <th class="has-text-centered">

                                    </th>
                                    <th class="has-text-centered">

                                    </th>
                                </tr>
                                @foreach($agentsList as $item)
                                    <tr>
                                        <td class="has-text-centered">
                                            {{$item->id}}
                                        </td>
                                        <td class="has-text-centered">
                                            {{$item->external_id}}
                                        </td>
                                        <td class="has-text-centered">
                                            {{$item->user_id}}
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
                                        @if($item->first_name)
                                            {{$item->first_name}} {{$item->last_name}}
                                        @else
                                            {{$item->full_name}}
                                        @endif
                                        </td>
                                        <td class="has-text-centered">
                                            {{$item->company}}
                                        </td>
                                        <td class="has-text-centered">
                                            {{$item->office_phone}}
                                        </td>

                                        <td class="has-text-centered">
                                            <form method="POST" action="{{ route('editAgent') }}" enctype="multipart/form-data">
                                                {{ csrf_field() }}
                                                <input type="submit" class="button is-info" name="submit" value="edit">
                                                <input type="hidden" name="id" value="{{$item->id}}">
                                            </form>
                                        </td>
                                        <td class="has-text-centered">
                                            <form method="POST" action="{{ route('deleteAgent') }}" enctype="multipart/form-data">
                                                {{ csrf_field() }}
                                                <input type="submit" class="button is-danger" name="submit" onclick="return confirm('Are you sure you want to delete this item?');" value="delete">
                                                <input type="hidden" name="id" value="{{$item->id}}">
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </table>
                            {{$agentsList->links()}}
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