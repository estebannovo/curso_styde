<?php

namespace Tests\User;

use App\Profession;
use App\User;

trait UserHelper
{
    protected $profession;
    protected $user = null;

    protected $userData = [
        'name' => 'Duilio',
        'email' => 'duilio@styde.net',
        'password' => 'laravel',
        'bio' => 'Programador',
        'twitter' => 'https://twitter.com/sileence',
        'profession_id'=> ''
    ];

    function loadNewUser(){
        if(is_null($this->user)){
            $this->profession  = factory(Profession::class)->create();
            $this->userData['profession_id'] = $this->profession->id;

            $user = factory(User::class)->create([
                'name' => $this->userData['name'],
                'email' => $this->userData['email'],
                'password' => $this->userData['password']
            ]);

            $user->profile()->create([
                'bio' => $this->userData['bio'],
                'twitter' => $this->userData['twitter'],
                'profession_id'=> $this->userData['profession_id']
            ]);

            $this->user = $user;
        }
    }
}