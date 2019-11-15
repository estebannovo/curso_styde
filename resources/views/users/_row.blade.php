<tr>
    <td rowspan="2">{{ $user->id }}</td>
    <th scope="row">
        {{ $user->name }}
        <span class="note">Nombre de Empresa</span>
    </th>
    <td>{{ $user->email }}</td>
    <td>{{ $user->role }}</td>
    <td>
        <span class="note">Registro: {{ $user->created_at->format('d/m/Y') }}</span>
        <span class="note">Último login: {{ $user->created_at->format('d/m/Y') }}</span>
    </td>
    <td class="text-right">
        @if ($user->trashed())
            <form action="{{ route('users.destroy', $user) }}" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-link" dusk="delete-{{$user->id}}"><span class="oi oi-circle-x"></span></button>
            </form>
        @else
            <form action="{{ route('users.trash', $user) }}" method="POST">
                @csrf
                @method('PATCH')
                <a href="{{route('users.show',['user'=>$user->id])}}" class="btn btn-outline-secondary btn-sm"><span class="oi oi-eye"></span></a>
                <a href="{{route('users.edit',$user)}}" class="btn btn-outline-secondary btn-sm"><span class="oi oi-pencil"></span></a>
                <button type="submit" class="btn btn-outline-danger btn-sm" dusk="delete-{{$user->id}}"><span class="oi oi-trash"></span></button>
            </form>
        @endif
    </td>
</tr>
<tr class="skills">
    <td colspan="1"><span class="note">{{ optional($user->profile->profession)->title}}</span></td>
    <td colspan="4"><span class="note">{{ $user->skills->implode('name', ', ') ?: 'Without Skills :(' }}</span></td>
</tr>