<?php

namespace Tests\Browser\Admin;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ListUserTest extends DuskTestCase
{
    use DatabaseMigrations;

    /**
     * @test
     */
    public function it_can_see_user_list()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/usuarios')
                ->assertSeeIn('h1', 'Listado de usuarios')
                ->assertSee('Nuevo usuario');
        });
    }
}
