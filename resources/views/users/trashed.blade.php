@extends('layout')
@section('title', 'Listado de usuarios' )
@section('content')
    <div class="d-flex justify-content-between align-items-end mb-3">
        <h1 class="pb-1">{{$title}}</h1>
    </div>

    @if ($users->isNotEmpty())
    <table class="table" style="background-color: beige">
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
                    @csrf
                    @method('DELETE')
                    <a class="btn btn-link" title="Undo delete" href="{{route('user.restore',$user->id)}}"><span class="oi oi-action-undo"></span></a>
                    <button type="submit" class="btn btn-link" dusk="delete-{{$user->id}}"><span class="oi oi-circle-x"></span></button>
                </form>
            </td>
        </tr>
        @endforeach
        </tbody>
    </table>
    @else
        <p>No hay usuarios eliminados</p>
    @endif
@endsection
@section('sidebar')
@endsection