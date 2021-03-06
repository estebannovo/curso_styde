@extends('layout')
@section('title', "Crear usuario")
@section('content')
    @card
        @slot('header', 'Editar usuario')

        @include('shared._errors')
        <form method="POST" action="{{url("usuarios/{$user->id}")}}">
            {{method_field('PUT')}}
            @render('UserFields', compact('user'))

            <div class="form-group mt-4">
                <button class="btn btn-primary" type="submit" dusk="update">Actualizar usuario</button>
                <a href="{{route('users.index')}}" class="btn btn-link">Regresar al listado de usuarios</a>
            </div>
        </form>
        <p>
            <a href="{{route('users.index')}}" class="">Regresar al listado de usuarios</a>
        </p>
    @endcard

@endsection
