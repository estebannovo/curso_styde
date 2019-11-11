<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Forms\UserForm;
use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\UpdateUserRequest;
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

    public function trashed()
    {
        $users = User::onlyTrashed()->get();

        $title = 'Listado de usuarios en la papelera';

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

    public function update(UpdateUserRequest $request, User $user)
    {
        $request->updateUser($user);

        return redirect()->route('users.show', ['user' => $user]);
    }

    public function destroy($id)
    {
        $user = User::onlyTrashed()->where('id', $id)->firstOrFail();

        $user->profile()->forceDelete();
        $user->forceDelete();

        return redirect()->route('users.trashed');
    }

    public function trash(User $user)
    {
        $user->delete();
        $user->profile()->delete();

        return redirect()->route('users.index');
    }
}
