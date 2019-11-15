@extends('layout')

@section('title', 'Habilidades')

@section('content')
    <div class="d-flex justify-content-between align-items-end mb-3">
        <h1 class="pb-1">Listado de habilidades</h1>
    </div>

    <table class="table">
        <thead class="thead-dark">
        <tr>
            <th scope="col">#</th>
            <th scope="col">Título</th>
            <th scope="col">Asociaciones</th>
            <th scope="col">Acciones</th>
        </tr>
        </thead>
        <tbody>
        @foreach($skills as $skill)
            <tr>
                <th scope="row">{{ $skill->id }}</th>
                <td>{{ $skill->name }}</td>
                <td>{{ $skill->users_count }}</td>
                <td>
                    @if ($skill->users_count == 0)
                    <form action="{{ route('skill.trash', $skill) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-link"><span class="oi oi-trash"></span></button>
                    </form>
                    @else
                        <button type="button" class="btn btn-link" disabled><span class="oi oi-trash"></span></button>
                    @endif
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection

@section('sidebar')
    @parent
@endsection