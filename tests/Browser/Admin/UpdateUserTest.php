<?php

namespace Tests\Browser\Admin;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\User\UserHelper;

class UpdateUserTest extends DuskTestCase
{
    use DatabaseMigrations;
    use UserHelper;

    /**
     * @test
     */
    public function it_can_edit_user()
    {
        //Firstly I have to create a new user to can update it later
        $this->loadNewUser();

        $this->browse(function (Browser $browser) {
            $browser->visit("/usuarios/{$this->user->id}/editar")
                ->assertValue('@name', $this->userData['name'])
                ->assertValue('@email', $this->userData['email'])
                ->assertSeeIn('@update', 'Actualizar usuario')
                ->assertSeeIn('p', 'Regresar al listado de usuarios')
                ->type('password', $this->userData['password'])
                ->press('Actualizar usuario')
                ->assertPathIs("/usuarios/{$this->user->id}")
                ->assertSeeIn('h1', "Usuario #{$this->user->id}")
                ->assertSeeIn('p', "Nombre del usuario: {$this->userData['name']}")
                ->assertSeeIn('@email', "Correo electrónico: {$this->userData['email']}");;
        });
    }

    /**
     * @test
     */
    public function the_field_name_its_required()
    {
        $this->loadNewUser();

        $this->browse(function (Browser $browser) {
            $browser->visit("/usuarios/{$this->user->id}/editar")
                ->assertValue('@name', $this->userData['name'])
                ->assertValue('@email', $this->userData['email'])
                ->assertSeeIn('@update', 'Actualizar usuario')
                ->assertSeeIn('p', 'Regresar al listado de usuarios')
                ->type('password', $this->userData['password'])
                ->type('name', '')
                ->press('Actualizar usuario')
                ->assertPathIs("/usuarios/{$this->user->id}/editar")
                ->assertSeeIn('.alert.alert-danger', "The field name is required")
                ->assertSeeIn('h4', "Editar usuario");
        });
    }
}
