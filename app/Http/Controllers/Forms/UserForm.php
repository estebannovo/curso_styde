<?php

namespace App\Http\Controllers\Forms;

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Support\Facades\Auth;
use App\{User, Profession, Skill};

class UserForm implements Responsable
{
    private $view;
    private $user;

    public function __construct($view, User $user)
    {
        $this->view = $view;
        $this->user = $user;
    }

    public function toResponse($request)
    {
        $loggedUser = Auth::user();
        return view($this->view, [
            'user'=>$this->user,
            'professions' => Profession::orderBy('title', 'ASC')->get(),
            'skills' => Skill::orderBy('name', 'ASC')->get(),
            'roles' => trans('users.roles'),
            'isAdmin' => isset($loggedUser)?$loggedUser->isAdmin(): null,
        ]);
    }
}