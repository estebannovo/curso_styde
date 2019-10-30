@extends('layout')
@section('title', "Usuario: {$user->id}")
@section('content')
    <h1>Usuario #{{$user->id}}</h1>
    <p>Nombre del usuario: {{$user->name}}</p>
    <p dusk="email">Correo electrónico: {{$user->email}}</p>
    
    <p>
        {{--<a href="{{url()->previous()}}" class="">Regresar</a>--}}
        {{--<a href="{{action('UserController@index')}}" class="">Regresar al listado de usuarios</a>--}}
        {{--<a href="{{url('/usuarios')}}" class="">Regresar al listado de usuarios</a>--}}
        <a href="{{route('users.index')}}" class="">Regresar al listado de usuarios</a>
    </p>
@endsection

