@extends('layout')
@section('title', "Crear usuario")
@section('content')
    <h1>Editar usuario</h1>
    @include('shared._errors')
    <form method="POST" action="{{url("usuarios/{$user->id}")}}">
        {{method_field('PUT')}}

        @include('users._fields')

        <div class="form-group mt-4">
            <button class="btn btn-primary" type="submit" dusk="update">Actualizar usuario</button>
            <a href="{{route('users.index')}}" class="btn btn-link">Regresar al listado de usuarios</a>
        </div>
    </form>
    <p>
        <a href="{{route('users.index')}}" class="">Regresar al listado de usuarios</a>
    </p>
@endsection
