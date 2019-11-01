@extends('layout')

@section('title', "Crear usuario")

@section('content')
    @card
        @slot('header', 'Crear usuario')

        @include('shared._errors')

        <form method="POST" action="{{url('usuarios')}}">
            @render('UserFields', ['user', $user])

            <div class="form-group mt-4">
                <button class="btn btn-primary" type="submit">Crear usuario</button>
                <a href="{{route('users.index')}}" class="btn btn-link">Regresar al listado de usuarios</a>
            </div>

        </form>
    @endcard

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
