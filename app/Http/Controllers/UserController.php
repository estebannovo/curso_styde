<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Forms\UserForm;
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
        //$user = new User;

        return new UserForm('users.create', new User);
//        return view('users.create', compact('user'))
//            ->with($this->formsData());
    }

    public function store(CreateUserRequest $request)
    {
        $request->createUser();
        return redirect()->route('users.index');
    }

    public function edit(User $user){
        return view('users.edit', compact('user'));
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
            'role' => '',
            'bio' => '',
            'profession_id' => '',
            'twitter' => '',
            'skills' => '',
        ],
            [
                'name.required' => 'The field name is required',
                'email.required' => 'The field email is required',
            ]);

        if ($data['password'] != null) {
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']);
        }

        $user->fill($data);
        $user->role = $data['role'] ?? 'user';
        $user->save();

        $user->profile()->update([
            'bio' => $data['bio'],
            'twitter' => $data['twitter'],
            'profession_id'=> $data['profession_id'],
        ]);

        $user->skills()->sync($data['skills'] ?? []);

        return redirect()->route('users.show', ['user' => $user]);
    }

    public function destroy(User $user){
        $user->profile()->delete();
        $user->delete();

        return redirect()->route('users.index');
    }
}
