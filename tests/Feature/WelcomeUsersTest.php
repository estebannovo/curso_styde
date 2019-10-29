<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Throwable;

class WelcomeUsersTest extends TestCase
{
    /** @test  */
    function it_welcomes_users_with_nickname()
    {
        $this->get('/saludo/esteban/novo')
            ->assertStatus(200)
            ->assertSee("Bienvenido Esteban, tu apodo es novo");
    }

    /** @test  */
    function it_welcomes_users_without_nickname()
    {
        $this->get('/saludo/esteban')
            ->assertStatus(200)
            ->assertSee("Bienvenido Esteban");
    }

    /** @test  */
    /*function i_can_edit_users(){
        $this->get('usuarios/1/editar')
            ->assertStatus(200)
            ->assertSee("Editar usuario");
    }*/

    /** @test  */
    function it_users_edit_can_not_accept_text(){
        $this->withExceptionHandling();
        $this->get('usuarios/texto/edit')
            ->assertStatus(404);
    }
}
