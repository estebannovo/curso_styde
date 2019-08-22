<?php

namespace Tests\Feature;

use App\Profession;
use App\User;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Throwable;

class UsersModuleTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function it_shows_the_users_list()
    {
        factory(Profession::class)->times(5)->create();

        factory(User::class)->create([
            'name' => 'Joel',
            'profession_id' => Profession::all()->random()->id
        ]);

        factory(User::class)->create([
            'name'=>'Ellie',
            'profession_id' => Profession::all()->random()->id
        ]);

        $this->get('/usuarios')
            ->assertStatus(200)
            ->assertSee('Listado de usuarios')
            ->assertSee('Joel')
            ->assertSee('Ellie');
    }

    /** @test */
    function it_shows_a_defaut_message_if_the_users_list_is_empty()
    {
        //DB::table('users')->truncate();
        
        $this->get('/usuarios')
            ->assertStatus(200)
            ->assertSee('No hay usuarios registrados');
    }

    /** @test */
    function it_displays_the_users_details()
    {
        $user = factory(User::class)->create([
            'name' => 'Marcelo Gularte',
        ]);

        $this->get('/usuarios/'.$user->id)
            ->assertStatus(200)
            ->assertSee('Marcelo Gularte');
            //->assertSee('Mostrando detalle  del usuario: '.$user->id);
    }

    /** @test */
    function  it_loads_the_new_users_page()
    {
        $this->withoutExceptionHandling();

        $this->get('/usuarios/nuevo')
            ->assertStatus(200)
            ->assertSee('Crear usuario nuevo');
    }
}
