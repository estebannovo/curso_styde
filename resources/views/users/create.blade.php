@extends('layout')
@section('title', "Crear usuario")
@section('content')
    <h1>Crear usuario</h1>
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{url('usuarios')}}">
        {{csrf_field()}}

        <label for="name">Nombre: </label>
        <input type="text" name="name" placeholder="Type your full name" value="{{old('name')}}">
        @if($errors->has('name'))
        <span>{{ $errors->first('name') }}</span>
        @endif
        <br>
        <label for="email">Email: </label>
        <input type="email" name="email" placeholder="Type your mail here"  value="{{old('email')}}">
        @if($errors->has('email'))
            <span>{{ $errors->first('email') }}</span>
        @endif
        <br>
        <label for="password">Password: </label>
        <input type="password" name="password" placeholder="Mayor than 6 characters">
        <br>
        <button type="submit">Crear usuario</button>
    </form>

    <p>
        <a href="{{route('users.index')}}" class="">Regresar al listado de usuarios</a>
    </p>
@endsection

