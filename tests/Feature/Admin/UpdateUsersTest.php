<?php

namespace Tests\Feature\Admin;

use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UpdateUsersTest extends TestCase
{
    use RefreshDatabase;

    protected $defaultData = [
        'name' => 'Esteban Novo',
        'email' => 'novo.esteban@gmail.com',
        'password' => 'laravel',
        'bio' => 'Programador de Laravel y Vue.js',
        'twitter' => 'https://twitter/estebannovo',
        'role' => 'user'
    ];

    /** @test */
    function  it_loads_the_edit_user_page()
    {
        $user = factory(User::class)->create([
            /*'name'=> 'Esteban Novo',
            'email'=> 'novo.esteban@gmail.com',
            'password' => 'laravel'*/
        ]);

        $this->get("/usuarios/{$user->id}/editar")
            ->assertStatus(200)
            ->assertViewIs('users.edit')
            ->assertSee('Editar usuario')
            ->assertViewHas('user', function($viewUser) use ($user){
                return $viewUser->id == $user->id;
            });
    }

    /** @test */
    function  it_updates_a_user()
    {
        $user = factory(User::class)->create();

        $this->put("/usuarios/{$user->id}", $this->withData())->assertRedirect("usuarios/{$user->id}");

        $this->assertCredentials([
            'name'=> 'Esteban Novo',
            'email'=> 'novo.esteban@gmail.com',
            'password' => 'laravel'
        ]);
    }

    /** @test */
    function  the_name_is_required()
    {
        $this->handleValidationExceptions();

        $user = factory(User::class)->create();

        $this
            ->from("/usuarios/{$user->id}/editar")
            ->put("/usuarios/{$user->id}", $this->withData([
                'name'=> ''
            ]))
            ->assertRedirect("usuarios/{$user->id}/editar")
            ->assertSessionHasErrors(['name']);

        $this->assertDatabaseMissing('users', ['email'=>'novo.esteban+2@gmail.com']);
    }

    /** @test */
    function the_email_is_required(){
        $this->handleValidationExceptions();

        $user = factory(User::class)->create();

        $this
            ->from("/usuarios/{$user->id}/editar")
            ->put("/usuarios/{$user->id}", $this->withData([
                'email'=> ''
            ]))
            ->assertRedirect("usuarios/{$user->id}/editar")
            ->assertSessionHasErrors(['email']);

        $this->assertDatabaseMissing('users', ['name'=>'Esteban Novo 2']);
    }

    /** @test */
    function the_password_is_optional(){
        $oldPassword = 'CLAVE_ANTERIOR';
        $user = factory(User::class)->create([
            'password' => bcrypt($oldPassword),
        ]);

        $this
            ->from("/usuarios/{$user->id}/editar")
            ->put("/usuarios/{$user->id}", $this->withData([
                'password' => '',
            ]))
            ->assertRedirect("usuarios/{$user->id}"); // (user.show)
        //->assertSessionHasErrors(['password']);

        //$this->assertDatabaseMissing('users', ['name'=>'Esteban Novo 2', 'email'=>'novo.esteban+3@gmail.com']);
        $this->assertCredentials( [
            'name' => 'Esteban Novo',
            'email' => 'novo.esteban@gmail.com',
            'password' => $oldPassword, //Very important
        ]);
    }

    /** @test */
    function the_users_email_can_stay_the_same(){
        $user = factory(User::class)->create();
        $this->from("/usuarios/{$user->id}/editar")
            ->put("/usuarios/{$user->id}", [
                'name'=> 'Esteban Novo 5',
                'email'=> 'novo.esteban+5@gmail.com',
                'password' => '1234535698',
            ])
            ->assertRedirect("usuarios/{$user->id}"); // (user.show)
        //->assertSessionHasErrors(['password']);

        $this->assertDatabaseHas('users',[
            'name'=>'Esteban Novo 5',
            'email'=>'novo.esteban+5@gmail.com'
        ]);
    }

    /** @test */
    function the_email_must_be_valid(){
        $this->handleValidationExceptions();
        $user = factory(User::class)->create();
        $this
            ->from("/usuarios/{$user->id}/editar")
            ->put("/usuarios/{$user->id}", $this->withData([
                'email'=> 'correo-no-valido',
            ]))
            ->assertRedirect("usuarios/{$user->id}/editar")
            ->assertSessionHasErrors(['email']);

        $this->assertDatabaseMissing('users', ['name'=>'Esteban Novo']);
    }

    /** @test */
    function the_email_must_be_unique(){
        $this->handleValidationExceptions();

        factory(User::class)->create([
            'email'=> 'existing-email@example.com'
        ]);

        $user = factory(User::class)->create([
            'email'=> 'novo.esteban+3@gmail.com'
        ]);

        $this
            ->from("/usuarios/{$user->id}/editar")
            ->put("/usuarios/{$user->id}",
                $this->withData([
                    'email'=> 'existing-email@example.com',
                ]))
            ->assertRedirect("usuarios/{$user->id}/editar")
            ->assertSessionHasErrors(['email']);

//        $this->assertDatabaseMissing('users', ['name'=>'Esteban Novo 2']);
    }

    /** @test */
    /*function the_password_must_be_at_least_six_characters(){
        $user = factory(User::class)->create();
        $this
            ->from("/usuarios/{$user->id}/editar")
            ->put("/usuarios/{$user->id}", [
                'name'=> 'Esteban Novo 2',
                'email'=> 'novo.esteban+3@gmail.com',
                'password' => ''
            ])
            ->assertRedirect("usuarios/{$user->id}/editar");
            //->assertSessionHasErrors(['password']);

        $this->assertDatabaseMissing('users', ['email'=>'novo.esteban+3@gmail.com']);
    }*/
}