<?php

namespace App\Http\Controllers;

use App\User;
use http\Env\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function index()
    {
        //$users = DB::table('users')->get();
        $users = User::all();
        //dd($users);

        $title = 'Listado de usuarios';

        //dd(compact('title', 'users'));

        /*return view('users.index')
            ->with('users', User::all())
            ->with('title', 'Listado de Usuarios');*/

        return view('users.index', compact('title', 'users'));
    }

    public function show(User $user)
    {
        //$user = User::findOrFail($id);
        /*if($user==null){
            return \response()->view('errors.404', [], 404);
        }*/
        return view('users.show', compact('user'));
    }

    public function create()
    {
        return view('users.create');
    }

    public function store()
    {
        $data = \request()->validate([
            'name' => 'required',
            'email' => ['required','email','unique:users,email'],
            'password' => 'required|min:6',
        ],
        [
            'name.required' => 'The field name is required',
            'email.required' => 'The field email is required',
            'password.required' => 'The field password is required'
        ]);

        /*if(empty($data['name'])){
            return redirect('usuarios/nuevo')->withErrors([
                'name' => 'The field name is required'
            ]);
        }*/
        User::create([
            'name'=> $data['name'],
            'email'=> $data['email'],
            'password' => bcrypt($data['password'])
        ]);

        return redirect()->route('users.index');
    }

    public function edit(User $user){
        return view('users.edit', ['user'=> $user]);
    }

    public function update(User $user){
//        $data = request()->all();

        $data = \request()->validate([
            'name' => 'required',
            'email' => ['required','email'],
            'password' => 'required|min:6',
        ],
            [
                'name.required' => 'The field name is required',
                'email.required' => 'The field email is required',
                'password.required' => 'The field password is required'
            ]);



        $data['password'] = bcrypt($data['password']);
        $user->update($data);
        return redirect()->route('users.show', ['user' => $user]);
    }
}
