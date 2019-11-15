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

    public function destroy($id)
    {
        $skill = Skill::onlyTrashed()->where('id', $id)->firstOrFail();

        $skill->forceDelete();

        return redirect()->route('trashed.index');
    }

    public function trash(Skill $skill)
    {
        abort_if($skill->users()->exists(), 400, 'Cannot delete a skill linked to a user.');
        $skill->delete();

        return redirect()->route('skill.index');
    }

    public function restore($id)
    {
        $skill = Skill::onlyTrashed()->where('id', $id)->firstOrFail();
        $skill->restore();

        return redirect()->route('skill.index');
    }
}
