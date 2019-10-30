<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateUserRequest;
use App\Profession;
use App\Skill;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();

        $title = 'Listado de usuarios';

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
        $professions = Profession::orderBy('title', 'ASC')->get();
        $skills = Skill::orderBy('name', 'ASC')->get();

        $roles = trans('users.roles');

        $user = Auth::user();
        $isAdmin = isset($user)?Auth::user()->isAdmin(): null;

        return view('users.create', compact('professions', 'skills', 'roles', 'isAdmin'));
    }

    public function store(CreateUserRequest $request)
    {
        $request->createUser();
        return redirect()->route('users.index');
    }

    public function edit(User $user){
        return view('users.edit', ['user'=> $user]);
    }

    public function update(User $user){
//        $data = request()->all();
        $data = \request()->validate([
            'name' => 'required',
            'email' => [
                'required',
                'email',
                Rule::unique('users')->ignore($user->id)
            ],
            'password' => '',
        ],
            [
                'name.required' => 'The field name is required',
                'email.required' => 'The field email is required',
            ]);

        if($data['password'] != null){
            $data['password'] = bcrypt($data['password']);
        }else{
            unset($data['password']);
        }
        $user->update($data);
        return redirect()->route('users.show', ['user' => $user]);
    }

    public function destroy(User $user){
        $user->profile()->delete();
        $user->delete();

        return redirect()->route('users.index');
    }
}
