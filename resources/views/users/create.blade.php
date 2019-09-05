@extends('layout')
@section('title', "Crear usuario")
@section('content')
    <div class="card">
        <h4 class="card-header">
            Crear usuario
        </h4>
        <div class="card-body">
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

                <div class="form-group">
                    <label for="name">Nombre: </label>
                    <input type="text" class="form-control" name="name" placeholder="Type your full name" value="{{old('name')}}">
                    @if($errors->has('name'))
                        <div class="invalid-feedback">
                            {{ $errors->first('name') }}
                        </div>
                    @endif
                </div>
                <div class="form-group">
                    <label for="email">Email: </label>
                    <input type="email" class="form-control" name="email" placeholder="Type your mail here"  value="{{old('email')}}">
                    @if($errors->has('email'))
                        <div class="invalid-feedback">
                            {{ $errors->first('email') }}
                        </div>
                    @endif
                </div>
                <div class="form-group">
                    <label for="password">Password: </label>
                    <input type="password" class="form-control" name="password" placeholder="Mayor than 6 characters">
                </div>
                <button class="btn btn-primary" type="submit">Crear usuario</button>
                <a href="{{route('users.index')}}" class="btn btn-link">Regresar al listado de usuarios</a>
            </form>
        </div>
    </div>

@endsection

