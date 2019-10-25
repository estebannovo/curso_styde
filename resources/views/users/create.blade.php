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
                    <label for="other_profession">Other profession: </label>
                    <input type="text" class="form-control" name="other_profession" id="other_profession" placeholder="Complete here if your profession is not on the list" value="{{old('other_profession')}}">
                    @if($errors->has('other_profession'))
                        <div class="invalid-feedback">
                            {{ $errors->first('other_profession') }}
                        </div>
                    @endif
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

                <h5 >Abilities</h5>

                @foreach($skills as $skill)
                <div class="form-check form-check-inline">
                    <input name="skills[{{$skill->id}}]"
                           class="form-check-input"
                           type="checkbox"
                           id="skill_{{$skill->id}}"
                           value="{{$skill->id}}"
                           {{old("skills.{$skill->id}")? 'checked': ''}}
                    >
                    <label class="form-check-label" for="skill_{{$skill->id}}">{{$skill->name}}</label>
                </div>
                @endforeach

                <div class="form-group mt-4">
                    <button class="btn btn-primary" type="submit">Crear usuario</button>
                    <a href="{{route('users.index')}}" class="btn btn-link">Regresar al listado de usuarios</a>
                </div>

            </form>
        </div>
    </div>

@endsection

@section('jquery')
    @parent
    console.log('Template: create.blade.php');
    $('#profession_id').on('change', function() {
        if( this.value != "" ){
            $('#other_profession').parent().hide();
        }else{
            $('#other_profession').parent().show();
        }
    });
@endsection
