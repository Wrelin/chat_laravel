@extends('layouts.app')

@section('content')
    @if(Auth::check())
        <private-chat-component :user="{{Auth::user()}}" :users="{{$users}}" :database-messages="{{$databaseMessages}}"></private-chat-component>
    @endif
@endsection