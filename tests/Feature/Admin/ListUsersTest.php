<?php

namespace Tests\Feature\Admin;

use App\Profession;
use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ListUsersTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function it_shows_the_users_list()
    {
        factory(Profession::class)->times(5)->create();

        factory(User::class)->create([
            'name' => 'Joel',
            //'profession_id' => Profession::all()->random()->id
        ]);

        factory(User::class)->create([
            'name'=>'Ellie',
            //'profession_id' => Profession::all()->random()->id
        ]);

        $this->get('/usuarios')
            ->assertStatus(200)
            ->assertSee('Listado de usuarios')
            ->assertSee('Joel')
            ->assertSee('Ellie');
    }


    /** @test */
    function it_paginates_the_users()
    {
        factory(User::class)->create([
            'name' => 'Tercer Usuario',
            'created_at' => now()->subDay(5),
        ]);

        factory(User::class)->create([
            'name' => 'Primer Usuario',
            'created_at' => now()->subWeek(),
        ]);

        factory(User::class)->create([
            'name' => 'Segundo Usuario',
            'created_at' => now()->subDay(6),
        ]);

        factory(User::class)->times(12)->create([
            'created_at' => now()->subDay(4),
        ]);

        factory(User::class)->create([
            'name'=>'Decimosseptimo Usuario',
            'created_at' => now()->subDay(2),
        ]);

        factory(User::class)->create([
            'name'=>'Decimosexto Usuario',
            'created_at' => now()->subDay(3),
        ]);

        $this->get('/usuarios')
            ->assertStatus(200)
            ->assertSeeInOrder([
                'Decimosseptimo Usuario',
                'Decimosexto Usuario',
                'Tercer Usuario',
            ])
            ->assertDontSee('Segundo Usuario')
            ->assertDontSee('Primer Usuario');

        $this->get('/usuarios?page=2')
            ->assertStatus(200)
            ->assertSeeInOrder([
                'Segundo Usuario',
                'Primer Usuario'
            ])
            ->assertDontSee('Tercer Usuario');
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
    function it_shows_the_deleted_users()
    {
        factory(Profession::class)->times(5)->create();

        factory(User::class)->create([
            'name' => 'Joel',
            'deleted_at' => now(),
            //'profession_id' => Profession::all()->random()->id
        ]);

        factory(User::class)->create([
            'name'=>'Ellie',
            //'profession_id' => Profession::all()->random()->id
        ]);

        $this->get('/trahed-items/')
            ->assertStatus(200)
            ->assertSee('Deleted users')
            ->assertSee('Joel')
            ->assertDontSee('Ellie');
    }
}
