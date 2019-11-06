<?php

namespace Tests\Feature\Admin;

use App\Profession;
use App\Skill;
use App\User;
use App\UserProfile;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UpdateUsersTest extends TestCase
{
    use RefreshDatabase;

    protected $defaultData = [
        'name' => 'Duilio Placios',
        'email' => 'duilio@styde.net',
        'password' => 'laravel',
        'bio' => 'Programador de Laravel y Vue.js',
        'twitter' => 'https://twitter/sileence',
        'role' => 'user'
    ];

    /** @test */
    function  it_loads_the_edit_user_page()
    {
        $user = factory(User::class)->create([
            /*'name'=> 'Duilio Placios',
            'email'=> 'duilio@styde.net',
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
        $oldProfession = factory(Profession::class)->create();

        $user->profile()->save(factory(UserProfile::class)->make([
            'profession_id'=> $oldProfession->id
        ]));

        $oldSkillA = factory(Skill::class)->create();
        $oldSkillB = factory(Skill::class)->create();

        $user->skills()->attach([$oldSkillA->id, $oldSkillB->id]);

        $newSkillA = factory(Skill::class)->create();
        $newSkillB = factory(Skill::class)->create();

        $this->put("/usuarios/{$user->id}", $this->withData([
            'skills'=>[
                $newSkillA->id, $newSkillB->id
            ],
            'role' => 'admin'
        ]))->assertRedirect("usuarios/{$user->id}");

        $this->assertCredentials([
            'name'=> 'Duilio Placios',
            'email'=> 'duilio@styde.net',
            'password' => 'laravel',
            'role' => 'admin'
        ]);

        $this->assertDatabaseHas('user_profiles', [
           'user_id' =>  $user->id,
           'bio' => 'Programador de Laravel y Vue.js',
           'twitter' => 'https://twitter/sileence',
           'profession_id' => $this->profession->id
        ]);

        $this->assertDatabaseCount('user_skill', 2);

        $this->assertDatabaseHas('user_skill',[
            'user_id' =>  $user->id,
            'skill_id' => $newSkillA->id
        ]);

        $this->assertDatabaseHas('user_skill',[
            'user_id' =>  $user->id,
            'skill_id' => $newSkillB->id
        ]);
    }

    /** @test */
    function  it_detaches_all_the_skills_if_none_is_checked()
    {
        $user = factory(User::class)->create();

        $oldSkillA = factory(Skill::class)->create();
        $oldSkillB = factory(Skill::class)->create();
        $user->skills()->attach([$oldSkillA->id, $oldSkillB->id]);

        $this->put("/usuarios/{$user->id}", $this->withData())
            ->assertRedirect("usuarios/{$user->id}");

        $this->assertDatabaseEmpty('user_skill');
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

        $this->assertDatabaseMissing('users', ['email'=>'duilio+2@styde.net']);
    }

    /** @test */
    function  the_role_is_required()
    {
        $this->handleValidationExceptions();

        $user = factory(User::class)->create();

        $this
            ->from("/usuarios/{$user->id}/editar")
            ->put("/usuarios/{$user->id}", $this->withData([
                'role'=> ''
            ]))
            ->assertRedirect("usuarios/{$user->id}/editar")
            ->assertSessionHasErrors(['role']);

        $this->assertDatabaseMissing('users', ['email'=>'duilio@styde.net']);
    }

    /** @test */
    function  the_bio_is_required()
    {
        $this->handleValidationExceptions();

        $user = factory(User::class)->create();

        $this
            ->from("/usuarios/{$user->id}/editar")
            ->put("/usuarios/{$user->id}", $this->withData([
                'bio'=> ''
            ]))
            ->assertRedirect("usuarios/{$user->id}/editar")
            ->assertSessionHasErrors(['bio']);

        $this->assertDatabaseMissing('users', ['email'=>'duilio@styde.net']);
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

        $this->assertDatabaseMissing('users', ['name'=>'Duilio Placios 2']);
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

        //$this->assertDatabaseMissing('users', ['name'=>'Duilio Placios 2', 'email'=>'duilio+3@styde.net']);
        $this->assertCredentials( [
            'name' => 'Duilio Placios',
            'email' => 'duilio@styde.net',
            'password' => $oldPassword, //Very important
        ]);
    }

    /** @test */
    function the_users_email_can_stay_the_same(){
        $user = factory(User::class)->create();
        $this->from("/usuarios/{$user->id}/editar")
            ->put("/usuarios/{$user->id}", $this->withData([
                'name'=> 'Duilio Placios 5',
                'email'=> 'duilio+5@styde.net',
                'password' => '1234535698',
            ]))
            ->assertRedirect("usuarios/{$user->id}"); // (user.show)
        //->assertSessionHasErrors(['password']);

        $this->assertDatabaseHas('users',[
            'name'=>'Duilio Placios 5',
            'email'=>'duilio+5@styde.net'
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

        $this->assertDatabaseMissing('users', ['name'=>'Duilio Placios']);
    }

    /** @test */
    function the_email_must_be_unique(){
        $this->handleValidationExceptions();

        factory(User::class)->create([
            'email'=> 'existing-email@example.com'
        ]);

        $user = factory(User::class)->create([
            'email'=> 'duilio+3@styde.net'
        ]);

        $this
            ->from("/usuarios/{$user->id}/editar")
            ->put("/usuarios/{$user->id}",
                $this->withData([
                    'email'=> 'existing-email@example.com',
                ]))
            ->assertRedirect("usuarios/{$user->id}/editar")
            ->assertSessionHasErrors(['email']);

//        $this->assertDatabaseMissing('users', ['name'=>'Duilio Placios 2']);
    }

    /** @test */
    /*function the_password_must_be_at_least_six_characters(){
        $user = factory(User::class)->create();
        $this
            ->from("/usuarios/{$user->id}/editar")
            ->put("/usuarios/{$user->id}", [
                'name'=> 'Duilio Placios 2',
                'email'=> 'duilio+3@styde.net',
                'password' => ''
            ])
            ->assertRedirect("usuarios/{$user->id}/editar");
            //->assertSessionHasErrors(['password']);

        $this->assertDatabaseMissing('users', ['email'=>'duilio+3@styde.net']);
    }*/
}