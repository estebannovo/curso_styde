<?php

namespace App\Http\ViewComponents;

use App\Profession;
use App\Skill;
use App\User;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Auth;

class UserFields implements Htmlable
{
    private $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function toHtml()
    {
        $loggedUser = Auth::user();

        return view('users._fields', [
            'professions' => Profession::orderBy('title', 'ASC')->get(),
            'skills' => Skill::orderBy('name', 'ASC')->get(),
            'roles' => trans('users.roles'),
            'isAdmin' => isset($loggedUser)?$loggedUser->isAdmin(): null,
            'user' => $this->user,
        ]);
    }
}