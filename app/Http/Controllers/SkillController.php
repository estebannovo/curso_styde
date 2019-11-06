<?php

namespace App\Http\Controllers;

use App\Skill;

class SkillController extends Controller
{
    public function index()
    {
        $skills = Skill::query()
            ->withCount('users')
            ->orderBy('name')
            ->get();
        return view('skills.index', [
            'skills' => $skills,
        ]);
    }

    public function destroy(Skill $skill)
    {
        abort_if($skill->users()->exists(), 400, 'Cannot delete a skill linked to a profile.');

        $skill->delete();

        return redirect(route('skill.index'));
    }
}
