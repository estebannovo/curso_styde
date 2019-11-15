<?php

namespace App\Http\Controllers;

use App\Profession;
use App\Skill;
use App\User;

class TrashController extends Controller
{
    public function index()
    {
        $users = User::onlyTrashed()->get();

        $professions = Profession::onlyTrashed()->get();

        $skills = Skill::onlyTrashed()->get();

        return view('trash.index', compact('users', 'professions', 'skills'));
    }
}
