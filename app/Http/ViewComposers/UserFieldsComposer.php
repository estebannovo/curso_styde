<?php

namespace App\Http\ViewComposers;

use App\Profession;
use App\Skill;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class UserFieldsComposer
{
    public function compose(View $view)
    {
        $professions = Profession::orderBy('title', 'ASC')->get();
        $skills = Skill::orderBy('name', 'ASC')->get();
        $roles = trans('users.roles');

        $loggedUser = Auth::user();
        $isAdmin = isset($loggedUser)?$loggedUser->isAdmin(): null;

        $view->with(compact('professions', 'skills', 'roles', 'isAdmin'));
    }
}