@extends('layout')
@section('title', 'Listado de usuarios' )
@section('content')
    <div class="d-flex justify-content-between align-items-end mb-3">
        <h1 class="pb-1">{{$title}}</h1>
        <p>
            <a href="{{route('users.create')}}" class="btn btn-primary">Nuevo usuario</a>
        </p>

    </div>


    @if ($users->isNotEmpty())
    <table class="table">
        <thead class="thead-dark">
        <tr>
            <th scope="col">#</th>
            <th scope="col">Name</th>
            <th scope="col">Email</th>
            <th scope="col">Actions</th>
        </tr>
        </thead>
        <tbody>
        @foreach($users as $user)
        <tr>
            <th scope="row">{{$user->id}}</th>
            <td>{{$user->name}}</td>
            <td>{{$user->email}}</td>
            <td>

                <form action="{{route('users.destroy',$user)}}" method="POST">
                    {{csrf_field()}}
                    {{method_field('DELETE')}}
                    <a class="btn btn-link" href="{{route('users.show',['user'=>$user->id])}}"><span class="oi oi-eye"></span></a>
                    <a class="btn btn-link" href="{{route('users.edit',$user)}}"><span class="oi oi-pencil"></span></a>
                    <button type="submit" class="btn btn-link" dusk="delete-{{$user->id}}"><span class="oi oi-trash"></span></button>
                </form>
            </td>
        </tr>
        @endforeach
        </tbody>
    </table>
    @else
        <p>No hay usuarios registrados</p>
    @endif
@endsection

@section('sidebar')
    @parent
    <h2>Barra lateral personalizada</h2>
@endsection