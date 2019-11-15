<?php

namespace App\Http\Controllers;

use App\Profession;

class ProfessionController extends Controller
{
    public function index()
    {
        $professions = Profession::query()
            ->withCount('profiles')
            ->orderBy('title')
            ->get();

        return view('professions.index', [
           'professions' => $professions,
        ]);
    }

    public function trash(Profession $profession)
    {
        abort_if($profession->profiles()->exists(), 400, 'Cannot delete a profession linked to a profile.');
        $profession->delete();

        return redirect()->route('profession.index');
    }

    public function destroy($id)
    {
        $profession = Profession::onlyTrashed()->where('id', $id)->firstOrFail();

        $profession->forceDelete();

        return redirect()->route('trashed.index');
    }

    public function restore($id)
    {
        $profession = Profession::onlyTrashed()->where('id', $id)->firstOrFail();
        $profession->restore();

        return redirect()->route('profession.index');
    }
}
