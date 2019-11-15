@extends('layout')
@section('title', 'Listado de usuarios' )
@section('content')
    <div class="d-flex justify-content-between align-items-end mb-3">
        <h1 class="pb-1">Deleted users</h1>
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
        <p>There aren't users in the bin</p>
    @endif

    <hr>

    <br>
    <div class="d-flex justify-content-between align-items-end mb-3">
        <h1 class="pb-1">Deleted professions</h1>
    </div>

    @if ($professions->isNotEmpty())
    <table class="table" style="background-color: beige">
        <thead class="thead-dark">
        <tr>
            <th scope="col">#</th>
            <th scope="col">Título</th>
            <th scope="col">Acciones</th>
        </tr>
        </thead>
        <tbody>
        @foreach($professions as $profession)
            <tr>
                <th scope="row">{{ $profession->id }}</th>
                <td>{{ $profession->title }}</td>
                <td>
                    <form action="{{ route('profession.destroy', $profession) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <a class="btn btn-link" title="Undo delete" href="{{route('profession.restore',$profession->id)}}"><span class="oi oi-action-undo"></span></a>
                        <button type="submit" class="btn btn-link" dusk="delete-{{$profession->id}}"><span class="oi oi-circle-x"></span></button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    @else
        <p>There aren't professions in the bin</p>
    @endif

    <hr>
    <br>
    <div class="d-flex justify-content-between align-items-end mb-3">
        <h1 class="pb-1">Deleted skills</h1>
    </div>

    @if ($skills->isNotEmpty())
    <table class="table" style="background-color: beige">
        <thead class="thead-dark">
        <tr>
            <th scope="col">#</th>
            <th scope="col">Name</th>
            <th scope="col">Acciones</th>
        </tr>
        </thead>
        <tbody>
        @foreach($skills as $skill)
            <tr>
                <th scope="row">{{ $skill->id }}</th>
                <td>{{ $skill->name }}</td>
                <td>
                    <form action="{{ route('skill.destroy', $skill) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <a class="btn btn-link" title="Undo delete" href="{{route('skill.restore',$skill->id)}}"><span class="oi oi-action-undo"></span></a>
                        <button type="submit" class="btn btn-link" dusk="delete-skill-{{$skill->id}}"><span class="oi oi-circle-x"></span></button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    @else
        <p>There aren't skill in the bin</p>
    @endif
@endsection
@section('sidebar')
@endsection