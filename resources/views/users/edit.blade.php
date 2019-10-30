@extends('layout')
@section('title', "Crear usuario")
@section('content')
    <h1>Editar usuario</h1>
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <form method="POST" action="{{url("usuarios/{$user->id}")}}">
        {{method_field('PUT')}}
        {{csrf_field()}}
        <label for="name">Nombre: </label>
        <input type="text" name="name" dusk="name" placeholder="Type your full name" value="{{old('name', $user->name)}}">
        <br>
        <label for="email">Email: </label>
        <input type="email" name="email" dusk="email" placeholder="Type your mail here"  value="{{old('email', $user->email)}}">
        <br>
        <label for="password">Password: </label>
        <input type="password" name="password" placeholder="Mayor than 6 characters" autocomplete="off">
        <br>
        <button type="submit" dusk="update">Actualizar usuario</button>
    </form>
    <p>
        <a href="{{route('users.index')}}" class="">Regresar al listado de usuarios</a>
    </p>
@endsection
