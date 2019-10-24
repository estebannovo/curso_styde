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

                <div class="form-group">
                    <label for="bio">Your Biography: </label>
                    <textarea name="bio" class="form-control" id="bio">{{old('bio')}}</textarea>
                    @if($errors->has('bio'))
                        <div class="invalid-feedback">
                            {{ $errors->first('bio') }}
                        </div>
                    @endif
                </div>

                <div class="form-group">
                    <label for="profession_id">Profession: </label>
                    <select name="profession_id" id="profession_id" class="form-control">
                        <option value="">Select one profession</option>
                        @foreach($professions as $profession)
                            <option value="{{$profession->id}}" {{old('profession_id') == $profession->id ? ' selected': ''}}>
                                {{$profession->title}}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="twitter">Twitter account: </label>
                    <input type="text" class="form-control" name="twitter" placeholder="Type your twitter account" value="{{old('twitter')}}">
                    @if($errors->has('twitter'))
                        <div class="invalid-feedback">
                            {{ $errors->first('twitter') }}
                        </div>
                    @endif
                </div>

                <button class="btn btn-primary" type="submit">Crear usuario</button>
                <a href="{{route('users.index')}}" class="btn btn-link">Regresar al listado de usuarios</a>
            </form>
        </div>
    </div>

@endsection

