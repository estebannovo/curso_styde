<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        if(request()->has('empty')){
            $users = [];
        }else{
            $users = [
                'Joel',
                'Ellie',
                'Tess',
                'Tommy',
                'Bill',
                'Esteban',
                '<script type="javascript"> alert("hola");</script>'
            ];
        }

        $title = 'Listado de usuarios';

        //dd(compact('title', 'users'));

        return view('users.index', compact('title', 'users'));
    }

    public function show($id)
    {
        return view('users.show', compact('id'));
    }

    public function create()
    {
        return 'Crear usuario nuevo';
    }
}
